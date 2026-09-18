import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function StudentsIndex({ students = [], parents = [] }) {
    const noteForm = useForm({
        student_id: '',
        title: '',
        note: '',
        category: 'General',
    });

    const timelineForm = useForm({
        student_id: '',
        title: '',
        description: '',
        event_type: 'general',
    });

    const submitNote = (e) => {
        e.preventDefault();
        noteForm.post('/students/notes', {
            preserveScroll: true,
            onSuccess: () => noteForm.reset(),
        });
    };

    const submitTimeline = (e) => {
        e.preventDefault();
        timelineForm.post('/students/timeline', {
            preserveScroll: true,
            onSuccess: () => timelineForm.reset(),
        });
    };

    return (
        <>
            <Head title="Students" />
            <div className="space-y-6">
                <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Directory</p>
                            <h2 className="mt-1 text-2xl font-bold text-slate-900">Students and parents</h2>
                            <p className="mt-1 text-sm text-slate-500">Review all records before adding a new student profile.</p>
                        </div>
                        <Link href="/students/create" className="rounded-xl bg-slate-900 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-slate-700">
                            Add student
                        </Link>
                    </div>

                    <div className="mt-6 overflow-x-auto">
                        <table className="w-full min-w-[640px] text-left text-sm">
                            <thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th className="px-3 py-3 font-semibold">Student</th>
                                    <th className="px-3 py-3 font-semibold">Admission no.</th>
                                    <th className="px-3 py-3 font-semibold">Gender</th>
                                    <th className="px-3 py-3 font-semibold">Date of birth</th>
                                    <th className="px-3 py-3 font-semibold">Status</th>
                                    <th className="px-3 py-3 font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {students.length > 0 ? students.map((student) => (
                                    <tr key={student.id} className="text-slate-700">
                                        <td className="px-3 py-4 font-semibold text-slate-900">{student.first_name} {student.last_name}</td>
                                        <td className="px-3 py-4">{student.admission_no || 'Not assigned'}</td>
                                        <td className="px-3 py-4">{student.gender || 'Not specified'}</td>
                                        <td className="px-3 py-4">{student.date_of_birth || 'Not specified'}</td>
                                        <td className="px-3 py-4"><span className="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">{student.status}</span></td>
                                        <td className="px-3 py-4"><Link href={`/students/${student.id}`} className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Show details</Link></td>
                                    </tr>
                                )) : (
                                    <tr><td colSpan="6" className="px-3 py-8 text-center text-slate-500">No student records yet.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 className="text-xl font-semibold text-slate-900">Parent records</h2>
                        <div className="mt-5 space-y-3">
                            {parents.length > 0 ? parents.map((parent) => (
                                <div key={parent.id} className="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <div className="font-semibold text-slate-900">{parent.first_name} {parent.last_name}</div>
                                    <div className="mt-1 text-xs text-slate-500">{parent.relationship || 'Guardian'} • {parent.phone || 'No phone'}</div>
                                </div>
                            )) : <p className="text-sm text-slate-500">No parents linked yet.</p>}
                        </div>
                    </div>
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 className="text-xl font-semibold text-slate-900">Student notes</h2>
                        <form onSubmit={submitNote} className="mt-5 space-y-3">
                            <select value={noteForm.data.student_id} onChange={(e) => noteForm.setData('student_id', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900">
                                <option value="">Select student</option>
                                {students.map((student) => (
                                    <option key={student.id} value={student.id}>{student.first_name} {student.last_name}</option>
                                ))}
                            </select>
                            <input placeholder="Title" value={noteForm.data.title} onChange={(e) => noteForm.setData('title', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <textarea placeholder="Write a note…" value={noteForm.data.note} onChange={(e) => noteForm.setData('note', e.target.value)} rows={5} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Category" value={noteForm.data.category} onChange={(e) => noteForm.setData('category', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <button type="submit" disabled={noteForm.processing} className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70">
                                {noteForm.processing ? 'Saving...' : 'Add note'}
                            </button>
                        </form>
                    </div>

                    <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 className="text-xl font-semibold text-slate-900">Student timeline</h2>
                        <form onSubmit={submitTimeline} className="mt-5 space-y-3">
                            <select value={timelineForm.data.student_id} onChange={(e) => timelineForm.setData('student_id', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900">
                                <option value="">Select student</option>
                                {students.map((student) => (
                                    <option key={student.id} value={student.id}>{student.first_name} {student.last_name}</option>
                                ))}
                            </select>
                            <input placeholder="Event title" value={timelineForm.data.title} onChange={(e) => timelineForm.setData('title', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <textarea placeholder="Description" value={timelineForm.data.description} onChange={(e) => timelineForm.setData('description', e.target.value)} rows={5} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Event type" value={timelineForm.data.event_type} onChange={(e) => timelineForm.setData('event_type', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <button type="submit" disabled={timelineForm.processing} className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70">
                                {timelineForm.processing ? 'Saving...' : 'Add timeline event'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}

StudentsIndex.layout = AuthenticatedLayout;
