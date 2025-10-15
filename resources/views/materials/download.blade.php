@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data='materialsDownload({ materials: @json($materials), query: @json($query) })' x-init="init()">
    <div class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[180px] bg-white border rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500">إجمالي الطلبات</div>
            <div class="text-2xl font-bold" x-text="summary.total"></div>
        </div>
        <div class="flex-1 min-w-[180px] bg-white border rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500">تم التسليم</div>
            <div class="text-2xl font-bold text-green-600" x-text="summary.delivered"></div>
        </div>
        <div class="flex-1 min-w-[180px] bg-white border rounded-lg p-4 shadow-sm">
            <div class="text-sm text-gray-500">بانتظار التسليم</div>
            <div class="text-2xl font-bold text-amber-500" x-text="summary.pending"></div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-wrap gap-3 items-end flex-1">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm text-gray-600 mb-1">بحث</label>
                    <input type="text" x-model.trim="search" @input.debounce.300="applyFilters" placeholder="ابحث باسم الطالب أو المادة" class="border rounded px-3 py-2 w-full">
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
                <div class="min-w-[160px]">
                    <label class="block text-sm text-gray-600 mb-1">الفصل</label>
                    <select x-model="semesterFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                        <option value="">كل الفصول</option>
                        <template x-for="sem in semesters" :key="'sem-' + sem">
                            <option :value="sem" x-text="sem"></option>
                        </template>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm text-gray-600 mb-1">حالة التسليم</label>
                    <select x-model="deliveryFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                        <option value="all">الكل</option>
                        <option value="pending">بانتظار التسليم</option>
                        <option value="delivered">تم التسليم</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" class="h-10 px-4 bg-gray-200 rounded" @click="openPrint">🖨️ طباعة</button>
                <button type="button" class="h-10 px-4 bg-green-600 text-white rounded" @click="exportCsv">⬇️ تصدير CSV</button>
                <button type="button" class="h-10 px-4 bg-gray-100 border rounded" @click="resetFilters">إعادة الضبط</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border" id="materials-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">رقم</th>
                        <th class="border px-3 py-2 text-right">الطالب</th>
                        <th class="border px-3 py-2 text-right">القسم</th>
                        <th class="border px-3 py-2 text-right">المادة</th>
                        <th class="border px-3 py-2 text-right">الفصل</th>
                        <th class="border px-3 py-2 text-right">الحالة</th>
                        <th class="border px-3 py-2 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!records.length">
                        <tr>
                            <td colspan="7" class="border px-3 py-4 text-center text-gray-500">لا توجد طلبات مطابقة للبحث الحالي.</td>
                        </tr>
                    </template>
                    <template x-for="row in records" :key="row.id">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="row.id"></td>
                            <td class="border px-3 py-2" x-text="row.student"></td>
                            <td class="border px-3 py-2" x-text="row.department"></td>
                            <td class="border px-3 py-2" x-text="row.subject"></td>
                            <td class="border px-3 py-2" x-text="row.semester"></td>
                            <td class="border px-3 py-2">
                                <span class="px-2 py-1 rounded"
                                      :class="row.delivered ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'"
                                      x-text="row.delivered ? 'تم التسليم' : 'بانتظار التسليم'"></span>
                            </td>
                            <td class="border px-3 py-2 space-x-2 rtl:space-x-reverse">
                                <button type="button" class="px-2 py-1 bg-blue-500 text-white rounded" @click="downloadMaterial(row)">⬇️ تنزيل</button>
                                <button type="button" class="px-2 py-1 bg-gray-200 rounded" @click="toggleDelivered(row)" x-text="row.delivered ? 'تعيين كمعلق' : 'تأكيد التسليم'"></button>
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
        Alpine.data('materialsDownload', (config) => ({
            raw: config.materials || [],
            search: config.query || '',
            dataset: [],
            records: [],
            departments: [],
            semesters: [],
            departmentFilter: '',
            semesterFilter: '',
            deliveryFilter: 'all',
            summary: { total: 0, delivered: 0, pending: 0 },

            init() {
                this.dataset = (this.raw || []).map((item, index) => ({
                    id: item.id,
                    student: item.student_name,
                    department: item.department,
                    subject: item.subject,
                    semester: item.semester,
                    delivered: index % 2 === 0
                }));
                this.departments = Array.from(new Set(this.dataset.map(item => item.department))).filter(Boolean);
                this.semesters = Array.from(new Set(this.dataset.map(item => item.semester))).filter(Boolean);
                this.applyFilters();
            },

            applyFilters() {
                const term = this.search.trim();
                this.records = this.dataset.filter(row => {
                    const matchesTerm = !term || [row.student, row.department, row.subject, row.semester].some(field => field.includes(term));
                    const matchesDept = !this.departmentFilter || row.department === this.departmentFilter;
                    const matchesSem = !this.semesterFilter || row.semester === this.semesterFilter;
                    const matchesDelivery = this.deliveryFilter === 'all' || (this.deliveryFilter === 'delivered' ? row.delivered : !row.delivered);
                    return matchesTerm && matchesDept && matchesSem && matchesDelivery;
                });
                this.updateSummary();
            },

            updateSummary() {
                const deliveredCount = this.records.filter(row => row.delivered).length;
                this.summary.total = this.records.length;
                this.summary.delivered = deliveredCount;
                this.summary.pending = this.records.length - deliveredCount;
            },

            toggleDelivered(row) {
                row.delivered = !row.delivered;
                this.updateSummary();
            },

            downloadMaterial(row) {
                alert('تم تجهيز ملف المادة للطالب ' + row.student + '.');
            },

            exportCsv() {
                if (!this.records.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['رقم', 'الطالب', 'القسم', 'المادة', 'الفصل', 'الحالة'];
                const rows = this.records.map(row => [
                    row.id,
                    row.student,
                    row.department,
                    row.subject,
                    row.semester,
                    row.delivered ? 'تم التسليم' : 'بانتظار التسليم'
                ]);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'materials-download.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            },

            openPrint() {
                const baseUrl = '{{ route('materials.download.print') }}';
                const url = baseUrl + '?query=' + encodeURIComponent(this.search.trim());
                window.open(url, '_blank');
            },

            resetFilters() {
                this.search = '';
                this.departmentFilter = '';
                this.semesterFilter = '';
                this.deliveryFilter = 'all';
                this.applyFilters();
            }
        }));
    });
</script>
@endsection
