<script setup>
import {ref, onMounted, computed} from 'vue'
import axios from 'axios'
import {usePage, router} from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'

const page = usePage()
const product = ref(null)
const loading = ref(true)
const error = ref(null)
const referrer = ref('/products/all') // Default fallback

const productId = computed(() => {

    return page.props.product?.id ||
        page.props.route?.params?.id ||
        window.location.pathname.replace(/\/$/, '').split('/').pop()
})

onMounted(async () => {
    try {
        console.log('Product ID:', productId.value)

        // Check if we came from the restore page
        if (document.referrer.includes('/products/restore')) {
            referrer.value = '/products/restore'
        } else if (document.referrer.includes('/products/all')) {
            referrer.value = '/products/all'
        } else if (localStorage.getItem('productReferrer')) {
            // Fallback to localStorage if document.referrer is not reliable
            referrer.value = localStorage.getItem('productReferrer')
        }

        // Store current referrer for future use
        localStorage.setItem('productReferrer', referrer.value)

        if (!productId.value || isNaN(productId.value)) {
            throw new Error('Invalid product ID')
        }

        const response = await axios.get(`/api/products/${productId.value}`)
        console.log('API Response:', response)

        if (!response.data) {
            throw new Error('Product data not found in response')
        }

        product.value = response.data.data
    } catch (err) {
        error.value = err.message
        console.error('Error details:', {
            message: err.message,
            response: err.response?.data,
            status: err.response?.status
        })
        // Redirect to the referrer page or fallback to all products
        router.visit(referrer.value)
    } finally {
        loading.value = false
    }
})

function formatCurrency(value) {
    if (!value) return 'Rp.0'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value)
}

</script>

