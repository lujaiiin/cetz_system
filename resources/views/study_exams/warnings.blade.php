@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="warningsManager()" x-init="applyFilters()">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">نوع الإنذار</label>
                <select x-model="filters.type" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">الكل</option>
                    <template x-for="type in types" :key="type">
                        <option :value="type" x-text="type"></option>
                    </template>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm text-gray-600 mb-1">بحث</label>
                <input type="text" x-model.trim="filters.search" @input.debounce.300="applyFilters" placeholder="ابحث باسم الطالب" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="flex gap-2">
                <button type="button" class="h-10 px-4 bg-gray-200 rounded" @click="window.print()">🖨️ طباعة</button>
                <button type="button" class="h-10 px-4 bg-green-600 text-white rounded" @click="exportCsv">⬇️ تصدير CSV</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">الطالب</th>
                        <th class="border px-3 py-2 text-right">القسم</th>
                        <th class="border px-3 py-2 text-right">نوع الإنذار</th>
                        <th class="border px-3 py-2 text-right">التاريخ</th>
                        <th class="border px-3 py-2 text-right">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!records.length">
                        <tr>
                            <td colspan="5" class="border px-3 py-4 text-center text-gray-500">لا توجد إنذارات حالية.</td>
                        </tr>
                    </template>
                    <template x-for="row in records" :key="row.student + row.date">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="row.student"></td>
                            <td class="border px-3 py-2" x-text="row.department"></td>
                            <td class="border px-3 py-2" x-text="row.type"></td>
                            <td class="border px-3 py-2" x-text="row.date"></td>
                            <td class="border px-3 py-2">
                                <span class="px-2 py-1 rounded" :class="row.status === 'active' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'" x-text="row.status === 'active' ? 'ساري' : 'مغلق'"></span>
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
        Alpine.data('warningsManager', () => ({
            dataset: [
                { student: 'آمنة علي', department: 'هندسة كهربائية', type: 'غياب', date: '2025-01-04', status: 'active' },
                { student: 'محمد عمر', department: 'علوم حاسوب', type: 'أكاديمي', date: '2024-12-30', status: 'closed' }
            ],
            records: [],
            filters: { type: '', search: '' },
            types: ['غياب', 'أكاديمي', 'سلوكي'],

            applyFilters() {
                const term = this.filters.search.trim();
                this.records = this.dataset.filter(row => {
                    const matchesType = !this.filters.type || row.type === this.filters.type;
                    const matchesTerm = !term || row.student.includes(term);
                    return matchesType && matchesTerm;
                });
            },

            exportCsv() {
                if (!this.records.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['الطالب', 'القسم', 'نوع الإنذار', 'التاريخ', 'الحالة'];
                const rows = this.records.map(row => [row.student, row.department, row.type, row.date, row.status === 'active' ? 'ساري' : 'مغلق']);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'warnings.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            }
        }));
    });
</script>
@endsection
