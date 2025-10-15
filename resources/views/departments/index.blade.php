@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">الأقسام</h1>
        <a href="{{ route('departments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">إضافة قسم</a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="p-2">#</th>
                    <th class="p-2">اسم القسم</th>
                    <th class="p-2"> الشعبة</th>
                    <th class="p-2">المستخدم </th>
                    <th class="p-2 text-right">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($departments as $dept)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2">{{ $loop->iteration }}</td>
                    <td class="p-2">{{ $dept->name }}</td>
                    <td class="p-2 text-right space-x-2 rtl:space-x-reverse">
                        <a href="{{ route('departments.edit', $dept) }}" class="text-green-600">تعديل</a>
                        <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
