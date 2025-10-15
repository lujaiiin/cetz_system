@extends('layouts.app')

@section('content')
<div class="p-6 max-w-md mx-auto">
    <h1 class="text-2xl font-bold mb-4">إضافة مادة جديدة</h1>

    <form action="{{ route('subjects.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
        @csrf

        <div>
            <label class="block text-sm font-medium">اسم المادة</label>
            <input type="text" name="name" class="border rounded w-full px-3 py-2" required>
        </div>

        <div>
            <label class="block text-sm font-medium">القسم</label>
            <select name="department_id" class="border rounded w-full px-3 py-2">
                <option value="">اختيار القسم</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">حفظ</button>
            <a href="{{ route('subjects.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">رجوع</a>
        </div>
    </form>
</div>
@endsection
