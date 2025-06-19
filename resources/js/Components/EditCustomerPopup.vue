<template>
    <div v-show="show" class="fixed inset-0 overflow-hidden z-50 transition-opacity duration-300">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black bg-opacity-50 transition-opacity" @click="closePopup"></div>

        <!-- Popup Panel -->
        <div class="absolute inset-y-0 right-0 max-w-[750px] w-full bg-white shadow-xl transform transition-transform duration-300 ease-in-out"
             :class="{ 'translate-x-0': show, 'translate-x-full': !show }">

            <!-- Header -->
            <div class="flex justify-between items-center p-5 border-b border-gray-300">
                <span class="text-black font-bold">EDIT CUSTOMER</span>
                <button @click="closePopup" class="text-black hover:text-gray-700">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.0006 8.82208L14.1255 4.69727L15.304 5.87577L11.1791 10.0006L15.304 14.1253L14.1255 15.3038L10.0006 11.1791L5.87584 15.3038L4.69733 14.1253L8.82213 10.0006L4.69733 5.87577L5.87584 4.69727L10.0006 8.82208Z" fill="black"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-5 overflow-y-auto h-[calc(100%-120px)]">
                <form @submit.prevent="updateCustomer">

                    <!-- Customer Name -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Name <span class="text-red-500">*</span></p>
                        <input
                            v-model="customerForm.name"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                            required
                        />
                    </div>

                    <!-- Customer Name -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Company Name</p>
                        <input
                            v-model="customerForm.company_name"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                        />
                    </div>


                    <!-- Email and Phone -->
                    <div class="flex gap-4 mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-1">Email <span class="text-red-500">*</span></p>
                            <input
                                v-model="customerForm.email"
                                type="email"
                                min="0"
                                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                                required
                            />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-1">Phone<span class="text-red-500">*</span></p>
                            <input
                                v-model="customerForm.phone"
                                min="0"
                                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                                required
                            />
                        </div>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-1">Bank Account <span class="text-red-500">*</span></p>
                            <input
                                v-model="customerForm.bank_account"
                                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                                required
                            />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-1">Bank Name<span class="text-red-500">*</span></p>
                            <input
                                v-model="customerForm.bank_name"
                                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                    </div>

                    <!-- Country and Province -->
                    <div class="flex gap-4 mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-1">Country <span class="text-red-500">*</span></p>
                            <input
                                v-model="customerForm.country"
                                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                                required
                            />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-1">Province<span class="text-red-500">*</span></p>
                            <input
                                v-model="customerForm.province"
                                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                    </div>

                    <!-- City and postal code -->
                    <div class="flex gap-4 mb-4">
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-1">City <span class="text-red-500">*</span></p>
                            <input
                                v-model="customerForm.city"
                                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                                required
                            />
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium mb-1">Postal Code<span class="text-red-500">*</span></p>
                            <input
                                v-model="customerForm.postal_code"
                                type="number"
                                min="0"
                                class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                            />
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Address <span class="text-red-500">*</span></p>
                        <input
                            v-model="customerForm.address"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                            required
                        />

                    </div>

                    <div class="mb-4">
                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                            <p class="text-[#000000] text-sm font-medium leading-6">
                                Location Map
                            </p>
                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <iframe
                            width="100%"
                            height="250"
                            frameborder="0"
                            style="border:0; border-radius: 10px"
                            referrerpolicy="no-referrer-when-downgrade"
                            :src="`https://www.google.com/maps/embed/v1/place?q=${encodeURIComponent(customerForm.address || 'Tangerang, Indonesia')}&key=${googleMapsKey}`"
                            allowfullscreen>
                        </iframe>
                        <p class="text-[#666666] text-xs leading-4 mt-1">
                            {{ getFullAddress }}
                        </p>
                    </div>

                    <!-- NPWP -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">NPWP </p>
                        <input
                            v-model="customerForm.npwp_number"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                        />
                    </div>

                    <!-- NIB -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">NIB </p>
                        <input
                            v-model="customerForm.nib_number"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                        />
                    </div>

                    <!-- SIUP -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">SIUP </p>
                        <input
                            v-model="customerForm.siup_number"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                        />
                    </div>

                    <!-- Business Type -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Business Type </p>
                        <input
                            v-model="customerForm.business_type"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg"
                        />
                    </div>

                    <!-- notes -->
                    <div class="mb-4">
                        <p class="text-sm font-medium mb-1">Notes </p>
                        <textarea
                            v-model="customerForm.note"
                            type="text"
                            class="w-full h-[45px] px-4 py-2 border border-gray-300 rounded-lg resize-none"
                            style="min-height: 100px; max-height: 200px; overflow-y: auto;"
                        />
                    </div>


                </form>
            </div>

            <!-- Footer -->
            <div class="absolute bottom-0 left-0 right-0 p-5 border-t border-gray-300 bg-white flex justify-center">
                <div class="flex gap-4 w-full max-w-md">
                    <button
                        @click="closePopup"
                        class="flex-1 py-2 px-4 border border-gray-300 rounded-lg text-black hover:bg-gray-50"
                    >
                        Cancel
                    </button>
                    <button
                        @click="updateCustomer"
                        class="flex-1 py-2 px-4 bg-green-700 text-white rounded-lg hover:bg-green-800"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
const googleMapsKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY;

const props = defineProps({
    show: Boolean,
    customerId: Number
});

const emit = defineEmits(['close', 'updated']);

const customerForm = ref({
    name: '',
    company_name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    province: '',
    postal_code: '',
    country: '',
    bank_name: '',
    bank_account: '',
    npwp_number: '',
    nib_number: '',
    siup_number: '',
    business_type: '',
    note: ''
})

const getFullAddress = computed(() => {
    const addressParts = [
        customerForm.value.address,
        customerForm.value.city,
        customerForm.value.province,
        customerForm.value.country
    ].filter(Boolean)

    return addressParts.length > 0 ? addressParts.join(', ') : 'Tangerang, Indonesia'
})


const loading = ref(false);

watch(() => props.customerId, async (newId) => {
    if (newId && props.show) {
        await fetchCustomer(newId);
    }
}, { immediate: true });

watch(() => props.show, async (newShow) => {
    if (newShow && props.customerId) {
        await fetchCustomer(props.customerId);
    }
});

const fetchCustomer = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if(!token) return;

        const response = await axios.get('/api/customers/' + props.customerId, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        const customer = response.data.data;

        customerForm.value = {
            name: customer.name,
            company_name: customer.company_name,
            email: customer.email,
            phone: customer.phone,
            address: customer.address,
            city: customer.city,
            country: customer.country,
            province: customer.province,
            postal_code: customer.postal_code,
            bank_name: customer.bank_name,
            bank_account: customer.bank_account,
            npwp_number: customer.npwp_number,
            nib_number: customer.nib_number,
            siup_number: customer.siup_number,
            business_type: customer.business_type,
            note: customer.note
        }
    } catch (error) {
        console.log('Error fetching customer data: ', error);

    } finally{
        loading.value = false;
    }

}

const updateCustomer = async () => {
    try {
        loading.value = true;
        const token = localStorage.getItem('X-API-TOKEN');
        if(!token) return;

        const formData = new FormData();
        formData.append('name', customerForm.value.name);
        formData.append('company_name', customerForm.value.company_name);
        formData.append('email', customerForm.value.email);
        formData.append('phone', customerForm.value.phone);
        formData.append('address', customerForm.value.address);
        formData.append('city', customerForm.value.city);
        formData.append('country', customerForm.value.country);
        formData.append('province', customerForm.value.province);
        formData.append('postal_code', customerForm.value.postal_code);
        formData.append('bank_name', customerForm.value.bank_name);
        formData.append('bank_account', customerForm.value.bank_account);
        formData.append('npwp_number', customerForm.value.npwp_number);
        formData.append('nib_number', customerForm.value.nib_number);
        formData.append('siup_number', customerForm.value.siup_number);
        formData.append('business_type', customerForm.value.business_type);
        formData.append('note', customerForm.value.note);


        const response = await axios.post(`/api/customers/${props.customerId}`, formData, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: 'application/json',
                'X-HTTP-Method-Override': 'PATCH'
            }
        });

        console.log(`--- RESPONSENYA MASSSSSSSSSSS 1 `, response.data.data);

        Swal.fire({
            title: 'Success',
            text: 'Customer data has been updated',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        emit('updated');
        closePopup();
    } catch (error) {
        console.error('Error updating customer:', error);

        let errorMessage = 'Failed to update customer. Please check the data or re-login.';
        let useHtml = false;

        if (error.response?.data?.errors?.message) {
            const messages = error.response.data.errors.message;
            const formattedErrors = Object.entries(messages)
                .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                .join('<br>');

            if (formattedErrors) {
                errorMessage = formattedErrors;
                useHtml = true;
            }
        }

        Swal.fire({
            title: 'Failed!',
            html: useHtml ? errorMessage : undefined,
            text: useHtml ? undefined : errorMessage,
            icon: 'error'
        });
    } finally {
        loading.value = false;
    }
}

const closePopup = () => {
    emit('close');
};

onMounted(() => {
    fetchCustomer();
});

</script>
