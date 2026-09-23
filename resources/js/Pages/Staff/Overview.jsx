import { Head, Link, router } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

export default function StaffOverview({ summary = {}, staff = [] }) {
    const deleteStaff = (member) => {
        if (
            window.confirm(`Delete ${member.first_name} ${member.last_name}?`)
        ) {
            router.delete(`/staff/${member.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Staff overview" />
            <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                            People operations
                        </p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">
                            Staff overview
                        </h2>
                        <p className="mt-2 text-sm text-slate-500">
                            Review staffing levels and portal account coverage
                            before adding profiles.
                        </p>
                    </div>
                    <Link
                        href="/staff"
                        className="rounded-xl bg-slate-900 px-4 py-2.5 text-center text-sm font-semibold text-white"
                    >
                        Add staff
                    </Link>
                </div>
                <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    {[
                        ["Total staff", summary.total],
                        ["Active", summary.active],
                        ["Teachers", summary.teachers],
                        ["Portal linked", summary.linked],
                    ].map(([label, value]) => (
                        <div
                            key={label}
                            className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"
                        >
                            <p className="text-xs uppercase tracking-wide text-slate-400">
                                {label}
                            </p>
                            <p className="mt-2 text-2xl font-bold text-slate-900">
                                {value || 0}
                            </p>
                        </div>
                    ))}
                </div>
                <section className="overflow-x-auto rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 className="text-lg font-semibold text-slate-900">
                        Recent staff
                    </h3>
                    <table className="mt-4 w-full min-w-[850px] text-left text-sm">
                        <thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                            <tr>
                                <th className="px-3 py-3">Employee</th>
                                <th className="px-3 py-3">ID</th>
                                <th className="px-3 py-3">Role</th>
                                <th className="px-3 py-3">Email</th>
                                <th className="px-3 py-3">Status</th>
                                <th className="px-3 py-3">Portal</th>
                                <th className="px-3 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-100">
                            {staff.map((member) => (
                                <tr key={member.id}>
                                    <td className="px-3 py-4 font-semibold">
                                        {member.first_name} {member.last_name}
                                    </td>
                                    <td className="px-3 py-4">
                                        {member.employee_id || "Pending"}
                                    </td>
                                    <td className="px-3 py-4">{member.role}</td>
                                    <td className="px-3 py-4">{member.email}</td>
                                    <td className="px-3 py-4 capitalize">
                                        {member.status}
                                    </td>
                                    <td className="px-3 py-4">
                                        {member.user_id
                                            ? "Linked"
                                            : "Not linked"}
                                    </td>
                                    <td className="px-3 py-4">
                                        <div className="flex flex-wrap gap-2">
                                            <Link
                                                href={`/staff/${member.id}`}
                                                className="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700"
                                            >
                                                Show
                                            </Link>
                                            <Link
                                                href={`/staff/${member.id}/edit`}
                                                className="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                type="button"
                                                onClick={() =>
                                                    deleteStaff(member)
                                                }
                                                className="rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </section>
            </div>
        </>
    );
}

StaffOverview.layout = AuthenticatedLayout;
