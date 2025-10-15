@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="attendanceForm()" x-init="selectClass(selectedClassId)">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h1 class="text-2xl font-bold">نموذج الحضور والغياب</h1>
        <p class="text-gray-600">اختر المجموعة الدراسية ثم حدّث حالات الطلبة، يمكنك الطباعة أو التصدير كملف CSV.</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">المجموعة الدراسية</label>
                <select x-model.number="selectedClassId" @change="selectClass(selectedClassId)" class="border rounded px-3 py-2 w-full">
                    <template x-for="group in classes" :key="group.id">
                        <option :value="group.id" x-text="group.name"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">التاريخ</label>
                <input type="date" x-model="currentClass.date" class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">المحاضر</label>
                <input type="text" x-model="currentClass.instructor" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="flex items-end gap-2">
                <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded" @click="printTable">🖨️ طباعة</button>
                <button type="button" class="px-4 py-2 bg-green-600 text-white rounded" @click="exportCsv">⬇️ تصدير CSV</button>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="button" class="px-4 py-2 bg-green-600 text-white rounded" @click="setAll('حاضر')">تحديد الجميع حاضر</button>
            <button type="button" class="px-4 py-2 bg-yellow-500 text-white rounded" @click="setAll('غائب بعذر')">تحديد الجميع غائب بعذر</button>
            <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="resetStatuses">إعادة الضبط</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border" id="attendance-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">رقم الطالب</th>
                        <th class="border px-3 py-2 text-right">اسم الطالب</th>
                        <th class="border px-3 py-2 text-right">الحالة</th>
                        <th class="border px-3 py-2 text-right">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!currentClass.students.length">
                        <tr>
                            <td colspan="4" class="border px-3 py-4 text-center text-gray-500">لا توجد بيانات.</td>
                        </tr>
                    </template>
                    <template x-for="student in currentClass.students" :key="student.number">
                        <tr>
                            <td class="border px-3 py-2" x-text="student.number"></td>
                            <td class="border px-3 py-2" x-text="student.name"></td>
                            <td class="border px-3 py-2">
                                <select class="border rounded px-2 py-1 w-full" x-model="student.status">
                                    <template x-for="option in statusOptions" :key="student.number + '-' + option">
                                        <option x-text="option" :value="option"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" class="border rounded px-2 py-1 w-full" x-model="student.note" placeholder="اكتب ملاحظة عند الحاجة">
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('attendanceForm', () => ({
            classes: [
                {
                    id: 1,
                    name: 'علوم حاسوب - المستوى الأول',
                    date: '2025-01-15',
                    instructor: 'د. سارة محمود',
                    students: [
                        { number: '2025-001', name: 'آمنة علي', status: 'حاضر', note: '' },
                        { number: '2025-002', name: 'ياسين عمران', status: 'غائب', note: '' },
                        { number: '2025-003', name: 'مروان الشريف', status: 'متأخر', note: '' }
                    ]
                },
                {
                    id: 2,
                    name: 'هندسة كهربائية - المستوى الثاني',
                    date: '2025-01-15',
                    instructor: 'م. أحمد علي',
                    students: [
                        { number: '2024-050', name: 'ليث الصادق', status: 'حاضر', note: '' },
                        { number: '2024-051', name: 'أماني محمد', status: 'حاضر', note: '' },
                        { number: '2024-052', name: 'سالم خليفة', status: 'غائب بعذر', note: 'موعد طبي' }
                    ]
                }
            ],
            statusOptions: ['حاضر', 'غائب', 'غائب بعذر', 'متأخر'],
            selectedClassId: 1,
            currentClass: { id: 0, name: '', date: '', instructor: '', students: [] },

            selectClass(id) {
                const found = this.classes.find(group => group.id === id);
                if (found) {
                    this.currentClass = JSON.parse(JSON.stringify(found));
                }
            },

            setAll(status) {
                this.currentClass.students.forEach(student => {
                    student.status = status;
                });
            },

            resetStatuses() {
                this.selectClass(this.selectedClassId);
            },

            exportCsv() {
                if (!this.currentClass.students.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['رقم الطالب', 'اسم الطالب', 'الحالة', 'ملاحظات'];
                const rows = this.currentClass.students.map(student => [student.number, student.name, student.status, student.note || '']);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'attendance-' + this.selectedClassId + '.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            },

            printTable() {
                window.print();
            }
        }));
    });
</script>
@endsection
