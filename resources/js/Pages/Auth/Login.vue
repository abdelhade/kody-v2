<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    uname: '',
    password: '',
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="تسجيل الدخول" />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 dir-rtl">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <Link :href="route('welcome')" class="text-3xl font-bold text-[#714B67]">نظام حسابات مبسط</Link>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                تسجيل الدخول لحسابك
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                أو
                <Link :href="route('register')" class="font-medium text-[#017E84] hover:text-teal-600">
                    إنشاء شركة وحساب جديد
                </Link>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" @submit.prevent="submit">
                    <div>
                        <label for="uname" class="block text-sm font-medium text-gray-700 text-right">
                            اسم المستخدم
                        </label>
                        <div class="mt-1">
                            <input id="uname" v-model="form.uname" type="text" required autofocus
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.uname" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.uname }}</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 text-right">
                            كلمة المرور
                        </label>
                        <div class="mt-1">
                            <input id="password" v-model="form.password" type="password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#714B67] hover:bg-[#5a3a52] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#714B67] disabled:opacity-50">
                            تسجيل الدخول
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.dir-rtl {
    direction: rtl;
}
</style>
