@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="dashboardPage({
    stats: @json($stats ?? []),
    latest: @json($latest ?? [])
})" x-init="init()">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <template x-for="card in cards" :key="card.key">
            <div class="p-4 bg-white rounded-lg shadow border border-gray-100 flex flex-col gap-2">
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span x-text="card.label"></span>
                    <span class="text-xs" :class="card.trendClass" x-text="card.trend"></span>
                </div>
                <div class="text-3xl font-semibold" x-text="card.value"></div>
                <div class="w-full h-2 bg-gray-100 rounded">
                    <div class="h-full rounded bg-blue-500" :style="'width:' + card.progress + '%'"
                         :class="card.progressClass"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Department Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">أداء الأقسام</h2>
                <span class="text-sm text-gray-500">آخر تحديث: {{ now()->format('Y-m-d') }}</span>
            </div>
            <template x-for="item in departmentPerformance" :key="item.name">
                <div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span x-text="item.name"></span>
                        <span class="font-medium" x-text="item.rate + '%' "></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded mt-1">
                        <div class="h-full rounded" :class="item.color" :style="'width:' + item.rate + '%'"> </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">مهام سريعة</h2>
                <button class="text-sm text-blue-600" @click="shuffleTasks">تحديث</button>
            </div>
            <template x-if="!tasks.length">
                <div class="text-sm text-gray-500">لا توجد مهام حالياً.</div>
            </template>
            <ul class="space-y-3">
                <template x-for="task in tasks" :key="task.title">
                    <li class="flex items-start gap-3 border rounded-lg p-3">
                        <div class="mt-1 w-2 h-2 rounded-full" :class="task.color"></div>
                        <div class="flex-1">
                            <div class="flex justify-between text-sm">
                                <span class="font-medium" x-text="task.title"></span>
                                <span class="text-gray-400" x-text="task.due"></span>
                            </div>
                            <p class="text-xs text-gray-500" x-text="task.note"></p>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
    </div>

    <!-- Latest Students -->
    <div class="bg-white p-6 rounded-lg shadow space-y-4">
        <div class="flex flex-wrap gap-3 items-center justify-between">
            <div>
                <h2 class="font-semibold">آخر الطلاب المسجلين</h2>
                <p class="text-sm text-gray-500">عرض سريع لحالة التسجيل الأخيرة.</p>
            </div>
            <div class="flex gap-2 items-center">
                <input type="text" x-model.trim="studentsSearch" @input.debounce.300="applyFilter"
                       placeholder="بحث بالاسم أو الرقم" class="border rounded px-3 py-2 w-60">
                <select x-model="statusFilter" @change="applyFilter" class="border rounded px-3 py-2">
                    <option value="all">جميع الحالات</option>
                    <option value="active">قيد الدراسة</option>
                    <option value="graduated">متخرج</option>
                </select>
            </div>
        </div>

        <template x-if="!filteredLatest.length">
            <div class="border border-dashed rounded-lg p-6 text-center text-gray-500">
                لا توجد نتائج مطابقة للبحث.
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="student in filteredLatest" :key="student.student_number">
                <div class="border rounded-lg p-4 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-semibold" x-text="student.name"></div>
                            <div class="text-xs text-gray-500" x-text="student.student_number + ' • ' + student.department"></div>
                        </div>
                        <span class="px-3 py-1 text-xs rounded"
                              :class="student.status === 'graduated' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'"
                              x-text="statusLabel(student.status)"></span>
                    </div>
                    <div class="text-xs text-gray-500 border-t pt-2">
                        تاريخ التسجيل: <span x-text="student.enrolled_at"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardPage', (config) => ({
            rawStats: config.stats || {},
            latest: config.latest || [],
            cards: [],
            tasks: [],
            departmentPerformance: [],
            studentsSearch: '',
            statusFilter: 'all',
            filteredLatest: [],

            init() {
                const stats = {
                    students: Number(this.rawStats.students || 0),
                    graduates: Number(this.rawStats.graduates || 0),
                    teachers: Number(this.rawStats.teachers || 0),
                    courses: Number(this.rawStats.courses || 0)
                };

                this.cards = [
                    this.cardConfig('students', 'عدد الطلاب', stats.students, '+4.1% هذا الشهر', 'text-blue-600', stats.students ? 80 : 0),
                    this.cardConfig('graduates', 'الخريجون', stats.graduates, '+2.0% عن العام الماضي', 'text-green-600', stats.graduates ? 65 : 0),
                    this.cardConfig('teachers', 'الأساتذة', stats.teachers, 'مستقر', 'text-amber-600', stats.teachers ? 55 : 0),
                    this.cardConfig('courses', 'المقررات', stats.courses, '+1 مقرر جديد', 'text-purple-600', stats.courses ? 70 : 0)
                ];

                this.departmentPerformance = [
                    { name: 'هندسة كهربائية', rate: 88, color: 'bg-green-500' },
                    { name: 'علوم حاسوب', rate: 82, color: 'bg-blue-500' },
                    { name: 'هندسة ميكانيك', rate: 75, color: 'bg-amber-500' }
                ];

                this.tasks = [
                    { title: 'مراجعة خطة الاعتماد', due: 'هذا الأسبوع', note: 'التأكد من استكمال وثائق البنية التحتية', color: 'bg-blue-500' },
                    { title: 'اجتماع اللجنة الأكاديمية', due: '15 فبراير', note: 'عرض مؤشرات الأداء للفصل الحالي', color: 'bg-green-500' },
                    { title: 'تحديث بيانات الخريجين', due: '22 فبراير', note: 'مطابقة الأرقام مع السجلات المالية', color: 'bg-amber-500' }
                ];

                this.filteredLatest = this.latest.map(item => ({
                    ...item,
                    enrolled_at: item.enrolled_at || '2025-01-10'
                }));
            },

            cardConfig(key, label, value, trend, trendClass, progress) {
                return {
                    key,
                    label,
                    value: value.toLocaleString('ar-LY'),
                    trend,
                    trendClass,
                    progress: progress || 0,
                    progressClass: key === 'graduates' ? 'bg-green-500' : (key === 'teachers' ? 'bg-amber-500' : 'bg-blue-500')
                };
            },

            shuffleTasks() {
                this.tasks = this.tasks.slice().reverse();
            },

            applyFilter() {
                const term = this.studentsSearch.trim();
                this.filteredLatest = this.latest.filter(item => {
                    const matchesTerm = !term || item.name.includes(term) || item.student_number.includes(term);
                    const matchesStatus = this.statusFilter === 'all' || item.status === this.statusFilter;
                    return matchesTerm && matchesStatus;
                }).map(item => ({
                    ...item,
                    enrolled_at: item.enrolled_at || '2025-01-10'
                }));
            },

            statusLabel(status) {
                return status === 'graduated' ? 'متخرج' : 'قيد الدراسة';
            }
        }));
    });
</script>
@endsection
