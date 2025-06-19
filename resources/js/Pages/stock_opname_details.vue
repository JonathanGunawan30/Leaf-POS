<template>
    <div v-if="loading" class="py-10 text-gray-500">
        <div class="bg-gray-100">
            <sidebar>
                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">Stock Opname Detail</span>
                </div>

                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-white rounded-[10px] animate-pulse">
                    <div class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-32 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-20 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-36 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-32 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                            </div>

                            <div class="mt-6 w-full">
                                <div class="h-5 w-40 bg-gray-200 rounded mb-2"></div>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <div class="h-10 w-full bg-gray-200 rounded-t-[10px]"></div>
                                    <div class="h-12 w-full bg-gray-100 border-b border-gray-200"></div>
                                    <div class="h-12 w-full bg-gray-100 border-b border-gray-200"></div>
                                    <div class="h-12 w-full bg-gray-100 rounded-b-[10px]"></div>
                                </div>
                            </div>
                        </div>
                        <div class="w-[186px] h-[48px] bg-gray-300 rounded-[10px]"></div>
                    </div>
                </div>
            </sidebar>
        </div>
    </div>

    <div v-else>
        <div class="bg-gray-100">
            <Sidebar>
                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">Stock Opname Detail</span>
                </div>

                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Stock Opname ID</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v2H7V5zm0 4h6v2H7V9zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                            {{ stockOpname?.id ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Conducted By</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ stockOpname?.user?.name ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Location
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ stockOpname?.location ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Opname Date
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ formatDate(stockOpname?.opname_date) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Status
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span
                                            :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                getStatusClass(stockOpname?.status)
                                            ]"
                                        >
                                            {{ capitalizeFirstLetter(stockOpname?.status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Approved By
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ stockOpname?.approved_by?.name ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                    <p class="text-[#000000] text-sm font-medium leading-6">
                                        Notes
                                    </p>
                                    <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17 10H3a1 1 0 000 2h14a1 1 0 100-2z" />
                                    </svg>
                                </div>
                                <div class="min-h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                    <span class="text-[#2E2E2E] text-[15px] leading-6">
                                        {{ stockOpname?.notes ?? '-' }}
                                    </span>
                                </div>
                            </div>


                            <div class="mt-6 w-full">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Opname Items</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead class="bg-lp-green bg-opacity-20">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                                            <th class="px-4 py-3 text-left font-medium">PRODUCT</th>
                                            <th class="px-4 py-3 text-left font-medium">SKU</th>
                                            <th class="px-4 py-3 text-left font-medium">BARCODE</th>
                                            <th class="px-4 py-3 text-left font-medium">SYSTEM STOCK</th>
                                            <th class="px-4 py-3 text-left font-medium">ACTUAL STOCK</th>
                                            <th class="px-4 py-3 text-left font-medium">DIFFERENCE</th>
                                            <th class="px-4 py-3 text-left font-medium">NOTES</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(item, index) in stockOpname?.items" :key="item.id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">{{ item.product.name || 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ item.product.sku || 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ item.product.barcode || 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ item.system_stock }}</td>
                                            <td class="px-4 py-3">{{ item.actual_stock }}</td>
                                            <td class="px-4 py-3">{{ item.difference }}</td>
                                            <td class="px-4 py-3">{{ item.notes || '-' }}</td>
                                        </tr>
                                        <tr v-if="stockOpname?.items && stockOpname.items.length === 0">
                                            <td colspan="8" class="px-4 py-3 text-center text-gray-500">No items found for this stock opname.</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-6 w-full">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Summary</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead class="bg-lp-green bg-opacity-20">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                                            <th class="px-4 py-3 text-left font-medium">TOTAL ITEMS</th>
                                            <th class="px-4 py-3 text-left font-medium">TOTAL DIFFERENCE</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ 1 }}</td>
                                            <td class="px-4 py-3">{{ stockOpname?.total_items ?? '-' }}</td>
                                            <td
                                                class="px-4 py-3 font-semibold rounded-r-md"
                                                :class="totalDifference === 0 ? 'text-green-600' : 'text-red-600'"
                                            >
                                                {{ totalDifference }}
                                            </td>

                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                        <button
                            @click="router.visit(referrer)"
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

<script setup>
import {ref, onMounted, computed} from 'vue'
import axios from 'axios'
import {usePage, router} from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const page = usePage()
const stockOpname = ref(null)
const loading = ref(true)
const error = ref(null)
const referrer = ref('/stock-opnames/all')

const stockOpnameId = computed(() => {
    return page.props.stockOpname?.id ||
        page.props.route?.params?.id ||
        window.location.pathname.replace(/\/$/, '').split('/').pop()
})

const totalDifference = computed(() => {
    if (!stockOpname.value?.items) return 0
    return stockOpname.value.items.reduce((sum, item) => sum + Number(item.difference || 0), 0)
})

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const capitalizeFirstLetter = (string) => {
    if (!string) return 'N/A'
    return string.charAt(0).toUpperCase() + string.slice(1).replace(/_/g, ' ')
}

const getStatusClass = (status) => {
    switch (status) {
        case 'draft':
            return 'bg-gray-100 text-gray-800'
        case 'pending':
            return 'bg-yellow-100 text-yellow-800'
        case 'completed':
            return 'bg-green-100 text-green-800'
        case 'approved':
            return 'bg-blue-100 text-blue-800'
        case 'rejected':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

onMounted(async () => {
    try {
        if (document.referrer.includes('/stock-opnames/restore')) {
            referrer.value = '/stock-opnames/restore'
        } else if (document.referrer.includes('/stock-opnames/all')) {
            referrer.value = '/stock-opnames/all'
        } else if (localStorage.getItem('inventoryReferrer')) {
            referrer.value = localStorage.getItem('inventoryReferrer')
        }

        localStorage.setItem('inventoryReferrer', referrer.value)

        if (!stockOpnameId.value || isNaN(stockOpnameId.value)) {
            throw new Error('Invalid Stock Opname ID')
        }

        const token = localStorage.getItem('X-API-TOKEN')

        const response = await axios.get(`/api/stock-opnames/${stockOpnameId.value}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        console.log('API Response:', response)

        if (!response.data) {
            throw new Error('Stock Opname data not found in response')
        }

        stockOpname.value = response.data.data

    } catch (err) {
        error.value = err.message
        console.error('Error details:', {
            message: err.message,
            response: err.response?.data,
            status: err.response?.status
        })
        router.visit(referrer.value)
    } finally {
        loading.value = false
    }
})
</script>
