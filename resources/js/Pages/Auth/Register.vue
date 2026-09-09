<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    company_name: '',
    company_code: '',
    uname: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="إنشاء حساب وتسجيل الشركة" />

    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8 dir-rtl">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <Link :href="route('welcome')" class="text-3xl font-bold text-[#714B67]">نظام حسابات مبسط</Link>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                تسجيل شركة وحساب جديد
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                أو
                <Link :href="route('login')" class="font-medium text-[#017E84] hover:text-teal-600">
                    تسجيل الدخول إذا كان لديك حساب
                </Link>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" @submit.prevent="submit">
                    <div v-if="form.errors.error" class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
                        <div class="flex">
                            <div class="ml-3">
                                <p class="text-sm text-red-700 text-right">{{ form.errors.error }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 text-right">
                            اسم الشركة أو المؤسسة
                        </label>
                        <div class="mt-1">
                            <input id="company_name" v-model="form.company_name" type="text" required autofocus
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.company_name" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.company_name }}</p>
                    </div>

                    <div>
                        <label for="company_code" class="block text-sm font-medium text-gray-700 text-right">
                            الرابط الفرعي للشركة (Subdomain)
                        </label>
                        <div class="mt-1 flex rounded-md shadow-sm" dir="ltr">
                            <input id="company_code" v-model="form.company_code" type="text" required
                                class="flex-1 appearance-none block w-full px-3 py-2 border border-gray-300 rounded-l-md placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" placeholder="mycompany" />
                            <span class="inline-flex items-center px-3 rounded-r-md border border-l-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                                .localhost
                            </span>
                        </div>
                        <p v-if="form.errors.company_code" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.company_code }}</p>
                    </div>

                    <div>
                        <label for="uname" class="block text-sm font-medium text-gray-700 text-right">
                            الاسم بالكامل
                        </label>
                        <div class="mt-1">
                            <input id="uname" v-model="form.uname" type="text" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.uname" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.uname }}</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 text-right">
                            البريد الإلكتروني
                        </label>
                        <div class="mt-1">
                            <input id="email" v-model="form.email" type="email" autocomplete="email" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.email" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 text-right">
                            رقم الهاتف
                        </label>
                        <div class="mt-1">
                            <input id="phone" v-model="form.phone" type="tel" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.phone" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.phone }}</p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 text-right">
                            كلمة المرور
                        </label>
                        <div class="mt-1">
                            <input id="password" v-model="form.password" type="password" autocomplete="new-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                        <p v-if="form.errors.password" class="mt-2 text-sm text-red-600 text-right">{{ form.errors.password }}</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 text-right">
                            تأكيد كلمة المرور
                        </label>
                        <div class="mt-1">
                            <input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#017E84] focus:border-[#017E84] sm:text-sm text-right" />
                        </div>
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#017E84] hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#017E84] disabled:opacity-50">
                            إنشاء الشركة وحسابي
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
