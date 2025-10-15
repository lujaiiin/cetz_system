@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="graduatesTranscript()" x-init="selectStudent(selectedNumber)">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h1 class="text-2xl font-bold">كشف الدرجات</h1>
        <p class="text-gray-600">اختر الطالب الخريج لعرض كشف درجاته مع إمكانية الطباعة أو التصدير.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">الطالب</label>
                <select x-model="selectedNumber" @change="selectStudent(selectedNumber)" class="border rounded px-3 py-2 w-full">
                    <template x-for="student in students" :key="student.number">
                        <option :value="student.number" x-text="student.name + ' — ' + student.number"></option>
                    </template>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="printTranscript">🖨️ طباعة</button>
                <button type="button" class="px-4 py-2 bg-green-600 text-white rounded" @click="exportCsv">⬇️ تصدير CSV</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-50 border rounded-lg p-4">
            <div>
                <div class="text-sm text-gray-500">القسم</div>
                <div class="text-lg font-semibold" x-text="currentStudent.department"></div>
            </div>
            <div>
                <div class="text-sm text-gray-500">الدفعة</div>
                <div class="text-lg font-semibold" x-text="currentStudent.year"></div>
            </div>
            <div>
                <div class="text-sm text-gray-500">إجمالي الساعات</div>
                <div class="text-lg font-semibold" x-text="totals.credits"></div>
            </div>
            <div>
                <div class="text-sm text-gray-500">المعدل التراكمي</div>
                <div class="text-lg font-semibold" x-text="totals.gpa.toFixed(2)"></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border" id="transcript-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">المقرر</th>
                        <th class="border px-3 py-2 text-right">عدد الوحدات</th>
                        <th class="border px-3 py-2 text-right">التقدير</th>
                        <th class="border px-3 py-2 text-right">النقاط</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="course in currentStudent.courses" :key="course.name">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="course.name"></td>
                            <td class="border px-3 py-2" x-text="course.credits"></td>
                            <td class="border px-3 py-2" x-text="course.grade"></td>
                            <td class="border px-3 py-2" x-text="course.points.toFixed(2)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('graduatesTranscript', () => ({
            students: [
                {
                    number: '2024-001',
                    name: 'آمنة علي',
                    department: 'هندسة كهربائية',
                    year: 2024,
                    courses: [
                        { name: 'تحليل دوائر كهربائية', credits: 3, grade: 'A', points: 12 },
                        { name: 'نظم رقمية', credits: 3, grade: 'B+', points: 10.5 },
                        { name: 'إدارة مشاريع', credits: 2, grade: 'A', points: 8 }
                    ]
                },
                {
                    number: '2024-010',
                    name: 'محمد عمر',
                    department: 'علوم حاسوب',
                    year: 2024,
                    courses: [
                        { name: 'هياكل البيانات', credits: 3, grade: 'A-', points: 11.1 },
                        { name: 'قواعد البيانات', credits: 3, grade: 'B', points: 9 },
                        { name: 'ذكاء اصطناعي', credits: 3, grade: 'A', points: 12 }
                    ]
                }
            ],
            selectedNumber: '2024-001',
            currentStudent: { number: '', name: '', department: '', year: '', courses: [] },
            totals: { credits: 0, gpa: 0 },

            selectStudent(number) {
                const found = this.students.find(student => student.number === number);
                if (found) {
                    this.currentStudent = JSON.parse(JSON.stringify(found));
                    this.recalculateTotals();
                }
            },

            recalculateTotals() {
                const totalCredits = this.currentStudent.courses.reduce((sum, course) => sum + course.credits, 0);
                const totalPoints = this.currentStudent.courses.reduce((sum, course) => sum + course.points, 0);
                this.totals.credits = totalCredits;
                this.totals.gpa = totalCredits ? totalPoints / totalCredits : 0;
            },

            exportCsv() {
                if (!this.currentStudent.courses.length) {
                    alert('لا توجد بيانات لتصديرها.');
                    return;
                }
                const header = ['المقرر', 'عدد الوحدات', 'التقدير', 'النقاط'];
                const rows = this.currentStudent.courses.map(course => [course.name, course.credits, course.grade, course.points.toFixed(2)]);
                const csv = [header].concat(rows).map(columns => columns.map(value => '"' + value + '"').join(',')).join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'transcript-' + this.currentStudent.number + '.csv';
                link.click();
                URL.revokeObjectURL(link.href);
            },

            printTranscript() {
                window.print();
            }
        }));
    });
</script>
@endsection
