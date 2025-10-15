<aside class="w-64 bg-gray-800 text-white min-h-screen">
  <div class="p-4 text-xl font-bold border-b border-gray-700">نظام الكلية</div>

  <nav class="mt-4 space-y-2">
    <!-- لوحة التحكم -->
    <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-gray-700 rounded">📊 لوحة التحكم</a>

    <!-- الأساسيات -->
    <div x-data="{ open: true }" class="px-4">
      <button @click="open = !open" class="flex items-center justify-between w-full py-2 hover:bg-gray-700 rounded">
        <span>🧩 الأساسيات</span>
        <span x-text="open ? '▲' : '▼'"></span>
      </button>

      <div x-show="open" class="mt-2 ml-2 space-y-1" x-transition>
        <a href="{{ route('students.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🎓 الطلاب</a>
        <a href="{{ route('departments.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🏫 الأقسام</a>
        <a href="{{ route('subjects.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">📚 المواد</a>
        <a href="{{ route('classrooms.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🏛️ الفصول الدراسية</a>
        <a href="{{ route('subject-distributions.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🗂️ توزيع المواد</a>
      </div>
    </div>

    <!-- التسجيل والقبول -->
    <div x-data="{ open2: false }" class="px-4">
      <button @click="open2 = !open2" class="flex items-center justify-between w-full py-2 hover:bg-gray-700 rounded">
        <span>🧾 التسجيل والقبول</span>
        <span x-text="open2 ? '▲' : '▼'"></span>
      </button>

      <div x-show="open2" class="mt-2 ml-2 space-y-1" x-transition>
        <a href="{{ route('students.create') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">تسجيل الطلبة</a>
        <a href="{{ route('materials.download') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">تنزيل المواد</a>
        <a href="{{ route('registration.courses') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">إدارة تسجيل المواد</a>
        <a href="{{ route('registration.enrollment-stop') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">إيقاف القيد</a>
        <a href="{{ route('registration.attendance-form') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">نموذج الحضور والغياب</a>
        <a href="{{ route('registration.student-certificate') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">تعريف الطالب</a>
        <a href="{{ route('registration.bank-report') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">كشف الطلبة حسب المصارف</a>
        <a href="{{ route('registration.department-report') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">كشف الطلبة حسب القسم</a>
      </div>
    </div>

    <!-- الدراسة والامتحانات -->
    <div x-data="{ open3: false }" class="px-4">
      <button @click="open3 = !open3" class="flex items-center justify-between w-full py-2 hover:bg-gray-700 rounded">
        <span>📘 الدراسة والامتحانات</span>
        <span x-text="open3 ? '▲' : '▼'"></span>
      </button>

      <div x-show="open3" class="mt-2 ml-2 space-y-1" x-transition>
        <a href="{{ route('results.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">📘 رصد وتعديل النتائج</a>
        <a href="{{ route('deprived.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🚫 إدخال وتعديل المحرومين</a>
        <a href="{{ route('grades.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">📄 كشف درجات الفصل</a>
        <a href="{{ route('final-results.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🏁 النتائج النهائية</a>
        <a href="{{ route('analysis.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">📊 كشف تحليل للنتائج</a>
        <a href="{{ route('projects.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🎓 كشف طلبة مشروع التخرج</a>
        <a href="{{ route('second-round.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🔁 كشف طلبة الدور الثاني</a>
        <a href="{{ route('deprived-list.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🚷 كشف طلبة المحرومين</a>
        <a href="{{ route('grade-sheet.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">📋 نموذج كشف الدرجات</a>
        <a href="{{ route('statistics.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">📈 الكشف الإحصائي</a>
        <a href="{{ route('warnings.index') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">⚠️ الإنذارات</a>
      </div>
    </div>

    <!-- الخريجين -->
    <div x-data="{ open4: false }" class="px-4">
      <button @click="open4 = !open4" class="flex items-center justify-between w-full py-2 hover:bg-gray-700 rounded">
        <span>🎓 الخريجين</span>
        <span x-text="open4 ? '▲' : '▼'"></span>
      </button>

      <div x-show="open4" class="mt-2 ml-2 space-y-1" x-transition>
        <a href="{{ route('graduates.transcript') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">📑 كشف الدرجات</a>
        <a href="{{ route('graduates.list') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🎖️ كشف الخريجين</a>
      </div>
    </div>

    <!-- إدارة البيانات -->
    <div x-data="{ open5: false }" class="px-4">
      <button @click="open5 = !open5" class="flex items-center justify-between w-full py-2 hover:bg-gray-700 rounded">
        <span>⚙️ إدارة البيانات</span>
        <span x-text="open5 ? '▲' : '▼'"></span>
      </button>

      <div x-show="open5" class="mt-2 ml-2 space-y-1" x-transition>
        <a href="{{ route('data.backup') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">💾 حفظ قاعدة البيانات</a>
        <a href="{{ route('data.restore') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">♻️ استرجاع قاعدة البيانات</a>
        <a href="{{ route('data.reset') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🔄 إعادة ضبط مبدئي</a>
        <a href="{{ route('data.change-password') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🔐 تغيير كلمة المرور</a>
        <a href="{{ route('data.users') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">👥 إدارة المستخدمين</a>
        <a href="{{ route('data.institute-number') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🏷️ رقم المعهد</a>
        <a href="{{ route('data.institute-info') }}" class="block px-3 py-1 hover:bg-gray-700 rounded">🏫 معهد / كلية</a>
      </div>
    </div>

    <!-- الاعتماد -->
    <div class="px-4">
      <a href="{{ route('accreditation.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded">✅ الاعتماد</a>
    </div>

  </nav>
</aside>

<!-- تفعيل القوائم المنسدلة -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
