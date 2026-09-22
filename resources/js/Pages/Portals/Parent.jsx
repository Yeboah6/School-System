import { Head } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

export default function ParentPortal({
    parent,
    students = [],
    announcements = [],
    notifications = [],
}) {
    return (
        <>
            <Head title="Parent portal" />
            <div className="space-y-6">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                        Family workspace
                    </p>
                    <h2 className="mt-1 text-3xl font-bold text-slate-900">
                        Welcome, {parent.name}
                    </h2>
                    <p className="mt-2 text-sm text-slate-500">
                        View your children’s school progress, attendance, fees,
                        and updates.
                    </p>
                </div>
                <section className="grid gap-4 lg:grid-cols-2">
                    {students.map((student) => (
                        <article
                            key={student.id}
                            className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm"
                        >
                            <div className="flex items-start justify-between">
                                <div>
                                    <h3 className="text-xl font-semibold text-slate-900">
                                        {student.name}
                                    </h3>
                                    <p className="mt-1 text-sm text-slate-500">
                                        {student.class || "Class not assigned"}
                                    </p>
                                </div>
                                <span className="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    {
                                        student.attendance.filter(
                                            (record) =>
                                                record.status === "present",
                                        ).length
                                    }{" "}
                                    present
                                </span>
                            </div>
                            <div className="mt-5 grid gap-3 sm:grid-cols-3">
                                <div className="rounded-xl bg-slate-50 p-3">
                                    <p className="text-xs text-slate-500">
                                        Results
                                    </p>
                                    <p className="mt-1 text-xl font-semibold text-slate-900">
                                        {student.results.length}
                                    </p>
                                </div>
                                <div className="rounded-xl bg-slate-50 p-3">
                                    <p className="text-xs text-slate-500">
                                        Attendance
                                    </p>
                                    <p className="mt-1 text-xl font-semibold text-slate-900">
                                        {student.attendance.length}
                                    </p>
                                </div>
                                <div className="rounded-xl bg-slate-50 p-3">
                                    <p className="text-xs text-slate-500">
                                        Balance
                                    </p>
                                    <p className="mt-1 text-xl font-semibold text-slate-900">
                                        {student.invoices
                                            .reduce(
                                                (sum, invoice) =>
                                                    sum + invoice.balance,
                                                0,
                                            )
                                            .toFixed(2)}
                                    </p>
                                </div>
                            </div>
                        </article>
                    ))}
                </section>
                <PortalUpdates
                    announcements={announcements}
                    notifications={notifications}
                />
            </div>
        </>
    );
}

function PortalUpdates({ announcements, notifications }) {
    return (
        <section className="grid gap-6 lg:grid-cols-2">
            <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                <h3 className="text-lg font-semibold text-slate-900">
                    School announcements
                </h3>
                <div className="mt-4 space-y-3">
                    {announcements.length ? (
                        announcements.map((item) => (
                            <div
                                key={item.id}
                                className="rounded-xl bg-slate-50 p-3"
                            >
                                <p className="font-semibold text-slate-900">
                                    {item.title}
                                </p>
                                <p className="mt-1 text-sm text-slate-600">
                                    {item.message}
                                </p>
                            </div>
                        ))
                    ) : (
                        <p className="text-sm text-slate-500">
                            No announcements.
                        </p>
                    )}
                </div>
            </div>
            <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                <h3 className="text-lg font-semibold text-slate-900">
                    Unread notifications
                </h3>
                <div className="mt-4 space-y-3">
                    {notifications.length ? (
                        notifications.map((item) => (
                            <div
                                key={item.id}
                                className="rounded-xl bg-indigo-50 p-3"
                            >
                                <p className="font-semibold text-slate-900">
                                    {item.title}
                                </p>
                                <p className="mt-1 text-sm text-slate-600">
                                    {item.message}
                                </p>
                            </div>
                        ))
                    ) : (
                        <p className="text-sm text-slate-500">
                            You are all caught up.
                        </p>
                    )}
                </div>
            </div>
        </section>
    );
}

ParentPortal.layout = AuthenticatedLayout;
