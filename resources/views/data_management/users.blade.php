@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="usersManager()" x-init="init()">
    <div class="bg-white rounded-lg shadow p-6 space-y-4">
        <h1 class="text-2xl font-bold">إدارة المستخدمين</h1>
        <p class="text-gray-600">تحكم في حسابات المستخدمين وتعيين الأدوار بشكل سريع.</p>

        <div class="flex flex-wrap gap-3 items-end">
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">الدور</label>
                <select x-model="roleFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">كل الأدوار</option>
                    <template x-for="role in roles" :key="role">
                        <option :value="role" x-text="role"></option>
                    </template>
                </select>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-sm text-gray-600 mb-1">الحالة</label>
                <select x-model="statusFilter" @change="applyFilters" class="border rounded px-3 py-2 w-full">
                    <option value="">الكل</option>
                    <option value="active">نشط</option>
                    <option value="disabled">موقوف</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm text-gray-600 mb-1">بحث</label>
                <input type="text" x-model.trim="search" @input.debounce.300="applyFilters" placeholder="ابحث باسم المستخدم" class="border rounded px-3 py-2 w-full">
            </div>
            <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded" @click="addUser">➕ إضافة مستخدم</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-right">الاسم</th>
                        <th class="border px-3 py-2 text-right">البريد</th>
                        <th class="border px-3 py-2 text-right">الدور</th>
                        <th class="border px-3 py-2 text-right">الحالة</th>
                        <th class="border px-3 py-2 text-right">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="!records.length">
                        <tr>
                            <td colspan="5" class="border px-3 py-4 text-center text-gray-500">لا يوجد مستخدمون مطابقون للبحث.</td>
                        </tr>
                    </template>
                    <template x-for="user in records" :key="user.email">
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2" x-text="user.name"></td>
                            <td class="border px-3 py-2" x-text="user.email"></td>
                            <td class="border px-3 py-2">
                                <select class="border rounded px-2 py-1" x-model="user.role" @change="updateRole(user)">
                                    <template x-for="role in roles" :key="user.email + '-' + role">
                                        <option :value="role" x-text="role"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="border px-3 py-2">
                                <span class="px-2 py-1 rounded"
                                      :class="user.active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                      x-text="user.active ? 'نشط' : 'موقوف'"></span>
                            </td>
                            <td class="border px-3 py-2 space-x-2 rtl:space-x-reverse">
                                <button type="button" class="px-2 py-1 bg-gray-200 rounded" @click="toggleUser(user)">
                                    <span x-text="user.active ? 'إيقاف' : 'تفعيل'"></span>
                                </button>
                                <button type="button" class="px-2 py-1 bg-red-100 text-red-700 rounded" @click="removeUser(user)">حذف</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <template x-if="flashMessage">
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded" x-text="flashMessage"></div>
        </template>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('usersManager', () => ({
            dataset: [
                { name: 'مدير النظام', email: 'admin@example.com', role: 'مدير', active: true },
                { name: 'مسؤول التسجيل', email: 'registration@example.com', role: 'مسؤول تسجيل', active: true },
                { name: 'مراقب الامتحانات', email: 'exams@example.com', role: 'مراقب امتحانات', active: false }
            ],
            records: [],
            roles: ['مدير', 'مسؤول تسجيل', 'مراقب امتحانات'],
            roleFilter: '',
            statusFilter: '',
            search: '',
            flashMessage: '',

            init() {
                this.applyFilters();
            },

            applyFilters() {
                const term = this.search.trim();
                const role = this.roleFilter;
                const status = this.statusFilter;
                this.records = this.dataset.filter(user => {
                    const matchesTerm = !term || user.name.includes(term) || user.email.includes(term);
                    const matchesRole = !role || user.role === role;
                    const matchesStatus = !status || (status === 'active' ? user.active : !user.active);
                    return matchesTerm && matchesRole && matchesStatus;
                });
            },

            toggleUser(user) {
                user.active = !user.active;
                this.flash('تم تحديث حالة المستخدم.');
            },

            updateRole(user) {
                this.flash('تم تعديل الدور للمستخدم ' + user.name + '.');
            },

            removeUser(user) {
                if (!confirm('هل تريد حذف المستخدم؟')) {
                    return;
                }
                this.dataset = this.dataset.filter(item => item.email !== user.email);
                this.applyFilters();
                this.flash('تم حذف المستخدم.');
            },

            addUser() {
                const name = prompt('اسم المستخدم الجديد؟');
                const email = prompt('البريد الإلكتروني؟');
                if (!name || !email) {
                    return;
                }
                this.dataset.push({ name: name, email: email, role: 'مسؤول تسجيل', active: true });
                this.applyFilters();
                this.flash('تمت إضافة المستخدم الجديد بنجاح.');
            },

            flash(message) {
                this.flashMessage = message;
                setTimeout(() => {
                    this.flashMessage = '';
                }, 2500);
            }
        }));
    });
</script>
@endsection
