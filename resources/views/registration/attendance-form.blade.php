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
            <div class="ms-auto flex items-center gap-2">
                <span class="text-sm text-gray-600">نموذج الطباعة (قديم):</span>
                <select x-model="meta.year" class="border rounded px-2 py-1 text-sm">
                    <template x-for="y in meta.years" :key="y"><option :value="y" x-text="y"></option></template>
                </select>
                <select x-model="meta.term" class="border rounded px-2 py-1 text-sm">
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
                <select x-model="meta.department" class="border rounded px-2 py-1 text-sm">
                    <template x-for="d in meta.departments" :key="d"><option :value="d" x-text="d"></option></template>
                </select>
                <select x-model="meta.group" class="border rounded px-2 py-1 text-sm">
                    <template x-for="g in meta.groups" :key="g"><option :value="g" x-text="g"></option></template>
                </select>
                <select x-model="meta.subject" class="border rounded px-2 py-1 text-sm">
                    <template x-for="s in meta.subjects" :key="s"><option :value="s" x-text="s"></option></template>
                </select>
                <button type="button" class="px-3 py-2 bg-indigo-600 text-white rounded" @click="printOldSheet">طباعة</button>
            </div>
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
            },
            // إعدادات بسيطة للطباعة على نمط المنظومة القديمة
            meta: {
                years: [2025,2024,2023],
                departments: ['القسم العام','علوم حاسوب','هندسة كهربائية'],
                groups: ['GS121','GS122','GS211'],
                subjects: ['رياضيات 1','برمجة','فيزياء'],
                year: 2025,
                term: 1,
                department: 'القسم العام',
                group: 'GS121',
                subject: 'رياضيات 1',
            },
            printOldSheet() {
                const days = Array.from({length: 30}, (_,i)=> i+1);
                const head = days.map(d=>'<th>'+d+'</th>').join('');
                const rows = this.currentClass.students.map((s,i)=>'<tr>'+
                    '<td>'+(i+1)+'</td>'+
                    '<td>'+s.number+'</td>'+
                    '<td class="text-right">'+s.name+'</td>'+
                    days.map(()=>'<td>&nbsp;</td>').join('')+
                '</tr>').join('');
                const m=this.meta;
                const metaTbl = '<table style="width:100%;border-collapse:collapse;margin-bottom:8px" dir="rtl">'
                    +'<tr><td>السنة: '+m.year+'</td><td>الفصل: '+m.term+'</td><td>القسم: '+m.department+'</td></tr>'
                    +'<tr><td>الشعبة: '+m.group+'</td><td>المادة: '+m.subject+'</td><td>'+new Date().toLocaleDateString('ar-LY')+'</td></tr>'
                    +'</table>';
                const html = '<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><title>كشف الحضور والغياب</title>'+
                    '<style>body{font-family:\'Tahoma\',\'Arial\',sans-serif;direction:rtl;padding:16px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #999;padding:4px;text-align:center;font-size:12px}thead{background:#f3f4f6} .text-right{text-align:right}</style>'+
                    '</head><body><h3 style="margin:0 0 8px">كشف الحضور والغياب</h3>'+metaTbl+
                    '<table><thead><tr><th>#</th><th>رقم القيد</th><th class="text-right">اسم الطالب</th>'+head+'</tr></thead><tbody>'+rows+'</tbody></table></body></html>';
                const w=window.open('', '_blank', 'width=900,height=650');
                w.document.write(html); w.document.close(); w.focus(); w.print();
            }
        }));
    });
</script>
@endsection
