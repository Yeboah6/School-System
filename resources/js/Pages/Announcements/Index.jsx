import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

const inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900';

export default function AnnouncementsIndex({ announcements = [] }) {
    const form = useForm({
        title: '',
        message: '',
        audience: 'all',
        published_at: '',
        expires_at: '',
        status: 'published',
    });

    const submit = (event) => {
        event.preventDefault();
        form.post('/announcements', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <>
            <Head title="Announcements" />
            <div className="space-y-6">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Communication</p>
                    <h2 className="mt-1 text-3xl font-bold text-slate-900">Announcements</h2>
                    <p className="mt-2 text-sm text-slate-500">Publish school-wide notices and targeted updates for the right audience.</p>
                </div>

                <form onSubmit={submit} className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                        <input value={form.data.title} onChange={(event) => form.setData('title', event.target.value)} placeholder="Announcement title" className={inputClass} />
                        <select value={form.data.audience} onChange={(event) => form.setData('audience', event.target.value)} className={inputClass}>
                            <option value="all">All</option>
                            <option value="students">Students</option>
                            <option value="parents">Parents</option>
                            <option value="staff">Staff</option>
                        </select>
                        <input type="datetime-local" value={form.data.published_at} onChange={(event) => form.setData('published_at', event.target.value)} className={inputClass} />
                        <input type="datetime-local" value={form.data.expires_at} onChange={(event) => form.setData('expires_at', event.target.value)} className={inputClass} />
                        <select value={form.data.status} onChange={(event) => form.setData('status', event.target.value)} className={inputClass}>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Archived</option>
                        </select>
                        <button type="submit" disabled={form.processing} className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white disabled:opacity-70">
                            {form.processing ? 'Saving...' : 'Publish'}
                        </button>
                    </div>
                    <textarea value={form.data.message} onChange={(event) => form.setData('message', event.target.value)} rows="4" placeholder="Write the announcement message" className={`${inputClass} mt-3`} />
                </form>

                <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 className="text-lg font-semibold text-slate-900">Recent notices</h3>
                    <div className="mt-4 space-y-3">
                        {announcements.length ? announcements.map((announcement) => (
                            <article key={announcement.id} className="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p className="font-semibold text-slate-900">{announcement.title}</p>
                                        <p className="mt-1 text-xs uppercase tracking-wide text-slate-500">{announcement.audience}</p>
                                    </div>
                                    <span className="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">{announcement.status}</span>
                                </div>
                                <p className="mt-3 text-sm text-slate-600 whitespace-pre-line">{announcement.message}</p>
                                <div className="mt-3 flex flex-wrap gap-2 text-xs text-slate-500">
                                    <span className="rounded-full bg-white px-2 py-1">Published: {announcement.published_at || '—'}</span>
                                    <span className="rounded-full bg-white px-2 py-1">Expires: {announcement.expires_at || 'No expiry'}</span>
                                </div>
                            </article>
                        )) : <p className="text-sm text-slate-500">No announcements have been published yet.</p>}
                    </div>
                </section>
            </div>
        </>
    );
}

AnnouncementsIndex.layout = AuthenticatedLayout;