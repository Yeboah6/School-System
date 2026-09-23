import { Head, Link, router } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

export default function StudentsIndex({ students = [] }) {
    const deleteStudent = (student) => {
        if (
            window.confirm(`Delete ${student.first_name} ${student.last_name}?`)
        ) {
            router.delete(`/students/${student.id}`, { preserveScroll: true });
        }
    };

    return (
        <>
            <Head title="Students" />
            <div className="space-y-6">
                <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                                Directory
                            </p>
                            <h2 className="mt-1 text-2xl font-bold text-slate-900">
                                Students and parents
                            </h2>
                            <p className="mt-1 text-sm text-slate-500">
                                Review all records before adding a new student
                                profile.
                            </p>
                        </div>
                        <Link
                            href="/students/create"
                            className="rounded-xl bg-slate-900 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-slate-700"
                        >
                            Add student
                        </Link>
                    </div>

                    <div className="mt-6 overflow-x-auto">
                        <table className="w-full min-w-160 text-left text-sm">
                            <thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th className="px-3 py-3 font-semibold">
                                        Student
                                    </th>
                                    <th className="px-3 py-3 font-semibold">
                                        Admission no.
                                    </th>
                                    <th className="px-3 py-3 font-semibold">
                                        Class
                                    </th>
                                    <th className="px-3 py-3 font-semibold">
                                        Branch
                                    </th>
                                    <th className="px-3 py-3 font-semibold">
                                        Gender
                                    </th>
                                    <th className="px-3 py-3 font-semibold">
                                        Date of birth
                                    </th>
                                    <th className="px-3 py-3 font-semibold">
                                        Status
                                    </th>
                                    <th className="px-3 py-3 font-semibold">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {students.length > 0 ? (
                                    students.map((student) => (
                                        <tr
                                            key={student.id}
                                            className="text-slate-700"
                                        >
                                            <td className="px-3 py-4 font-semibold text-slate-900">
                                                {student.first_name}{" "}
                                                {student.last_name}
                                            </td>
                                            <td className="px-3 py-4">
                                                {student.admission_no ||
                                                    "Not assigned"}
                                            </td>
                                            <td className="px-3 py-4">
                                                {student.class || "Unassigned"}
                                            </td>
                                            <td className="px-3 py-4">
                                                {student.branch ||
                                                    "Main school"}
                                            </td>
                                            <td className="px-3 py-4">
                                                {student.gender ||
                                                    "Not specified"}
                                            </td>
                                            <td className="px-3 py-4">
                                                {student.date_of_birth ||
                                                    "Not specified"}
                                            </td>
                                            <td className="px-3 py-4">
                                                <span className="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">
                                                    {student.status}
                                                </span>
                                            </td>
                                            <td className="px-3 py-4">
                                                <div className="flex flex-wrap gap-2">
                                                    <Link
                                                        href={`/students/${student.id}`}
                                                        className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                                    >
                                                        Show details
                                                    </Link>
                                                    <Link
                                                        href={`/students/${student.id}/edit`}
                                                        className="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                                    >
                                                        Edit
                                                    </Link>
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            deleteStudent(
                                                                student,
                                                            )
                                                        }
                                                        className="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td
                                            colSpan="8"
                                            className="px-3 py-8 text-center text-slate-500"
                                        >
                                            No student records yet.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </>
    );
}

StudentsIndex.layout = AuthenticatedLayout;
