@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="finalResults()" x-init="applyFilters()">
    <div class="flex flex-wrap gap-3">
        <template x-for="card in summary" :key="card.key">
            <div class="flex-1 min-w-[180px] bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-sm text-gray-500" x-text="card.label"></div>
                <div class="text-2xl font-bold" :class="card.color" x-text="card.value"></div>
            </div>
        </template>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">الدفعة</label>
                <select x-model="filters.year" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">كل الدفعات</option>
                    <template x-for="year in years" :key="year">
                        <option :value="year" x-text="year"></option>
                    </template>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">القسم</label>
                <select x-model="filters.department" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">كل الأقسام</option>
                    <template x-for="dept in departments" :key="dept">
                        <option :value="dept" x-text="dept"></option>
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
                        <th class="border px-3 py-2 text-right">الدفعة</th>
                        <th class="border px-3 py-2 text-right">المعدل التراكمي</th>
                        <th class="border px-3 py-2 text-right">التقدير</th>
                        <th class="border px-3 py-2 text-right">الساعات</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!records.length">
                        <tr>
                            <td colspan="6" class="border px-3 py-4 text-center text-gray-500">لا توجد نتائج مطابقة.</td>
                        </tr>
                    </template>
                    <template x-for="row in records" :key="row.name + row.year">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="row.name"></td>
                            <td class="border px-3 py-2" x-text="row.department"></td>
                            <td class="border px-3 py-2" x-text="row.year"></td>
                            <td class="border px-3 py-2" x-text="row.gpa.toFixed(2)"></td>
                            <td class="border px-3 py-2" x-text="row.classification"></td>
                            <td class="border px-3 py-2" x-text="row.credits"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('finalResults', () => ({
            dataset: [
                { name: 'آمنة علي', department: 'هندسة كهربائية', year: 2024, gpa: 3.92, credits: 136 },
                { name: 'محمد عمر', department: 'علوم حاسوب', year: 2024, gpa: 3.45, credits: 132 },
                { name: 'سارة محمود', department: 'هندسة ميكانيك', year: 2023, gpa: 3.78, credits: 134 }
            ],
            records: [],
            filters: { year: '', department: '', search: '' },
            years: [2024, 2023],
            departments: ['هندسة كهربائية', 'علوم حاسوب', 'هندسة ميكانيك'],
            summary: [
                { key: 'graduates', label: 'عدد الخريجين', value: '0', color: '' },
                { key: 'avgGpa', label: 'متوسط المعدل', value: '0.00', color: 'text-blue-600' },
                { key: 'top', label: 'أعلى معدل', value: '0.00', color: 'text-green-600' }
            ],

            applyFilters() {
                const term = this.filters.search.trim();
                this.records = this.dataset.filter(row => {
                    const matchesYear = !this.filters.year || row.year === Number(this.filters.year);
                    const matchesDept = !this.filters.department || row.department === this.filters.department;
                    const matchesTerm = !term || row.name.includes(term);
                    return matchesYear && matchesDept && matchesTerm;
                }).map(row => ({
                    ...row,
                    classification: row.gpa >= 3.6 ? 'امتياز' : row.gpa >= 3.0 ? 'جيد جداً' : 'جيد'
                }));
                this.updateSummary();
            },

            updateSummary() {
                const total = this.records.length;
                const avg = total ? (this.records.reduce((sum, row) => sum + row.gpa, 0) / total).toFixed(2) : '0.00';
                const max = total ? Math.max(...this.records.map(row => row.gpa)).toFixed(2) : '0.00';
                this.summary.find(card => card.key === 'graduates').value = String(total);
                this.summary.find(card => card.key === 'avgGpa').value = avg;
                this.summary.find(card => card.key === 'top').value = max;
            },

            exportCsv() {
                if (!this.records.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['الاسم', 'القسم', 'الدفعة', 'المعدل', 'التقدير', 'الساعات'];
                const rows = this.records.map(row => [row.name, row.department, row.year, row.gpa.toFixed(2), row.classification, row.credits]);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'final-results.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            }
        }));
    });
</script>
@endsection
