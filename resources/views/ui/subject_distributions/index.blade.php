@extends('layouts.app')

@section('content')
<div class="p-6">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">توزيع المواد</h1>
    <a href="{{ route('subject-distributions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">إضافة توزيع جديد</a>
  </div>

  <div class="bg-white rounded-lg shadow p-4">
    <table class="w-full text-sm">
      <thead class="bg-gray-100 border-b">
        <tr>
          <th class="p-2">القسم</th>
          <th class="p-2">اسم المادة</th>
          <th class="p-2">رمز المادة</th>
          <th class="p-2">أستاذ المادة</th>
          <th class="p-2">رقم الفصل</th>
          <th class="p-2 text-right">إجراءات</th>
        </tr>
      </thead>
      <tbody>
        @foreach($distributions as $dist)
        <tr class="border-b hover:bg-gray-50">
          <td class="p-2">{{ $dist->department }}</td>
          <td class="p-2">{{ $dist->subject_name }}</td>
          <td class="p-2">{{ $dist->subject_code }}</td>
          <td class="p-2">{{ $dist->teacher }}</td>
          <td class="p-2">{{ $dist->semester }}</td>
          <td class="p-2 text-right space-x-2 rtl:space-x-reverse">
            <a href="{{ route('subject-distributions.edit', $dist->id) }}" class="text-green-600">تعديل</a>
            <form action="{{ route('subject-distributions.destroy', $dist->id) }}" method="POST" class="inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="text-red-600" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-4 flex justify-end">
      <a href="{{ route('subject-distributions.print') }}" class="px-4 py-2 bg-gray-200 rounded">طباعة</a>
    </div>
  </div>
</div>
@endsection
