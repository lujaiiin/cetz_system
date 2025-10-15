@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="certificateGenerator()">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h1 class="text-2xl font-bold">إنشاء تعريف طالب</h1>
        <p class="text-gray-600">اختر الطالب وأدخل سبب إصدار التعريف ثم اطبع الوثيقة.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-600 mb-1">الطالب</label>
                <select x-model="selectedNumber" class="border rounded px-3 py-2 w-full">
                    <option value="">اختر الطالب</option>
                    <template x-for="student in students" :key="student.number">
                        <option :value="student.number" x-text="student.name + ' — ' + student.number"></option>
                    </template>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">تاريخ الإصدار</label>
                <input type="date" x-model="issueDate" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">سبب إصدار التعريف</label>
                <input type="text" x-model="purpose" placeholder="مثال: تقديم للمنحة الدراسية" class="border rounded px-3 py-2 w-full">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm text-gray-600 mb-1">ملاحظات إضافية (اختياري)</label>
                <textarea x-model="notes" rows="2" class="border rounded px-3 py-2 w-full"></textarea>
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded" @click="generateCertificate" :disabled="!selectedNumber || !issueDate || !purpose">
                معاينة الشهادة
            </button>
            <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="resetForm">إعادة الضبط</button>
        </div>

        <template x-if="alertMessage">
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded" x-text="alertMessage"></div>
        </template>
    </div>

    <div x-show="certificateHtml" class="bg-white rounded-lg shadow p-6 space-y-4" x-cloak>
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">المعاينة</h2>
            <div class="flex gap-2">
                <button type="button" class="px-4 py-2 bg-gray-200 rounded" @click="downloadHtml">⬇️ تنزيل نسخة HTML</button>
                <button type="button" class="px-4 py-2 bg-gray-100 border rounded" @click="printCertificate">🖨️ طباعة</button>
            </div>
        </div>
        <div class="border rounded-xl p-6 space-y-4" x-html="certificateHtml"></div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('certificateGenerator', () => ({
            students: [
                { number: '2025-001', name: 'آمنة علي', department: 'هندسة كهربائية', nationalId: 'LY123456', semester: 'الأول' },
                { number: '2025-010', name: 'محمد عمر', department: 'علوم حاسوب', nationalId: 'LY987654', semester: 'الأول' },
                { number: '2024-075', name: 'سارة محمود', department: 'هندسة ميكانيك', nationalId: 'LY246810', semester: 'الثاني' }
            ],
            institute: {
                name: 'كلية العلوم التطبيقية',
                address: 'طرابلس - ليبيا',
                phone: '+218 21 1234567'
            },
            selectedNumber: '',
            issueDate: new Date().toISOString().slice(0, 10),
            purpose: '',
            notes: '',
            alertMessage: '',
            certificateHtml: '',

            generateCertificate() {
                const student = this.students.find(item => item.number === this.selectedNumber);
                if (!student) {
                    this.alertMessage = 'يرجى اختيار الطالب أولاً.';
                    return;
                }

                const issueDateFormatted = new Date(this.issueDate).toLocaleDateString('ar-LY', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });

                const parts = [];
                parts.push('<div class="text-center space-y-2">');
                parts.push('<h1 class="text-2xl font-bold">' + this.escapeHtml(this.institute.name) + '</h1>');
                parts.push('<p class="text-sm text-gray-600">' + this.escapeHtml(this.institute.address) + ' — ' + this.escapeHtml(this.institute.phone) + '</p>');
                parts.push('<hr class="my-4">');
                parts.push('<h2 class="text-xl font-semibold">تعريف طالب</h2>');
                parts.push('</div>');

                parts.push('<div class="space-y-2 text-right leading-8">');
                parts.push('<p>تشهد إدارة ' + this.escapeHtml(this.institute.name) + ' بأن الطالب/ة <strong>' + this.escapeHtml(student.name) + '</strong> والرقم الجامعي <strong>' + this.escapeHtml(student.number) + '</strong> مسجل/ة بقسم <strong>' + this.escapeHtml(student.department) + '</strong> في الفصل <strong>' + this.escapeHtml(student.semester) + '</strong>.</p>');
                parts.push('<p>وقد تم إصدار هذا التعريف بتاريخ <strong>' + this.escapeHtml(issueDateFormatted) + '</strong> لغرض <strong>' + this.escapeHtml(this.purpose) + '</strong>.</p>');
                parts.push('<p>الرقم الوطني: <strong>' + this.escapeHtml(student.nationalId) + '</strong></p>');
                if (this.notes.trim().length) {
                    parts.push('<p class="mt-4">ملاحظات: ' + this.escapeHtml(this.notes) + '</p>');
                }
                parts.push('</div>');

                parts.push('<div class="mt-8 flex justify-between text-sm">');
                parts.push('<div><p>التوقيع:</p><p class="mt-6">___________________</p></div>');
                parts.push('<div><p>ختم الكلية</p><p class="mt-6">___________________</p></div>');
                parts.push('</div>');

                this.certificateHtml = parts.join('');
                this.alertMessage = 'تم تجهيز التعريف، يمكنك الطباعة الآن.';
            },

            resetForm() {
                this.selectedNumber = '';
                this.issueDate = new Date().toISOString().slice(0, 10);
                this.purpose = '';
                this.notes = '';
                this.certificateHtml = '';
                this.alertMessage = '';
            },

            printCertificate() {
                if (!this.certificateHtml) {
                    return;
                }
                const html = '<!doctype html><html lang="ar" dir="rtl"><head><title>تعريف طالب</title><meta charset="utf-8"><style>body{font-family:\'Tahoma\',\'Arial\',sans-serif;direction:rtl;padding:32px;line-height:1.8;}h1,h2{margin:0;}hr{border:none;border-top:1px solid #e5e7eb;}strong{font-weight:bold;}</style></head><body>' + this.certificateHtml + '</body></html>';
                const win = window.open('', '_blank', 'width=900,height=650');
                if (!win) {
                    alert('يرجى السماح بفتح النوافذ المنبثقة للطباعة.');
                    return;
                }
                win.document.write(html);
                win.document.close();
                win.focus();
                win.print();
                win.close();
            },

            downloadHtml() {
                if (!this.certificateHtml) {
                    return;
                }
                const html = '<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><title>تعريف طالب</title></head><body>' + this.certificateHtml + '</body></html>';
                const blob = new Blob([html], { type: 'text/html;charset=utf-8;' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'student-certificate.html';
                link.click();
                URL.revokeObjectURL(link.href);
            },

            escapeHtml(value) {
                return String(value).replace(/[&<>"']/g, function (char) {
                    const entities = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
                    return entities[char] || char;
                });
            }
        }));
    });
</script>
@endsection
