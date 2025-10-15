<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;

class UiController extends Controller
{
    public function dashboard()
    {
        // بيانات تجريبية
        $stats = [
            'students' => 1240,
            'graduates' => 320,
            'teachers' => 82,
            'courses' => 148
        ];

        $latest = [
            ['student_number'=>'2025-001','name'=>'آمنة علي','department'=>'هندسة كهربائية','status'=>'active'],
            ['student_number'=>'2025-010','name'=>'محمد عمر','department'=>'علوم حاسوب','status'=>'active'],
            ['student_number'=>'2024-075','name'=>'سارة محمود','department'=>'هندسة ميكانيك','status'=>'graduated'],
        ];

        return view('ui.dashboard', compact('stats','latest'));
    }

    public function studentsIndex()
    {
        $departments = [
            ['id'=>1,'name'=>'هندسة كهربائية'],
            ['id'=>2,'name'=>'علوم حاسوب'],
            ['id'=>3,'name'=>'هندسة ميكانيك'],
        ];

        $students = [
            (object)['student_number'=>'2025-001','name'=>'آمنة علي','department'=>'هندسة كهربائية','status'=>'active','photo'=>null],
            (object)['student_number'=>'2025-010','name'=>'محمد عمر','department'=>'علوم حاسوب','status'=>'active','photo'=>null],
            (object)['student_number'=>'2024-075','name'=>'سارة محمود','department'=>'هندسة ميكانيك','status'=>'graduated','photo'=>null],
        ];

        return view('ui.students.index', compact('students','departments'));
    }

    public function studentsCreate()
    {
        $departments = [
            (object)['id'=>1,'name'=>'هندسة كهربائية'],
            (object)['id'=>2,'name'=>'علوم حاسوب'],
            (object)['id'=>3,'name'=>'هندسة ميكانيك'],
        ];
        return view('ui.students.create', compact('departments'));
    }

    public function studentsShow($id)
{
    // البيانات التجريبية، لاحظ نختار الطالب حسب الـ id
    $students = [
        1 => (object)[
            'student_number'=>'2025-001',
            'name'=>'آمنة علي',
            'nationality'=>'ليبيا',
            'gender'=>'أنثى',
            'department'=>'هندسة كهربائية',
            'enrollment_year'=>'2025',
            'semester'=>'الاول',
            'manual_number'=>'001',
            'national_id'=>'LY123456',
            'passport'=>'P1234567',
            'dob'=>'2005-01-15',
            'bank'=>'بنك ليبيا',
            'account_number'=>'1234567890',
            'mother_name'=>'فاطمة علي',
            'registry_book'=>'1234'
        ],
        2 => (object)[
            'student_number'=>'2025-010',
            'name'=>'محمد عمر',
            'nationality'=>'مصر',
            'gender'=>'ذكر',
            'department'=>'علوم حاسوب',
            'enrollment_year'=>'2025',
            'semester'=>'الاول',
            'manual_number'=>'010',
            'national_id'=>'EG987654',
            'passport'=>'P9876543',
            'dob'=>'2004-12-10',
            'bank'=>'بنك مصر',
            'account_number'=>'9876543210',
            'mother_name'=>'أمينة محمود',
            'registry_book'=>'5678'
        ]
        // ... تضيف باقي الطلاب
    ];

    $student = $students[$id] ?? null;

    if (!$student) {
        return redirect()->route('students.index')->with('error', 'الطالب غير موجود');
    }

    return view('ui.students.show', compact('student'));
}
public function studentsAllRecords()
{
    // بيانات تجريبية لجميع الطلاب
    $students = [
        (object)['student_number'=>'2025-001','name'=>'آمنة علي','department'=>'هندسة كهربائية','semester'=>'الأول','enrollment_year'=>'2025'],
        (object)['student_number'=>'2025-010','name'=>'محمد عمر','department'=>'علوم حاسوب','semester'=>'الأول','enrollment_year'=>'2025'],
        (object)['student_number'=>'2024-075','name'=>'سارة محمود','department'=>'هندسة ميكانيك','semester'=>'الثاني','enrollment_year'=>'2024'],
        (object)['student_number'=>'2023-050','name'=>'علي حسن','department'=>'هندسة كهربائية','semester'=>'الثالث','enrollment_year'=>'2023'],
        // تضيف باقي الطلاب هنا
    ];

    // أقسام متاحة للفلترة
    $departments = ['هندسة كهربائية','علوم حاسوب','هندسة ميكانيك'];

    // جلب القيم من البحث GET
    $q = request('q');
    $departmentFilter = request('department');

    // فلترة البيانات
    $students = array_filter($students, function($s) use ($q, $departmentFilter) {
        $match = true;
        if($q) {
            $match = str_contains($s->name, $q) || str_contains($s->student_number, $q);
        }
        if($departmentFilter) {
            $match = $match && $s->department == $departmentFilter;
        }
        return $match;
    });

    return view('ui.students.allRecords', compact('students','departments','q','departmentFilter'));
}

}
