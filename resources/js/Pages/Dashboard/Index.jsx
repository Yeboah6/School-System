import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

export default function DashboardIndex({ user, stats }) {
    return (
        <>
            <Head title="Dashboard" />
            <div className="space-y-6">
                <header className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p className="text-sm font-medium text-indigo-600">Good morning</p>
                    <h1 className="mt-2 text-3xl font-bold text-slate-900">{user?.name || 'Administrator'}</h1>
                    <p className="mt-2 text-sm text-slate-600">Here&apos;s what&apos;s happening at your school today.</p>
                </header>

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    {stats.map((stat) => (
                        <div key={stat.label} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <p className="text-sm text-slate-500">{stat.label}</p>
                            <p className="mt-4 text-3xl font-bold text-slate-900">{stat.value}</p>
                        </div>
                    ))}
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 className="text-lg font-semibold text-slate-900">Attendance overview</h2>
                        <div className="mt-6 h-52 rounded-xl bg-slate-100 p-4">
                            <div className="flex h-full items-end gap-3">
                                {[48, 62, 55, 72, 80, 68, 90].map((bar, index) => (
                                    <div key={index} className="flex-1 rounded-t-xl bg-indigo-500/80" style={{ height: `${bar}%` }} />
                                ))}
                            </div>
                        </div>
                    </div>

                    <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 className="text-lg font-semibold text-slate-900">Fee collection</h2>
                        <div className="mt-6 h-52 rounded-xl bg-slate-100 p-4">
                            <div className="flex h-full items-end gap-3">
                                {[35, 50, 45, 70, 80, 94].map((bar, index) => (
                                    <div key={index} className="flex-1 rounded-t-xl bg-emerald-500/80" style={{ height: `${bar}%` }} />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

DashboardIndex.layout = AuthenticatedLayout;
