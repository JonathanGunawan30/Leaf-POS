<script setup>
import { ref } from 'vue';
import axios from 'axios';

const successMessage = ref(false);
const submittedEmail = ref('');
const email = ref('');
const error = ref('');
const isLoading = ref(false);
const handleSubmit = async () => {
    try {
        isLoading.value = true;
        error.value = '';
        await axios.post('http://127.0.0.1:8000/api/forgot-password', {
            email: email.value
        });
        submittedEmail.value = email.value;
        successMessage.value = true;
        email.value = '';
    } catch (err) {
        error.value = err.response?.data?.message || 'Something went wrong';
    } finally {
        isLoading.value = false;
    }
};
</script>
<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 forgot">
        <div class="max-w-md w-full space-y-8">
            <div class="bg-white py-8 px-6 shadow rounded-lg sm:px-10">
                <template v-if="!successMessage">
                    <div class="mb-8 text-center">
                        <h2 class="text-3xl font-bold text-[#2F8451]">
                            Forgot Password
                        </h2>
                    </div>
                    <form class="space-y-6" @submit.prevent="handleSubmit">
                        <div>
                            <label class="block font-medium mb-3">EMAIL ADDRESS</label>
                            <div class="relative">
                                <input 
                                    type="email" 
                                    v-model="email" 
                                    class="w-full p-3 border rounded-lg focus:ring focus:ring-green-300" 
                                    :class="{ 'border-red-500': error }"
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
                            <p v-if="error" class="text-red-500 font-light text-sm mt-1">{{ error }}</p>
                        </div>
                        <div>
                            <button 
                                type="submit" 
                                class="w-full bg-[#2F8451] font-semibold text-white p-3 rounded-lg hover:bg-[#1F6D3D] hover:opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="isLoading"
                            >
                                <span v-if="isLoading">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 inline-block text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                                <span v-else>Request a reset link</span>
                            </button>
                        </div>
                    </form>
                </template>
                <template v-else>
                    <div class="text-left">
                        <h3 class="font-bold text-[#2F8451] mb-4">Email Sent</h3>
                        <p class="text-black">We sent an email to {{ submittedEmail }} with a link to reset your password.</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>