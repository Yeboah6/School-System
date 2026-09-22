import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';

const inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-slate-900';
const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

export default function TimetableIndex({ classes = [], subjects = [], staff = [], slots = [], filters = {} }) {
    const form = useForm({ class_id: '', subject_id: '', staff_id: '', day_of_week: 'monday', start_time: '08:00', end_time: '09:00', room: '' });
    const editForm = useForm({ class_id: '', subject_id: '', staff_id: '', day_of_week: 'monday', start_time: '08:00', end_time: '09:00', room: '' });
    const [editingSlot, setEditingSlot] = useState(null);
    const submit = (event) => { event.preventDefault(); form.post('/timetable/slots', { preserveScroll: true, onSuccess: () => form.reset() }); };
    const edit = (slot) => { setEditingSlot(slot); editForm.setData({ class_id: slot.class_id, subject_id: slot.subject_id, staff_id: slot.staff_id, day_of_week: slot.day_of_week, start_time: slot.start_time, end_time: slot.end_time, room: slot.room || '' }); };
    const saveEdit = (event) => { event.preventDefault(); editForm.put(`/timetable/slots/${editingSlot.id}`, { preserveScroll: true, onSuccess: () => { setEditingSlot(null); editForm.reset(); } }); };
    const filter = (event) => { event.preventDefault(); router.get('/timetable', Object.fromEntries(new FormData(event.currentTarget)), { preserveState: true, preserveScroll: true }); };

    return <>
        <Head title="Timetable" />
        <div className="space-y-6">
            <div>
                <p className="text-xs font-semibold uppercase tracking-[0.22em] text-slate-400">Schedule</p>
                <h2 className="mt-1 text-3xl font-bold text-slate-900">Class timetable</h2>
                <p className="mt-2 text-sm text-slate-500">Assign lessons to classes, staff, and rooms in a daily timetable.</p>
            </div>
            <form onSubmit={submit} className="rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                <div className="grid gap-3 md:grid-cols-3 xl:grid-cols-6">
                    <select value={form.data.class_id} onChange={(event) => form.setData('class_id', event.target.value)} className={inputClass}><option value="">Class</option>{classes.map((schoolClass) => <option key={schoolClass.id} value={schoolClass.id}>{schoolClass.name}</option>)}</select>
                    <select value={form.data.subject_id} onChange={(event) => form.setData('subject_id', event.target.value)} className={inputClass}><option value="">Subject</option>{subjects.map((subject) => <option key={subject.id} value={subject.id}>{subject.name}</option>)}</select>
                    <select value={form.data.staff_id} onChange={(event) => form.setData('staff_id', event.target.value)} className={inputClass}><option value="">Teacher</option>{staff.map((member) => <option key={member.id} value={member.id}>{member.first_name} {member.last_name}</option>)}</select>
                    <select value={form.data.day_of_week} onChange={(event) => form.setData('day_of_week', event.target.value)} className={inputClass}>{days.map((day) => <option key={day} value={day}>{day}</option>)}</select>
                    <input type="time" value={form.data.start_time} onChange={(event) => form.setData('start_time', event.target.value)} className={inputClass} />
                    <input type="time" value={form.data.end_time} onChange={(event) => form.setData('end_time', event.target.value)} className={inputClass} />
                </div>
                <div className="mt-3 flex gap-3"><input placeholder="Room" value={form.data.room} onChange={(event) => form.setData('room', event.target.value)} className={inputClass} /><button className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Add slot</button></div>
            </form>
            <form onSubmit={filter} className="grid gap-3 rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm md:grid-cols-4"><input name="search" defaultValue={filters.search || ''} placeholder="Search class, subject, or teacher" className={inputClass} /><select name="day" defaultValue={filters.day || ''} className={inputClass}><option value="">All days</option>{days.map((day) => <option key={day} value={day}>{day}</option>)}</select><select name="class_id" defaultValue={filters.class_id || ''} className={inputClass}><option value="">All classes</option>{classes.map((schoolClass) => <option key={schoolClass.id} value={schoolClass.id}>{schoolClass.name}</option>)}</select><button className="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Apply filters</button></form>
            {editingSlot && <form onSubmit={saveEdit} className="grid gap-3 rounded-[28px] border border-amber-200 bg-amber-50 p-5 md:grid-cols-3 xl:grid-cols-6"><select value={editForm.data.class_id} onChange={(event) => editForm.setData('class_id', event.target.value)} className={inputClass}>{classes.map((schoolClass) => <option key={schoolClass.id} value={schoolClass.id}>{schoolClass.name}</option>)}</select><select value={editForm.data.subject_id} onChange={(event) => editForm.setData('subject_id', event.target.value)} className={inputClass}>{subjects.map((subject) => <option key={subject.id} value={subject.id}>{subject.name}</option>)}</select><select value={editForm.data.staff_id} onChange={(event) => editForm.setData('staff_id', event.target.value)} className={inputClass}>{staff.map((member) => <option key={member.id} value={member.id}>{member.first_name} {member.last_name}</option>)}</select><select value={editForm.data.day_of_week} onChange={(event) => editForm.setData('day_of_week', event.target.value)} className={inputClass}>{days.map((day) => <option key={day} value={day}>{day}</option>)}</select><input type="time" value={editForm.data.start_time} onChange={(event) => editForm.setData('start_time', event.target.value)} className={inputClass} /><input type="time" value={editForm.data.end_time} onChange={(event) => editForm.setData('end_time', event.target.value)} className={inputClass} /><input value={editForm.data.room} onChange={(event) => editForm.setData('room', event.target.value)} placeholder="Room" className={inputClass} /><div className="flex gap-2"><button className="rounded-xl bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white">Save edit</button><button type="button" onClick={() => setEditingSlot(null)} className="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Cancel</button></div></form>}
            <section className="overflow-x-auto rounded-[28px] border border-slate-200 bg-white p-5 shadow-sm">
                <table className="w-full min-w-160 text-left text-sm">
                    <thead className="border-b border-slate-200 text-xs uppercase tracking-wide text-slate-400"><tr><th className="px-3 py-3">Day</th><th className="px-3 py-3">Class</th><th className="px-3 py-3">Subject</th><th className="px-3 py-3">Teacher</th><th className="px-3 py-3">Time</th><th className="px-3 py-3">Room</th><th className="px-3 py-3">Action</th></tr></thead>
                    <tbody className="divide-y divide-slate-100">{slots.length ? slots.map((slot) => <tr key={slot.id}><td className="px-3 py-4 capitalize">{slot.day_of_week}</td><td className="px-3 py-4 font-semibold text-slate-900">{slot.class}</td><td className="px-3 py-4">{slot.subject}</td><td className="px-3 py-4">{slot.teacher}</td><td className="px-3 py-4">{slot.start_time} - {slot.end_time}</td><td className="px-3 py-4">{slot.room || '—'}</td><td className="px-3 py-4"><button type="button" onClick={() => edit(slot)} className="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Save same row</button></td></tr>) : <tr><td colSpan="7" className="px-3 py-10 text-center text-slate-500">No timetable slots yet.</td></tr>}</tbody>
                </table>
            </section>
        </div>
    </>;
}

TimetableIndex.layout = AuthenticatedLayout;
