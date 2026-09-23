import { Head, Link, router, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

const formatDate = (value) => {
    if (!value) return "Not provided";

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    }).format(date);
};

export default function StaffShow({ staff, classes = [] }) {
    const assignmentForm = useForm({ class_id: "", is_primary: true });

    const assignClass = (event) => {
        event.preventDefault();
        assignmentForm.post(`/staff/${staff.id}/classes`, {
            preserveScroll: true,
            onSuccess: () => assignmentForm.reset("class_id"),
        });
    };

    const unassignClass = (schoolClass) => {
        if (
            window.confirm(
                `Remove ${staff.first_name} ${staff.last_name} from ${schoolClass.name}?`,
            )
        ) {
            router.delete(`/staff/${staff.id}/classes/${schoolClass.id}`, {
                preserveScroll: true,
            });
        }
    };

    return (
        <>
            <Head title={`${staff.first_name} ${staff.last_name}`} />
            <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                            Staff details
                        </p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">
                            {staff.first_name} {staff.last_name}
                        </h2>
                        <p className="mt-2 text-sm font-semibold text-indigo-700">
                            Employee ID: {staff.employee_id || "Not assigned"}
                        </p>
                    </div>
                    <div className="flex gap-2">
                        <Link
                            href={`/staff/${staff.id}/edit`}
                            className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white"
                        >
                            Edit
                        </Link>
                        <Link
                            href="/staff/overview"
                            className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700"
                        >
                            Back to staff
                        </Link>
                    </div>
                </div>
                <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 className="text-lg font-semibold text-slate-900">
                        Employee profile
                    </h3>
                    <dl className="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        {[
                            ["Role", staff.role],
                            ["Position", staff.position],
                            ["Department", staff.department_name],
                            ["Gender", staff.gender],
                            ["Date of birth", formatDate(staff.date_of_birth)],
                            ["Qualification", staff.qualification],
                            ["Employment type", staff.employment_type],
                            ["Joining date", formatDate(staff.joining_date)],
                            ["Branch", staff.branch],
                            ["Email", staff.email],
                            ["Phone", staff.phone],
                            ["Status", staff.status],
                        ].map(([label, value]) => (
                            <div key={label}>
                                <dt className="text-xs uppercase tracking-wide text-slate-400">
                                    {label}
                                </dt>
                                <dd className="mt-1 text-sm text-slate-700">
                                    {value || "Not provided"}
                                </dd>
                            </div>
                        ))}
                    </dl>
                    <div className="mt-5 border-t border-slate-100 pt-5">
                        <dt className="text-xs uppercase tracking-wide text-slate-400">
                            Address
                        </dt>
                        <dd className="mt-1 text-sm text-slate-700">
                            {staff.address || "Not provided"}
                        </dd>
                    </div>
                </section>
                <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-start justify-between gap-4">
                        <div>
                            <h3 className="text-lg font-semibold text-slate-900">
                                Assigned classes
                            </h3>
                            <p className="mt-1 text-sm text-slate-500">
                                Assign this teacher to one or more active
                                classes.
                            </p>
                        </div>
                    </div>
                    <form
                        onSubmit={assignClass}
                        className="mt-4 flex flex-col gap-3 sm:flex-row"
                    >
                        <select
                            required
                            value={assignmentForm.data.class_id}
                            onChange={(event) =>
                                assignmentForm.setData(
                                    "class_id",
                                    event.target.value,
                                )
                            }
                            className="flex-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-900"
                        >
                            <option value="">Select class</option>
                            {classes
                                .filter(
                                    (schoolClass) =>
                                        !staff.classes?.some(
                                            (assigned) =>
                                                assigned.id === schoolClass.id,
                                        ),
                                )
                                .map((schoolClass) => (
                                    <option
                                        key={schoolClass.id}
                                        value={schoolClass.id}
                                    >
                                        {schoolClass.name}
                                        {schoolClass.level
                                            ? ` · ${schoolClass.level}`
                                            : ""}
                                        {schoolClass.branch
                                            ? ` · ${schoolClass.branch}`
                                            : ""}
                                    </option>
                                ))}
                        </select>
                        <label className="flex items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                checked={assignmentForm.data.is_primary}
                                onChange={(event) =>
                                    assignmentForm.setData(
                                        "is_primary",
                                        event.target.checked,
                                    )
                                }
                            />{" "}
                            Primary teacher
                        </label>
                        <button
                            disabled={assignmentForm.processing}
                            className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white disabled:opacity-60"
                        >
                            {assignmentForm.processing
                                ? "Assigning..."
                                : "Assign class"}
                        </button>
                    </form>
                    <div className="mt-5 flex flex-wrap gap-3">
                        {staff.classes?.length ? (
                            staff.classes.map((schoolClass) => (
                                <div
                                    key={schoolClass.id}
                                    className="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-700"
                                >
                                    <span>
                                        <span className="font-semibold text-slate-900">
                                            {schoolClass.name}
                                        </span>
                                        {schoolClass.level
                                            ? ` · ${schoolClass.level}`
                                            : ""}
                                        {schoolClass.branch ? (
                                            <span className="ml-2 text-xs text-slate-400">
                                                {schoolClass.branch}
                                            </span>
                                        ) : null}
                                    </span>
                                    <button
                                        type="button"
                                        onClick={() =>
                                            unassignClass(schoolClass)
                                        }
                                        className="text-xs font-semibold text-rose-700"
                                    >
                                        Remove
                                    </button>
                                </div>
                            ))
                        ) : (
                            <p className="text-sm text-slate-500">
                                No classes assigned yet.
                            </p>
                        )}
                    </div>
                </section>
            </div>
        </>
    );
}

StaffShow.layout = AuthenticatedLayout;
