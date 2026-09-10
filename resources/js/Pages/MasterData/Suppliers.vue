<template>
  <AppLayout>
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">إدارة الموردين</h1>
        <p class="text-slate-500 text-sm mt-1">إضافة وتعديل بيانات الموردين</p>
      </div>
      <button v-if="can('create_supplier')" @click="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        إضافة مورد جديد
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-right">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm">
            <tr>
              <th class="py-3 px-4 font-semibold">رقم الحساب</th>
              <th class="py-3 px-4 font-semibold">اسم المورد</th>
              <th class="py-3 px-4 font-semibold">رقم الجوال</th>
              <th class="py-3 px-4 font-semibold">الرصيد الافتتاحي</th>
              <th class="py-3 px-4 font-semibold">الرقم الضريبي</th>
              <th class="py-3 px-4 font-semibold text-center">الإجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <tr v-for="supplier in suppliers.data" :key="supplier.id" class="hover:bg-slate-50 transition-colors">
              <td class="py-3 px-4 text-slate-500 font-mono">{{ supplier.code }}</td>
              <td class="py-3 px-4 font-medium text-slate-800">{{ supplier.aname }}</td>
              <td class="py-3 px-4 text-slate-600">{{ supplier.phone || '-' }}</td>
              <td class="py-3 px-4 font-medium" :class="supplier.start_balance < 0 ? 'text-red-600' : 'text-emerald-600'">
                {{ Math.abs(supplier.start_balance).toLocaleString() }} 
                <span class="text-xs text-slate-400">{{ supplier.start_balance < 0 ? 'دائن' : 'مدين' }}</span>
              </td>
              <td class="py-3 px-4 text-slate-600">{{ supplier.info || '-' }}</td>
              <td class="py-3 px-4 flex justify-center gap-2">
                <button v-if="can('update_supplier')" @click="openModal(supplier)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md transition" title="تعديل">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <button v-if="can('delete_supplier')" @click="deleteSupplier(supplier.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition" title="حذف">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </td>
            </tr>
            <tr v-if="suppliers.data.length === 0">
              <td colspan="6" class="py-8 text-center text-slate-500">لا يوجد موردين مسجلين بعد.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h3 class="text-lg font-bold text-slate-800">{{ form.id ? 'تعديل بيانات المورد' : 'إضافة مورد جديد' }}</h3>
          <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <form @submit.prevent="submit" class="flex-1 overflow-y-auto p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">اسم المورد <span class="text-red-500">*</span></label>
            <input v-model="form.aname" type="text" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">رقم الجوال</label>
              <input v-model="form.phone" type="text" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" dir="ltr">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">الرقم الضريبي</label>
              <input v-model="form.info" type="text" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" dir="ltr">
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">العنوان</label>
            <input v-model="form.address" type="text" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
          </div>

          <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 mt-2">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">الرصيد الافتتاحي</label>
              <div class="flex">
                <input v-model="form.start_balance" type="number" step="0.01" class="w-full rounded-r-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm border-l-0" dir="ltr" :disabled="form.id">
                <select v-model="form.nature" class="rounded-l-lg border-slate-300 bg-slate-50 focus:border-blue-500 focus:ring-blue-500 text-sm px-2" :disabled="form.id">
                  <option :value="1">مدين</option>
                  <option :value="2">دائن</option>
                </select>
              </div>
            </div>
          </div>
        </form>

        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
          <button @click="closeModal()" type="button" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50">إلغاء</button>
          <button @click="submit" :disabled="form.processing" type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50">
            حفظ البيانات
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ suppliers: Object });
const page = usePage();

const can = (permission) => page.props.auth?.permissions?.includes(permission) || true;

const isModalOpen = ref(false);

const form = useForm({
  id: null,
  aname: '',
  phone: '',
  address: '',
  info: '',
  start_balance: 0,
  nature: 2, // Suppliers usually default to credit (2)
});

const openModal = (supplier = null) => {
  if (supplier) {
    form.id = supplier.id;
    form.aname = supplier.aname;
    form.phone = supplier.phone;
    form.address = supplier.address;
    form.info = supplier.info;
    form.start_balance = Math.abs(supplier.start_balance);
    form.nature = supplier.start_balance < 0 ? 2 : 1;
  } else {
    form.reset();
    form.id = null;
    form.nature = 2;
  }
  form.clearErrors();
  isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

const submit = () => {
  if (form.id) {
    form.put(`/master-data/suppliers/${form.id}`, { onSuccess: () => closeModal() });
  } else {
    form.post('/master-data/suppliers', { onSuccess: () => closeModal() });
  }
};

const deleteSupplier = (id) => {
  if (confirm('هل أنت متأكد من حذف هذا المورد؟')) form.delete(`/master-data/suppliers/${id}`);
};
</script>
