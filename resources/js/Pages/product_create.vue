<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import {onMounted, ref} from 'vue'
import axios from 'axios'
import Swal from "sweetalert2";

const token = localStorage.getItem('X-API-TOKEN')

const api = axios.create({
    baseURL: '/api',
    timeout: 8000,
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    }
})

const productForm = ref({
    name: '',
    description: '',
    category_id: '',
    unit_id: '',
    barcode: '',
    weight: '',
    volume: '',
    selling_price: '',
    purchase_price: '',
    stock: '',
    stock_alert: '',
    sku: '',
    discount: '',
    brand: '',
    tax_id: ''
})

const categories = ref([])
const units = ref([])
const taxRates = ref([])

const fetchTaxes = async () => {
    try {
        const { data } = await api.get('/taxes')
        taxRates.value = data.data
    } catch (error) {
        console.error('Error fetching taxes:', error)
    }
}

const fetchCategories = async () => {
    try {
        const { data } = await api.get('/categories')
        categories.value = data.data
    } catch (error) {
        console.error('Error fetching categories:', error)
    }
}

const fetchUnits = async () => {
    try {
        const { data } = await api.get('/units')
        units.value = data.data
    } catch (error) {
        console.error('Error fetching units:', error)
    }
}

const imageFile = ref(null)
const imagePreview = ref(null)

const handleImageUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png']
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                title: 'Invalid File Type',
                text: 'Please select a JPG, JPEG, or PNG image file.',
                icon: 'error',
                confirmButtonText: 'OK'
            })
            event.target.value = ''
            return
        }

        imageFile.value = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

const onBarcodeInput = (event) => {
    productForm.barcode = event.target.value.replace(/\D/g, '').slice(0, 13);
}

// Prevent negative values in numeric fields
const preventNegativeValues = (field) => {
    if (productForm.value[field] < 0) {
        productForm.value[field] = 0;
    }
}


const formatCurrency = (value) => {
    if (!value) return ''
    const numericValue = value.toString().replace(/[^0-9]/g, '')

    return 'Rp' + numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}

const handleCurrencyInput = (event, field) => {
    const formattedValue = formatCurrency(event.target.value)
    productForm.value[field] = formattedValue
}

// Handle form submission
const createProduct = async () => {
    try {
        const formData = new FormData()
        formData.append('name', productForm.value.name)
        formData.append('description', productForm.value.description)
        formData.append('category_id', productForm.value.category_id)
        formData.append('unit_id', productForm.value.unit_id)
        formData.append('barcode', productForm.value.barcode)
        formData.append('weight', productForm.value.weight)
        formData.append('volume', productForm.value.volume)
        formData.append('selling_price', parseInt(productForm.value.selling_price.replace(/[^0-9]/g, '')))
        formData.append('purchase_price', parseInt(productForm.value.purchase_price.replace(/[^0-9]/g, '')))
        formData.append('stock', parseInt(productForm.value.stock) || 0)
        formData.append('stock_alert', parseInt(productForm.value.stock_alert) || 0)
        formData.append('sku', productForm.value.sku)
        formData.append('discount', productForm.value.discount || 0)
        formData.append('brand', productForm.value.brand)
        formData.append('taxes[tax_id][tax_id]', parseInt(productForm.value.tax_id));

        if (imageFile.value) {
            formData.append('images', imageFile.value)
        }

        const response = await axios.post('/api/products', formData, {
            headers: {
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            }
        })

        console.log('Product created:', response.data)

        productForm.value = {
            name: '',
            description: '',
            category_id: '',
            unit_id: '',
            barcode: '',
            weight: '',
            volume: '',
            selling_price: formatCurrency(''),
            purchase_price: formatCurrency(''),
            stock: '',
            stock_alert: '',
            sku: '',
            discount: '',
            brand: '',
            tax_id: ''
        }

        // Reset image variables
        imageFile.value = null
        imagePreview.value = null

        Swal.fire({
            title: 'Success!',
            text: 'Product has been successfully added.',
            icon: 'success',
            confirmButtonText: 'OK'
        })

    } catch (error) {
        console.error('Failed to add product:', error.response?.data || error.message)

        let errorMessage = 'Failed to add product. Please check the data or re-login.';
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
            confirmButtonText: 'OK'
        })
    }
}

onMounted(async () => {
    await Promise.all([
        fetchTaxes(),
        fetchCategories(),
        fetchUnits()
    ])

    // Initialize price fields with formatted values
    productForm.value.selling_price = formatCurrency('')
    productForm.value.purchase_price = formatCurrency('')
})
</script>

