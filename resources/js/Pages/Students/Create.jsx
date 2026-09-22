import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

const inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white';

export default function StudentsCreate({ classes = [], branches = [] }) {
    const studentForm = useForm({
        first_name: '',
        last_name: '',
        middle_name: '',
        gender: 'Male',
        date_of_birth: '',
        admission_no: 'Auto-generated',
        class_id: '',
        branch_id: '',
        parent_name: '',
        parent_first_name: '',
        parent_last_name: '',
        parent_email: '',
        parent_phone: '',
        parent_address: '',
        parent_occupation: '',
        parent_emergency_contact: '',
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
                            Branch
                            <select value={studentForm.data.branch_id} onChange={(event) => studentForm.setData('branch_id', event.target.value)} className={`mt-1 ${inputClass}`}>
                                <option value="">Main school / no branch</option>
                                {branches.map((branch) => <option key={branch.id} value={branch.id}>{branch.name} ({branch.code})</option>)}
                            </select>
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Class assignment
                            <select value={studentForm.data.class_id} onChange={(event) => studentForm.setData('class_id', event.target.value)} className={`mt-1 ${inputClass}`}>
                                <option value="">Assign later</option>
                                {classes.filter((schoolClass) => !studentForm.data.branch_id || !schoolClass.branch_id || String(schoolClass.branch_id) === String(studentForm.data.branch_id)).map((schoolClass) => <option key={schoolClass.id} value={schoolClass.id}>{schoolClass.name}{schoolClass.level ? ` · ${schoolClass.level}` : ''}</option>)}
                            </select>
                            <span className="mt-1 block text-xs font-normal text-slate-400">Students are assigned through this class field.</span>
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Admission number
                            <input readOnly value={studentForm.data.admission_no} className={`mt-1 cursor-not-allowed ${inputClass} bg-slate-100 text-slate-500`} />
                            <span className="mt-1 block text-xs font-normal text-slate-400">Generated automatically in AD123 format.</span>
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
                            Email
                            <input type="email" value={studentForm.data.parent_email} onChange={(event) => studentForm.setData('parent_email', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Relationship
                            <input placeholder="Father, mother, guardian" value={studentForm.data.relationship} onChange={(event) => studentForm.setData('relationship', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Occupation
                            <input value={studentForm.data.parent_occupation} onChange={(event) => studentForm.setData('parent_occupation', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700">
                            Emergency contact
                            <input value={studentForm.data.parent_emergency_contact} onChange={(event) => studentForm.setData('parent_emergency_contact', event.target.value)} className={`mt-1 ${inputClass}`} />
                        </label>
                        <label className="text-sm font-medium text-slate-700 md:col-span-2">
                            Address
                            <textarea rows="2" value={studentForm.data.parent_address} onChange={(event) => studentForm.setData('parent_address', event.target.value)} className={`mt-1 ${inputClass}`} />
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
