import { Head, Link, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

const inputClass =
    "mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900";

export default function StaffEdit({ staff, branches = [] }) {
    const form = useForm({
        first_name: staff.first_name || "",
        last_name: staff.last_name || "",
        role: staff.role || "",
        department_name: staff.department_name || "",
        email: staff.email || "",
        phone: staff.phone || "",
        branch_id: staff.branch_id || "",
        employee_id: staff.employee_id || "",
        gender: staff.gender || "",
        date_of_birth: staff.date_of_birth || "",
        address: staff.address || "",
        position: staff.position || "",
        qualification: staff.qualification || "",
        employment_type: staff.employment_type || "",
        joining_date: staff.joining_date || "",
        status: staff.status || "active",
    });

    const submit = (event) => {
        event.preventDefault();
        form.put(`/staff/${staff.id}`);
    };

    return (
        <>
            <Head title={`Edit ${staff.first_name} ${staff.last_name}`} />
            <div className="mx-auto max-w-4xl space-y-6">
                <div className="flex items-end justify-between gap-4">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                            Staff records
                        </p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">
                            Edit staff profile
                        </h2>
                        <p className="mt-2 text-sm text-slate-500">
                            Employee ID: {staff.employee_id || "Not assigned"}
                        </p>
                    </div>
                    <Link
                        href={`/staff/${staff.id}`}
                        className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700"
                    >
                        Cancel
                    </Link>
                </div>
                <form
                    onSubmit={submit}
                    className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-8"
                >
                    <div className="grid gap-4 md:grid-cols-2">
                        {[
                            "first_name",
                            "last_name",
                            "department_name",
                            "position",
                            "qualification",
                            "employment_type",
                            "email",
                            "phone",
                        ].map((field) => (
                            <label
                                key={field}
                                className="text-sm font-medium capitalize text-slate-700"
                            >
                                {field.replaceAll("_", " ")}
                                <input
                                    type={field === "email" ? "email" : "text"}
                                    value={form.data[field]}
                                    onChange={(event) =>
                                        form.setData(field, event.target.value)
                                    }
                                    className={inputClass}
                                />
                            </label>
                        ))}
                        <label className="text-sm font-medium text-slate-700">
                            Role
                            <select
                                required
                                value={form.data.role}
                                onChange={(event) =>
                                    form.setData("role", event.target.value)
                                }
                                className={inputClass}
                            >
                                <option value="">Select role</option>
                                <option>Teacher</option>
                                <option>Head Teacher</option>
                                <option>Accountant</option>
                                <option>Principal</option>
                                <option>Vice Principal</option>
                                <option>Librarian</option>
                                <option>Nurse</option>
                                <option>Receptionist</option>
                                <option>Other</option>
                            </select>
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Gender
                            <select
                                value={form.data.gender}
                                onChange={(event) =>
                                    form.setData("gender", event.target.value)
                                }
                                className={inputClass}
                            >
                                <option value="">Not specified</option>
                                <option>Male</option>
                                <option>Female</option>
                                <option>Other</option>
                            </select>
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Date of birth
                            <input
                                type="date"
                                value={form.data.date_of_birth}
                                onChange={(event) =>
                                    form.setData(
                                        "date_of_birth",
                                        event.target.value,
                                    )
                                }
                                className={inputClass}
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Joining date
                            <input
                                type="date"
                                value={form.data.joining_date}
                                onChange={(event) =>
                                    form.setData(
                                        "joining_date",
                                        event.target.value,
                                    )
                                }
                                className={inputClass}
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Employee ID
                            <input
                                readOnly
                                value={form.data.employee_id}
                                className={`${inputClass} cursor-not-allowed bg-slate-100 text-slate-500`}
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Branch
                            <select
                                value={form.data.branch_id}
                                onChange={(event) =>
                                    form.setData(
                                        "branch_id",
                                        event.target.value,
                                    )
                                }
                                className={inputClass}
                            >
                                <option value="">Main school</option>
                                {branches.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.name} ({branch.code})
                                    </option>
                                ))}
                            </select>
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Status
                            <select
                                value={form.data.status}
                                onChange={(event) =>
                                    form.setData("status", event.target.value)
                                }
                                className={inputClass}
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </label>
                        <label className="text-sm font-medium text-slate-700 md:col-span-2">
                            Address
                            <textarea
                                rows="3"
                                value={form.data.address}
                                onChange={(event) =>
                                    form.setData("address", event.target.value)
                                }
                                className={inputClass}
                            />
                        </label>
                    </div>
                    <div className="mt-8 flex justify-end">
                        <button
                            disabled={form.processing}
                            className="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60"
                        >
                            {form.processing ? "Saving..." : "Save changes"}
                        </button>
                    </div>
                </form>
            </div>
        </>
    );
}

StaffEdit.layout = AuthenticatedLayout;
