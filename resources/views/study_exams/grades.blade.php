@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="gradeSheet()" x-init="init()">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">المادة</label>
                <select x-model="filters.subject" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <template x-for="subject in filters.subjects" :key="subject">
                        <option :value="subject" x-text="subject"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">الشعبة</label>
                <select x-model="filters.section" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">جميع الشعب</option>
                    <template x-for="section in filters.sections" :key="section">
                        <option :value="section" x-text="section"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">الفصل</label>
                <select x-model="filters.semester" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <template x-for="sem in filters.semesters" :key="sem">
                        <option :value="sem" x-text="sem"></option>
                    </template>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="window.print()">🖨️ طباعة</button>
                <button type="button" class="px-4 py-2 bg-green-600 text-white rounded" @click="exportCsv">⬇️ تصدير CSV</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">#</th>
                        <th class="border px-3 py-2 text-right">الطالب</th>
                        <th class="border px-3 py-2 text-right">الرقم الجامعي</th>
                        <th class="border px-3 py-2 text-right">درجة الأعمال</th>
                        <th class="border px-3 py-2 text-right">درجة الامتحان</th>
                        <th class="border px-3 py-2 text-right">المجموع</th>
                        <th class="border px-3 py-2 text-right">التقدير</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, idx) in records" :key="row.number">
                        <tr class="odd:bg-gray-50">
                            <td class="border px-3 py-2" x-text="idx + 1"></td>
                            <td class="border px-3 py-2" x-text="row.name"></td>
                            <td class="border px-3 py-2" x-text="row.number"></td>
                            <td class="border px-3 py-2" x-text="row.coursework"></td>
                            <td class="border px-3 py-2" x-text="row.exam"></td>
                            <td class="border px-3 py-2" x-text="row.total"></td>
                            <td class="border px-3 py-2" x-text="row.grade"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('gradeSheet', () => ({
            dataset: [
                { number: '2025-001', name: 'آمنة علي', subject: 'رياضيات 1', section: 'A', semester: 'ربيع 2025', coursework: 38, exam: 56 },
                { number: '2025-010', name: 'محمد عمر', subject: 'رياضيات 1', section: 'A', semester: 'ربيع 2025', coursework: 35, exam: 52 },
                { number: '2025-015', name: 'ليلى يوسف', subject: 'رياضيات 1', section: 'B', semester: 'ربيع 2025', coursework: 30, exam: 48 },
                { number: '2024-075', name: 'سارة محمود', subject: 'فيزياء 1', section: 'A', semester: 'خريف 2024', coursework: 32, exam: 40 }
            ],
            filters: {
                subject: '',
                section: '',
                semester: '',
                subjects: [],
                sections: [],
                semesters: []
            },
            records: [],

            init() {
                this.filters.subjects = Array.from(new Set(this.dataset.map(item => item.subject)));
                this.filters.sections = Array.from(new Set(this.dataset.map(item => item.section)));
                this.filters.semesters = Array.from(new Set(this.dataset.map(item => item.semester)));
                this.filters.subject = this.filters.subjects[0] || '';
                this.filters.semester = this.filters.semesters[0] || '';
                this.applyFilters();
            },

            applyFilters() {
                this.records = this.dataset
                    .filter(row => (!this.filters.subject || row.subject === this.filters.subject)
                        && (!this.filters.section || row.section === this.filters.section)
                        && (!this.filters.semester || row.semester === this.filters.semester))
                    .map(row => ({
                        ...row,
                        total: row.coursework + row.exam,
                        grade: this.gradeFromTotal(row.coursework + row.exam)
                    }));
            },

            gradeFromTotal(total) {
                if (total >= 85) return 'ممتاز';
                if (total >= 75) return 'جيد جداً';
                if (total >= 65) return 'جيد';
                if (total >= 50) return 'مقبول';
                return 'ضعيف';
            },

            exportCsv() {
                if (!this.records.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['الاسم', 'الرقم الجامعي', 'درجة الأعمال', 'درجة الامتحان', 'المجموع', 'التقدير'];
                const rows = this.records.map(row => [row.name, row.number, row.coursework, row.exam, row.total, row.grade]);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'grade-sheet.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            }
        }));
    });
</script>
@endsection
