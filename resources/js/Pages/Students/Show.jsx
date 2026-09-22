import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function StudentsShow({ student, parents = [], notes = [], timelines = [], results = [] }) {
    return (
        <>
            <Head title={`${student.first_name} ${student.last_name}`} />
            <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Student details</p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">{student.first_name} {student.last_name}</h2>
                        <p className="mt-2 text-sm text-slate-500">Admission number: {student.admission_no || 'Not assigned'}</p>
                        <p className="mt-1 text-sm text-slate-500">{student.class || 'Class not assigned'} · {student.branch || 'Main school'}</p>
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
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Class</dt><dd className="mt-1 text-sm text-slate-700">{student.class || 'Not assigned'}</dd></div>
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Branch</dt><dd className="mt-1 text-sm text-slate-700">{student.branch || 'Main school'}</dd></div>
                        </dl>
                    </section>

                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Parents and guardians</h3>
                        <div className="mt-4 space-y-3">
                            {parents.length ? parents.map((parent) => <div key={parent.id} className="rounded-xl bg-slate-50 p-3"><p className="font-semibold text-slate-900">{parent.name}</p><p className="mt-1 text-xs text-slate-500">{parent.relationship || 'Guardian'} · {parent.phone || 'No phone'}</p><p className="mt-1 text-xs text-slate-500">{parent.email || 'No email'} · {parent.occupation || 'Occupation not provided'}</p><p className="mt-1 text-xs text-slate-500">{parent.address || 'Address not provided'}</p>{parent.is_primary && <p className="mt-2 text-xs font-semibold text-emerald-700">Primary contact</p>}</div>) : <p className="text-sm text-slate-500">No parent linked.</p>}
                        </div>
                    </section>
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Notes</h3>
                        <div className="mt-4 space-y-3">
                            {notes.length ? notes.map((note) => <article key={note.id} className="rounded-xl bg-slate-50 p-3"><div className="flex items-center justify-between gap-3"><p className="font-semibold text-slate-900">{note.title}</p><span className="text-xs text-slate-400">{note.created_at}</span></div><p className="mt-2 text-sm text-slate-600">{note.note}</p><div className="mt-2 flex flex-wrap gap-2 text-xs font-semibold text-slate-500"><span>{note.category}</span><span className="rounded-full bg-amber-100 px-2 py-1 text-amber-700">{note.priority || 'normal'}</span><span className="rounded-full bg-slate-200 px-2 py-1">{note.visibility || 'staff_only'}</span>{note.follow_up_date && <span>Follow-up: {note.follow_up_date}</span>}</div></article>) : <p className="text-sm text-slate-500">No notes recorded.</p>}
                        </div>
                    </section>

                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Timeline</h3>
                        <div className="mt-4 space-y-3">
                            {timelines.length ? timelines.map((event) => <article key={event.id} className="rounded-xl bg-slate-50 p-3"><div className="flex items-center justify-between gap-3"><p className="font-semibold text-slate-900">{event.title}</p><span className="text-xs text-slate-400">{event.occurred_at || 'No date'}</span></div><p className="mt-2 text-sm text-slate-600">{event.description || 'No description.'}</p><p className="mt-2 text-xs font-semibold text-slate-500">{event.event_type}</p></article>) : <p className="text-sm text-slate-500">No timeline events recorded.</p>}
                        </div>
                    </section>
                </div>

                <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                    <div className="flex items-center justify-between"><div><h3 className="text-lg font-semibold text-slate-900">Examination results</h3><p className="mt-1 text-sm text-slate-500">Published and draft results recorded for this student.</p></div></div>
                    <div className="mt-4 overflow-x-auto"><table className="w-full min-w-160 text-left text-sm"><thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400"><tr><th className="px-3 py-3">Examination</th><th className="px-3 py-3">Subject</th><th className="px-3 py-3">Marks</th><th className="px-3 py-3">Grade</th><th className="px-3 py-3">Status</th></tr></thead><tbody className="divide-y divide-slate-100">{results.length ? results.map((result) => <tr key={result.id}><td className="px-3 py-4">{result.examination}</td><td className="px-3 py-4 font-semibold text-slate-900">{result.subject}</td><td className="px-3 py-4">{result.marks} / {result.maximum_marks}</td><td className="px-3 py-4 font-semibold">{result.grade || '-'}</td><td className="px-3 py-4 capitalize">{result.status}</td></tr>) : <tr><td colSpan="5" className="px-3 py-8 text-center text-slate-500">No examination results recorded yet.</td></tr>}</tbody></table></div>
                </section>
            </div>
        </>
    );
}

StudentsShow.layout = AuthenticatedLayout;
