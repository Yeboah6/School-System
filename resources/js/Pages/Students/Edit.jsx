import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

const inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none focus:border-slate-400 focus:bg-white';

export default function StudentsEdit({ student, classes = [], branches = [] }) {
    const form = useForm({
        first_name: student.first_name || '', last_name: student.last_name || '', middle_name: student.middle_name || '',
        gender: student.gender || '', nationality: student.nationality || '', email: student.email || '', phone: student.phone || '',
        address: student.address || '', date_of_birth: student.date_of_birth || '', admission_date: student.admission_date || '',
        previous_school: student.previous_school || '', student_type: student.student_type || '', status: student.status || 'active',
        branch_id: student.branch_id || '', class_id: student.class_id || '',
    });

    const submit = (event) => {
        event.preventDefault();
        form.put(`/students/${student.id}`);
    };

    return <>
        <Head title={`Edit ${student.first_name} ${student.last_name}`} />
        <div className="mx-auto max-w-4xl space-y-6">
            <div className="flex items-end justify-between gap-4"><div><p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Student records</p><h2 className="mt-1 text-3xl font-bold text-slate-900">Edit student</h2><p className="mt-2 text-sm text-slate-500">Update identity, contact, academic assignment, and status.</p></div><Link href={`/students/${student.id}`} className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Cancel</Link></div>
            <form onSubmit={submit} className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
                <div className="grid gap-4 md:grid-cols-2">
                    {['first_name', 'middle_name', 'last_name', 'nationality', 'email', 'phone', 'previous_school', 'student_type'].map((field) => <label key={field} className="text-sm font-medium capitalize text-slate-700">{field.replaceAll('_', ' ')}<input type={field === 'email' ? 'email' : 'text'} value={form.data[field]} onChange={(event) => form.setData(field, event.target.value)} className={`mt-1 ${inputClass}`} /></label>)}
                    <label className="text-sm font-medium text-slate-700">Gender<select value={form.data.gender} onChange={(event) => form.setData('gender', event.target.value)} className={`mt-1 ${inputClass}`}><option value="">Select gender</option><option>Male</option><option>Female</option><option>Other</option></select></label>
                    <label className="text-sm font-medium text-slate-700">Status<select value={form.data.status} onChange={(event) => form.setData('status', event.target.value)} className={`mt-1 ${inputClass}`}><option value="applicant">Applicant</option><option value="active">Active</option><option value="graduated">Graduated</option><option value="transferred">Transferred</option><option value="withdrawn">Withdrawn</option><option value="suspended">Suspended</option></select></label>
                    <label className="text-sm font-medium text-slate-700">Date of birth<input type="date" value={form.data.date_of_birth} onChange={(event) => form.setData('date_of_birth', event.target.value)} className={`mt-1 ${inputClass}`} /></label>
                    <label className="text-sm font-medium text-slate-700">Admission date<input type="date" value={form.data.admission_date} onChange={(event) => form.setData('admission_date', event.target.value)} className={`mt-1 ${inputClass}`} /></label>
                    <label className="text-sm font-medium text-slate-700">Branch<select value={form.data.branch_id} onChange={(event) => form.setData('branch_id', event.target.value)} className={`mt-1 ${inputClass}`}><option value="">Main school</option>{branches.map((branch) => <option key={branch.id} value={branch.id}>{branch.name} ({branch.code})</option>)}</select></label>
                    <label className="text-sm font-medium text-slate-700">Class<select value={form.data.class_id} onChange={(event) => form.setData('class_id', event.target.value)} className={`mt-1 ${inputClass}`}><option value="">Unassigned</option>{classes.filter((item) => !form.data.branch_id || !item.branch_id || String(item.branch_id) === String(form.data.branch_id)).map((item) => <option key={item.id} value={item.id}>{item.name}{item.level ? ` · ${item.level}` : ''}</option>)}</select></label>
                    <label className="text-sm font-medium text-slate-700 md:col-span-2">Address<textarea rows="3" value={form.data.address} onChange={(event) => form.setData('address', event.target.value)} className={`mt-1 ${inputClass}`} /></label>
                </div>
                {form.hasErrors && <p className="mt-5 rounded-xl bg-rose-50 p-3 text-sm text-rose-700">Please review the submitted details.</p>}
                <div className="mt-8 flex justify-end"><button disabled={form.processing} className="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white disabled:opacity-60">{form.processing ? 'Saving...' : 'Save student'}</button></div>
            </form>
        </div>
    </>;
}

StudentsEdit.layout = AuthenticatedLayout;