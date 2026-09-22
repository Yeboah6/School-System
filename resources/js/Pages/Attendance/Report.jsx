import { Head, Link, router } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

export default function AttendanceReport({ records, from, to }) {
    return (
        <>
            <Head title="Attendance reports" />
            <div className="space-y-6">
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                            Reports
                        </p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">
                            Attendance report
                        </h2>
                    </div>
                    <Link
                        href="/attendance"
                        className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700"
                    >
                        Record attendance
                    </Link>
                </div>
                <form className="flex flex-col gap-3 rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-end">
                    <label className="text-sm font-medium text-slate-700">
                        From
                        <input
                            type="date"
                            name="from"
                            defaultValue={from}
                            className="mt-1 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5"
                        />
                    </label>
                    <label className="text-sm font-medium text-slate-700">
                        To
                        <input
                            type="date"
                            name="to"
                            defaultValue={to}
                            className="mt-1 block rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5"
                        />
                    </label>
                    <button className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">
                        Filter report
                    </button>
                </form>
                <section className="overflow-x-auto rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <table className="w-full min-w-160 text-left text-sm">
                        <thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th className="px-3 py-3">Date</th>
                                <th className="px-3 py-3">Student</th>
                                <th className="px-3 py-3">Class</th>
                                <th className="px-3 py-3">Status</th>
                                <th className="px-3 py-3">Note</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {records.data.length ? (
                                records.data.map((record) => (
                                    <tr key={record.id}>
                                        <td className="px-3 py-4">
                                            {record.date}
                                        </td>
                                        <td className="px-3 py-4 font-semibold text-slate-900">
                                            {record.student}
                                        </td>
                                        <td className="px-3 py-4">
                                            {record.class || "Unassigned"}
                                        </td>
                                        <td className="px-3 py-4 capitalize">
                                            {record.status}
                                        </td>
                                        <td className="px-3 py-4 text-slate-500">
                                            {record.note || "-"}
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td
                                        colSpan="5"
                                        className="px-3 py-10 text-center text-slate-500"
                                    >
                                        No attendance records found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </section>
            </div>
        </>
    );
}

AttendanceReport.layout = AuthenticatedLayout;
