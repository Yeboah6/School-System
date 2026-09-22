import { Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

const navigation = [
    { label: 'Dashboard', href: '/dashboard', icon: '⌂', permission: 'view-system-dashboard' },
    { label: 'School setup', href: '/school', icon: '▦', permission: 'manage-school-setup' },
    { label: 'Students', href: '/students', icon: '♙', permission: 'view-students' },
    // { label: 'Add student', href: '/students/create', icon: '+', permission: 'create-students' },
    { label: 'Staffs', href: '/staff/overview', icon: '♟', permission: 'view-staff' },
    // { label: 'Add staff', href: '/staff', icon: '+', permission: 'manage-staff' },
    { label: 'Attendance', href: '/attendance', icon: '✓', permission: 'manage-school-setup' },
    { label: 'Finance', href: '/finance', icon: '₵', permissions: ['manage-finance', 'manage-school-setup'] },
    { label: 'Accountant portal', href: '/accountant-portal', icon: '₵', role: 'Accountant' },
    { label: 'Examinations', href: '/examinations', icon: '▤', permission: 'manage-school-setup' },
    { label: 'Timetable', href: '/timetable', icon: '🗓', permission: 'manage-school-setup' },
    { label: 'Events', href: '/events', icon: '✦', permission: 'manage-school-setup' },
    { label: 'Announcements', href: '/announcements', icon: '📣', permission: 'manage-school-setup' },
    { label: 'Notifications', href: '/notifications', icon: '🔔', auth: true },
    { label: 'Parent portal', href: '/parent-portal', icon: '⌂', role: 'Parent' },
    { label: 'Teacher portal', href: '/teacher-portal', icon: '♟', role: 'Teacher' },
    { label: 'Classes', href: '/classes', icon: '▤', permission: 'manage-school-setup' },
    { label: 'Audit', href: '/audit', icon: '◷', permission: 'manage-school-setup' },
    { label: 'Profile', href: '/profile', icon: '◎', auth: true },
];

export default function AuthenticatedLayout({ children, title = 'Dashboard' }) {
    const { url, props } = usePage();
    const [mobileOpen, setMobileOpen] = useState(false);
    const handleLogout = () => {
        router.post('/logout');
    };

    const user = props.auth?.user;
    const flash = props.flash || {};
    const permissions = user?.permissions || [];
    const roles = user?.roles || [];
    const visibleNavigation = navigation.filter((item) => item.auth || (item.role && roles.includes(item.role)) || (item.permission && permissions.includes(item.permission)) || (item.permissions && item.permissions.some((permission) => permissions.includes(permission))));
    const errorMessages = Object.values(props.errors || {}).flat();

    return (
        <div className="min-h-screen bg-[var(--app-canvas)] text-[var(--app-ink)]">
            <div className="flex min-h-screen">
                {mobileOpen && <button type="button" aria-label="Close navigation" onClick={() => setMobileOpen(false)} className="fixed inset-0 z-20 bg-slate-950/40 lg:hidden" />}
                <aside className={`app-sidebar fixed inset-y-0 left-0 z-30 flex w-72 flex-col transition-transform lg:translate-x-0 ${mobileOpen ? 'translate-x-0' : '-translate-x-full'}`}>
                    <div className="border-b border-white/10 px-6 py-6">
                        <p className="text-lg font-bold tracking-tight text-white">School System</p>
                        <p className="mt-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Operations platform</p>
                    </div>

                    <nav className="flex-1 space-y-1 p-4">
                        {visibleNavigation.map((item) => (
                            <Link
                                key={item.label}
                                href={item.href}
                                onClick={() => setMobileOpen(false)}
                                className={`app-nav-item ${url === item.href || (item.href !== '/dashboard' && url.startsWith(item.href)) ? 'is-active' : ''}`}
                            >
                                <span className="w-6 text-center text-base">{item.icon}</span>
                                <span>{item.label}</span>
                            </Link>
                        ))}
                    </nav>

                    <div className="border-t border-white/10 p-4">
                        <div className="mb-3 rounded-xl bg-white/5 p-3">
                            <p className="truncate text-sm font-medium text-white">{user?.school?.name || 'Your school'}</p>
                            <p className="mt-1 text-xs text-slate-400">{user?.roles?.[0] || 'Administrator'}</p>
                        </div>
                        <button type="button" onClick={handleLogout} className="app-nav-item w-full text-left">
                            <span className="w-6 text-center">↪</span>
                            <span>Sign out</span>
                        </button>
                    </div>
                </aside>

                <main className="flex-1 lg:ml-72">
                    <header className="app-topbar sticky top-0 z-10 border-b border-[var(--app-border)] bg-white/90 backdrop-blur">
                        <div className="mx-auto flex max-w-[1440px] items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                            <button type="button" aria-label="Open navigation" onClick={() => setMobileOpen(true)} className="mr-3 rounded-lg border border-[var(--app-border)] px-3 py-2 text-[var(--app-muted)] lg:hidden">☰</button>
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--app-muted)]">Workspace</p>
                                <h1 className="mt-1 text-xl font-semibold text-[var(--app-ink)]">{title}</h1>
                            </div>

                            <div className="flex items-center gap-3">
                                <span className="hidden text-right sm:block"><span className="block text-sm font-semibold text-[var(--app-ink)]">{user?.name || 'Administrator'}</span><span className="block text-xs text-[var(--app-muted)]">{user?.email}</span></span>
                                <span className="flex h-10 w-10 items-center justify-center rounded-full bg-[var(--app-accent-soft)] font-semibold text-[var(--app-accent)]">{(user?.name || 'A').charAt(0).toUpperCase()}</span>
                            </div>
                        </div>
                    </header>

                    <div className="mx-auto max-w-[1440px] px-4 py-6 sm:px-6 lg:px-8">
                        {(flash.success || errorMessages.length) && <div className={`mb-6 rounded-2xl border p-4 text-sm ${errorMessages.length ? 'border-rose-200 bg-rose-50 text-rose-800' : 'border-emerald-200 bg-emerald-50 text-emerald-800'}`}><p className="font-semibold">{errorMessages.length ? 'Please review the highlighted fields.' : flash.success}</p>{errorMessages.length > 0 && <ul className="mt-1 list-disc pl-5">{errorMessages.slice(0, 5).map((message, index) => <li key={index}>{message}</li>)}</ul>}</div>}
                        {flash.portal_credentials && <div className="mb-6 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-950"><p className="font-semibold">{flash.portal_credentials.role} portal account created</p><p className="mt-1">Share these temporary credentials securely. The password will not be shown again.</p><dl className="mt-3 grid gap-2 sm:grid-cols-3"><div><dt className="text-xs uppercase tracking-wide text-amber-700">Login email</dt><dd className="font-mono">{flash.portal_credentials.email}</dd></div><div><dt className="text-xs uppercase tracking-wide text-amber-700">Temporary password</dt><dd className="font-mono">{flash.portal_credentials.password}</dd></div><div><dt className="text-xs uppercase tracking-wide text-amber-700">Portal</dt><dd>{flash.portal_credentials.role === 'Parent' ? '/parent-portal' : '/teacher-portal'}</dd></div></dl></div>}
                        {children}
                    </div>
                </main>
            </div>
        </div>
    );
}
