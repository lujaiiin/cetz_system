@extends('layouts.app')

@section('content')
<div class="space-y-4">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-semibold">الطلاب</h2>
    <div class="flex items-center gap-2">
      <a href="{{ route('students.create') }}" class="px-4 py-2 bg-green-600 text-white rounded">إضافة طالب</a>
    </div>
  </div>

  <div class="bg-white p-4 rounded-lg shadow-sm">
    <div class="flex items-center gap-3 mb-4">
      <select name="department" class="border rounded p-2">
        <option value="">كل الأقسام</option>
        @foreach($departments as $d)
          <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
        @endforeach
      </select>
      <input type="text" placeholder="ابحث..." class="border rounded p-2 flex-1" />
      <button class="px-3 py-2 bg-blue-600 text-white rounded">فلتر</button>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead>
          <tr class="text-right">
            <th class="p-2 text-sm">الرقم</th>
            <th class="p-2 text-sm">الاسم</th>
            <th class="p-2 text-sm">القسم</th>
            <th class="p-2 text-sm">الحالة</th>
            <th class="p-2 text-sm">إجراءات</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-100">
          @foreach($students as $s)
          <tr class="text-right">
            <td class="p-2 text-sm">{{ $s->student_number }}</td>
            <td class="p-2 text-sm">{{ $s->name }}</td>
            <td class="p-2 text-sm">{{ $s->department }}</td>
            <td class="p-2 text-sm">
              <span class="px-2 py-1 rounded {{ $s->status=='graduated' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                {{ $s->status }}
              </span>
            </td>
            <td class="p-2 text-sm">
            <a href="{{ route('students.show', $loop->index + 1) }}" class="text-blue-600">عرض</a>

              <a href="#" class="px-2 py-1 bg-yellow-100 rounded">تعديل</a>
              <a href="#" class="px-2 py-1 bg-red-100 rounded">حذف</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- pagination placeholder -->
    <div class="mt-4 flex justify-center">
      <nav class="inline-flex -space-x-px rounded-md">
        <a class="px-3 py-2 bg-white border">1</a>
        <a class="px-3 py-2 bg-white border">2</a>
        <a class="px-3 py-2 bg-white border">3</a>
      </nav>
    </div>
  </div>
</div>
@endsection
