import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function StaffIndex({ staff = [] }) {
    const form = useForm({
        first_name: '',
        last_name: '',
        role: '',
        department_name: '',
        email: '',
        phone: '',
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
                            <input placeholder="Role" value={form.data.role} onChange={(e) => form.setData('role', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 md:col-span-2" />
                            <input placeholder="Department" value={form.data.department_name} onChange={(e) => form.setData('department_name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Email" type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Phone" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 md:col-span-2" />
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
                            </div>
                        )) : <p className="text-sm text-slate-500">No staff profiles added yet.</p>}
                    </div>
                </div>
            </div>
        </>
    );
}

StaffIndex.layout = AuthenticatedLayout;
