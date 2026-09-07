<template>
  <AppLayout>
    <div class="space-y-6">
      <!-- Welcome Banner -->
      <div class="bg-gradient-to-l from-blue-700 via-indigo-700 to-blue-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
          <span class="inline-block px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-blue-100 mb-3 border border-white/10">
            مرحباً بك في نظام كودي ERP v2
          </span>
          <h2 class="text-2xl sm:text-3xl font-black mb-2">لوحة التحكم السريعة (SPA)</h2>
          <p class="text-blue-100 text-sm leading-relaxed">
            تم إعادة بناء النظام باستخدام Vue 3 و Inertia.js مع دعم Spatie Multi-Tenancy وعزل كامل للبيانات وأعلى سرعة استجابة.
          </p>

          <div class="flex flex-wrap gap-3 mt-6">
            <Link
              href="/pos"
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm rounded-xl transition-all shadow-md shadow-emerald-900/20"
            >
              <span>⚡ فتح شاشة الكاشير (POS)</span>
            </Link>
            <Link
              href="/items"
              class="inline-flex items-center gap-2 px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-sm rounded-xl transition-all border border-white/20 backdrop-blur-md"
            >
              <span>📦 إدارة الأصناف والمخازن</span>
            </Link>
          </div>
        </div>

        <!-- Decorative Circles -->
        <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 bg-blue-400/10 rounded-full blur-2xl pointer-events-none"></div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500">إجمالي الأصناف</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ counts.items || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
            📦
          </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500">العملاء النشطين</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ counts.clients || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
            👥
          </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500">الموردين المسجلين</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ counts.suppliers || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
            🚚
          </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-slate-500">إجمالي الفواتير</p>
            <p class="text-2xl font-black text-slate-800 mt-1">{{ counts.invoices || 0 }}</p>
          </div>
          <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
            🧾
          </div>
        </div>
      </div>

      <!-- Quick Recent Items & Invoices Overview -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Items -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base">آخر الأصناف المضافة</h3>
            <Link href="/items" class="text-xs font-bold text-blue-600 hover:text-blue-700">عرض الكل &larr;</Link>
          </div>
          <div class="divide-y divide-slate-100">
            <div
              v-for="item in recentItems"
              :key="item.id"
              class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition-colors"
            >
              <div>
                <p class="font-bold text-slate-800 text-sm">{{ item.iname }}</p>
                <p class="text-xs text-slate-400 mt-0.5">باركود: {{ item.barcode || '—' }}</p>
              </div>
              <div class="text-left">
                <span class="inline-block font-black text-sm text-slate-700">
                  {{ Number(item.price1 || 0).toLocaleString('ar-EG') }} ج.م
                </span>
                <span class="block text-xs text-slate-400">الكمية: {{ item.itmqty || 0 }}</span>
              </div>
            </div>
            <div v-if="!recentItems || recentItems.length === 0" class="p-8 text-center text-xs text-slate-400">
              لا توجد أصناف مسجلة حتى الآن
            </div>
          </div>
        </div>

        <!-- Recent Invoices -->
        <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-base">آخر الفواتير</h3>
            <span class="text-xs font-bold text-slate-400">الحركات الأخيرة</span>
          </div>
          <div class="divide-y divide-slate-100">
            <div
              v-for="inv in recentInvoices"
              :key="inv.id"
              class="p-4 flex items-center justify-between hover:bg-slate-50/50 transition-colors"
            >
              <div>
                <p class="font-bold text-slate-800 text-sm">فاتورة #{{ inv.id }}</p>
                <p class="text-xs text-slate-400 mt-0.5">{{ inv.pro_date || '—' }}</p>
              </div>
              <div class="text-left">
                <span class="font-black text-sm text-emerald-600 block">
                  {{ Number(inv.fat_net || 0).toLocaleString('ar-EG') }} ج.م
                </span>
                <span class="text-xs text-slate-400">
                  {{ inv.pro_tybe === 3 ? 'مبيعات' : (inv.pro_tybe === 4 ? 'مشتريات' : 'أخرى') }}
                </span>
              </div>
            </div>
            <div v-if="!recentInvoices || recentInvoices.length === 0" class="p-8 text-center text-xs text-slate-400">
              لا توجد فواتير حديثة
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
  counts: {
    type: Object,
    default: () => ({}),
  },
  recentItems: {
    type: Array,
    default: () => [],
  },
  recentInvoices: {
    type: Array,
    default: () => [],
  },
});
</script>
