<header class="bg-white border-b p-4 flex items-center justify-between">
  <div class="flex items-center gap-3">
    <button class="lg:hidden p-2 rounded bg-gray-100">☰</button>
    <form action="{{ route('students.index') }}" method="GET" class="flex items-center gap-2">
      <input type="text" name="q" placeholder="ابحث بالاسم أو رقم الطالب" class="border rounded p-2 w-64" />
      <button class="px-3 py-2 bg-blue-600 text-white rounded">بحث</button>
    </form>
  </div>

  <div class="flex items-center gap-3">
    <div class="text-sm text-gray-600">مرحبا، مدير النظام</div>
    <img src="/images/placeholder.png" alt="user" class="w-10 h-10 rounded-full">
  </div>
</header>
