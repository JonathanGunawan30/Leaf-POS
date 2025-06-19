<template>
    <Head title="Login" />
    <div class="flex h-screen animate-fade-in">
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
            <div class="w-full max-w-md animate-slide-in-up delay-500">
                <h2 class="text-[50px] font-bold text-center mb-6 text-[#2F8451] animate-slide-in-left delay-300">Welcome</h2>
                <h2 class="text-[20px] font-medium text-center mb-6 text-black">Login To Your Account</h2>
                <form @submit.prevent="submit" class="space-y-4">
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
                                class="w-full p-3 pr-10 border rounded-lg focus:ring focus:ring-green-300"
                                :class="{ 'border-red-500': form.errors.password }"
                                placeholder="LeafPostPassword"
                                required
                            />
                            <button
                                type="button"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-[#2F8451]"
                                @click="togglePassword"
                            >
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                    <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <div class="flex-1">
                            <p v-if="form.errors.password" class="text-red-500 font-light">{{ form.errors.password }}</p>
                        </div>
                        <a href="/forgot-password" class="font-light text-black ml-4">Forgot Your Password ?</a>
                    </div>
                    <button
                        type="submit"
                        class="w-full bg-[#2F8451] font-semibold text-white p-3 rounded-lg hover:bg-[#1F6D3D] hover:scale-105 transition-all duration-200 hover:opacity-80 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="loading || form.processing"
                    >
                    <span v-if="loading || form.processing">Loading...</span>
                        <span v-else>Login</span>
                    </button>
                </form>
                <p class="text-center font-light text-black mt-4">
                    Don't have an account ?
                    <a href="/register" class="text-green-600 font-medium hover:text-green-700">Sign Up</a>
                </p>
            </div>
        </div>
    </div>
</template>
<script>
import { defineComponent, onMounted, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';

export default defineComponent({
    setup() {
        const loading = ref(false);
        const showPassword = ref(false);

        const form = useForm({
            email: '',
            password: '',
            remember: false
        });

        const togglePassword = () => {
            showPassword.value = !showPassword.value;
        };

        onMounted(() => {
            const script = document.createElement('script');
            script.src = `https://www.google.com/recaptcha/api.js?render=${import.meta.env.VITE_RECAPTCHA_SITE_KEY}`;
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        });

        const waitForRecaptcha = () => {
            return new Promise((resolve) => {
                const check = () => {
                    if (window.grecaptcha && window.grecaptcha.execute) {
                        resolve();
                    } else {
                        setTimeout(check, 100);
                    }
                };
                check();
            });
        };

        const submit = async () => {
            if (loading.value) return;

            if (!form.email || !form.password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields',
                    confirmButtonColor: '#2F8451'
                });
                return;
            }

            await waitForRecaptcha();

            const recaptchaToken = await grecaptcha.execute(import.meta.env.VITE_RECAPTCHA_SITE_KEY, { action: 'login' });

            if (!recaptchaToken) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please verify you are not a robot.',
                    confirmButtonColor: '#2F8451'
                });
                return;
            }

            loading.value = true;

            try {
                const response = await axios.post('/api/users/login', {
                    email: form.email,
                    password: form.password,
                    remember: form.remember,
                    recaptcha_token: recaptchaToken
                });

                const token = response.data.data.token;
                localStorage.setItem('X-API-TOKEN', token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

                Swal.fire({
                    icon: 'success',
                    title: 'Login Successful',
                    text: 'Redirecting to dashboard...',
                    timer: 1500,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);


            } catch (error) {
                let errorMessage = 'Authentication failed. Please try again.';

                try {
                    if (error.response?.data) {
                        const responseData = error.response.data;

                        if (responseData.errors && responseData.errors.message) {
                            const messageErrors = responseData.errors.message;

                            const errorFields = Object.keys(messageErrors);
                            if (errorFields.length > 0) {
                                const firstField = errorFields[0];
                                const fieldErrors = messageErrors[firstField];

                                if (Array.isArray(fieldErrors) && fieldErrors.length > 0) {
                                    errorMessage = fieldErrors[0];
                                } else if (typeof fieldErrors === 'string') {
                                    errorMessage = fieldErrors;
                                }
                            }
                        }
                        else if (responseData.errors) {
                            const errors = responseData.errors;
                            const errorKeys = Object.keys(errors);

                            if (errorKeys.length > 0) {
                                const firstError = errors[errorKeys[0]];
                                if (Array.isArray(firstError) && firstError.length > 0) {
                                    errorMessage = firstError[0];
                                } else if (typeof firstError === 'string') {
                                    errorMessage = firstError;
                                }
                            }
                        }
                        else if (responseData.message && typeof responseData.message === 'string') {
                            errorMessage = responseData.message;
                        }
                        else if (typeof responseData === 'string') {
                            errorMessage = responseData;
                        }
                    }
                    else if (error.message) {
                        errorMessage = error.message;
                    }

                } catch (parseError) {
                    errorMessage = 'An unexpected error occurred during login.';
                }


                Swal.fire({
                    icon: 'error',
                    title: 'Login Failed',
                    text: errorMessage,
                    confirmButtonColor: '#2F8451'
                });

            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            submit,
            loading,
            showPassword,
            togglePassword
        };
    }
});
</script>
<style>
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes slide-in-left {
    from { opacity: 0; transform: translateX(-30px); }
    to { opacity: 1; transform: translateX(0); }
}
@keyframes slide-in-up {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out forwards;
}
.animate-slide-in-left {
    animation: slide-in-left 0.8s ease-out forwards;
}
.animate-slide-in-up {
    animation: slide-in-up 0.8s ease-out forwards;
}
.delay-300 {
    animation-delay: 0.3s;
}
.delay-500 {
    animation-delay: 0.5s;
}
</style>

