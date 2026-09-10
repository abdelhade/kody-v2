<template>
  <AppLayout>
    <div class="mb-6 flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">مجموعات الأصناف</h1>
        <p class="text-slate-500 text-sm mt-1">إضافة وتعديل الأقسام الرئيسية والفرعية للمنتجات</p>
      </div>
      <button v-if="can('create_item_group')" @click="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        إضافة مجموعة
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-right">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 text-sm">
            <tr>
              <th class="py-3 px-4 font-semibold">م</th>
              <th class="py-3 px-4 font-semibold">اسم المجموعة</th>
              <th class="py-3 px-4 font-semibold">تتبع لـ (المجموعة الأب)</th>
              <th class="py-3 px-4 font-semibold">ملاحظات</th>
              <th class="py-3 px-4 font-semibold text-center">الإجراءات</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 text-sm">
            <tr v-for="group in groups.data" :key="group.id" class="hover:bg-slate-50 transition-colors">
              <td class="py-3 px-4 text-slate-500 font-mono">{{ group.id }}</td>
              <td class="py-3 px-4 font-bold text-slate-800">{{ group.gname }}</td>
              <td class="py-3 px-4 text-slate-600">
                <span v-if="group.parent_group" class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs">{{ group.parent_group.gname }}</span>
                <span v-else class="text-slate-400">-</span>
              </td>
              <td class="py-3 px-4 text-slate-500">{{ group.info || '-' }}</td>
              <td class="py-3 px-4 flex justify-center gap-2">
                <button v-if="can('update_item_group')" @click="openModal(group)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md transition" title="تعديل">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
                <button v-if="can('delete_item_group')" @click="deleteGroup(group.id)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-md transition" title="حذف">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </td>
            </tr>
            <tr v-if="groups.data.length === 0">
              <td colspan="5" class="py-8 text-center text-slate-500">لا يوجد مجموعات مسجلة بعد.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
          <h3 class="text-lg font-bold text-slate-800">{{ form.id ? 'تعديل المجموعة' : 'إضافة مجموعة جديدة' }}</h3>
          <button @click="closeModal()" class="text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
        
        <form @submit.prevent="submit" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">اسم المجموعة <span class="text-red-500">*</span></label>
            <input v-model="form.gname" type="text" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">المجموعة الأب (اختياري)</label>
            <select v-model="form.parent" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
              <option value="0">مجموعة رئيسية</option>
              <option v-for="pgroup in parentGroups" :key="pgroup.id" :value="pgroup.id" :disabled="form.id === pgroup.id">
                {{ pgroup.gname }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">ملاحظات</label>
            <input v-model="form.info" type="text" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
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

const props = defineProps({ groups: Object, parentGroups: Array });
const page = usePage();

const can = (permission) => page.props.auth?.permissions?.includes(permission) || true;

const isModalOpen = ref(false);

const form = useForm({
  id: null,
  gname: '',
  parent: 0,
  info: '',
});

const openModal = (group = null) => {
  if (group) {
    form.id = group.id;
    form.gname = group.gname;
    form.parent = group.parent;
    form.info = group.info;
  } else {
    form.reset();
    form.id = null;
  }
  form.clearErrors();
  isModalOpen.value = true;
};

const closeModal = () => { isModalOpen.value = false; };

const submit = () => {
  if (form.id) {
    form.put(`/master-data/item-groups/${form.id}`, { onSuccess: () => closeModal() });
  } else {
    form.post('/master-data/item-groups', { onSuccess: () => closeModal() });
  }
};

const deleteGroup = (id) => {
  if (confirm('هل أنت متأكد من حذف هذه المجموعة؟')) form.delete(`/master-data/item-groups/${id}`);
};
</script>
