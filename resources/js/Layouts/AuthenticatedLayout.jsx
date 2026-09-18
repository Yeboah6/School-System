import { Link, router } from '@inertiajs/react';

const navigation = [
    { label: 'Dashboard', href: '/dashboard' },
    { label: 'School', href: '/school' },
    { label: 'Students', href: '/students' },
    { label: 'Parents', href: '/parents' },
    { label: 'Staff', href: '/staff' },
    { label: 'Attendance', href: '/attendance' },
    { label: 'Finance', href: '/finance' },
    { label: 'Examinations', href: '/examinations' },
    { label: 'Timetable', href: '/timetable' },
    { label: 'Reports', href: '/reports' },
    { label: 'Administration', href: '/administration' },
];

export default function AuthenticatedLayout({ children, title = 'Dashboard' }) {
    const handleLogout = () => {
        router.post('/logout');
    };

    return (
        <div className="min-h-screen bg-slate-100 text-slate-900">
            <div className="flex min-h-screen">
                <aside className="hidden w-72 flex-col border-r border-slate-200 bg-white p-5 lg:flex">
                    <div className="mb-8 px-3">
                        <p className="text-lg font-bold tracking-tight text-slate-900">School System</p>
                        <p className="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">SMS</p>
                    </div>

                    <nav className="space-y-1">
                        {navigation.map((item) => (
                            <Link
                                key={item.label}
                                href={item.href}
                                className="flex items-center rounded-xl px-3 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-900"
                            >
                                {item.label}
                            </Link>
                        ))}
                    </nav>

                    <div className="mt-auto rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p className="text-sm font-medium text-slate-900">System ready</p>
                        <p className="mt-1 text-xs text-slate-500">RBAC + school foundation active</p>
                    </div>
                </aside>

                <main className="flex-1">
                    <header className="sticky top-0 z-10 border-b border-slate-200 bg-white/80 backdrop-blur">
                        <div className="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                            <div>
                                <p className="text-sm font-medium text-slate-500">Overview</p>
                                <h1 className="text-xl font-semibold text-slate-900">{title}</h1>
                            </div>

                            <div className="flex items-center gap-3">
                                <button
                                    type="button"
                                    onClick={handleLogout}
                                    className="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                >
                                    Logout
                                </button>
                            </div>
                        </div>
                    </header>

                    <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">{children}</div>
                </main>
            </div>
        </div>
    );
}
