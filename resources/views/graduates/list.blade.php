@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="graduatesList()" x-init="applyFilters()">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h1 class="text-2xl font-bold">كشف الخريجين</h1>
        <p class="text-gray-600">تصفية الخريجين حسب الدفعة أو القسم، ثم طباعة أو تنزيل القائمة.</p>

        <div class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">الدفعة</label>
                <select x-model.number="yearFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="0">جميع الدفعات</option>
                    <template x-for="year in years" :key="'year-' + year">
                        <option :value="year" x-text="year"></option>
                    </template>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">القسم</label>
                <select x-model="departmentFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">كل الأقسام</option>
                    <template x-for="dept in departments" :key="'dept-' + dept">
                        <option :value="dept" x-text="dept"></option>
                    </template>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm text-gray-600 mb-1">بحث</label>
                <input type="text" x-model.trim="search" @input.debounce.300="applyFilters" placeholder="ابحث باسم الخريج" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="flex gap-2">
                <button type="button" class="h-10 px-4 bg-gray-200 rounded" @click="printTable">🖨️ طباعة</button>
                <button type="button" class="h-10 px-4 bg-green-600 text-white rounded" @click="exportCsv">⬇️ تصدير CSV</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">الاسم</th>
                        <th class="border px-3 py-2 text-right">القسم</th>
                        <th class="border px-3 py-2 text-right">الدفعة</th>
                        <th class="border px-3 py-2 text-right">المعدل التراكمي</th>
                        <th class="border px-3 py-2 text-right">التقدير</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!records.length">
                        <tr>
                            <td colspan="5" class="border px-3 py-4 text-center text-gray-500">لا يوجد خريجون مطابقون للبحث.</td>
                        </tr>
                    </template>
                    <template x-for="row in records" :key="row.name + row.year">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="row.name"></td>
                            <td class="border px-3 py-2" x-text="row.department"></td>
                            <td class="border px-3 py-2" x-text="row.year"></td>
                            <td class="border px-3 py-2" x-text="row.gpa.toFixed(2)"></td>
                            <td class="border px-3 py-2">
                                <span class="px-2 py-1 rounded" :class="row.honors ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'" x-text="row.honors ? 'مع مرتبة الشرف' : '—'"></span>
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
        Alpine.data('graduatesList', () => ({
            dataset: [
                { name: 'آمنة علي', department: 'هندسة كهربائية', year: 2024, gpa: 3.85, honors: true },
                { name: 'محمد عمر', department: 'علوم حاسوب', year: 2024, gpa: 3.45, honors: false },
                { name: 'سارة محمود', department: 'هندسة ميكانيك', year: 2023, gpa: 3.92, honors: true },
                { name: 'ليث الصادق', department: 'هندسة كهربائية', year: 2022, gpa: 3.55, honors: false }
            ],
            records: [],
            years: [2024, 2023, 2022],
            departments: ['هندسة كهربائية', 'علوم حاسوب', 'هندسة ميكانيك'],
            yearFilter: 0,
            departmentFilter: '',
            search: '',

            applyFilters() {
                const year = this.yearFilter;
                const dept = this.departmentFilter;
                const term = this.search.trim();
                this.records = this.dataset.filter(row => {
                    const matchesYear = !year || row.year === year;
                    const matchesDept = !dept || row.department === dept;
                    const matchesTerm = !term || row.name.includes(term);
                    return matchesYear && matchesDept && matchesTerm;
                });
            },

            exportCsv() {
                if (!this.records.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['الاسم', 'القسم', 'الدفعة', 'المعدل التراكمي', 'التقدير'];
                const rows = this.records.map(row => [row.name, row.department, row.year, row.gpa.toFixed(2), row.honors ? 'مع مرتبة الشرف' : '—']);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'graduates-list.csv';
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
