@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="deprivedManager()" x-init="applyFilters()">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-wrap gap-3 items-end flex-1">
                <div class="min-w-[160px]">
                    <label class="block text-sm text-gray-600 mb-1">القسم</label>
                    <select x-model="filters.department" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                        <option value="">كل الأقسام</option>
                        <template x-for="dept in filters.departments" :key="dept">
                            <option :value="dept" x-text="dept"></option>
                        </template>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm text-gray-600 mb-1">المادة</label>
                    <select x-model="filters.subject" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                        <option value="">كل المواد</option>
                        <template x-for="subject in filters.subjects" :key="subject">
                            <option :value="subject" x-text="subject"></option>
                        </template>
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm text-gray-600 mb-1">بحث</label>
                    <input type="text" x-model.trim="filters.search" @input.debounce.300="applyFilters" placeholder="ابحث بالاسم أو السبب" class="border rounded px-3 py-2 w-full">
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" class="h-10 px-4 bg-gray-200 rounded" @click="printList">🖨️ طباعة</button>
                <button type="button" class="h-10 px-4 bg-green-600 text-white rounded" @click="exportCsv">⬇️ تصدير CSV</button>
                <button type="button" class="h-10 px-4 bg-gray-100 border rounded" @click="resetFilters">إعادة الضبط</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">الطالب</th>
                        <th class="border px-3 py-2 text-right">القسم</th>
                        <th class="border px-3 py-2 text-right">المادة</th>
                        <th class="border px-3 py-2 text-right">السبب</th>
                        <th class="border px-3 py-2 text-right">تاريخ القرار</th>
                        <th class="border px-3 py-2 text-right">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!records.length">
                        <tr>
                            <td colspan="6" class="border px-3 py-4 text-center text-gray-500">لا توجد حالات محرومين حالياً.</td>
                        </tr>
                    </template>
                    <template x-for="row in records" :key="row.id">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="row.student"></td>
                            <td class="border px-3 py-2" x-text="row.department"></td>
                            <td class="border px-3 py-2" x-text="row.subject"></td>
                            <td class="border px-3 py-2" x-text="row.reason"></td>
                            <td class="border px-3 py-2" x-text="row.date"></td>
                            <td class="border px-3 py-2">
                                <span class="px-2 py-1 rounded" :class="row.active ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'" x-text="row.active ? 'ساري' : 'مرفوع'"></span>
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
        Alpine.data('deprivedManager', () => ({
            dataset: [
                { id: 1, student: 'آمنة علي', department: 'هندسة كهربائية', subject: 'رياضيات 1', reason: 'نسبة الغياب تجاوزت 30%', date: '2025-01-05', active: true },
                { id: 2, student: 'محمد عمر', department: 'علوم حاسوب', subject: 'برمجة 2', reason: 'سلوك غير لائق داخل القاعة', date: '2025-01-08', active: true },
                { id: 3, student: 'سارة محمود', department: 'هندسة ميكانيك', subject: 'ميكانيكا', reason: 'تأخير في سداد الرسوم', date: '2024-12-28', active: false }
            ],
            records: [],
            filters: {
                department: '',
                subject: '',
                search: '',
                departments: [],
                subjects: []
            },

            applyFilters() {
                if (!this.filters.departments.length) {
                    this.filters.departments = Array.from(new Set(this.dataset.map(item => item.department))).filter(Boolean);
                    this.filters.subjects = Array.from(new Set(this.dataset.map(item => item.subject))).filter(Boolean);
                }
                const term = this.filters.search.trim();
                this.records = this.dataset.filter(row => {
                    const matchesTerm = !term || [row.student, row.reason].some(field => field.includes(term));
                    const matchesDept = !this.filters.department || row.department === this.filters.department;
                    const matchesSub = !this.filters.subject || row.subject === this.filters.subject;
                    return matchesTerm && matchesDept && matchesSub;
                });
            },

            resetFilters() {
                this.filters.department = '';
                this.filters.subject = '';
                this.filters.search = '';
                this.applyFilters();
            },

            printList() {
                window.print();
            },

            exportCsv() {
                if (!this.records.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['الطالب', 'القسم', 'المادة', 'السبب', 'التاريخ', 'الحالة'];
                const rows = this.records.map(row => [row.student, row.department, row.subject, row.reason, row.date, row.active ? 'ساري' : 'مرفوع']);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'deprived-students.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            }
        }));
    });
</script>
@endsection
