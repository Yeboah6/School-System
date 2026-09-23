import { Head, Link, router, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

export default function StaffIndex({ staff = [], branches = [] }) {
    const form = useForm({
        first_name: "",
        last_name: "",
        role: "",
        department_name: "",
        position: "",
        email: "",
        phone: "",
        branch_id: "",
        employee_id: "",
        gender: "",
        date_of_birth: "",
        address: "",
        qualification: "",
        employment_type: "",
        joining_date: "",
    });

    const submit = (e) => {
        e.preventDefault();
        form.post("/staff", {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    const deleteStaff = (member) => {
        if (window.confirm(`Delete ${member.first_name} ${member.last_name}?`))
            router.delete(`/staff/${member.id}`, { preserveScroll: true });
    };

    return (
        <>
            <Head title="Staff" />
            <div className="mx-auto max-w-4xl space-y-6">
                <h2 className="text-xl font-semibold text-slate-900">
                    Staff profile
                </h2>
                <form onSubmit={submit} className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                    <div className="grid gap-4 md:grid-cols">
                        <label className="text-sm font-medium text-slate-700">
                            First name
                            <input
                                required
                                value={form.data.first_name}
                                onChange={(e) =>
                                    form.setData("first_name", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Last name
                            <input
                                required
                                value={form.data.last_name}
                                onChange={(e) =>
                                    form.setData("last_name", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700 md:col-span-2">
                            Role
                            <select
                                required
                                value={form.data.role}
                                onChange={(e) =>
                                    form.setData("role", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
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
                            Department
                            <input
                                value={form.data.department_name}
                                onChange={(e) =>
                                    form.setData(
                                        "department_name",
                                        e.target.value,
                                    )
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Position
                            <input
                                value={form.data.position || ""}
                                onChange={(e) =>
                                    form.setData("position", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Email
                            <input
                                type="email"
                                value={form.data.email}
                                onChange={(e) =>
                                    form.setData("email", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700 md:col-span-2">
                            Phone
                            <input
                                value={form.data.phone}
                                onChange={(e) =>
                                    form.setData("phone", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Employee ID
                            <input
                                readOnly
                                placeholder="Auto-generated"
                                value={form.data.employee_id}
                                onChange={(e) =>
                                    form.setData("employee_id", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-slate-500"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Qualification
                            <input
                                value={form.data.qualification}
                                onChange={(e) =>
                                    form.setData(
                                        "qualification",
                                        e.target.value,
                                    )
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Branch
                            <select
                                value={form.data.branch_id}
                                onChange={(e) =>
                                    form.setData("branch_id", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            >
                                <option value="">Not specified</option>
                                {branches.map((branch) => (
                                    <option key={branch.id} value={branch.id}>
                                        {branch.name}
                                    </option>
                                ))}
                            </select>
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Gender
                            <select
                                value={form.data.gender || ""}
                                onChange={(e) =>
                                    form.setData("gender", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
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
                                value={form.data.date_of_birth || ""}
                                onChange={(e) =>
                                    form.setData(
                                        "date_of_birth",
                                        e.target.value,
                                    )
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Employment type

                            <select
                                value={form.data.employment_type || ""}
                                onChange={(e) =>
                                    form.setData("employment_type", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            >
                                <option value="">Not specified</option>
                                <option>Full Time</option>
                                <option>Part Time</option>
                            </select>
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Joining date
                            <input
                                type="date"
                                value={form.data.joining_date}
                                onChange={(e) =>
                                    form.setData("joining_date", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                        <label className="text-sm font-medium text-slate-700 md:col-span-2">
                            Address
                            <textarea
                                rows="2"
                                value={form.data.address || ""}
                                onChange={(e) =>
                                    form.setData("address", e.target.value)
                                }
                                className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"
                            />
                        </label>
                    </div>
                    <button
                        type="submit"
                        disabled={form.processing}
                        className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70"
                    >
                        {form.processing ? "Saving..." : "Add staff"}
                    </button>
                </form>
            </div>
        </>
    );
}

StaffIndex.layout = AuthenticatedLayout;
