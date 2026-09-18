import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function StudentsShow({ student, parents = [], notes = [], timelines = [] }) {
    return (
        <>
            <Head title={`${student.first_name} ${student.last_name}`} />
            <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Student details</p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">{student.first_name} {student.last_name}</h2>
                        <p className="mt-2 text-sm text-slate-500">Admission number: {student.admission_no || 'Not assigned'}</p>
                    </div>
                    <Link href="/students" className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to students</Link>
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
                        <h3 className="text-lg font-semibold text-slate-900">Profile</h3>
                        <dl className="mt-5 grid gap-4 sm:grid-cols-2">
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Full name</dt><dd className="mt-1 text-sm text-slate-700">{student.first_name} {student.middle_name || ''} {student.last_name}</dd></div>
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Gender</dt><dd className="mt-1 text-sm text-slate-700">{student.gender || 'Not specified'}</dd></div>
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Date of birth</dt><dd className="mt-1 text-sm text-slate-700">{student.date_of_birth || 'Not specified'}</dd></div>
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Status</dt><dd className="mt-1"><span className="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">{student.status}</span></dd></div>
                        </dl>
                    </section>

                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Parents and guardians</h3>
                        <div className="mt-4 space-y-3">
                            {parents.length ? parents.map((parent) => <div key={parent.id} className="rounded-xl bg-slate-50 p-3"><p className="font-semibold text-slate-900">{parent.name}</p><p className="mt-1 text-xs text-slate-500">{parent.relationship || 'Guardian'} · {parent.phone || 'No phone'}</p>{parent.is_primary && <p className="mt-2 text-xs font-semibold text-emerald-700">Primary contact</p>}</div>) : <p className="text-sm text-slate-500">No parent linked.</p>}
                        </div>
                    </section>
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Notes</h3>
                        <div className="mt-4 space-y-3">
                            {notes.length ? notes.map((note) => <article key={note.id} className="rounded-xl bg-slate-50 p-3"><div className="flex items-center justify-between gap-3"><p className="font-semibold text-slate-900">{note.title}</p><span className="text-xs text-slate-400">{note.created_at}</span></div><p className="mt-2 text-sm text-slate-600">{note.note}</p><p className="mt-2 text-xs font-semibold text-slate-500">{note.category}</p></article>) : <p className="text-sm text-slate-500">No notes recorded.</p>}
                        </div>
                    </section>

                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Timeline</h3>
                        <div className="mt-4 space-y-3">
                            {timelines.length ? timelines.map((event) => <article key={event.id} className="rounded-xl bg-slate-50 p-3"><div className="flex items-center justify-between gap-3"><p className="font-semibold text-slate-900">{event.title}</p><span className="text-xs text-slate-400">{event.occurred_at || 'No date'}</span></div><p className="mt-2 text-sm text-slate-600">{event.description || 'No description.'}</p><p className="mt-2 text-xs font-semibold text-slate-500">{event.event_type}</p></article>) : <p className="text-sm text-slate-500">No timeline events recorded.</p>}
                        </div>
                    </section>
                </div>
            </div>
        </>
    );
}

StudentsShow.layout = AuthenticatedLayout;
