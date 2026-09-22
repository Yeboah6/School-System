import { Head, Link, router, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

const statusStyles = {
    present: "bg-emerald-100 text-emerald-700",
    absent: "bg-rose-100 text-rose-700",
    late: "bg-amber-100 text-amber-700",
    excused: "bg-sky-100 text-sky-700",
};

export default function AttendanceIndex({
    classes = [],
    students = [],
    selectedClassId = "",
    selectedDate,
    summary = {},
    session,
    canViewReports = false,
}) {
    const form = useForm({
        class_id: selectedClassId || "",
        attendance_date: selectedDate,
        session: "daily",
        notes: session?.notes || "",
        records: students.map((student) => ({
            student_id: student.id,
            status: student.status || "present",
            note: student.note || "",
        })),
    });
    const selectClass = (event) =>
        router.get(
            "/attendance",
            { class_id: event.target.value, date: selectedDate },
            { preserveState: true, preserveScroll: true },
        );
    const submit = (event) => {
        event.preventDefault();
        form.post("/attendance", { preserveScroll: true });
    };
    const setAll = (status) =>
        form.setData(
            "records",
            form.data.records.map((record) => ({ ...record, status })),
        );
    const setStatus = (studentId, status) =>
        form.setData(
            "records",
            form.data.records.map((record) =>
                record.student_id === studentId
                    ? { ...record, status }
                    : record,
            ),
        );

    return (
        <>
            <Head title="Attendance" />
            <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                            Daily records
                        </p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">
                            Student attendance
                        </h2>
                        <p className="mt-2 text-sm text-slate-500">
                            Record one attendance session per class and day.
                        </p>
                    </div>
                    {canViewReports && <Link
                        href="/attendance/report"
                        className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700"
                    >
                        View reports
                    </Link>}
                </div>
                <div className="grid gap-4 rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm md:grid-cols-[1fr_220px_auto] md:items-end">
                    <label className="text-sm font-medium text-slate-700">
                        Class
                        <select
                            value={selectedClassId || ""}
                            onChange={selectClass}
                            className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5"
                        >
                            <option value="">Select class</option>
                            {classes.map((schoolClass) => (
                                <option
                                    key={schoolClass.id}
                                    value={schoolClass.id}
                                >
                                    {schoolClass.name}
                                    {schoolClass.branch
                                        ? ` · ${schoolClass.branch}`
                                        : ""}
                                </option>
                            ))}
                        </select>
                    </label>
                    <label className="text-sm font-medium text-slate-700">
                        Date
                        <input
                            type="date"
                            value={selectedDate}
                            onChange={(event) =>
                                router.get(
                                    "/attendance",
                                    {
                                        class_id: selectedClassId,
                                        date: event.target.value,
                                    },
                                    { preserveState: true },
                                )
                            }
                            className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5"
                        />
                    </label>
                    <div className="flex gap-2">
                        <button
                            type="button"
                            onClick={() => setAll("present")}
                            className="rounded-xl bg-emerald-600 px-3 py-2.5 text-xs font-semibold text-white"
                        >
                            Present all
                        </button>
                        <button
                            type="button"
                            onClick={() => setAll("absent")}
                            className="rounded-xl bg-rose-600 px-3 py-2.5 text-xs font-semibold text-white"
                        >
                            Absent all
                        </button>
                    </div>
                </div>
                <div className="grid grid-cols-2 gap-3 md:grid-cols-4">
                    {["present", "absent", "late", "excused"].map((status) => (
                        <div
                            key={status}
                            className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
                        >
                            <p className="text-xs uppercase tracking-wide text-slate-400">
                                {status}
                            </p>
                            <p className="mt-2 text-2xl font-bold text-slate-900">
                                {summary[status] || 0}
                            </p>
                        </div>
                    ))}
                </div>
                <form
                    onSubmit={submit}
                    className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm"
                >
                    <div className="overflow-x-auto">
                        <table className="w-full min-w-[680px] text-left text-sm">
                            <thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th className="px-3 py-3">Student</th>
                                    <th className="px-3 py-3">Admission no.</th>
                                    <th className="px-3 py-3">Status</th>
                                    <th className="px-3 py-3">Note</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {students.length ? (
                                    students.map((student) => {
                                        const record = form.data.records.find(
                                            (item) =>
                                                item.student_id === student.id,
                                        );
                                        return (
                                            <tr key={student.id}>
                                                <td className="px-3 py-4 font-semibold text-slate-900">
                                                    {student.name}
                                                </td>
                                                <td className="px-3 py-4 text-slate-500">
                                                    {student.admission_no ||
                                                        "Not assigned"}
                                                </td>
                                                <td className="px-3 py-4">
                                                    <select
                                                        value={
                                                            record?.status ||
                                                            "present"
                                                        }
                                                        onChange={(event) =>
                                                            setStatus(
                                                                student.id,
                                                                event.target
                                                                    .value,
                                                            )
                                                        }
                                                        className={`rounded-full border-0 px-3 py-1.5 text-xs font-semibold ${statusStyles[record?.status || "present"]}`}
                                                    >
                                                        <option value="present">
                                                            Present
                                                        </option>
                                                        <option value="absent">
                                                            Absent
                                                        </option>
                                                        <option value="late">
                                                            Late
                                                        </option>
                                                        <option value="excused">
                                                            Excused
                                                        </option>
                                                    </select>
                                                </td>
                                                <td className="px-3 py-4">
                                                    <input
                                                        value={
                                                            record?.note || ""
                                                        }
                                                        onChange={(event) =>
                                                            form.setData(
                                                                "records",
                                                                form.data.records.map(
                                                                    (item) =>
                                                                        item.student_id ===
                                                                        student.id
                                                                            ? {
                                                                                  ...item,
                                                                                  note: event
                                                                                      .target
                                                                                      .value,
                                                                              }
                                                                            : item,
                                                                ),
                                                            )
                                                        }
                                                        placeholder="Optional note"
                                                        className="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs"
                                                    />
                                                </td>
                                            </tr>
                                        );
                                    })
                                ) : (
                                    <tr>
                                        <td
                                            colSpan="4"
                                            className="px-3 py-10 text-center text-slate-500"
                                        >
                                            Create a class and add active
                                            students before recording
                                            attendance.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    <div className="mt-5 flex justify-end">
                        <button
                            type="submit"
                            disabled={form.processing || !students.length}
                            className="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-50"
                        >
                            {form.processing ? "Saving..." : "Save attendance"}
                        </button>
                    </div>
                </form>
            </div>
        </>
    );
}

AttendanceIndex.layout = AuthenticatedLayout;
