<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialDownloadController extends Controller
{
    // بيانات تجريبية بشكل يقارب شاشة المواد في الصور
    private $materials = [
        [
            'number' => 350,
            'code' => 'EE393',
            'name' => 'تطبيقات حاسوب 4',
            'units' => 3,
            'hours' => 3,
            'depends_on' => null,
            'alternative_for' => null,
            'user_name' => 'admin',
        ],
        [
            'number' => 366,
            'code' => 'CE411',
            'name' => 'مشروع',
            'units' => 2,
            'hours' => 4,
            'depends_on' => 'CE4111',
            'alternative_for' => null,
            'user_name' => 'reda',
        ],
        [
            'number' => 395,
            'code' => 'EE393',
            'name' => 'تطبيقات حاسوب 3',
            'units' => 3,
            'hours' => 3,
            'depends_on' => null,
            'alternative_for' => null,
            'user_name' => 'reda',
        ],
    ];

    // عرض صفحة تنزيل المواد
    public function index(Request $request)
    {
        $query = $request->input('query', '');

        // بحث: على رقم/رمز/اسم/ملاحظات/مستخدم
        $filtered = array_filter($this->materials, function($item) use ($query) {
            if ($query === '') return true;
            return stripos((string)$item['number'], $query) !== false
                || stripos($item['code'] ?? '', $query) !== false
                || stripos($item['name'] ?? '', $query) !== false
                || stripos($item['depends_on'] ?? '', $query) !== false
                || stripos($item['alternative_for'] ?? '', $query) !== false
                || stripos($item['user_name'] ?? '', $query) !== false;
        });

        return view('materials.download', ['materials' => $filtered, 'query' => $query]);
    }

    // طباعة البيانات
    public function print(Request $request)
    {
        $query = $request->input('query', '');

        $filtered = array_filter($this->materials, function($item) use ($query) {
            if ($query === '') return true;
            return stripos((string)$item['number'], $query) !== false
                || stripos($item['code'] ?? '', $query) !== false
                || stripos($item['name'] ?? '', $query) !== false
                || stripos($item['depends_on'] ?? '', $query) !== false
                || stripos($item['alternative_for'] ?? '', $query) !== false
                || stripos($item['user_name'] ?? '', $query) !== false;
        });

        return view('materials.download-print', ['materials' => $filtered]);
    }
}
