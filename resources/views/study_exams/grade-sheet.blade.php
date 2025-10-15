@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="gradeSheetTemplate()">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h1 class="text-2xl font-bold">نموذج كشف الدرجات</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">المادة</label>
                <input type="text" x-model="form.subject" class="border rounded px-3 py-2 w-full" placeholder="مثال: رياضيات 1">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">الشعبة</label>
                <input type="text" x-model="form.section" class="border rounded px-3 py-2 w-full" placeholder="A">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">الفصل الدراسي</label>
                <input type="text" x-model="form.semester" class="border rounded px-3 py-2 w-full" placeholder="ربيع 2025">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">المحاضر</label>
                <input type="text" x-model="form.instructor" class="border rounded px-3 py-2 w-full" placeholder="اسم المحاضر">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">تاريخ الرصد</label>
                <input type="date" x-model="form.recorded_at" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="flex items-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="addRow">➕ إضافة صف</button>
                <button type="button" class="px-4 py-2 bg-gray-100 border rounded" @click="resetRows">إعادة الضبط</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">#</th>
                        <th class="border px-3 py-2 text-right">الرقم الجامعي</th>
                        <th class="border px-3 py-2 text-right">اسم الطالب</th>
                        <th class="border px-3 py-2 text-right">درجة الأعمال</th>
                        <th class="border px-3 py-2 text-right">درجة الامتحان</th>
                        <th class="border px-3 py-2 text-right">المجموع</th>
                        <th class="border px-3 py-2 text-right">ملاحظات</th>
                        <th class="border px-3 py-2 text-right">إزالة</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, idx) in rows" :key="row.id">
                        <tr class="odd:bg-gray-50">
                            <td class="border px-3 py-2" x-text="idx + 1"></td>
                            <td class="border px-3 py-2">
                                <input type="text" x-model="row.number" class="border rounded px-2 py-1 w-full" placeholder="2025-001">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="text" x-model="row.name" class="border rounded px-2 py-1 w-full" placeholder="اسم الطالب">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="number" x-model.number="row.coursework" class="border rounded px-2 py-1 w-full" min="0" max="40">
                            </td>
                            <td class="border px-3 py-2">
                                <input type="number" x-model.number="row.exam" class="border rounded px-2 py-1 w-full" min="0" max="60">
                            </td>
                            <td class="border px-3 py-2" x-text="rowTotal(row)"></td>
                            <td class="border px-3 py-2">
                                <input type="text" x-model="row.note" class="border rounded px-2 py-1 w-full">
                            </td>
                            <td class="border px-3 py-2">
                                <button type="button" class="px-2 py-1 bg-red-100 text-red-700 rounded" @click="removeRow(idx)">حذف</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end gap-2">
            <button type="button" class="px-4 py-2 bg-green-600 text-white rounded" @click="downloadCsv">⬇️ تنزيل CSV</button>
            <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="printSheet">🖨️ طباعة</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('gradeSheetTemplate', () => ({
            form: {
                subject: 'رياضيات 1',
                section: 'A',
                semester: 'ربيع 2025',
                instructor: 'د. أحمد علي',
                recorded_at: new Date().toISOString().slice(0, 10)
            },
            rows: [
                { id: 1, number: '2025-001', name: 'آمنة علي', coursework: 38, exam: 56, note: '' },
                { id: 2, number: '2025-010', name: 'محمد عمر', coursework: 35, exam: 52, note: '' }
            ],
            counter: 3,

            addRow() {
                this.rows.push({ id: this.counter++, number: '', name: '', coursework: 0, exam: 0, note: '' });
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            },

            resetRows() {
                this.rows = [];
                this.counter = 1;
            },

            rowTotal(row) {
                return Number(row.coursework || 0) + Number(row.exam || 0);
            },

            collectMeta() {
                return 'المادة: ' + this.form.subject + ' | الشعبة: ' + this.form.section + ' | الفصل: ' + this.form.semester + ' | المحاضر: ' + this.form.instructor;
            },

            downloadCsv() {
                if (!this.rows.length) {
                    alert('أضف صفوفاً قبل التنزيل.');
                    return;
                }
                const header = ['الرقم الجامعي', 'اسم الطالب', 'درجة الأعمال', 'درجة الامتحان', 'المجموع', 'ملاحظات'];
                const rows = this.rows.map(row => [row.number, row.name, row.coursework, row.exam, this.rowTotal(row), row.note]);
                const meta = ['بيانات الكشف', this.collectMeta()];
                const csv = meta.join('\n') + '\n' + [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'grade-sheet-template.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            },

            printSheet() {
                const printWindow = window.open('', '_blank', 'width=900,height=650');
                if (!printWindow) {
                    return;
                }
                const rowsHtml = this.rows.map((row, idx) => '<tr>' +
                    '<td>' + (idx + 1) + '</td>' +
                    '<td>' + (row.number || '') + '</td>' +
                    '<td>' + (row.name || '') + '</td>' +
                    '<td>' + (row.coursework || 0) + '</td>' +
                    '<td>' + (row.exam || 0) + '</td>' +
                    '<td>' + this.rowTotal(row) + '</td>' +
                    '<td>' + (row.note || '') + '</td>' +
                '</tr>').join('');
                const html = '<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><title>كشف الدرجات</title>' +
                    '<style>body{font-family:\'Tahoma\',\'Arial\',sans-serif;padding:32px;direction:rtl;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ccc;padding:8px;text-align:right;}thead{background:#f3f4f6;}</style>' +
                    '</head><body>' +
                    '<h2>كشف درجات - ' + this.form.subject + '</h2>' +
                    '<p>' + this.collectMeta() + ' - تاريخ الرصد: ' + this.form.recorded_at + '</p>' +
                    '<table><thead><tr><th>#</th><th>الرقم الجامعي</th><th>الاسم</th><th>الأعمال</th><th>الامتحان</th><th>المجموع</th><th>ملاحظات</th></tr></thead><tbody>' + rowsHtml + '</tbody></table>' +
                    '</body></html>';
                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }
        }));
    });
</script>
@endsection
