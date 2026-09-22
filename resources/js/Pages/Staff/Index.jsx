import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function StaffIndex({ staff = [], branches = [] }) {
    const form = useForm({
        first_name: '',
        last_name: '',
        role: '',
        department_name: '',
        email: '',
        phone: '',
        branch_id: '',
        employee_id: '',
        gender: '',
        qualification: '',
        employment_type: '',
        joining_date: '',
    });

    const submit = (e) => {
        e.preventDefault();
        form.post('/staff', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <>
            <Head title="Staff" />
            <div className="grid gap-6 xl:grid-cols-2">
                <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 className="text-xl font-semibold text-slate-900">Staff profile</h2>
                    <form onSubmit={submit} className="mt-5 space-y-4">
                        <div className="grid gap-4 md:grid-cols-2">
                            <input placeholder="First name" value={form.data.first_name} onChange={(e) => form.setData('first_name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Last name" value={form.data.last_name} onChange={(e) => form.setData('last_name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <select value={form.data.role} onChange={(e) => form.setData('role', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 md:col-span-2"><option value="">Select role</option><option value="Teacher">Teacher</option><option value="Head Teacher">Head Teacher</option><option value="Accountant">Accountant</option><option value="Principal">Principal</option><option value="Vice Principal">Vice Principal</option><option value="Librarian">Librarian</option><option value="Nurse">Nurse</option><option value="Receptionist">Receptionist</option><option value="Other">Other</option></select>
                            <input placeholder="Department" value={form.data.department_name} onChange={(e) => form.setData('department_name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Email" type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Phone" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 md:col-span-2" />
                            <input placeholder="Employee ID" value={form.data.employee_id} onChange={(e) => form.setData('employee_id', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Qualification" value={form.data.qualification} onChange={(e) => form.setData('qualification', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <select value={form.data.branch_id} onChange={(e) => form.setData('branch_id', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900"><option value="">Main school</option>{branches.map((branch) => <option key={branch.id} value={branch.id}>{branch.name}</option>)}</select>
                            <input type="date" value={form.data.joining_date} onChange={(e) => form.setData('joining_date', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                        </div>
                        <button type="submit" disabled={form.processing} className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70">
                            {form.processing ? 'Saving...' : 'Add staff'}
                        </button>
                    </form>
                </div>

                <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 className="text-xl font-semibold text-slate-900">Staff directory</h2>
                    <div className="mt-5 space-y-3">
                        {staff.length > 0 ? staff.map((member) => (
                            <div key={member.id} className="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <div className="font-semibold text-slate-900">{member.first_name} {member.last_name}</div>
                                <div className="mt-1 text-xs text-slate-500">{member.role} • {member.department_name || 'General'}</div>
                                <div className="mt-1 text-xs text-slate-500">{member.email || 'No email'} • {member.phone || 'No phone'}</div>
                                <div className="mt-1 text-xs text-slate-500">{member.employee_id || 'No employee ID'} • {member.branch || 'Main school'}{member.qualification ? ` • ${member.qualification}` : ''}</div>
                            </div>
                        )) : <p className="text-sm text-slate-500">No staff profiles added yet.</p>}
                    </div>
                </div>
            </div>
        </>
    );
}

StaffIndex.layout = AuthenticatedLayout;
