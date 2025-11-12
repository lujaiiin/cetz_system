@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="materialsAssign()" x-init="init()">
    <div class="bg-white rounded-lg shadow p-4 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
        <div>
            <label class="block text-sm text-gray-600 mb-1">الطالب</label>
            <select x-model="selectedStudent" class="border rounded px-3 py-2 w-full">
                <template x-for="s in students" :key="s.number">
                    <option :value="s.number" x-text="s.name + ' — ' + s.number"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">القسم</label>
            <select x-model="selectedDepartment" class="border rounded px-3 py-2 w-full">
                <template x-for="d in departments" :key="d"><option :value="d" x-text="d"></option></template>
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">الفصل</label>
            <select x-model="selectedSemester" class="border rounded px-3 py-2 w-full">
                <option value="ربيع 2025">ربيع 2025</option>
                <option value="خريف 2024">خريف 2024</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button class="px-4 py-2 bg-gray-200 rounded" @click="printSheet">🖨️ طباعة المواد</button>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded" @click="printResult">🖨️ طباعة النتيجة</button>
            <button class="px-4 py-2 bg-green-600 text-white rounded" @click="exportCsv">⬇️ تصدير CSV</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">المواد المتاحة</h2>
                <input type="text" class="border rounded px-3 py-1" placeholder="بحث في المواد" x-model.trim="searchAvailable">
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">رقم</th>
                            <th class="border px-2 py-1">رمز</th>
                            <th class="border px-2 py-1">اسم المادة</th>
                            <th class="border px-2 py-1">وحدات</th>
                            <th class="border px-2 py-1">ساعات</th>
                            <th class="border px-2 py-1">إضافة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="m in filteredAvailable()" :key="m.number + m.code">
                            <tr class="hover:bg-gray-50">
                                <td class="border px-2 py-1" x-text="m.number"></td>
                                <td class="border px-2 py-1" x-text="m.code"></td>
                                <td class="border px-2 py-1" x-text="m.name"></td>
                                <td class="border px-2 py-1" x-text="m.units"></td>
                                <td class="border px-2 py-1" x-text="m.hours"></td>
                                <td class="border px-2 py-1"><button class="px-2 py-1 bg-blue-600 text-white rounded" @click="assign(m)">إضافة</button></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">مواد الطالب</h2>
                <div class="text-sm text-gray-600">إجمالي الوحدات: <span class="font-semibold" x-text="totals.units"></span> • الساعات: <span class="font-semibold" x-text="totals.hours"></span></div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-2 py-1">رقم</th>
                            <th class="border px-2 py-1">رمز</th>
                            <th class="border px-2 py-1">اسم المادة</th>
                            <th class="border px-2 py-1">وحدات</th>
                            
                            <th class="border px-2 py-1">إعادة؟</th>
                            <th class="border px-2 py-1">المجموعة</th>
                            <th class="border px-2 py-1"> 100؟</th>
                            
                            
                            
                            <th class="border px-2 py-1">ملاحظة</th>
                            <th class="border px-2 py-1">إزالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="m in assignedList()" :key="m.number + m.code">
                            <tr class="hover:bg-gray-50">
                                <td class="border px-2 py-1" x-text="m.number"></td>
                                <td class="border px-2 py-1" x-text="m.code"></td>
                                <td class="border px-2 py-1" x-text="m.name"></td>
                                <td class="border px-2 py-1" x-text="m.units"></td>
                                
                                <td class="border px-2 py-1 text-center">
                                    <input type="checkbox" x-model="m.is_repeat" @change="recalcTotals" title="وضع علامة إعادة">
                                </td>
                                <td class="border px-2 py-1">
                                    <select x-model="m.group" class="border rounded px-2 py-1">
                                        <template x-for="g in [1,2,3,4,5,6]" :key="'g'+g">
                                            <option :value="g" x-text="g"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="border px-2 py-1 text-center">
                                    <input type="checkbox" x-model="m.on100" title="إدخال على 100%">
                                </td>
                                
                                <td class="border px-2 py-1">
                                    <input type="text" class="border rounded px-2 py-1 w-full" x-model="m.note">
                                </td>
                                <td class="border px-2 py-1"><button class="px-2 py-1 bg-red-100 text-red-700 rounded" @click="unassign(m)">حذف</button></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('materialsAssign', () => ({
        students: [
            { number: '1091252001', name: 'آمنة علي', department: 'القسم العام' },
            { number: '1091252002', name: 'محمد عمر', department: 'القسم العام' },
            { number: '1091252003', name: 'سارة محمود', department: 'القسم العام' }
        ],
        departments: ['القسم العام','علوم حاسوب','هندسة كهربائية','هندسة ميكانيك'],
        catalog: {
            'القسم العام': {
                'ربيع 2025': [
                    { number: 350, code: 'CE1', name: 'رسم 1', units: 2, hours: 3 },
                    { number: 352, code: 'CE2', name: 'رسم 2', units: 2, hours: 3 },
                    { number: 363, code: 'CE411', name: 'مشروع', units: 2, hours: 4 }
                ],
                'خريف 2024': [
                    { number: 395, code: 'EE393', name: 'تطبيقات حاسوب 4', units: 3, hours: 3 }
                ]
            }
        },

        selectedStudent: '1091252001',
        selectedDepartment: 'القسم العام',
        selectedSemester: 'ربيع 2025',
        searchAvailable: '',
        assignments: {},
        totals: { units: 0, hours: 0, termAvg: 0, passedUnits: 0, warnings: 0 },

        init() { this.recalcTotals(); },
        key() { return this.selectedStudent + '|' + this.selectedSemester; },
        available() {
            const list = (this.catalog[this.selectedDepartment] && this.catalog[this.selectedDepartment][this.selectedSemester]) || [];
            const current = new Set(this.assignedList().map(x => x.code));
            return list.filter(x => !current.has(x.code));
        },
        filteredAvailable() {
            const term = this.searchAvailable.trim();
            return this.available().filter(m => !term || [String(m.number), m.code, m.name].some(v => (v||'').includes(term)));
        },
        assignedList() { return this.assignments[this.key()] || [] },
        assign(m) { const list = this.assignedList().slice(); list.push({ ...m, is_repeat:false, group:1, on100:false, grade: '', attempt:1, note:'' }); this.assignments[this.key()] = list; this.recalcGpa(); },
        unassign(m) { const list = this.assignedList().filter(x => !(x.code === m.code && x.number === m.number)); this.assignments[this.key()] = list; this.recalcGpa(); },
        recalcTotals() { const list = this.assignedList(); this.totals.units = list.reduce((s,x)=>s+(+x.units||0),0); this.totals.hours = list.reduce((s,x)=>s+(+x.hours||0),0); },
        recalcGpa() {
            this.recalcTotals();
            const list = this.assignedList();
            const sumUnits = list.reduce((s,x)=>s+(+x.units||0),0);
            const sumWeighted = list.reduce((s,x)=> s + ((+x.grade||0) * (+x.units||0)), 0);
            this.totals.termAvg = sumUnits ? (sumWeighted / sumUnits).toFixed(2) : 0;
            this.totals.passedUnits = list.reduce((s,x)=> s + (((+x.grade||0) >= 50) ? (+x.units||0) : 0), 0);
            this.totals.warnings = list.filter(x => (+x.grade||0) < 50).length;
        },
        exportCsv() { const list=this.assignedList(); if(!list.length){alert('لا توجد مواد مسجلة للطالب.');return;} const header=['رقم','رمز','اسم المادة','الوحدات','الساعات','إعادة','المجموعة','على 100','الدرجة','الدور','الوحدة×الدرجة','ملاحظة']; const rows=list.map(m=>[m.number,m.code,m.name,m.units,m.hours,m.is_repeat?'نعم':'لا',m.group??'',m.on100?'نعم':'لا',m.grade??'',m.attempt??1,(((+m.units||0)*(+m.grade||0)).toFixed(2)),m.note??'']); const csv=[header].concat(rows).map(r=>r.map(v=>'"'+v+'"').join(',')).join('\n'); const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'}); const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download=this.selectedStudent+'-'+this.selectedSemester+'-materials.csv'; a.click(); URL.revokeObjectURL(a.href); },
        printResult() {
            const list = this.assignedList();
            const htmlRows = list.map((m,idx)=> '<tr>'+
                '<td>'+ (m.code||'') +'</td>'+
                '<td>'+ (m.name||'') +'</td>'+
                '<td>'+ (m.units||'') +'</td>'+
                '<td>'+ (m.grade||'') +'</td>'+
                '<td>'+ (m.attempt||1) +'</td>'+
                '<td>'+ (((+m.units||0) * (+m.grade||0)).toFixed(2)) +'</td>'+
                '<td>'+ (m.note||'') +'</td>'+
            '</tr>').join('');
            const meta = {
                student: this.students.find(s=>s.number===this.selectedStudent)?.name || '',
                number: this.selectedStudent,
                dept: this.selectedDepartment,
                sem: this.selectedSemester,
                avg: this.totals.termAvg,
                passed: this.totals.passedUnits,
                warns: this.totals.warnings
            };
            const html = '<!doctype html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><title>كشف نتيجة</title>'+
            '<style>body{font-family:\'Tahoma\',\'Arial\',sans-serif;direction:rtl;padding:24px;}' +
            'table{width:100%;border-collapse:collapse;margin-top:8px;}th,td{border:1px solid #999;padding:6px;text-align:center;font-size:13px;}thead{background:#f3f4f6;}' +
            '.meta{margin-bottom:10px;font-size:13px;display:flex;gap:16px;flex-wrap:wrap}.footer{margin-top:10px;font-size:13px}' +
            '</style></head><body>'+
            '<div class="meta"><div>رقم القيد: '+meta.number+'</div><div>الطالب: '+meta.student+'</div><div>القسم: '+meta.dept+'</div><div>الفصل: '+meta.sem+'</div><div>'+new Date().toLocaleDateString('ar-LY')+'</div></div>'+
            '<table><thead><tr><th>رمز المادة</th><th>اسم المادة</th><th>عدد الوحدات</th><th>الدرجة</th><th>الدور</th><th>الوحدة × الدرجة</th><th>ملاحظة</th></tr></thead><tbody>'+htmlRows+'</tbody></table>'+
            '<div class="footer"><div>المعدل الفصلي % '+meta.avg+'</div><div>الوحدات المنجزة '+meta.passed+'</div><div>الإنذارات '+meta.warns+'</div></div>'+
            '</body></html>';
            const w = window.open('', '_blank', 'width=900,height=650');
            w.document.write(html); w.document.close(); w.focus(); w.print();
        },
        printSheet(){ window.print(); }
    }));
});
</script>
@endsection
