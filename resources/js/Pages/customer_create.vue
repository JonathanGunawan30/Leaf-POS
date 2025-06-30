<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div
                    class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Customer</span></div>

                <!-- FIRST 2 ROW -->
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">

                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">

                        <p class="self-stretch text-[#000000] font-medium leading-6">Customer Profile</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">

                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Customer Name <span
                                    class="text-red-500">*</span></p>
                                </div>
                                <input v-model="customerForm.name" placeholder="Enter customer name"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Company Name </p>
                                </div>
                                <input v-model="customerForm.company_name" placeholder="Enter company name"
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
                                    <input v-model="customerForm.email" type="email" placeholder="Enter email address"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none" required />
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Phone <span class="text-red-500">*</span></p>
                                    <input v-model="customerForm.phone" placeholder="Enter phone number"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none" required />
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Bank Name <span class="text-red-500">*</span></p>
                                    <input v-model="customerForm.bank_name" placeholder="Enter bank name"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none" required />
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Bank Account Number <span class="text-red-500">*</span></p>
                                    <input v-model="customerForm.bank_account" placeholder="Enter bank account number"
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
                                    <input v-model="customerForm.country" placeholder="Enter country"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none"/>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Province <span
                                        class="text-red-500">*</span></p>
                                    <input v-model="customerForm.province" placeholder="Enter province"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none"/>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">City <span class="text-red-500">*</span>
                                    </p>
                                    <input v-model="customerForm.city" placeholder="Enter city"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none"/>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <p class="text-[#000000] text-xs leading-6">Postal Code <span
                                        class="text-red-500">*</span></p>
                                    <input v-model="customerForm.postal_code" placeholder="Enter postal code"
                                           class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none"/>
                                </div>
                            </div>

                            <div class="flex flex-col gap-[5px] self-stretch">
                                <p class="text-[#000000] text-xs leading-6">Address <span class="text-red-500">*</span></p>
                                <input v-model="customerForm.address" placeholder="Enter full address"
                                       class="py-[9px] px-[15px] border border-gray-300 rounded-[10px] h-[45px] text-[15px] outline-none w-full"
                                       :class="{'border-red-500': addressError}" /> <p v-if="addressError" class="text-red-500 text-xs mt-1">{{ addressError }}</p> </div>

<!--                            <iframe-->
<!--                                width="100%"-->
<!--                                height="250"-->
<!--                                frameborder="0"-->
<!--                                style="border:0; border-radius: 10px"-->
<!--                                referrerpolicy="no-referrer-when-downgrade"-->
<!--                                :src="`https://www.google.com/maps/embed/v1/place?q=${encodeURIComponent(customerForm.address || 'Tangerang, Indonesia')}&key=${googleMapsKey}`"-->
<!--                                allowfullscreen>-->
<!--                            </iframe>-->

                            <iframe
                                width="100%"
                                height="250"
                                frameborder="0"
                                style="border:0; border-radius: 10px"
                                referrerpolicy="no-referrer-when-downgrade"
                                :src="customerMapSrc"
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
                                <input v-model="customerForm.npwp_number" placeholder="Enter NPWP number"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px"/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">NIB </p>
                                </div>
                                <input v-model="customerForm.nib_number" placeholder="Enter NIB number"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px"/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">SIUP </p>
                                </div>
                                <input v-model="customerForm.siup_number" placeholder="Enter SIUP number"
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
                                <input v-model="customerForm.business_type" placeholder="Enter business type"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>



                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Notes</p>
                                <textarea v-model="customerForm.note" placeholder="Enter notes"
                                          class="w-full py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] text-[#000000] text-[15px] leading-6 outline-none resize-none"
                                          style="min-height: 100px; max-height: 200px; overflow-y: auto;"></textarea>
                            </div>


                        </div>
                    </div>

                </div>


<!--                <div class="flex self-stretch justify-start items-center pt-4">-->
<!--                    <button-->
<!--                        @click="createCustomer"-->
<!--                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"-->
<!--                    >-->
<!--                        <span class="text-white text-sm font-medium leading-6">Create Customer</span>-->
<!--                    </button>-->
<!--                </div>-->



                <div class="flex self-stretch justify-start items-center pt-4">
                    <button
                        @click="createCustomer"
                        :disabled="!isAddressGeocodedValid && customerForm.address.length > 0"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"
                        :class="{'opacity-50 cursor-not-allowed': !isAddressGeocodedValid && customerForm.address.length > 0}"
                    >
                        <span class="text-white text-sm font-medium leading-6">Create Customer</span>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>



<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import {onMounted, ref, watch} from 'vue'
import axios from 'axios'
import Swal from "sweetalert2";
import {router} from '@inertiajs/vue3'

// Fungsi debounce sederhana
const debounce = (func, delay) => {
    let timeout;
    return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), delay);
    };
};

const token = localStorage.getItem('X-API-TOKEN')

