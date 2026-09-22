<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use App\Services\AuditService;

class AttendanceController extends Controller
{
    private const STATUSES = ['present', 'absent', 'late', 'excused'];

    public function index(Request $request)
    {
        $school = $request->user()->school;
        abort_unless(Gate::allows('manage-school-setup') || $request->user()->hasRole('Teacher'), 403);
        if ($request->user()->hasRole('Teacher')) {
            $teacher = $request->user()->staffProfile;
            $classes = $school->classes()->whereHas('timetableSlots', fn ($q) => $q->where('staff_id', $teacher?->id))->with('branch')->where('status', 'active')->orderBy('name')->get();
        } else {
            $classes = $school->classes()->with('branch')->where('status', 'active')->orderBy('name')->get();
        }
        $classes = $school->classes()->with('branch')->where('status', 'active')->orderBy('name')->get();
        $selectedClassId = $request->integer('class_id') ?: $classes->first()?->id;
        $date = $request->date('date')?->toDateString() ?: now()->toDateString();
        $selectedClass = $classes->firstWhere('id', $selectedClassId);
        $students = $selectedClass
            ? $selectedClass->students()->where('status', 'active')->orderBy('last_name')->orderBy('first_name')->get()
            : collect();
        $session = $selectedClass
            ? AttendanceSession::with('records')->where('school_id', $school->id)->where('class_id', $selectedClass->id)->whereDate('attendance_date', $date)->where('session', 'daily')->first()
            : null;
        $existing = $session?->records->keyBy('student_id') ?? collect();

        return Inertia::render('Attendance/Index', [
            'classes' => $classes->map(fn ($schoolClass) => ['id' => $schoolClass->id, 'name' => $schoolClass->name, 'level' => $schoolClass->level, 'branch' => $schoolClass->branch?->name]),
            'selectedClassId' => $selectedClass?->id,
            'selectedDate' => $date,
            'session' => $session ? ['id' => $session->id, 'notes' => $session->notes] : null,
            'students' => $students->map(fn (Student $student) => [
                'id' => $student->id,
                'name' => trim($student->first_name.' '.$student->last_name),
                'admission_no' => $student->admission_no,
                'status' => $existing->get($student->id)?->status,
                'note' => $existing->get($student->id)?->note,
            ]),
            'summary' => $this->summary($school->id, $selectedClass?->id, $date),
            'canViewReports' => Gate::allows('manage-school-setup'),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(Gate::allows('manage-school-setup') || $request->user()->hasRole('Teacher'), 403);
        $data = $request->validate([
            'class_id' => ['required', 'integer'],
            'attendance_date' => ['required', 'date'],
            'session' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'records' => ['required', 'array', 'min:1'],
            'records.*.student_id' => ['required', 'integer'],
            'records.*.status' => ['required', 'in:present,absent,late,excused'],
            'records.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $school = $request->user()->school;
        $class = $school->classes()->with('branch')->whereKey($data['class_id'])->firstOrFail();
        if ($request->user()->hasRole('Teacher')) {
            abort_unless($school->timetableSlots()->where('staff_id', $request->user()->staffProfile?->id)->where('class_id', $class->id)->exists(), 403);
        }
        $students = $class->students()->whereIn('id', collect($data['records'])->pluck('student_id'))->get()->keyBy('id');
        abort_if($students->count() !== count($data['records']), 422, 'Every attendance student must belong to the selected class.');

        DB::transaction(function () use ($school, $class, $data, $request): void {
            $sessionKey = [
                'school_id' => $school->id,
                'class_id' => $class->id,
                'attendance_date' => $data['attendance_date'],
                'session' => $data['session'] ?? 'daily',
            ];
            DB::table('attendance_sessions')->updateOrInsert(
                $sessionKey,
                [
                    'academic_year_id' => $class->academic_year_id,
                    'branch_id' => $class->branch_id,
                    'recorded_by' => $request->user()->id,
                    'notes' => $data['notes'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
            $session = AttendanceSession::where($sessionKey)->firstOrFail();

            foreach ($data['records'] as $record) {
                Attendance::updateOrCreate(
                    ['attendance_session_id' => $session->id, 'student_id' => $record['student_id']],
                    ['recorded_by' => $request->user()->id, 'status' => $record['status'], 'note' => $record['note'] ?? null],
                );
            }
        });

        app(AuditService::class)->record($request, 'attendance.recorded', 'Attendance saved for '.$class->name.'.', $class, ['date' => $data['attendance_date']]);

        return redirect()->route('attendance.index', ['class_id' => $class->id, 'date' => $data['attendance_date']])->with('success', 'Attendance saved successfully.');
    }

    public function report(Request $request)
    {
        Gate::authorize('manage-school-setup');
        $school = $request->user()->school;
        $from = $request->date('from')?->startOfDay() ?: now()->startOfMonth();
        $to = $request->date('to')?->endOfDay() ?: now()->endOfDay();
        $query = Attendance::query()->with(['student.schoolClass', 'session'])->whereHas('session', fn ($q) => $q->where('school_id', $school->id)->whereBetween('attendance_date', [$from->toDateString(), $to->toDateString()]));

        $records = $query->latest()->paginate(50)->withQueryString();

        return Inertia::render('Attendance/Report', [
            'records' => $records->through(fn (Attendance $record) => [
                'id' => $record->id,
                'student' => trim($record->student->first_name.' '.$record->student->last_name),
                'class' => $record->student->schoolClass?->name,
                'date' => $record->session->attendance_date->format('Y-m-d'),
                'status' => $record->status,
                'note' => $record->note,
            ]),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }

    private function summary(int $schoolId, ?int $classId, string $date): array
    {
        if (! $classId) {
            return array_fill_keys(self::STATUSES, 0);
        }

        return Attendance::whereHas('session', fn ($query) => $query->where('school_id', $schoolId)->where('class_id', $classId)->whereDate('attendance_date', $date))
            ->select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status')->all() + array_fill_keys(self::STATUSES, 0);
    }
}