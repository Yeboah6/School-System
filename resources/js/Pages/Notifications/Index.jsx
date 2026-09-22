import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function NotificationsIndex({ notifications = [] }) {
    const form = useForm({});

    const markRead = (id) => form.post(`/notifications/${id}/read`, { preserveScroll: true });

    return <>
        <Head title="Notifications" />
        <div className="space-y-6">
            <div><p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Inbox</p><h2 className="mt-1 text-3xl font-bold text-slate-900">Notifications</h2><p className="mt-2 text-sm text-slate-500">Keep up with the latest updates from your school.</p></div>
            <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm"><div className="space-y-3">{notifications.length ? notifications.map((notification) => <article key={notification.id} className={`rounded-xl border p-4 ${notification.read_at ? 'border-slate-200 bg-slate-50' : 'border-indigo-200 bg-indigo-50/50'}`}><div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"><div><h3 className="font-semibold text-slate-900">{notification.title}</h3><p className="mt-1 text-sm text-slate-600">{notification.message}</p></div>{!notification.read_at && <button type="button" onClick={() => markRead(notification.id)} className="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white">Mark read</button>}</div></article>) : <p className="text-sm text-slate-500">You have no notifications.</p>}</div></section>
        </div>
    </>;
}

NotificationsIndex.layout = AuthenticatedLayout;