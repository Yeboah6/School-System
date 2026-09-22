import { Head } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

export default function TeacherPortal({
    teacher,
    schedule = [],
    classes = [],
    announcements = [],
    notifications = [],
}) {
    return (
        <>
            <Head title="Teacher portal" />
            <div className="space-y-6">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                        Teaching workspace
                    </p>
                    <h2 className="mt-1 text-3xl font-bold text-slate-900">
                        Welcome, {teacher.name}
                    </h2>
                    <p className="mt-2 text-sm text-slate-500">
                        Your teaching schedule, class rosters, and school
                        updates.
                    </p>
                </div>
                <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center justify-between">
                        <h3 className="text-lg font-semibold text-slate-900">
                            My timetable
                        </h3>
                        <a
                            href="/attendance"
                            className="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white"
                        >
                            Take attendance
                        </a>
                    </div>
                    <div className="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                        {schedule.length ? (
                            schedule.map((slot, index) => (
                                <article
                                    key={`${slot.day}-${slot.start_time}-${index}`}
                                    className="rounded-xl border border-slate-200 bg-slate-50 p-4"
                                >
                                    <p className="text-xs font-semibold uppercase tracking-wide text-indigo-600">
                                        {slot.day}
                                    </p>
                                    <p className="mt-2 font-semibold text-slate-900">
                                        {slot.subject || "Subject"} ·{" "}
                                        {slot.class || "Class"}
                                    </p>
                                    <p className="mt-1 text-sm text-slate-500">
                                        {slot.start_time} - {slot.end_time} ·{" "}
                                        {slot.room || "Room not set"}
                                    </p>
                                </article>
                            ))
                        ) : (
                            <p className="text-sm text-slate-500">
                                No timetable slots assigned.
                            </p>
                        )}
                    </div>
                </section>
                <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 className="text-lg font-semibold text-slate-900">
                        Assigned classes
                    </h3>
                    <div className="mt-4 grid gap-4 md:grid-cols-2">
                        {classes.length ? (
                            classes.map((item) => (
                                <div
                                    key={item.id}
                                    className="rounded-xl bg-slate-50 p-4"
                                >
                                    <p className="font-semibold text-slate-900">
                                        {item.name}
                                    </p>
                                    <div className="mt-3 space-y-1 text-sm text-slate-600">
                                        {item.students.length ? (
                                            item.students.map((student) => (
                                                <p key={student.id}>
                                                    {student.name}{" "}
                                                    <span className="text-xs text-slate-400">
                                                        {student.admission_no ||
                                                            ""}
                                                    </span>
                                                </p>
                                            ))
                                        ) : (
                                            <p>No students assigned.</p>
                                        )}
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p className="text-sm text-slate-500">
                                No classes assigned.
                            </p>
                        )}
                    </div>
                </section>
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
            </div>
        </>
    );
}

TeacherPortal.layout = AuthenticatedLayout;
