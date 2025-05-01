<script>
import { defineComponent, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';
export default defineComponent({
    components: {
        Head
    },
    setup() {
        const loading = ref(false);
        const showPassword = ref(false);
        const showConfirmPassword = ref(false);
        const form = useForm({name: '', email: '', password: '', password_confirmation: ''});
        const togglePassword = () => {showPassword.value = !showPassword.value;};
        const toggleConfirmPassword = () => {showConfirmPassword.value = !showConfirmPassword.value;};
        const submit = async () => {
            if (loading.value) return;
            if (form.password !== form.password_confirmation) {
                form.errors.password_confirmation = 'Password confirmation does not match';
                return;
            }
            loading.value = true;
            try {
                const response = await axios.post('/api/users', {
                    name: form.name,
                    email: form.email,
                    password: form.password,
                    password_confirmation: form.password_confirmation
                });
                await Swal.fire({
                    icon: 'success',
                    title: 'Registration Successful',
                    text: 'Your account has been created successfully!',
                    confirmButtonColor: '#2F8451'
                });
                window.location.href = '/';
            } catch (error) {
                if (error.response?.data?.errors?.message) {
                    const errors = error.response.data.errors.message;
                    for (const key in errors) {
                        form.errors[key] = errors[key][0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: 'Registration failed. Please try again.',
                        confirmButtonColor: '#2F8451'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: 'Registration failed. Please try again.',
                        confirmButtonColor: '#2F8451'
                    });
                }
            } finally {
                loading.value = false;
            }
        };
        return {form, submit, loading, showPassword, togglePassword, showConfirmPassword, toggleConfirmPassword};
    }
});
</script>
<template>
    <Head title="Login" />
    <div class="flex h-screen">
        <!-- Left Side -->
        <div class="hidden md:flex w-3/5 bg-[#2F8451] text-white items-center justify-center p-10 rounded-r-[50px] relative">
            <img src="/images/Ellipse2.svg" alt="Logo" class="absolute top-0 left-0 h-[260px]"/>
            <div class="text-center">
                <img src="/images/leafPos1.svg" alt="Logo" class="h-[293.72px] mx-auto mb-4"/>
            </div>
            <img src="/images/Ellipse3.svg" alt="Logo" class="absolute bottom-0 -right-[180px] h-[280px]"/>
        </div>
        <!-- Right Side -->
        <div class="w-full md:w-2/5 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <h2 class="text-[50px] font-bold text-center mb-2 text-[#2F8451]">Welcome</h2>
                <h2 class="text-[20px] font-medium text-center mb-2 text-black">Create Your Account</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label class="block font-medium mb-3">NAME</label>
                        <div class="relative">
                            <input
                                type="text"
                                v-model="form.name"
                                class="w-full p-3  border rounded-lg focus:ring focus:ring-green-300"
                                :class="{ 'border-red-500': form.errors.name }"
                                placeholder="LeafPost"
                                required
                            />
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#2F8451]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        <p v-if="form.errors.name" class="text-red-500 font-lighttext-sm mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block font-medium mb-3">EMAIL</label>
                        <div class="relative">
                            <input
                                type="email"
                                v-model="form.email"
                                class="w-full p-3  border rounded-lg focus:ring focus:ring-green-300"
                                :class="{ 'border-red-500': form.errors.email }"
                                placeholder="LeafPost@gmail.com"
                                required
                            />
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#2F8451]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </span>
                        </div>
                        <p v-if="form.errors.email" class="text-red-500 font-lighttext-sm mt-1">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block font-medium mb-3">PASSWORD</label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                class="w-full p-3 border rounded-lg focus:ring focus:ring-green-300"
                                :class="{ 'border-red-500': form.errors.password }"
                                placeholder="Enter your password"
                                required
                            />
                            <span
                                @click="togglePassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#2F8451] cursor-pointer"
                            >
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </span>
                        </div>
                        <p v-if="form.errors.password" class="text-red-500 font-lighttext-sm mt-1">{{ form.errors.password }}</p>
                    </div>
                    <div>
                        <label class="block font-medium mb-3">CONFIRM PASSWORD</label>
                        <div class="relative">
                            <input
                                :type="showConfirmPassword ? 'text' : 'password'"
                                v-model="form.password_confirmation"
                                class="w-full p-3 border rounded-lg focus:ring focus:ring-green-300"
                                :class="{ 'border-red-500': form.errors.password_confirmation }"
                                placeholder="Confirm your password"
                                required
                            />
                            <span
                                @click="toggleConfirmPassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#2F8451] cursor-pointer"
                            >
                                <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </span>
                        </div>
                        <p v-if="form.errors.password_confirmation" class="text-red-500 font-lighttext-sm mt-1">{{ form.errors.password_confirmation }}</p>
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-[#2F8451] font-semibold text-white p-3 rounded-lg hover:bg-[#1F6D3D] hover:opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="loading || form.processing"
                    >
                        <span v-if="loading || form.processing">Loading...</span>
                        <span v-else>Sign Up</span>
                    </button>
                </form>
                <p class="text-center font-light text-black mt-4">
                    Already have an account ?
                    <a href="/" class="text-green-600 font-medium hover:text-green-700">Login</a>
                </p>
            </div>
        </div>
    </div>
</template>
