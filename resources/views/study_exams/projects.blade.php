@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="gradProjects()" x-init="applyFilters()">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">القسم</label>
                <select x-model="filters.department" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">كل الأقسام</option>
                    <template x-for="dept in departments" :key="dept">
                        <option :value="dept" x-text="dept"></option>
                    </template>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">المشرف</label>
                <select x-model="filters.supervisor" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">الجميع</option>
                    <template x-for="sup in supervisors" :key="sup">
                        <option :value="sup" x-text="sup"></option>
                    </template>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm text-gray-600 mb-1">بحث</label>
                <input type="text" x-model.trim="filters.search" @input.debounce.300="applyFilters" placeholder="ابحث عن عنوان المشروع" class="border rounded px-3 py-2 w-full">
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
                        <th class="border px-3 py-2 text-right">عنوان المشروع</th>
                        <th class="border px-3 py-2 text-right">الفريق</th>
                        <th class="border px-3 py-2 text-right">القسم</th>
                        <th class="border px-3 py-2 text-right">المشرف</th>
                        <th class="border px-3 py-2 text-right">التقدم</th>
                        <th class="border px-3 py-2 text-right">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!records.length">
                        <tr>
                            <td colspan="6" class="border px-3 py-4 text-center text-gray-500">لا توجد مشاريع مطابقة.</td>
                        </tr>
                    </template>
                    <template x-for="project in records" :key="project.title">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="project.title"></td>
                            <td class="border px-3 py-2" x-text="project.team"></td>
                            <td class="border px-3 py-2" x-text="project.department"></td>
                            <td class="border px-3 py-2" x-text="project.supervisor"></td>
                            <td class="border px-3 py-2">
                                <div class="flex items-center gap-2">
                                    <span x-text="project.progress + '%'" class="text-sm"></span>
                                    <div class="flex-1 h-2 bg-gray-200 rounded">
                                        <div class="h-full bg-blue-500 rounded" :style="'width:' + project.progress + '%'"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="border px-3 py-2">
                                <span class="px-2 py-1 rounded" :class="statusBadge(project.status)" x-text="statusLabel(project.status)"></span>
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
        Alpine.data('gradProjects', () => ({
            dataset: [
                { title: 'نظام إدارة الطاقة في المباني الذكية', team: 'آمنة علي / ليلى يوسف', department: 'هندسة كهربائية', supervisor: 'د. أحمد بن عمران', progress: 80, status: 'on-track' },
                { title: 'تطبيق لتتبع التعلم باستخدام الذكاء الاصطناعي', team: 'محمد عمر / ياسين عمران', department: 'علوم حاسوب', supervisor: 'د. سارة محمود', progress: 60, status: 'needs-support' },
                { title: 'تصميم ذراع روبوتية للصيانة الصناعية', team: 'سارة محمود / علي حسن', department: 'هندسة ميكانيك', supervisor: 'م. خالد الهادي', progress: 45, status: 'delayed' }
            ],
            records: [],
            filters: { department: '', supervisor: '', search: '' },
            departments: ['هندسة كهربائية', 'علوم حاسوب', 'هندسة ميكانيك'],
            supervisors: ['د. أحمد بن عمران', 'د. سارة محمود', 'م. خالد الهادي'],

            applyFilters() {
                const term = this.filters.search.trim();
                this.records = this.dataset.filter(project => {
                    const matchesDept = !this.filters.department || project.department === this.filters.department;
                    const matchesSup = !this.filters.supervisor || project.supervisor === this.filters.supervisor;
                    const matchesTerm = !term || project.title.includes(term) || project.team.includes(term);
                    return matchesDept && matchesSup && matchesTerm;
                });
            },

            exportCsv() {
                if (!this.records.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['العنوان', 'الفريق', 'القسم', 'المشرف', 'التقدم', 'الحالة'];
                const rows = this.records.map(project => [project.title, project.team, project.department, project.supervisor, project.progress + '%', this.statusLabel(project.status)]);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'graduate-projects.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            },

            statusLabel(status) {
                if (status === 'on-track') return 'في المسار الصحيح';
                if (status === 'needs-support') return 'يحتاج إلى متابعة';
                return 'متأخر';
            },

            statusBadge(status) {
                if (status === 'on-track') return 'bg-green-100 text-green-700';
                if (status === 'needs-support') return 'bg-amber-100 text-amber-700';
                return 'bg-red-100 text-red-700';
            }
        }));
    });
</script>
@endsection
