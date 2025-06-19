<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div
                    class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Supplier</span></div>

                <!-- FIRST 2 ROW -->
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">

                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">

                        <p class="self-stretch text-[#000000] font-medium leading-6">Supplier Profile</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">

                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Supplier Name <span
                                    class="text-red-500">*</span></p>
                                </div>
                                <input v-model="supplierForm.name" placeholder="Enter supplier name"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Company Name </p>
                                </div>
                                <input v-model="supplierForm.company_name" placeholder="Enter company name"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px"/>
                            </div>

                        </div>
                    </div>


                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Contact & Bank Information</p>

                        <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">

                            <div class="flex self-stretch justify-start items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Email <span class="text-red-500">*</span></p>
                                    <input v-model="supplierForm.email" type="email" placeholder="Enter email address"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none" required />
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Phone <span class="text-red-500">*</span></p>
                                    <input v-model="supplierForm.phone" placeholder="Enter phone number"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none" required />
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Bank Name <span class="text-red-500">*</span></p>
                                    <input v-model="supplierForm.bank_name" placeholder="Enter bank name"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none" required />
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Bank Account Number <span class="text-red-500">*</span></p>
                                    <input v-model="supplierForm.bank_account" placeholder="Enter bank account number"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none" required />
                                </div>
                            </div>

                        </div>
                    </div>


                </div>

                <!-- SECOND 2 ROW -->
                <div class="flex self-stretch justify-start items-stretch items-start flex-row gap-2.5">
                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Location Information</p>
                        <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">

                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Country <span
                                        class="text-red-500">*</span></p>
                                    <input v-model="supplierForm.country" placeholder="Enter country"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none"/>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Province <span
                                        class="text-red-500">*</span></p>
                                    <input v-model="supplierForm.province" placeholder="Enter province"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none"/>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">City <span class="text-red-500">*</span>
                                    </p>
                                    <input v-model="supplierForm.city" placeholder="Enter city"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none"/>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Postal Code <span
                                        class="text-red-500">*</span></p>
                                    <input v-model="supplierForm.postal_code" placeholder="Enter postal code"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none"/>
                                </div>
                            </div>

                            <div class="flex flex-col gap-[5px] self-stretch">
                                <p class="text-[#000000] text-xs leading-6">Address <span class="text-red-500">*</span>
                                </p>
                                <input v-model="supplierForm.address" placeholder="Enter full address"
                                       class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none w-full"/>
                            </div>

                            <iframe
                                width="100%"
                                height="250"
                                frameborder="0"
                                style="border:0; border-radius: 10px"
                                referrerpolicy="no-referrer-when-downgrade"
                                :src="`https://www.google.com/maps/embed/v1/place?q=${encodeURIComponent(supplierForm.address || 'Tangerang, Indonesia')}&key=${googleMapsKey}`"
                                allowfullscreen>
                            </iframe>

                        </div>
                    </div>

                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Company Legal Information</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">NPWP </p>
                                </div>
                                <input v-model="supplierForm.npwp_number" placeholder="Enter NPWP number"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px"/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">NIB </p>
                                </div>
                                <input v-model="supplierForm.nib_number" placeholder="Enter NIB number"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px"/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">SIUP </p>
                                </div>
                                <input v-model="supplierForm.siup_number" placeholder="Enter SIUP number"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px"/>
                            </div>
                        </div>
                    </div>

                </div>


                <!-- THIRD 2 ROW -->
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">

                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">More Information</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">

                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Business Type </p>
                                </div>
                                <input v-model="supplierForm.business_type" placeholder="Enter business type"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>



                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Notes</p>
                                <textarea v-model="supplierForm.note" placeholder="Enter notes"
                                          class="w-full py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] text-[#000000] text-[15px] leading-6 outline-none resize-none"
                                          style="min-height: 100px; max-height: 200px; overflow-y: auto;"></textarea>
                            </div>


                        </div>
                    </div>

                </div>


                <div class="flex self-stretch justify-start items-center pt-4">
                    <button
                        @click="createSupplier"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"
                    >
                        <span class="text-white text-sm font-medium leading-6">Create Supplier</span>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import {onMounted, ref} from 'vue'
import axios from 'axios'
import Swal from "sweetalert2";
import {router} from '@inertiajs/vue3'
const googleMapsKey = import.meta.env.VITE_GOOGLE_MAPS_API_KEY;


const token = localStorage.getItem('X-API-TOKEN')

const supplierForm = ref({
    name: '',
    company_name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    province: '',
    postal_code: '',
    country: '',
    bank_account: '',
    bank_name: '',
    npwp_number: '',
    siup_number: '',
    nib_number: '',
    business_type: '',
    note: ''
});


const createSupplier = async () => {
    try {
        const response = await axios.post('/api/suppliers', supplierForm.value, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })

        console.log('Supplier created:', response.data)

        supplierForm.value = {
            name: '',
            company_name: '',
            email: '',
            phone: '',
            address: '',
            city: '',
            province: '',
            postal_code: '',
            country: '',
            bank_account: '',
            bank_name: '',
            npwp_number: '',
            siup_number: '',
            nib_number: '',
            business_type: '',
            note: ''
        }

        Swal.fire({
            title: 'Success!',
            text: 'Supplier has been successfully created.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            }
        })
    } catch (error) {
        console.error('Failed to create supplier:', error.response?.data || error.message)

        let errorMessage = 'Failed to create user. Please check the data.';
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
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200 '
            }
        })
    }
}

onMounted(async () => {
    const token = localStorage.getItem('X-API-TOKEN');
    if (!token) {
        router.visit('/')
    }

})




</script>
