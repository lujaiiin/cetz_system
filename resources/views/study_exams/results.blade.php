@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="studyResults()" x-init="init()">
    <div class="flex flex-wrap gap-3">
        <template x-for="card in summaryCards" :key="card.key">
            <div class="flex-1 min-w-[180px] bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-sm text-gray-500" x-text="card.label"></div>
                <div class="text-2xl font-bold" :class="card.color" x-text="card.value"></div>
            </div>
        </template>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="flex flex-wrap gap-3 items-end flex-1">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm text-gray-600 mb-1">بحث</label>
                    <input type="text" x-model.trim="filters.search" @input.debounce.300="applyFilters" placeholder="ابحث باسم الطالب أو المادة" class="border rounded px-3 py-2 w-full">
                </div>
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
                <div class="min-w-[160px]">
                    <label class="block text-sm text-gray-600 mb-1">الفصل</label>
                    <select x-model="filters.semester" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                        <option value="">كل الفصول</option>
                        <template x-for="sem in filters.semesters" :key="sem">
                            <option :value="sem" x-text="sem"></option>
                        </template>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-sm text-gray-600 mb-1">الحالة</label>
                    <select x-model="filters.status" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                        <option value="all">الكل</option>
                        <option value="passed">ناجح</option>
                        <option value="failed">راسب</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="button" class="h-10 px-4 bg-gray-200 rounded" @click="printResults">🖨️ طباعة</button>
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
                        <th class="border px-3 py-2 text-right">الفصل</th>
                        <th class="border px-3 py-2 text-right">الدرجة</th>
                        <th class="border px-3 py-2 text-right">النسبة %</th>
                        <th class="border px-3 py-2 text-right">الحالة</th>
                        <th class="border px-3 py-2 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!records.length">
                        <tr>
                            <td colspan="8" class="border px-3 py-4 text-center text-gray-500">لا توجد نتائج مطابقة.</td>
                        </tr>
                    </template>
                    <template x-for="row in records" :key="row.id">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="row.student"></td>
                            <td class="border px-3 py-2" x-text="row.department"></td>
                            <td class="border px-3 py-2" x-text="row.subject"></td>
                            <td class="border px-3 py-2" x-text="row.semester"></td>
                            <td class="border px-3 py-2" x-text="row.grade"></td>
                            <td class="border px-3 py-2" x-text="row.percentage"></td>
                            <td class="border px-3 py-2">
                                <span class="px-2 py-1 rounded" :class="row.status === 'passed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="row.status === 'passed' ? 'ناجح' : 'راسب'"></span>
                            </td>
                            <td class="border px-3 py-2">
                                <button type="button" class="px-2 py-1 bg-blue-500 text-white rounded" @click="showDetails(row)">تفاصيل</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <template x-if="selected">
        <div class="bg-white rounded-lg shadow p-6 space-y-3">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">تفاصيل الطالب</h2>
                <button type="button" class="text-sm text-gray-500" @click="selected = null">إغلاق</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div><span class="text-gray-500">الاسم:</span> <span x-text="selected.student"></span></div>
                <div><span class="text-gray-500">القسم:</span> <span x-text="selected.department"></span></div>
                <div><span class="text-gray-500">المادة:</span> <span x-text="selected.subject"></span></div>
                <div><span class="text-gray-500">الفصل:</span> <span x-text="selected.semester"></span></div>
                <div><span class="text-gray-500">الدرجة:</span> <span x-text="selected.grade"></span></div>
                <div><span class="text-gray-500">النسبة:</span> <span x-text="selected.percentage"></span></div>
                <div class="md:col-span-3"><span class="text-gray-500">ملاحظات:</span> <span x-text="selected.notes || 'لا توجد ملاحظات'"></span></div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('studyResults', () => ({
            dataset: [
                { id: 1, student: 'آمنة علي', department: 'هندسة كهربائية', subject: 'رياضيات 1', semester: 'ربيع 2025', grade: 'A', percentage: 94, status: 'passed', notes: 'أداء ممتاز' },
                { id: 2, student: 'محمد عمر', department: 'علوم حاسوب', subject: 'برمجة 1', semester: 'خريف 2024', grade: 'B+', percentage: 87, status: 'passed', notes: '' },
                { id: 3, student: 'سارة محمود', department: 'هندسة ميكانيك', subject: 'ميكانيكا', semester: 'ربيع 2025', grade: 'D', percentage: 59, status: 'failed', notes: 'يحتاج إلى إعادة الامتحان' },
                { id: 4, student: 'ليث الصادق', department: 'هندسة كهربائية', subject: 'دوائر كهربائية', semester: 'خريف 2024', grade: 'C', percentage: 72, status: 'passed', notes: '' }
            ],
            records: [],
            selected: null,
            filters: {
                search: '',
                department: '',
                subject: '',
                semester: '',
                status: 'all',
                departments: [],
                subjects: [],
                semesters: []
            },
            summaryCards: [
                { key: 'total', label: 'عدد الحالات المعروضة', value: '0', color: '' },
                { key: 'passed', label: 'عدد الناجحين', value: '0', color: 'text-green-600' },
                { key: 'failed', label: 'عدد الراسبين', value: '0', color: 'text-red-500' },
                { key: 'avg', label: 'متوسط النسبة', value: '0%', color: 'text-blue-600' }
            ],

            init() {
                this.filters.departments = Array.from(new Set(this.dataset.map(item => item.department))).filter(Boolean);
                this.filters.subjects = Array.from(new Set(this.dataset.map(item => item.subject))).filter(Boolean);
                this.filters.semesters = Array.from(new Set(this.dataset.map(item => item.semester))).filter(Boolean);
                this.applyFilters();
            },

            applyFilters() {
                const term = this.filters.search.trim();
                this.records = this.dataset.filter(row => {
                    const matchesTerm = !term || [row.student, row.subject, row.department, row.semester].some(field => String(field).includes(term));
                    const matchesDept = !this.filters.department || row.department === this.filters.department;
                    const matchesSub = !this.filters.subject || row.subject === this.filters.subject;
                    const matchesSem = !this.filters.semester || row.semester === this.filters.semester;
                    const matchesStatus = this.filters.status === 'all' || row.status === this.filters.status;
                    return matchesTerm && matchesDept && matchesSub && matchesSem && matchesStatus;
                });
                this.updateSummary();
            },

            updateSummary() {
                const total = this.records.length;
                const passed = this.records.filter(row => row.status === 'passed').length;
                const failed = this.records.filter(row => row.status === 'failed').length;
                const average = total ? Math.round(this.records.reduce((sum, row) => sum + Number(row.percentage), 0) / total) : 0;
                this.summaryCards.find(card => card.key === 'total').value = String(total);
                this.summaryCards.find(card => card.key === 'passed').value = String(passed);
                this.summaryCards.find(card => card.key === 'failed').value = String(failed);
                this.summaryCards.find(card => card.key === 'avg').value = average + '%';
            },

            resetFilters() {
                this.filters.search = '';
                this.filters.department = '';
                this.filters.subject = '';
                this.filters.semester = '';
                this.filters.status = 'all';
                this.applyFilters();
            },

            showDetails(row) {
                this.selected = row;
            },

            printResults() {
                window.print();
            },

            exportCsv() {
                if (!this.records.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['الطالب', 'القسم', 'المادة', 'الفصل', 'الدرجة', 'النسبة', 'الحالة'];
                const rows = this.records.map(row => [row.student, row.department, row.subject, row.semester, row.grade, row.percentage, row.status === 'passed' ? 'ناجح' : 'راسب']);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'exam-results.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            }
        }));
    });
</script>
@endsection