<template>
    <div v-if="loading" class="py-10 text-gray-500">
        <sidebar>
            <!-- Skeleton loading bang -->
            <div class="bg-white rounded-lg p-5 mb-4 shadow-sm animate-pulse">
                <div class="h-6 bg-gray-300 rounded w-40 mb-4"></div>
                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px]">
                    <div class="w-[239px] h-[148px] bg-gray-200 rounded mb-4"></div>
                    <div class="flex flex-col gap-4 w-full">
                        <div class="h-5 bg-gray-300 rounded w-32"></div>
                        <div class="h-[45px] bg-gray-200 rounded w-full"></div>
                        <div class="h-5 bg-gray-300 rounded w-32"></div>
                        <div class="h-[45px] bg-gray-200 rounded w-full"></div>
                        <div class="h-5 bg-gray-300 rounded w-32"></div>
                        <div class="h-[45px] bg-gray-200 rounded w-full"></div>
                        <div class="h-5 bg-gray-300 rounded w-32"></div>
                        <div class="h-[45px] bg-gray-200 rounded w-full"></div>
                        <div class="h-5 bg-gray-300 rounded w-32"></div>
                        <div class="h-[45px] bg-gray-200 rounded w-full"></div>
                    </div>
                </div>
            </div>
        </sidebar>
    </div>


    <div v-else>
        <div class="bg-gray-100">
            <!-- Sidebar -->
            <Sidebar>

                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">Product Detail</span>
                </div>

                <div
                    class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                                <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                                    <div class="flex justify-start items-start flex-col gap-[5px] w-[192px]"
                                         style="width: 192px"><p class="self-stretch text-[#000000] text-sm font-medium leading-6">{{ product?.name || '-' }}</p>

                                        <div class="flex justify-start items-start flex-row gap-[15px]">
                                            <svg width="239" height="148" viewBox="0 0 239 148" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 xmlns:xlink="http://www.w3.org/1999/xlink">
                                                <rect x="0.475812" y="0.475812" width="238.048" height="146.678"
                                                      rx="9.04043" stroke="#AEADAD" stroke-width="0.951625"
                                                      stroke-dasharray="19.03 19.03"/>
                                                <foreignObject x="23.7906" y="23.7905" width="191.419" height="100.048">
                                                    <img v-if="product && product.images"
                                                         :src="product.images"
                                                         :alt="product?.name"
                                                         style="width: 100%; height: 100%; object-fit: contain;" />
                                                    <img v-else
                                                         src="https://via.placeholder.com/191x100"
                                                         alt="Product Image"
                                                         style="width: 100%; height: 100%; object-fit: contain;" />
                                                </foreignObject>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                    <div class="flex flex-1 justify-start items-start flex-row gap-2.5">
                                        <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">
                                                Category</p></div>
                                            <div
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                                style="height: 45px"><p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                                {{ product?.category?.name || '-' }}
                                            </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-1 justify-start items-start flex-row gap-[15px] h-[74px]"
                                         style="height: 74px">
                                        <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">Stock</p>
                                            </div>
                                            <div
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                                style="height: 45px">
                                            <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                  {{ product?.stock ?? 0 }}
                                                </span>
                                            </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">Stock
                                                Alert</p></div>
                                            <div
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                                style="height: 45px"><span
                                                class="text-[#2E2E2E] text-[15px] leading-6">
                                            <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                  {{ product?.stock_alert ?? 0 }}
                                                </span>
                                        </span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                            class="self-stretch text-[#000000] text-sm font-medium leading-6">Barcode</p>
                                        </div>
                                        <div
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                            style="height: 45px"><span class="text-[#2E2E2E] text-[15px] leading-6"><span class="text-[#2E2E2E] text-[15px] leading-6">
                                                  {{ product?.barcode ?? '-' }}
                                                </span></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-1 justify-start items-start flex-row gap-[15px] h-[74px]"
                                         style="height: 74px">
                                        <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">Selling
                                                Price</p></div>
                                            <div
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                                style="height: 45px"><span class="text-[#2E2E2E] text-[15px] leading-6"><span>{{ formatCurrency(product?.selling_price) }}</span></span>
                                            </div>
                                        </div>
                                        <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">Purchase
                                                Price</p></div>
                                            <div
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                                style="height: 45px"><span class="text-[#2E2E2E] text-[15px] leading-6"><span>{{ formatCurrency(product?.purchase_price) }}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px] h-[74px]"
                                         style="height: 74px">
                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                            class="self-stretch text-[#000000] text-sm font-medium leading-6">SKU</p></div>
                                        <div
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                            style="height: 45px"><span class="text-[#2E2E2E] text-[15px] leading-6"><span>{{ product?.sku || '-' }}</span></span>
                                        </div>
                                    </div>
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px] h-[74px]"
                                         style="height: 74px">
                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                            class="self-stretch text-[#000000] text-sm font-medium leading-6">Discount</p>
                                        </div>
                                        <div
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                            style="height: 45px"><span
                                            class="text-[#2E2E2E] text-[15px] leading-6">{{product?.discount}}%</span></div>
                                    </div>
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px] h-[74px]"
                                         style="height: 74px">
                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                            class="self-stretch text-[#000000] text-sm font-medium leading-6">Brand</p>
                                        </div>
                                        <div
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                            style="height: 45px"><span
                                            class="text-[#2E2E2E] text-[15px] leading-6"><p>{{ product?.brand || '-' }}</p></span></div>
                                    </div>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                        class="self-stretch text-[#000000] text-sm font-medium leading-6">Description</p>
                                    </div>
                                    <div
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-2.5 px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[136px]"
                                        style="height: 136px">
                                        <div class="flex flex-1 self-stretch justify-start items-start flex-col gap-[80px]">
                                            <p class="self-stretch text-[#2E2E2E] text-[15px] leading-6"><p>{{ product?.description || '-' }}</p></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">

                                    <div class="flex flex-1 justify-start items-start flex-row gap-[15px] h-[74px]"
                                         style="height: 74px">
                                        <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">Unit
                                                ID</p></div>
                                            <div
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                                style="height: 45px"><p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                                <p>{{ product?.unit?.code || '-' }}</p></p></div>
                                        </div>
                                        <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">Weight
                                                (KG)</p></div>
                                            <div
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                                style="height: 45px"><span class="text-[#2E2E2E] text-[15px] leading-6"><p>{{ product?.weight ?? 0 }}kg</p></span>
                                            </div>
                                        </div>
                                        <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]"><p
                                                class="self-stretch text-[#000000] text-sm font-medium leading-6">Volume
                                                (M³)</p></div>
                                            <div
                                                class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] h-[45px]"
                                                style="height: 45px"><span class="text-[#2E2E2E] text-[15px] leading-6">{{product?.volume ?? 0 }}m³</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button
                            @click="$inertia.visit(referrer)"
                            class="flex justify-center items-center flex-row gap-2.5 py-[5px] px-[30px] bg-[#2F8451] rounded-[10px] w-[186px] h-[48px]"
                        >
                            <span class="text-[#FFFFFF] text-sm font-medium leading-6">Back</span>
                        </button>

                    </div>
                </div>
            </Sidebar>
        </div>
    </div>


</template>
