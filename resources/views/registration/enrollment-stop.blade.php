@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="enrollmentStop()" x-init="applyFilters()">
    <div class="flex flex-wrap gap-3">
        <template x-for="status in statusOptions" :key="status">
            <div class="flex-1 min-w-[160px] bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-sm text-gray-500" x-text="status"></div>
                <div class="text-2xl font-bold" x-text="statusCount(status)"></div>
            </div>
        </template>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-wrap gap-3 items-end flex-1">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm text-gray-600 mb-1">بحث</label>
                    <input type="text" x-model.trim="search" @input.debounce.300="applyFilters" placeholder="ابحث باسم الطالب أو السبب" class="border rounded px-3 py-2 w-full">
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm text-gray-600 mb-1">حالة الطلب</label>
                    <select x-model="statusFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                        <option value="">الكل</option>
                        <template x-for="status in statusOptions" :key="'filter-' + status">
                            <option x-text="status" :value="status"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="flex gap-2">
                <button type="button" @click="printTable" class="h-10 px-4 bg-gray-200 rounded">🖨️ طباعة</button>
                <button type="button" @click="exportCsv" class="h-10 px-4 bg-green-600 text-white rounded">⬇️ تصدير CSV</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">رقم الطلب</th>
                        <th class="border px-3 py-2 text-right">اسم الطالب</th>
                        <th class="border px-3 py-2 text-right">القسم</th>
                        <th class="border px-3 py-2 text-right">سبب الإيقاف</th>
                        <th class="border px-3 py-2 text-right">الحالة</th>
                        <th class="border px-3 py-2 text-right">تاريخ التقديم</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!requests.length">
                        <tr>
                            <td colspan="6" class="border px-3 py-4 text-center text-gray-500">لا توجد طلبات مطابقة للبحث الحالي.</td>
                        </tr>
                    </template>
                    <template x-for="row in requests" :key="row.id">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="row.id"></td>
                            <td class="border px-3 py-2" x-text="row.student"></td>
                            <td class="border px-3 py-2" x-text="row.department"></td>
                            <td class="border px-3 py-2" x-text="row.reason"></td>
                            <td class="border px-3 py-2">
                                <span class="px-2 py-1 rounded"
                                      :class="row.status === 'مقبول' ? 'bg-green-100 text-green-700' : (row.status === 'مرفوض' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')"
                                      x-text="row.status"></span>
                            </td>
                            <td class="border px-3 py-2" x-text="row.submitted_at"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('enrollmentStop', () => ({
            statusOptions: ['قيد المراجعة', 'مقبول', 'مرفوض'],
            requestsSeed: [
                { id: 1001, student: 'آمنة علي', department: 'هندسة كهربائية', reason: 'ظروف صحية', status: 'قيد المراجعة', submitted_at: '2025-01-05' },
                { id: 1002, student: 'محمد عمر', department: 'علوم حاسوب', reason: 'سفر طويل', status: 'مقبول', submitted_at: '2024-12-20' },
                { id: 1003, student: 'سارة محمود', department: 'هندسة ميكانيك', reason: 'ظروف عائلية', status: 'مرفوض', submitted_at: '2025-01-10' },
                { id: 1004, student: 'ليلى يوسف', department: 'علوم حاسوب', reason: 'مرض مزمن', status: 'قيد المراجعة', submitted_at: '2025-01-12' }
            ],
            requests: [],
            search: '',
            statusFilter: '',

            applyFilters() {
                const term = this.search.trim();
                const status = this.statusFilter;
                this.requests = this.requestsSeed.filter(row => {
                    const matchesTerm = !term || [row.student, row.department, row.reason].some(field => field.includes(term));
                    const matchesStatus = !status || row.status === status;
                    return matchesTerm && matchesStatus;
                });
            },

            statusCount(status) {
                return this.requests.filter(row => row.status === status).length;
            },

            exportCsv() {
                if (!this.requests.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['رقم الطلب', 'اسم الطالب', 'القسم', 'سبب الإيقاف', 'الحالة', 'تاريخ التقديم'];
                const rows = this.requests.map(row => [row.id, row.student, row.department, row.reason, row.status, row.submitted_at]);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'enrollment-stop.csv';
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
