import { Head, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

const inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900';

export default function EventsIndex({ events = [] }) {
    const form = useForm({
        title: '',
        event_type: 'academic',
        event_date: '',
        location: '',
        description: '',
    });

    const submit = (event) => {
        event.preventDefault();
        form.post('/events', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };

    return (
        <>
            <Head title="School events" />
            <div className="space-y-6">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">School calendar</p>
                    <h2 className="mt-1 text-3xl font-bold text-slate-900">School events</h2>
                    <p className="mt-2 text-sm text-slate-500">Track academic events, meetings, holidays, and key school activities.</p>
                </div>

                <form onSubmit={submit} className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                        <input value={form.data.title} onChange={(event) => form.setData('title', event.target.value)} placeholder="Event title" className={inputClass} />
                        <select value={form.data.event_type} onChange={(event) => form.setData('event_type', event.target.value)} className={inputClass}>
                            <option value="academic">Academic</option>
                            <option value="meeting">Meeting</option>
                            <option value="holiday">Holiday</option>
                            <option value="exam">Exam</option>
                            <option value="other">Other</option>
                        </select>
                        <input type="date" value={form.data.event_date} onChange={(event) => form.setData('event_date', event.target.value)} className={inputClass} />
                        <input value={form.data.location} onChange={(event) => form.setData('location', event.target.value)} placeholder="Location" className={inputClass} />
                        <button type="submit" disabled={form.processing} className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white disabled:opacity-70">
                            {form.processing ? 'Saving...' : 'Add event'}
                        </button>
                    </div>
                    <textarea value={form.data.description} onChange={(event) => form.setData('description', event.target.value)} rows="3" placeholder="Event description" className={`${inputClass} mt-3`} />
                </form>

                <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 className="text-lg font-semibold text-slate-900">Upcoming events</h3>
                    <div className="mt-4 space-y-3">
                        {events.length ? events.map((event) => (
                            <div key={event.id} className="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p className="font-semibold text-slate-900">{event.title}</p>
                                        <p className="mt-1 text-xs uppercase tracking-wide text-slate-500">{event.event_type}</p>
                                    </div>
                                    <span className="inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">{event.event_date}</span>
                                </div>
                                <div className="mt-3 text-sm text-slate-600">
                                    <p>{event.location || 'Location not set'}</p>
                                    {event.description && <p className="mt-2 text-slate-500">{event.description}</p>}
                                </div>
                            </div>
                        )) : <p className="text-sm text-slate-500">No school events added yet.</p>}
                    </div>
                </section>
            </div>
        </>
    );
}

EventsIndex.layout = AuthenticatedLayout;