const customerForm = ref({
    name: '',
    company_name: '',
    email: '',
    phone: '',
    address: '', // Pastikan ini string kosong
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

const customerMapSrc = ref('');
const addressError = ref(''); // Variabel baru untuk pesan error alamat
const isAddressGeocodedValid = ref(false); // Variabel baru untuk status validasi alamat


// Fungsi asinkron yang akan di-debounce
const updateMapBasedOnAddress = async (newAddress) => {
    console.log('Debounced function called. newAddress:', newAddress);

    // Reset error dan status validasi setiap kali fungsi dipanggil
    addressError.value = '';
    isAddressGeocodedValid.value = false; // Asumsikan tidak valid sampai terbukti

    if (newAddress) {
        try {
            console.log('Fetching coordinates via Laravel proxy for:', newAddress);
            const response = await axios.get('/api/geocode-address', {
                params: {
                    q: newAddress,
                }
            });

            console.log('Nominatim Proxy API Response:', response.data);

            if (response.data && response.data.length > 0) {
                const lat = response.data[0].lat;
                const lon = response.data[0].lon;

                if (lat && lon) {
                    customerMapSrc.value = `https://www.openstreetmap.org/export/embed.html?bbox=${lon},${lat},${lon},${lat}&layer=mapnik&marker=${lat},${lon}`;
                    console.log('Map URL updated to:', customerMapSrc.value);
                    isAddressGeocodedValid.value = true; // Alamat ditemukan dan valid
                } else {
                    console.warn('Nominatim returned data but lat/lon were invalid.');
                    addressError.value = 'The geocoding service returned invalid coordinates.';
                    customerMapSrc.value = `https://www.openstreetmap.org/export/embed.html?bbox=106.6300, -6.2000, 106.6300, -6.2000&layer=mapnik&marker=-6.2000,106.6300`;
                }
            } else {
                console.warn('Address not found by Nominatim proxy, showing default map.');
                addressError.value = 'Sorry, the address you entered could not be found on the map. Please ensure the address is complete and correct.';
                customerMapSrc.value = `https://www.openstreetmap.org/export/embed.html?bbox=106.6300, -6.2000, 106.6300, -6.2000&layer=mapnik&marker=-6.2000,106.6300`;
            }
        } catch (error) {
            console.error('Error fetching geocoding data from Laravel proxy:', error.response?.data || error.message);

            if (error.response && (error.response.status === 404 || error.response.status === 400)) {
                // Nominatim mungkin mengembalikan 400 jika query terlalu pendek/invalid
                addressError.value = 'Could not find address on map. Please check the address.';
            } else {
                addressError.value = 'Network error or geocoding service unavailable. Please try again.';
            }
            customerMapSrc.value = `https://www.openstreetmap.org/export/embed.html?bbox=106.6300, -6.2000, 106.6300, -6.2000&layer=mapnik&marker=-6.2000,106.6300`;
        }
    } else {
        console.log('Address input is empty, setting default map.');
        addressError.value = ''; // Hapus error jika alamat kosong
        isAddressGeocodedValid.value = false; // Alamat kosong tidak valid secara geocoding
        customerMapSrc.value = `https://www.openstreetmap.org/export/embed.html?bbox=106.6300, -6.2000, 106.6300, -6.2000&layer=mapnik&marker=-6.2000,106.6300`;
    }
};

// Terapkan debounce pada fungsi watcher
const debouncedUpdateMap = debounce(updateMapBasedOnAddress, 500);

// Watcher untuk memantau perubahan pada alamat pelanggan
watch(() => customerForm.value.address, (newAddress) => {
    debouncedUpdateMap(newAddress);
}, { immediate: true });


const createCustomer = async () => {
    // Validasi tambahan sebelum submit
    if (!customerForm.value.address) {
        Swal.fire({
            title: 'Validation Error',
            text: 'Address field is required.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return; // Hentikan submit
    }

    if (!isAddressGeocodedValid.value) {
        Swal.fire({
            title: 'Address Not Valid',
            text: 'Please ensure the address is correct and can be located on the map before submitting.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return; // Hentikan submit
    }

    try {
        const response = await axios.post('/api/customers', customerForm.value, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })

        console.log('Customer created:', response.data)

        // Reset form setelah berhasil
        customerForm.value = {
            name: '', company_name: '', email: '', phone: '', address: '', city: '',
            province: '', postal_code: '', country: '', bank_account: '', bank_name: '',
            npwp_number: '', siup_number: '', nib_number: '', business_type: '', note: ''
        };
        addressError.value = ''; // Bersihkan error setelah submit berhasil
        isAddressGeocodedValid.value = false; // Reset status validasi


        Swal.fire({
            title: 'Success!',
            text: 'Customer has been successfully created.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            }
        })
    } catch (error) {
        console.error('Failed to create customer:', error.response?.data || error.message)

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
