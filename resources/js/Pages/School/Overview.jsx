import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

const statColorMap = {
    teal: 'bg-teal-100 text-teal-700',
    rose: 'bg-rose-100 text-rose-700',
    amber: 'bg-amber-100 text-amber-700',
    sky: 'bg-sky-100 text-sky-700',
};

export default function SchoolOverview({ school, academicYears = [], terms = [], departments = [], classes = [], subjects = [], stats = [] }) {
    return (
        <>
            <Head title="School overview" />
            <div className="space-y-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">School overview</p>
                        <h2 className="mt-1 text-3xl font-bold text-slate-900">{school?.name || 'School'}</h2>
                        <p className="mt-2 text-sm text-slate-500">Review your school setup before opening the details workspace.</p>
                    </div>
                    <Link href="/school/details" className="rounded-xl bg-slate-900 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-slate-700">Show details</Link>
                </div>

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    {stats.map((stat) => (
                        <div key={stat.label} className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div className={`mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl text-sm font-bold ${statColorMap[stat.tone] || 'bg-slate-100 text-slate-700'}`}>{stat.value}</div>
                            <div className="text-sm font-medium text-slate-500">{stat.label}</div>
                        </div>
                    ))}
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <div className="flex items-center justify-between">
                            <h3 className="text-lg font-semibold text-slate-900">School profile</h3>
                            <span className="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">{school?.school_type || 'Active'}</span>
                        </div>
                        <dl className="mt-5 grid gap-4 sm:grid-cols-2">
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Email</dt><dd className="mt-1 text-sm text-slate-700">{school?.email || 'Not provided'}</dd></div>
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Phone</dt><dd className="mt-1 text-sm text-slate-700">{school?.phone || 'Not provided'}</dd></div>
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Principal</dt><dd className="mt-1 text-sm text-slate-700">{school?.principal_name || 'Not provided'}</dd></div>
                            <div><dt className="text-xs uppercase tracking-wide text-slate-400">Motto</dt><dd className="mt-1 text-sm text-slate-700">{school?.motto || 'Not provided'}</dd></div>
                        </dl>
                    </section>

                    <section className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Academic calendar</h3>
                        <div className="mt-4 space-y-3">
                            {academicYears.length ? academicYears.map((year) => (
                                <div key={year.id} className="flex items-center justify-between rounded-xl bg-slate-50 p-3">
                                    <div><p className="font-semibold text-slate-900">{year.name}</p><p className="mt-1 text-xs text-slate-500">{year.starts_at} to {year.ends_at}</p></div>
                                    {year.is_current && <span className="text-xs font-semibold text-emerald-700">Current</span>}
                                </div>
                            )) : <p className="text-sm text-slate-500">No academic years added yet.</p>}
                        </div>
                    </section>
                </div>

                <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                    {[
                        ['Terms', terms, (item) => item.name],
                        ['Departments', departments, (item) => item.name],
                        ['Classes', classes, (item) => `${item.name}${item.level ? ` · ${item.level}` : ''}`],
                        ['Subjects', subjects, (item) => `${item.name}${item.code ? ` · ${item.code}` : ''}`],
                    ].map(([title, items, label]) => (
                        <section key={title} className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                            <h3 className="text-lg font-semibold text-slate-900">{title}</h3>
                            <div className="mt-4 space-y-2">
                                {items.length ? items.slice(0, 5).map((item) => <div key={item.id} className="rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-700">{label(item)}</div>) : <p className="text-sm text-slate-500">No records yet.</p>}
                            </div>
                        </section>
                    ))}
                </div>
            </div>
        </>
    );
}

SchoolOverview.layout = AuthenticatedLayout;