<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div
                    class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Products</span></div>
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Basic Information</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Input your product name <span class="text-red-500">*</span></p>
                                </div>
                                <input v-model="productForm.name" placeholder="Enter product name"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       style="height: 45px" required/>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">input your description
                                    here</p></div>
                                <textarea v-model="productForm.description" placeholder="Enter product description"
                                          class="flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[145px] text-[#000000] text-[15px] leading-6 outline-none resize-none w-full"
                                          style="height: 145px"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-1 self-stretch justify-start items-start flex-col gap-4">
                        <div
                            class="flex self-stretch flex-1 justify-start items-start flex-col gap-4 p-5 bg-white rounded-lg">
                            <p class="self-stretch text-black font-medium leading-6">Product Image</p>

                            <div class="flex justify-start items-center gap-2.5 p-2.5 bg-white border border-gray-300 rounded-lg">

                                <label class="cursor-pointer">
                                    <input
                                        type="file"
                                        class="hidden"
                                        accept="image/jpeg,image/jpg,image/png"
                                        @change="handleImageUpload"
                                    />
                                    <div
                                        class="flex justify-center items-center bg-[#E4E9F2] rounded-lg w-24 h-24">

                                        <!-- Tampilkan tanda + jika belum ada gambar -->
                                        <p v-if="!imagePreview" class="text-[#989797] text-4xl text-center font-medium leading-[68px]">
                                            +
                                        </p>

                                        <!-- Tampilkan gambar jika sudah diupload -->
                                        <img
                                            v-if="imagePreview"
                                            :src="imagePreview"
                                            alt="Product Image"
                                            class="w-full h-full object-cover rounded-lg"
                                        />
                                    </div>
                                </label>
                            </div>
                        </div>


                        <div
                            class="flex self-stretch flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <span class="text-[#000000] font-medium leading-6">Product Category</span>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                    class="self-stretch text-[#000000] text-xs leading-6">Input your product
                                    Category <span class="text-red-500">*</span></p></div>
                                <select v-model="productForm.category_id" required
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select category</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div
                        class="flex flex-1 self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Product Details</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border-solid border-[rgba(174,173,173,0.4)] border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Unit <span class="text-red-500">*</span></p>
                                </div>

                                <select v-model="productForm.unit_id" required
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select unit</option>
                                    <option v-for="unit in units" :key="unit.id" :value="unit.id">
                                        {{ unit.name }}
                                    </option>
                                </select>

                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Barcode</p>
                                </div>
                                <input
                                    v-model="productForm.barcode"
                                    type="text"
                                    inputmode="numeric"
                                    pattern="\d{13}"
                                    maxlength="13"
                                    minlength="13"
                                    @input="onBarcodeInput"
                                    class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                    placeholder="Enter 13-digit barcode"
                                />

                            </div>
                            <div class="flex flex-col gap-1 w-full">
                                <!-- Weight Input -->
                                <label class="text-xs text-black">Weight (KG)</label>
                                <div class="relative">
                                    <input
                                        v-model="productForm.weight"
                                        type="number"
                                        min="0"
                                        @input="preventNegativeValues('weight')"
                                        class="w-full py-[9px] px-[15px] pr-8 bg-white border border-gray-300 rounded-[10px] h-[45px] text-black text-[15px] outline-none"
                                        placeholder="Enter weight"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-black text-[15px] pointer-events-none">KG</span>
                                </div>
                            </div>

                            <!-- Volume Input -->
                            <div class="flex flex-col gap-1 w-full">
                                <label class="text-xs text-black">Volume (M³)</label>
                                <div class="relative">
                                    <input
                                        v-model="productForm.volume"
                                        type="number"
                                        min="0"
                                        @input="preventNegativeValues('volume')"
                                        class="w-full py-[9px] px-[15px] pr-8 bg-white border border-gray-300 rounded-[10px] h-[45px] text-black text-[15px] outline-none"
                                        placeholder="Enter volume"
                                    />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-black text-[15px] pointer-events-none">M³</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-1 justify-start items-start flex-col gap-2.5">
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF]  rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Product Price</p>
                            <div
                                class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Input selling price <span class="text-red-500">*</span></p>
                                    </div>
                                    <input
                                        v-model="productForm.selling_price"
                                        @input="(e) => handleCurrencyInput(e, 'selling_price')"
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                        placeholder="Enter selling price" required/>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Input purchase
                                            price <span class="text-red-500">*</span></p>
                                    </div>
                                    <input
                                        v-model="productForm.purchase_price"
                                        @input="(e) => handleCurrencyInput(e, 'purchase_price')"
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                        placeholder="Enter purchase price" required/>
                                </div>
                            </div>
                        </div>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px] mt-2.5">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Product Stock</p>
                            <div
                                class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border-solid border border-gray-300 rounded-[10px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Stock <span class="text-red-500">*</span></p>
                                    </div>
                                    <input v-model="productForm.stock" type="number" min="0" required
                                           @input="preventNegativeValues('stock')"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           placeholder="Enter stock quantity"/>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Stock Alert</p>
                                    </div>
                                    <input v-model="productForm.stock_alert" type="number" min="0"
                                           @input="preventNegativeValues('stock_alert')"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           placeholder="Enter stock alert threshold"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div
                        class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">More Information</p>
                        <div
                            class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border border-gray-300 border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">SKU</p>
                                    </div>
                                    <input v-model="productForm.sku"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           placeholder="Enter SKU"/>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Discount</p>
                                    </div>
                                    <div class="relative w-full">
                                        <input
                                            v-model="productForm.discount"
                                            type="number"
                                            min="0"
                                            max="100"
                                            @input="preventNegativeValues('discount')"
                                            class="w-full py-[9px] px-[15px] pr-8 bg-white border border-gray-300 rounded-[10px] h-[45px] text-black text-[15px] outline-none"
                                            placeholder="Enter discount"
                                        />
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-black text-[15px] pointer-events-none">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Brand</p>
                                    </div>
                                    <input v-model="productForm.brand"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           placeholder="Enter brand name"/>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Tax <span class="text-red-500">*</span></p>
                                    </div>

                                        <select v-model="productForm.tax_id" required
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                        <option value="" disabled selected>Select tax rate</option>
                                        <option v-for="tax in taxRates" :key="tax.id" :value="tax.id">
                                            {{ tax.name }} ({{ tax.rate }}%)
                                        </option>
                                        </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex self-stretch justify-start items-center pt-4">
                    <button
                        @click="createProduct"
                        class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105"
                    >
                        <span class="text-white text-sm font-medium leading-6">Create Product</span>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>
