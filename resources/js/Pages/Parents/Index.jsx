import { Head, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "../../Layouts/AuthenticatedLayout";

const inputClass =
    "w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900";

export default function ParentsIndex({ parents = [] }) {
    const form = useForm({
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        address: "",
        occupation: "",
        emergency_contact: "",
        relationship_to_student: "",
    });
    const submit = (event) => {
        event.preventDefault();
        form.post("/parents", {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <>
            <Head title="Parents" />
            <div className="space-y-6">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">
                        Family records
                    </p>
                    <h2 className="mt-1 text-3xl font-bold text-slate-900">
                        Parents and guardians
                    </h2>
                    <p className="mt-2 text-sm text-slate-500">
                        Maintain family contacts and see their linked students.
                    </p>
                </div>
                <div className="grid gap-6 xl:grid-cols-[.7fr_1.3fr]">
                    <form
                        onSubmit={submit}
                        className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm"
                    >
                        <h3 className="text-lg font-semibold text-slate-900">
                            Add parent
                        </h3>
                        <div className="mt-4 space-y-3">
                            {[
                                "first_name",
                                "last_name",
                                "email",
                                "phone",
                                "occupation",
                                "emergency_contact",
                                "relationship_to_student",
                            ].map((field) => (
                                <input
                                    key={field}
                                    type={field === "email" ? "email" : "text"}
                                    placeholder={field.replaceAll("_", " ")}
                                    value={form.data[field]}
                                    onChange={(event) =>
                                        form.setData(field, event.target.value)
                                    }
                                    className={inputClass}
                                />
                            ))}
                            <textarea
                                placeholder="Address"
                                rows="3"
                                value={form.data.address}
                                onChange={(event) =>
                                    form.setData("address", event.target.value)
                                }
                                className={inputClass}
                            />
                            <button
                                disabled={form.processing}
                                className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white"
                            >
                                {form.processing ? "Saving..." : "Save parent"}
                            </button>
                        </div>
                    </form>
                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">
                            Parent directory
                        </h3>
                        <div className="mt-4 grid gap-3 md:grid-cols-2">
                            {parents.length ? (
                                parents.map((parent) => (
                                    <article
                                        key={parent.id}
                                        className="rounded-xl border border-slate-200 bg-slate-50 p-4"
                                    >
                                        <p className="font-semibold text-slate-900">
                                            {parent.first_name}{" "}
                                            {parent.last_name}
                                        </p>
                                        <p className="mt-1 text-xs text-slate-500">
                                            {parent.relationship || "Guardian"}{" "}
                                            · {parent.phone || "No phone"}
                                        </p>
                                        <p className="mt-1 text-xs text-slate-500">
                                            {parent.email || "No email"} ·{" "}
                                            {parent.occupation ||
                                                "Occupation not provided"}
                                        </p>
                                        <div className="mt-3 border-t border-slate-200 pt-3 text-xs text-slate-600">
                                            {parent.students?.length
                                                ? parent.students.map(
                                                      (student) => (
                                                          <span
                                                              key={student.id}
                                                              className="mr-2 inline-block rounded-full bg-white px-2 py-1"
                                                          >
                                                              {student.name}
                                                          </span>
                                                      ),
                                                  )
                                                : "No students linked"}
                                        </div>
                                    </article>
                                ))
                            ) : (
                                <p className="text-sm text-slate-500">
                                    No parent records yet.
                                </p>
                            )}
                        </div>
                    </section>
                </div>
            </div>
        </>
    );
}

ParentsIndex.layout = AuthenticatedLayout;
