<template>
  <div class="min-h-screen bg-slate-50 flex flex-col font-sans" dir="rtl">
    <!-- Top Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-xl shadow-md shadow-blue-500/20">
            K
          </div>
          <div>
            <h1 class="text-lg font-bold text-slate-800 tracking-tight leading-tight">كودي ERP</h1>
            <div class="flex items-center gap-1.5 text-xs text-slate-500">
              <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
              <span>المشترك: {{ tenantName || 'الفرع الرئيسي' }}</span>
            </div>
          </div>
        </div>

        <nav class="hidden md:flex items-center gap-1">
          <Link
            :href="'/'"
            :class="[
              'px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors',
              $page.url === '/' ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100'
            ]"
          >
            لوحة التحكم
          </Link>
          <Link
            :href="'/items'"
            :class="[
              'px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors',
              $page.url.startsWith('/items') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100'
            ]"
          >
            الأصناف والمخزون
          </Link>
          <Link
            :href="'/accounts'"
            :class="[
              'px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors',
              $page.url.startsWith('/accounts') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100'
            ]"
          >
            شجرة الحسابات
          </Link>
          <Link
            :href="'/pos'"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-bold transition-all shadow-sm',
              $page.url.startsWith('/pos') ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'
            ]"
          >
            ⚡ كاشير POS
          </Link>
          <Link
            :href="'/shifts'"
            :class="[
              'px-3.5 py-2 rounded-lg text-sm font-semibold transition-colors',
              $page.url.startsWith('/shifts') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100'
            ]"
          >
            الورديات
          </Link>
          <div class="relative group">
            <button class="px-3.5 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100 flex items-center gap-1">
              الموارد البشرية 
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div class="absolute right-0 w-48 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
              <Link href="/hr/employees" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">الموظفين</Link>
              <Link href="/hr/attendance" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">الحضور والانصراف</Link>
              <Link href="/hr/payroll" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">مسير الرواتب</Link>
            </div>
          </div>
          <div class="relative group">
            <button class="px-3.5 py-2 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100 flex items-center gap-1">
              التقارير المالية 
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div class="absolute right-0 w-48 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
              <Link href="/reports/account-statement" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">كشف الحساب</Link>
              <Link href="/reports/trial-balance" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">ميزان المراجعة</Link>
              <Link href="/reports/profit-loss" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">الأرباح والخسائر</Link>
            </div>
          </div>
        </nav>

        <div class="flex items-center gap-3">
          <div class="text-sm font-semibold text-slate-700 hidden md:block">
             أهلاً بك، {{ $page.props.auth?.user?.uname || 'مستخدم' }}
          </div>
          <Link href="/logout" method="post" as="button" class="text-xs bg-red-50 border border-red-200 text-red-600 px-3 py-1.5 rounded-md font-medium hover:bg-red-100 transition-colors">
            تسجيل الخروج
          </Link>
        </div>
      </div>
    </header>

    <!-- Main Page Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
      نظام كودي ERP & POS &copy; {{ new Date().getFullYear() }} — مدعوم بـ Laravel 12 & Vue 3 SPA
    </footer>
  </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const tenantName = computed(() => page.props.currentTenant?.name);
</script>
