import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

const inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white';

export default function StudentsCreate() {
    const studentForm = useForm({
        first_name: '',
        last_name: '',
        middle_name: '',
        gender: 'Male',
        date_of_birth: '',
        admission_no: '',
        parent_name: '',
        parent_phone: '',
        relationship: '',
    });

    const submitStudent = (event) => {
        event.preventDefault();
        studentForm.post('/students', {
            preserveScroll: true,
        });
    };

    return (
        <>
            <Head title="Add student" />
            <div className="mx-auto max-w-4xl space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Student records</p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">Add student details</h2>
                        <p className="mt-2 text-sm text-slate-500">Create the student profile and link a parent or guardian.</p>
                    </div>
                    <Link href="/students" className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Back to students
                    </Link>
                </div>

                <form onSubmit={submitStudent} className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                    <div className="border-b border-slate-100 pb-5">
                        <h3 className="text-lg font-semibold text-slate-900">Student profile</h3>
                        <p className="mt-1 text-sm text-slate-500">The first name and last name are required.</p>
                    </div>

                    <div className="mt-6 grid gap-4 md:grid-cols-2">
                        <label className="text-sm font-medium text-slate-700">
                            First name
                            <input required value={studentForm.data.first_name} onChange={(event) => studentForm.setData('first_name', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Last name
                            <input required value={studentForm.data.last_name} onChange={(event) => studentForm.setData('last_name', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Middle name
                            <input value={studentForm.data.middle_name} onChange={(event) => studentForm.setData('middle_name', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Gender
                            <select value={studentForm.data.gender} onChange={(event) => studentForm.setData('gender', event.target.value)} className={`mt-1 ${inputClass}`}>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Date of birth
                            <input type="date" value={studentForm.data.date_of_birth} onChange={(event) => studentForm.setData('date_of_birth', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Admission number
                            <input value={studentForm.data.admission_no} onChange={(event) => studentForm.setData('admission_no', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                    </div>

                    <div className="mt-8 border-b border-slate-100 pb-5">
                        <h3 className="text-lg font-semibold text-slate-900">Parent or guardian</h3>
                        <p className="mt-1 text-sm text-slate-500">These details will be linked to the new student record.</p>
                    </div>

                    <div className="mt-6 grid gap-4 md:grid-cols-2">
                        <label className="text-sm font-medium text-slate-700 md:col-span-2">
                            Full name
                            <input value={studentForm.data.parent_name} onChange={(event) => studentForm.setData('parent_name', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Phone
                            <input value={studentForm.data.parent_phone} onChange={(event) => studentForm.setData('parent_phone', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Relationship
                            <input placeholder="Father, mother, guardian" value={studentForm.data.relationship} onChange={(event) => studentForm.setData('relationship', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                    </div>

                    {studentForm.hasErrors && <div className="mt-5 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-700">Please review the highlighted details and try again.</div>}

                    <div className="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <Link href="/students" className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Cancel
                        </Link>
                        <button type="submit" disabled={studentForm.processing} className="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-70">
                            {studentForm.processing ? 'Saving...' : 'Save student'}
                        </button>
                    </div>
                </form>
            </div>
        </>
    );
}

StudentsCreate.layout = AuthenticatedLayout;
