import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

const statColorMap = {
    teal: 'bg-teal-100 text-teal-700',
    rose: 'bg-rose-100 text-rose-700',
    amber: 'bg-amber-100 text-amber-700',
    sky: 'bg-sky-100 text-sky-700',
};

const emptyTerm = { academic_year_id: '', name: '', starts_at: '', ends_at: '', is_current: true };
const emptyDepartment = { name: '', head_name: '', description: '' };
const emptyClass = { name: '', level: '' };
const emptySubject = { name: '', code: '', description: '' };

export default function SchoolIndex({ school, academicYears, terms, departments, classes, subjects, stats }) {
    const { data, setData, post, processing } = useForm({
        name: school?.name || '',
        email: school?.email || '',
        phone: school?.phone || '',
        address: school?.address || '',
        motto: school?.motto || '',
        principal_name: school?.principal_name || '',
        school_type: school?.school_type || 'Primary',
        currency: school?.currency || 'GHS',
        timezone: school?.timezone || 'Africa/Accra',
    });

    const academicYearForm = useForm({
        id: '',
        name: '',
        starts_at: '',
        ends_at: '',
        is_current: true,
    });

    const termForm = useForm({
        id: '',
        academic_year_id: academicYears?.[0]?.id || '',
        name: '',
        starts_at: '',
        ends_at: '',
        is_current: true,
    });

    const departmentForm = useForm({
        id: '',
        name: '',
        head_name: '',
        description: '',
    });

    const classForm = useForm({
        id: '',
        name: '',
        level: '',
    });

    const subjectForm = useForm({
        id: '',
        name: '',
        code: '',
        description: '',
    });

    const handleSchoolSubmit = (e) => {
        e.preventDefault();
        post('/school/profile', {
            preserveScroll: true,
        });
    };

    const handleAcademicYearSubmit = (e) => {
        e.preventDefault();

        if (academicYearForm.data.id) {
            router.put(`/school/academic-years/${academicYearForm.data.id}`, academicYearForm.data, {
                preserveScroll: true,
                onSuccess: () => academicYearForm.reset(),
            });
            return;
        }

        academicYearForm.post('/school/academic-years', {
            preserveScroll: true,
            onSuccess: () => academicYearForm.reset(),
        });
    };

    const handleTermSubmit = (e) => {
        e.preventDefault();

        if (termForm.data.id) {
            router.put(`/school/terms/${termForm.data.id}`, termForm.data, {
                preserveScroll: true,
                onSuccess: () => termForm.reset(),
            });
            return;
        }

        termForm.post('/school/terms', {
            preserveScroll: true,
            onSuccess: () => termForm.reset(),
        });
    };

    const handleTermSave = (e) => handleTermSubmit(e);

    const handleDepartmentSubmit = (e) => {
        e.preventDefault();

        if (departmentForm.data.id) {
            router.put(`/school/departments/${departmentForm.data.id}`, departmentForm.data, {
                preserveScroll: true,
                onSuccess: () => departmentForm.reset(),
            });
            return;
        }

        departmentForm.post('/school/departments', {
            preserveScroll: true,
            onSuccess: () => departmentForm.reset(),
        });
    };

    const handleDepartmentSave = (e) => handleDepartmentSubmit(e);

    const handleClassSubmit = (e) => {
        e.preventDefault();

        if (classForm.data.id) {
            router.put(`/school/classes/${classForm.data.id}`, classForm.data, {
                preserveScroll: true,
                onSuccess: () => classForm.reset(),
            });
            return;
        }

        classForm.post('/school/classes', {
            preserveScroll: true,
            onSuccess: () => classForm.reset(),
        });
    };

    const handleClassSave = (e) => handleClassSubmit(e);

    const handleSubjectSubmit = (e) => {
        e.preventDefault();

        if (subjectForm.data.id) {
            router.put(`/school/subjects/${subjectForm.data.id}`, subjectForm.data, {
                preserveScroll: true,
                onSuccess: () => subjectForm.reset(),
            });
            return;
        }

        subjectForm.post('/school/subjects', {
            preserveScroll: true,
            onSuccess: () => subjectForm.reset(),
        });
    };

    const handleSubjectSave = (e) => handleSubjectSubmit(e);

    const currentYear = academicYears?.[0]?.name || 'No academic year';

    const deleteEntity = (url) => {
        if (window.confirm('Delete this record?')) {
            router.delete(url, {
                preserveScroll: true,
            });
        }
    };

    const handleAcademicYearEdit = (year) => {
        academicYearForm.setData({
            id: year.id,
            name: year.name,
            starts_at: year.starts_at,
            ends_at: year.ends_at,
            is_current: Boolean(year.is_current),
        });
    };

    const handleTermEdit = (term) => {
        termForm.setData({
            id: term.id,
            academic_year_id: term.academic_year_id || academicYears?.[0]?.id || '',
            name: term.name,
            starts_at: term.starts_at,
            ends_at: term.ends_at,
            is_current: Boolean(term.is_current),
        });
    };

    const handleDepartmentEdit = (department) => {
        departmentForm.setData({
            id: department.id,
            name: department.name,
            head_name: department.head_name || '',
            description: department.description || '',
        });
    };

    const handleClassEdit = (schoolClass) => {
        classForm.setData({
            id: schoolClass.id,
            name: schoolClass.name,
            level: schoolClass.level || '',
        });
    };

    const handleSubjectEdit = (subject) => {
        subjectForm.setData({
            id: subject.id,
            name: subject.name,
            code: subject.code || '',
            description: subject.description || '',
        });
    };

    return (
        <>
            <Head title="School" />
            <div className="space-y-6">
                <div className="flex items-center justify-between gap-4">
                    <div>
                        <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">School details</p>
                        <h2 className="mt-1 text-2xl font-bold text-slate-900">Manage school setup</h2>
                    </div>
                    <Link href="/school" className="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Back to overview
                    </Link>
                </div>
                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    {stats.map((stat) => (
                        <div key={stat.label} className="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div className={`mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl ${statColorMap[stat.tone] || 'bg-slate-100 text-slate-700'}`}>
                                {stat.value}
                            </div>
                            <div className="text-3xl font-bold text-slate-900">{stat.value}</div>
                            <div className="mt-1 text-sm text-slate-500">{stat.label}</div>
                        </div>
                    ))}
                </div>

                <div className="grid gap-6 xl:grid-cols-[1.4fr_0.6fr]">
                    <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <div className="mb-6 flex items-center justify-between">
                            <div>
                                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">School profile</p>
                                <h2 className="mt-1 text-2xl font-bold text-slate-900">{school?.name || 'Hillcrest Academy'}</h2>
                            </div>
                            <span className="rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-700">Active</span>
                        </div>

                        <form onSubmit={handleSchoolSubmit} className="space-y-4">
                            <div className="grid gap-4 md:grid-cols-2">
                                <label className="block text-sm font-medium text-slate-700">
                                    School name
                                    <input value={data.name} onChange={(e) => setData('name', e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                                <label className="block text-sm font-medium text-slate-700">
                                    School type
                                    <input value={data.school_type} onChange={(e) => setData('school_type', e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                                <label className="block text-sm font-medium text-slate-700">
                                    Email
                                    <input value={data.email} onChange={(e) => setData('email', e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                                <label className="block text-sm font-medium text-slate-700">
                                    Phone
                                    <input value={data.phone} onChange={(e) => setData('phone', e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                                <label className="block text-sm font-medium text-slate-700 md:col-span-2">
                                    Address
                                    <textarea value={data.address} onChange={(e) => setData('address', e.target.value)} rows={3} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                                <label className="block text-sm font-medium text-slate-700">
                                    Motto
                                    <input value={data.motto} onChange={(e) => setData('motto', e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                                <label className="block text-sm font-medium text-slate-700">
                                    Principal
                                    <input value={data.principal_name} onChange={(e) => setData('principal_name', e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                                <label className="block text-sm font-medium text-slate-700">
                                    Currency
                                    <input value={data.currency} onChange={(e) => setData('currency', e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                                <label className="block text-sm font-medium text-slate-700">
                                    Timezone
                                    <input value={data.timezone} onChange={(e) => setData('timezone', e.target.value)} className="mt-1 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900 outline-none transition focus:border-slate-400 focus:bg-white" />
                                </label>
                            </div>

                            <div className="flex justify-end">
                                <button type="submit" disabled={processing} className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 disabled:cursor-not-allowed disabled:opacity-70">
                                    {processing ? 'Saving...' : 'Save changes'}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div className="space-y-6">
                        <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                            <div className="mb-4 flex items-center justify-between">
                                <h3 className="text-lg font-semibold text-slate-900">Academic calendar</h3>
                                <button type="button" className="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">Today</button>
                            </div>

                            <div className="space-y-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div className="flex items-center justify-between text-sm text-slate-500">
                                    <span>Current year</span>
                                    <span className="rounded-full bg-indigo-100 px-2 py-1 text-xs font-semibold text-indigo-700">{currentYear}</span>
                                </div>
                                {academicYears && academicYears.length > 0 ? (
                                    academicYears.map((year) => (
                                        <div key={year.id} className="rounded-xl border border-slate-200 bg-white p-3">
                                            <div className="flex items-center justify-between">
                                                <p className="font-semibold text-slate-900">{year.name}</p>
                                                {year.is_current && <span className="text-xs font-medium text-emerald-700">Current</span>}
                                            </div>
                                            <p className="mt-1 text-xs text-slate-500">{year.starts_at} to {year.ends_at}</p>
                                        </div>
                                    ))
                                ) : (
                                    <div className="rounded-xl border border-dashed border-slate-200 bg-white p-4 text-sm text-slate-500">No academic years yet.</div>
                                )}
                            </div>
                        </div>

                        <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                            <h3 className="text-lg font-semibold text-slate-900">Add academic year</h3>
                            <form onSubmit={handleAcademicYearSubmit} className="mt-4 space-y-3">
                                <input placeholder="2026/2027" value={academicYearForm.data.name} onChange={(e) => academicYearForm.setData('name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                                <div className="grid gap-3 sm:grid-cols-2">
                                    <input type="date" value={academicYearForm.data.starts_at} onChange={(e) => academicYearForm.setData('starts_at', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                                    <input type="date" value={academicYearForm.data.ends_at} onChange={(e) => academicYearForm.setData('ends_at', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                                </div>
                                <label className="flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" checked={academicYearForm.data.is_current} onChange={(e) => academicYearForm.setData('is_current', e.target.checked)} />
                                    Set as current academic year
                                </label>
                                <button type="submit" disabled={academicYearForm.processing} className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70">
                                    {academicYearForm.processing ? 'Saving...' : 'Create academic year'}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Terms</h3>
                        <div className="mt-4 space-y-3">
                            {terms && terms.length > 0 ? terms.map((term) => (
                                <div key={term.id} className="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                                    <div className="flex items-center justify-between gap-2">
                                        <span className="font-semibold text-slate-900">{term.name}</span>
                                        <div className="flex gap-2">
                                            <button type="button" onClick={() => handleTermEdit(term)} className="rounded-lg border border-slate-200 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700">Edit</button>
                                            <button type="button" onClick={() => deleteEntity(`/school/terms/${term.id}`)} className="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700">Delete</button>
                                        </div>
                                    </div>
                                    {term.is_current && <div className="mt-2 inline-flex rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-semibold text-emerald-700">Current</div>}
                                    <div className="mt-2 text-xs text-slate-500">{term.starts_at} to {term.ends_at}</div>
                                </div>
                            )) : <p className="text-sm text-slate-500">No terms have been added yet.</p>}
                        </div>
                        <form onSubmit={handleTermSave} className="mt-4 space-y-3">
                            <input placeholder="Term name" value={termForm.data.name} onChange={(e) => termForm.setData('name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <div className="grid gap-3 sm:grid-cols-2">
                                <input type="date" value={termForm.data.starts_at} onChange={(e) => termForm.setData('starts_at', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                                <input type="date" value={termForm.data.ends_at} onChange={(e) => termForm.setData('ends_at', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            </div>
                            <label className="flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" checked={termForm.data.is_current} onChange={(e) => termForm.setData('is_current', e.target.checked)} />
                                Set as current term
                            </label>
                            <button type="submit" disabled={termForm.processing} className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70">
                                {termForm.processing ? 'Saving...' : 'Save term'}
                            </button>
                        </form>
                    </div>

                    <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Departments</h3>
                        <div className="mt-4 space-y-3">
                            {departments && departments.length > 0 ? departments.map((department) => (
                                <div key={department.id} className="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                                    <div className="flex items-center justify-between gap-2">
                                        <div className="font-semibold text-slate-900">{department.name}</div>
                                        <div className="flex gap-2">
                                            <button type="button" onClick={() => handleDepartmentEdit(department)} className="rounded-lg border border-slate-200 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700">Edit</button>
                                            <button type="button" onClick={() => deleteEntity(`/school/departments/${department.id}`)} className="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700">Delete</button>
                                        </div>
                                    </div>
                                    {department.head_name && <div className="mt-1 text-xs text-slate-500">Head: {department.head_name}</div>}
                                    {department.description && <div className="mt-1 text-xs text-slate-500">{department.description}</div>}
                                </div>
                            )) : <p className="text-sm text-slate-500">No departments yet.</p>}
                        </div>
                        <form onSubmit={handleDepartmentSave} className="mt-4 space-y-3">
                            <input placeholder="Department name" value={departmentForm.data.name} onChange={(e) => departmentForm.setData('name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Head name" value={departmentForm.data.head_name} onChange={(e) => departmentForm.setData('head_name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <textarea placeholder="Description" value={departmentForm.data.description} onChange={(e) => departmentForm.setData('description', e.target.value)} rows={3} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <button type="submit" disabled={departmentForm.processing} className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70">
                                {departmentForm.processing ? 'Saving...' : 'Save department'}
                            </button>
                        </form>
                    </div>
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Classes</h3>
                        <div className="mt-4 space-y-3">
                            {classes && classes.length > 0 ? classes.map((schoolClass) => (
                                <div key={schoolClass.id} className="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                                    <div className="flex items-center justify-between gap-2">
                                        <div className="font-semibold text-slate-900">{schoolClass.name}</div>
                                        <div className="flex gap-2">
                                            <button type="button" onClick={() => handleClassEdit(schoolClass)} className="rounded-lg border border-slate-200 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700">Edit</button>
                                            <button type="button" onClick={() => deleteEntity(`/school/classes/${schoolClass.id}`)} className="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700">Delete</button>
                                        </div>
                                    </div>
                                    {schoolClass.level && <div className="mt-1 text-xs text-slate-500">Level: {schoolClass.level}</div>}
                                </div>
                            )) : <p className="text-sm text-slate-500">No classes added yet.</p>}
                        </div>
                        <form onSubmit={handleClassSave} className="mt-4 space-y-3">
                            <input placeholder="Class name" value={classForm.data.name} onChange={(e) => classForm.setData('name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Level (e.g. JHS / SHS)" value={classForm.data.level} onChange={(e) => classForm.setData('level', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <button type="submit" disabled={classForm.processing} className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70">
                                {classForm.processing ? 'Saving...' : 'Save class'}
                            </button>
                        </form>
                    </div>

                    <div className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 className="text-lg font-semibold text-slate-900">Subjects</h3>
                        <div className="mt-4 space-y-3">
                            {subjects && subjects.length > 0 ? subjects.map((subject) => (
                                <div key={subject.id} className="rounded-xl border border-slate-200 bg-slate-50 p-3 text-sm text-slate-700">
                                    <div className="flex items-center justify-between gap-2">
                                        <div className="flex items-center gap-2">
                                            <span className="font-semibold text-slate-900">{subject.name}</span>
                                            {subject.code && <span className="rounded-full bg-indigo-100 px-2 py-1 text-[10px] font-semibold text-indigo-700">{subject.code}</span>}
                                        </div>
                                        <div className="flex gap-2">
                                            <button type="button" onClick={() => handleSubjectEdit(subject)} className="rounded-lg border border-slate-200 bg-white px-2 py-1 text-[10px] font-semibold text-slate-700">Edit</button>
                                            <button type="button" onClick={() => deleteEntity(`/school/subjects/${subject.id}`)} className="rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-semibold text-rose-700">Delete</button>
                                        </div>
                                    </div>
                                    {subject.description && <div className="mt-1 text-xs text-slate-500">{subject.description}</div>}
                                </div>
                            )) : <p className="text-sm text-slate-500">No subjects added yet.</p>}
                        </div>
                        <form onSubmit={handleSubjectSave} className="mt-4 space-y-3">
                            <input placeholder="Subject name" value={subjectForm.data.name} onChange={(e) => subjectForm.setData('name', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <input placeholder="Code (e.g. MATH)" value={subjectForm.data.code} onChange={(e) => subjectForm.setData('code', e.target.value)} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <textarea placeholder="Description" value={subjectForm.data.description} onChange={(e) => subjectForm.setData('description', e.target.value)} rows={3} className="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900" />
                            <button type="submit" disabled={subjectForm.processing} className="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700 disabled:opacity-70">
                                {subjectForm.processing ? 'Saving...' : 'Save subject'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}

SchoolIndex.layout = AuthenticatedLayout;
