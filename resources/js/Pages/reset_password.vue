<script setup>
import {ref, onMounted} from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const form = ref({
    password: '',
    password_confirmation: '',
    token: '',
});
const errors = ref({});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const loading = ref(false);

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    form.value.token = params.get('token') || '';
    form.value.email = params.get('email') || '';
});

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const toggleConfirmPasswordVisibility = () => {
    showConfirmPassword.value = !showConfirmPassword.value;
};

const submit = async () => {
    if (loading.value) return;
    loading.value = true;
    errors.value = {}; // Reset error

    try {
        const response = await axios.post('/api/reset-password', form.value);
        await Swal.fire({
            icon: 'success',
            title: 'Password Reset Successful!',
            text: 'You can now login with your new password.',
            confirmButtonColor: '#2F8451',
        });
        window.location.href = '/';
    } catch (error) {
        const serverErrors = error.response?.data?.errors?.message;
        console.log(serverErrors);
        if (serverErrors) {
            errors.value = serverErrors;
        } else {
            await Swal.fire({
                icon: 'error',
                title: 'Reset Failed',
                text: 'Something went wrong.',
                confirmButtonColor: '#2F8451',
            });
        }
    } finally {
        loading.value = false;
    }
};


</script>

<template>
    <div class="flex items-center justify-center min-h-screen bg-white px-4">
        <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
            <h2 class="text-3xl font-bold text-[#2F8451] text-center mb-2">Reset Password</h2>
            <p class="text-gray-500 text-center mb-8">Enter your new password</p>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="relative">
                    <input
                        v-model="form.password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="New Password"
                        class="w-full border border-gray-300 p-3 pr-12 rounded-lg focus:ring-2 focus:ring-green-300"
                        required
                    />

                    <span
                        class="absolute right-4 top-1/2 transform -translate-y-1/2  text-[#2F8451] cursor-pointer"
                        @click="togglePasswordVisibility"
                    >
    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
         fill="currentColor">
      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
      <path fill-rule="evenodd"
            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
            clip-rule="evenodd"/>
    </svg>
    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd"
            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
            clip-rule="evenodd"/>
      <path
          d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
    </svg>


  </span>

                </div>
                <div v-if="errors.password" class="text-red-500 text-sm">
                    {{ errors.password[0] }}
                </div>

                <div class="relative">
                    <input
                        v-model="form.password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        placeholder="Confirm Password"
                        class="w-full border border-gray-300 p-3 pr-12 rounded-lg focus:ring-2 focus:ring-green-300"
                        required
                    />

                    <span
                        class="absolute right-4 top-1/2 transform -translate-y-1/2  text-[#2F8451] cursor-pointer"
                        @click="toggleConfirmPasswordVisibility"
                    >
    <svg v-if="!showConfirmPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
         fill="currentColor">
      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
      <path fill-rule="evenodd"
            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
            clip-rule="evenodd"/>

    </svg>
    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd"
            d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z"
            clip-rule="evenodd"/>
      <path
          d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"/>
    </svg>
  </span>
                </div>
                <div v-if="errors.password_confirmation" class="text-red-500 text-sm">
                    {{ errors.password_confirmation[0] }}
                </div>


                <button
                    type="submit"
                    class="w-full bg-[#2F8451] text-white font-bold py-3 rounded-lg hover:bg-[#246d42] disabled:opacity-50"
                    :disabled="loading"
                >
                    <span v-if="loading">Resetting...</span>
                    <span v-else>Reset Password</span>
                </button>
            </form>
        </div>
    </div>
</template>

