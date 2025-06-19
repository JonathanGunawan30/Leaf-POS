<template>
    <div
        v-if="show"
        class="fixed top-0 right-0 h-full w-full bg-black bg-opacity-40 flex justify-end items-stretch z-50"
    >
        <div class="bg-white w-[750px] h-full flex flex-col">
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-lg text-gray-900">EDIT COURIER</h3>
                <button @click="closePopup" class="text-gray-600 hover:text-gray-900 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-auto px-6 py-4 space-y-4 text-gray-800">
                <template v-if="loading">
                    <div class="space-y-4 animate-pulse">
                        <div>
                            <div class="h-4 w-24 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                        <div>
                            <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                        <div>
                            <div class="h-4 w-32 bg-gray-200 rounded mb-2"></div>
                            <div class="h-10 w-full bg-gray-200 rounded"></div>
                        </div>
                    </div>
                </template>

                <template v-else>
                    <div>
                        <label class="block font-medium text-sm mb-1">Courier Name</label>
                        <input
                            v-model="courierForm.name"
                            type="text"
                            placeholder="Enter courier name"
                            class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                        />
                    </div>

                    <div>
                        <label class="block font-medium text-sm mb-1">Courier Phone Number</label>
                        <input
                            v-model="courierForm.phone"
                            type="text"
                            min="0"
                            placeholder="Enter courier phone number"
                            class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                        />
                    </div>
                    <div>
                        <label class="block font-medium text-sm mb-1">Courier Status</label>
                        <select
                            v-model="courierForm.status"
                            class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-green-600"
                        >
                            <option value="" disabled>Select Status</option>
                            <option value="available" >Available</option>
                            <option value="unavailable" >Unavailable</option>
                        </select>
                    </div>
                </template>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex justify-center gap-4">
                <button
                    @click="closePopup"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100"
                >
                    Cancel
                </button>
                <button
                    @click="updateCourier"
                    class="px-6 py-2 bg-lp-green text-white rounded-md hover:bg-green-700 transition"
                    :disabled="isSubmitting || loading"
                >
                    {{ isSubmitting ? 'Updating...' : 'Update Courier' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const loading = ref(false);
const isSubmitting = ref(false);
const props = defineProps({
    show: Boolean,
    courierId: Number
});

const emit = defineEmits(['close', 'updated']);

const courierForm = ref({
    name: '',
    phone: '',
    status: ''
})

const fetchCouriers = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if(!token) return;

        const response = await axios.get(`/api/couriers/${props.courierId}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        courierForm.value = response.data.data;
    } catch (error) {
        console.error('Error fetching courier:', error);

    } finally {
        loading.value = false;
    }
}

const updateCourier = async () => {
    if (isSubmitting.value || loading.value) {
        return;
    }

    isSubmitting.value = true;

    try {
        const token = localStorage.getItem('X-API-TOKEN');
        if(!token) {
            isSubmitting.value = false;
            return;
        }

        await axios.patch(`/api/couriers/${props.courierId}`, courierForm.value, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        Swal.fire({
            title: 'Success',
            text: 'Courier updated successfully',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
            buttonsStyling: false
        });
        emit('updated');
        closePopup();
    } catch (error) {
        console.error('Error updating data:', error);

        let errorMessage = 'Failed to update courier. Please check the data or re-login.'
        let useHtml = false;

        if(error.response?.data?.errors?.message){
            const messages = error.response.data.errors.message;

            const formattedErrors = Object.entries(messages)
                .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                .join('<br>');

            if(formattedErrors) {
                errorMessage = formattedErrors;
                useHtml = true;
            }
        }

        Swal.fire({
            title: 'Failed!',
            html: useHtml ? errorMessage : undefined,
            text: useHtml ? undefined : errorMessage,
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
            buttonsStyling: false
        });
    } finally {
        isSubmitting.value = false;
    }
}

const closePopup = () => {
    emit('close');
}

watch(() => props.courierId, async (newId) => {
    if (newId && props.show) {
        await fetchCouriers(newId);
    }
}, { immediate: true });

watch(() => props.show, async (newShow) => {
    if (newShow && props.courierId) {
        await fetchCouriers(props.courierId);
    }
});

onMounted(()=> {
    fetchCouriers()
});

</script>
