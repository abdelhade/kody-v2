<template>
  <AppLayout>
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">إدارة الأصناف (المنتجات)</h1>
        <p class="text-slate-500 text-sm mt-1">إضافة الأصناف، التسعير، تعدد الوحدات والباركودات</p>
      </div>
      <Link v-if="can('create_item')" href="/master-data/items/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        إضافة صنف جديد
      </Link>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="p-4 border-b border-slate-100 flex gap-4">
        <input type="text" placeholder="بحث بالاسم أو الباركود..." class="w-1/3 rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full text-right">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm">
            <tr>
              <th class="py-3 px-4 font-semibold">م</th>
              <th class="py-3 px-4 font-semibold">اسم الصنف</th>
              <th class="py-3 px-4 font-semibold">المجموعة</th>
              <th class="py-3 px-4 font-semibold">سعر البيع (أساسي)</th>
              <th class="py-3 px-4 font-semibold">الرصيد الكلي</th>
              <th class="py-3 px-4 font-semibold text-center">الإجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <tr v-for="item in items.data" :key="item.id" class="hover:bg-slate-50 transition-colors">
              <td class="py-3 px-4 text-slate-500 font-mono">{{ item.id }}</td>
              <td class="py-3 px-4 font-bold text-slate-800">{{ item.iname }}</td>
              <td class="py-3 px-4 text-slate-600">
                <span v-if="item.group" class="bg-blue-50 text-blue-700 px-2 py-1 rounded-md text-xs font-medium">{{ item.group.gname }}</span>
              </td>
              <td class="py-3 px-4 text-emerald-600 font-bold">{{ item.price1.toLocaleString() }}</td>
              <td class="py-3 px-4 font-medium" :class="item.itmqty <= 0 ? 'text-red-600' : 'text-slate-700'">
                {{ item.itmqty }}
              </td>
              <td class="py-3 px-4 flex justify-center gap-2">
                <Link v-if="can('update_item')" :href="`/master-data/items/${item.id}/edit`" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md transition" title="تعديل أو استعراض">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </Link>
                <button v-if="can('delete_item')" @click="deleteItem(item.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition" title="حذف">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </td>
            </tr>
            <tr v-if="items.data.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-500">لا يوجد أصناف مسجلة بعد.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ items: Object });
const page = usePage();

const can = (permission) => page.props.auth?.permissions?.includes(permission) || true;

const form = useForm({});

const deleteItem = (id) => {
  if (confirm('هل أنت متأكد من حذف هذا الصنف؟ ستُحذف وحداته وباركوداته أيضاً.')) {
    form.delete(`/master-data/items/${id}`);
  }
};
</script>
