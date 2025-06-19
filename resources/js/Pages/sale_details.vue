<template>
    <div v-if="loading" class="py-10 text-gray-500">
        <div class="bg-gray-100">
            <sidebar>
                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">Sale Detail</span>
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

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-36 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-32 bg-gray-200 rounded"></div>
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

                            <div class="mt-6 w-full">
                                <div class="h-5 w-20 bg-gray-200 rounded mb-2"></div>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <div class="h-10 w-full bg-gray-200 rounded-t-[10px]"></div>
                                    <div class="h-12 w-full bg-gray-100 border-b border-gray-200"></div>
                                    <div class="h-12 w-full bg-gray-100 rounded-b-[10px]"></div>
                                </div>
                            </div>

                            <div class="mt-6 w-full">
                                <div class="h-5 w-28 bg-gray-200 rounded mb-2"></div>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <div class="h-10 w-full bg-gray-200 rounded-t-[10px]"></div>
                                    <div class="h-12 w-full bg-gray-100 border-b border-gray-200"></div>
                                    <div class="h-12 w-full bg-gray-100 rounded-b-[10px]"></div>
                                </div>
                            </div>

                            <div class="mt-6 w-full">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-5 h-5 bg-gray-300 rounded-full"></div>
                                    <div class="h-5 w-36 bg-gray-200 rounded"></div>
                                </div>

                                <div class="bg-gray-100 rounded-lg p-6 border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-gray-300">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="h-4 w-24 bg-gray-200 rounded mb-1"></div>
                                                    <div class="h-7 w-32 bg-gray-200 rounded"></div>
                                                </div>
                                                <div class="p-3 bg-gray-200 rounded-full w-12 h-12"></div>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-gray-300">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="h-4 w-20 bg-gray-200 rounded mb-1"></div>
                                                    <div class="h-7 w-28 bg-gray-200 rounded"></div>
                                                </div>
                                                <div class="p-3 bg-gray-200 rounded-full w-12 h-12"></div>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-lg p-4 shadow-sm border-l-4 border-gray-300">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="h-4 w-32 bg-gray-200 rounded mb-1"></div>
                                                    <div class="h-7 w-24 bg-gray-200 rounded"></div>
                                                </div>
                                                <div class="p-3 bg-gray-200 rounded-full w-12 h-12"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="h-4 w-32 bg-gray-200 rounded"></div>
                                            <div class="h-4 w-12 bg-gray-200 rounded"></div>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-3">
                                            <div class="h-3 rounded-full bg-gray-300" style="width: 40%"></div>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                                            <div class="h-3 w-8 bg-gray-200 rounded"></div>
                                            <div class="h-3 w-20 bg-gray-200 rounded"></div>
                                            <div class="h-3 w-10 bg-gray-200 rounded"></div>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex justify-center">
                                        <div class="h-8 w-32 bg-gray-200 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 w-full">
                                <div class="h-5 w-24 bg-gray-200 rounded mb-2"></div>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <div class="h-10 w-full bg-gray-200 rounded-t-[10px]"></div>
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
                    <span class="text-black font-medium">Sale Detail</span>
                </div>

                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Invoice Number</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v2H7V5zm0 4h6v2H7V9zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                            {{ sale?.invoice_number ?? '-' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Customer</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ sale?.customer?.label ?? '-' }}
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
                                                getStatusClass(sale?.status)
                                            ]"
                                        >
                                            {{ capitalizeFirstLetter(sale?.status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Payment Status
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span
                                            :class="[
                                                'px-2 py-1 rounded-full text-xs font-medium',
                                                getPaymentStatusClass(sale?.payment_status)
                                            ]"
                                        >
                                            {{ formatPaymentStatus(sale?.payment_status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Sale Date
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ formatDate(sale?.sale_date) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Due Date
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ formatDate(sale?.due_date) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Total Amount
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ formatCurrency(sale?.total_amount) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Grand Total
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] font-bold leading-6">
                                            {{ formatCurrency(sale?.grand_total) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 w-full">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Sale Details</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead class="bg-lp-green bg-opacity-20">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                                            <th class="px-4 py-3 text-left font-medium">PRODUCT</th>
                                            <th class="px-4 py-3 text-left font-medium">QUANTITY</th>
                                            <th class="px-4 py-3 text-left font-medium">UNIT</th>
                                            <th class="px-4 py-3 text-left font-medium">PRICE</th>
                                            <th class="px-4 py-3 text-left font-medium">SUBTOTAL</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(detail, index) in sale?.sale_details" :key="detail.id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">{{ detail.product.name || 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ detail.quantity }}</td>
                                            <td class="px-4 py-3">{{ detail.product.unit.code || 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(detail.unit_price) }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(detail.sub_total) }}</td>
                                        </tr>
                                        <tr v-if="sale?.sale_details && sale.sale_details.length === 0">
                                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">No sale details found.</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-6 w-full" v-if="sale?.taxes && sale.taxes.length > 0">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Taxes</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead class="bg-lp-green bg-opacity-20">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                                            <th class="px-4 py-3 text-left font-medium">TAX NAME</th>
                                            <th class="px-4 py-3 text-left font-medium">RATE</th>
                                            <th class="px-4 py-3 text-left font-medium">AMOUNT</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(tax, index) in sale.taxes" :key="tax.id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">{{ tax.name }}</td>
                                            <td class="px-4 py-3">{{ tax.rate }}%</td>
                                            <td class="px-4 py-3">{{ formatCurrency(tax?.amount) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-6 w-full" v-if="sale?.sale_payments && sale.sale_payments.length > 0">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Payments</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead class="bg-lp-green bg-opacity-20">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                                            <th class="px-4 py-3 text-left font-medium">DATE</th>
                                            <th class="px-4 py-3 text-left font-medium">AMOUNT</th>
                                            <th class="px-4 py-3 text-left font-medium">METHOD</th>
                                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                                            <th class="px-4 py-3 text-left font-medium">NOTE</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(payment, index) in sale.sale_payments" :key="payment.id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">{{ formatDate(payment.payment_date) }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(payment.amount) }}</td>
                                            <td class="px-4 py-3">{{ formatPaymentMethod(payment.payment_method) }}</td>
                                            <td class="px-4 py-3">
                                                <span
                                                    :class="[
                                                            'px-2 py-1 rounded-full text-xs font-medium',
                                                            getPaymentStatusClass(payment.status)
                                                        ]"
                                                >
                                                        {{ capitalizeFirstLetter(payment.status) }}
                                                    </span>
                                            </td>
                                            <td class="px-4 py-3">{{ payment.note || '-' }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-6 w-full" v-if="sale?.shipments && sale.shipments.length > 0">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Shipments</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead class="bg-lp-green bg-opacity-20">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                                            <th class="px-4 py-3 text-left font-medium">COURIER</th>
                                            <th class="px-4 py-3 text-left font-medium">VEHICLE</th>
                                            <th class="px-4 py-3 text-left font-medium">SHIPPING DATE</th>
                                            <th class="px-4 py-3 text-left font-medium">EST. ARRIVAL</th>
                                            <th class="px-4 py-3 text-left font-medium">ACTUAL ARRIVAL</th>
                                            <th class="px-4 py-3 text-left font-medium">STATUS</th>
                                            <th class="px-4 py-3 text-left font-medium">COST</th>
                                            <th class="px-4 py-3 text-left font-medium">NOTE</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(shipment, index) in sale.shipments" :key="shipment.id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">{{ shipment.courier.name || 'N/A' }}</td>
                                            <td class="px-4 py-3">
                                                {{ formatVehicleType(shipment.vehicle_type) || 'N/A' }}
                                                ({{ shipment.vehicle_number || 'N/A' }})
                                            </td>
                                            <td class="px-4 py-3">{{ formatDate(shipment.shipping_date) }}</td>
                                            <td class="px-4 py-3">{{ formatDate(shipment.estimated_arrival_date) }}</td>
                                            <td class="px-4 py-3">{{ formatDate(shipment.actual_arrival_date) }}</td>
                                            <td class="px-4 py-3">
                                                <span
                                                    :class="[
                                                            'px-2 py-1 rounded-full text-xs font-medium',
                                                            getShipmentStatusClass(shipment.status)
                                                        ]"
                                                >
                                                        {{ capitalizeFirstLetter(shipment.status) }}
                                                    </span>
                                            </td>
                                            <td class="px-4 py-3">{{ formatCurrency(shipment.shipping_cost) }}</td>
                                            <td class="px-4 py-3">{{ shipment.note || '-' }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-6 w-full mx-auto">
                                <div class="mb-6">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="p-2 bg-emerald-100 rounded-lg">
                                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Payment Summary</h3>
                                    </div>
                                    <p class="text-sm text-gray-600">Overview of payment status and financial breakdown</p>
                                </div>

                                <div class="border-0 shadow-lg bg-gradient-to-br from-emerald-50 via-white to-emerald-50 rounded-lg">
                                    <div class="p-8">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                            <div class="border border-blue-200 bg-white shadow-sm hover:shadow-md transition-shadow rounded-lg">
                                                <div class="p-6">
                                                    <div class="flex items-start justify-between">
                                                        <div class="space-y-2">
                                                            <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Grand Total</p>
                                                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(sale?.grand_total) }}</p>
                                                            <p class="text-xs text-gray-500">Total invoice amount</p>
                                                        </div>
                                                        <div class="p-3 bg-blue-50 rounded-xl">
                                                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 h-1 bg-blue-100 rounded-full">
                                                        <div class="h-1 bg-blue-500 rounded-full w-full"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="border border-emerald-200 bg-white shadow-sm hover:shadow-md transition-shadow rounded-lg">
                                                <div class="p-6">
                                                    <div class="flex items-start justify-between">
                                                        <div class="space-y-2">
                                                            <p class="text-sm font-medium text-emerald-600 uppercase tracking-wide">Total Paid</p>
                                                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(totalPaid) }}</p>
                                                            <p class="text-xs text-gray-500">Amount received</p>
                                                        </div>
                                                        <div class="p-3 bg-emerald-50 rounded-xl">
                                                            <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 h-1 bg-emerald-100 rounded-full">
                                                        <div
                                                            class="h-1 bg-emerald-500 rounded-full transition-all duration-500"
                                                            :style="{ width: Math.min(paymentPercentage, 100) + '%' }"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="bg-white shadow-sm hover:shadow-md transition-shadow rounded-lg border"
                                                :class="remainingBalance > 0 ? 'border-amber-200' : 'border-emerald-200'"
                                            >
                                                <div class="p-6">
                                                    <div class="flex items-start justify-between">
                                                        <div class="space-y-2">
                                                            <p
                                                                class="text-sm font-medium uppercase tracking-wide"
                                                                :class="remainingBalance > 0 ? 'text-amber-600' : 'text-emerald-600'"
                                                            >
                                                                Outstanding
                                                            </p>
                                                            <p
                                                                class="text-2xl font-bold"
                                                                :class="remainingBalance > 0 ? 'text-gray-900' : 'text-emerald-600'"
                                                            >
                                                                {{ formatCurrency(remainingBalance) }}
                                                            </p>
                                                            <p class="text-xs text-gray-500">Amount pending</p>
                                                        </div>
                                                        <div
                                                            class="p-3 rounded-xl"
                                                            :class="remainingBalance > 0 ? 'bg-amber-50' : 'bg-emerald-50'"
                                                        >
                                                            <svg
                                                                v-if="remainingBalance > 0"
                                                                class="w-6 h-6 text-amber-600"
                                                                fill="currentColor"
                                                                viewBox="0 0 20 20"
                                                            >
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                            </svg>
                                                            <svg
                                                                v-else
                                                                class="w-6 h-6 text-emerald-600"
                                                                fill="currentColor"
                                                                viewBox="0 0 20 20"
                                                            >
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="mt-4 h-1 bg-gray-100 rounded-full">
                                                        <div
                                                            class="h-1 rounded-full transition-all duration-500"
                                                            :class="remainingBalance > 0 ? 'bg-amber-500' : 'bg-emerald-500'"
                                                            :style="{ width: (remainingBalance > 0 ? (100 - paymentPercentage) : 100) + '%' }"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-3">
                                                    <h4 class="text-base font-semibold text-gray-900">Payment Progress</h4>
                                                    <span
                                                        class="text-xs font-medium px-2 py-1 rounded-full"
                                                        :class="getOverallPaymentStatusClass()">
                                                    {{ getOverallPaymentStatus() }}
                                                  </span>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-2xl font-bold text-gray-900">{{ paymentPercentage }}%</p>
                                                    <p class="text-xs text-gray-500">Completed</p>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                <div class="w-full bg-gray-100 rounded-full h-3">
                                                    <div
                                                        class="h-3 rounded-full transition-all duration-300 ease-in-out"
                                                        :class="paymentPercentage >= 100 ? 'bg-emerald-500' : 'bg-blue-500'"
                                                        :style="{ width: Math.min(paymentPercentage, 100) + '%' }"
                                                    ></div>
                                                </div>

                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-gray-500 font-medium">0%</span>
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            v-if="paymentPercentage >= 100"
                                                            class="text-emerald-600 font-semibold flex items-center gap-1"
                                                        >
                                                          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                          </svg>
                                                          Payment Complete
                                                        </span>
                                                        <span
                                                            v-else-if="paymentPercentage > 0 && paymentPercentage < 100"
                                                            class="text-blue-600 font-semibold"
                                                        >
                                                          {{ formatCurrency(remainingBalance) }} remaining
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="text-amber-600 font-semibold"
                                                        >
                                                          Payment pending
                                                        </span>
                                                    </div>
                                                    <span class="text-gray-500 font-medium">100%</span>
                                                </div>
                                            </div>

                                            <div class="mt-6 pt-6 border-t border-gray-100">
                                                <div class="grid grid-cols-2 gap-6">
                                                    <div class="space-y-2">
                                                        <p class="text-sm font-medium text-gray-600">Payment Ratio</p>
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                                            <span class="text-sm text-gray-700">
                                                            Paid: {{ ((totalPaid / (sale?.grand_total || 1)) * 100).toFixed(1) }}%
                                                          </span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                                            <span class="text-sm text-gray-700">
                                                                Outstanding: {{ ((remainingBalance / (sale?.grand_total || 1)) * 100).toFixed(1) }}%
                                                              </span>
                                                        </div>
                                                    </div>

                                                    <div class="space-y-2">
                                                        <p class="text-sm font-medium text-gray-600">Next Action</p>
                                                        <p
                                                            class="text-sm font-medium"
                                                            :class="remainingBalance > 0 ? 'text-amber-600' : 'text-emerald-600'"
                                                        >
                                                            {{ remainingBalance > 0 ? 'Follow up on outstanding payment' : 'Payment completed successfully' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 w-full">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Summary</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead class="bg-lp-green bg-opacity-20">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                                            <th class="px-4 py-3 text-left font-medium">TOTAL AMOUNT</th>
                                            <th class="px-4 py-3 text-left font-medium">DISCOUNT</th>
                                            <th class="px-4 py-3 text-left font-medium">TAX</th>
                                            <th class="px-4 py-3 text-left font-medium">SHIPPING</th>
                                            <th class="px-4 py-3 text-left font-medium">GRAND TOTAL</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ 1 }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(sale?.total_amount) }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(sale?.total_discount) }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(sale?.total_tax) }}</td>
                                            <td class="px-4 py-3">
                                                {{ formatCurrency(totalShippingCost) }}
                                            </td>
                                            <td class="px-4 py-3 font-semibold text-lp-green  rounded-r-md">
                                                {{ formatCurrency(sale?.grand_total) }}
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
const sale = ref(null)
const loading = ref(true)
const error = ref(null)
const referrer = ref('/sales/all') // Default referrer for sales

const saleId = computed(() => {
    return page.props.sale?.id ||
        page.props.route?.params?.id ||
        window.location.pathname.replace(/\/$/, '').split('/').pop()
})

const totalShippingCost = ref(0)


const totalPaid = computed(() => {
    if (!sale.value?.sale_payments) return 0
    return sale.value.sale_payments
        .filter(payment => payment.status === 'completed' || payment.status === 'confirmed' || payment.status === 'paid')
        .reduce((sum, payment) => sum + Number(payment.amount || 0), 0)
})

const remainingBalance = computed(() => {
    const grandTotal = Number(sale.value?.grand_total || 0)
    return Math.max(0, grandTotal - totalPaid.value)
})

const paymentPercentage = computed(() => {
    const grandTotal = Number(sale.value?.grand_total || 0)
    if (grandTotal === 0) return 0
    return Math.round((totalPaid.value / grandTotal) * 100)
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

const formatCurrency = (amount) => {
    if (amount === undefined || amount === null) return 'N/A'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount)
}

const capitalizeFirstLetter = (string) => {
    if (!string) return 'N/A'
    return string.charAt(0).toUpperCase() + string.slice(1).replace(/_/g, ' ')
}

const formatPaymentStatus = (status) => {
    if (!status) return 'N/A'
    return status.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const formatPaymentMethod = (method) => {
    if (!method) return 'N/A'
    return method.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const getStatusClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800'
        case 'confirmed':
            return 'bg-blue-100 text-blue-800'
        case 'shipped':
            return 'bg-purple-100 text-purple-800'
        case 'delivered':
            return 'bg-green-100 text-green-800'
        case 'cancelled':
            return 'bg-red-100 text-red-800'
        case 'processing':
            return 'bg-indigo-100 text-indigo-800';
        case 'in_transit':
            return 'bg-orange-100 text-orange-800';
        case 'on_hold':
            return 'bg-red-100 text-red-800';
        case 'delivered_partially':
            return 'bg-teal-100 text-teal-800';
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getPaymentStatusClass = (status) => {
    switch (status) {
        case 'paid':
        case 'completed':
        case 'confirmed':
            return 'bg-green-100 text-green-800'
        case 'partially_paid':
        case 'partial':
            return 'bg-blue-100 text-blue-800'
        case 'unpaid':
        case 'pending':
            return 'bg-red-100 text-red-800'
        case 'failed':
            return 'bg-red-100 text-red-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getShipmentStatusClass = (status) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'shipped':
            return 'bg-purple-100 text-purple-800';
        case 'delivered':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        case 'processing':
            return 'bg-indigo-100 text-indigo-800';
        case 'in_transit':
            return 'bg-orange-100 text-orange-800';
        case 'on_hold':
            return 'bg-red-100 text-red-800';
        case 'delivered_partially':
            return 'bg-teal-100 text-teal-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

const getOverallPaymentStatus = () => {
    if (remainingBalance.value === 0) {
        return 'Fully Paid'
    } else if (totalPaid.value > 0) {
        return 'Partially Paid'
    } else {
        return 'Unpaid'
    }
}

const getOverallPaymentStatusClass = () => {
    if (remainingBalance.value === 0) {
        return 'bg-green-100 text-green-800 border border-green-200'
    } else if (totalPaid.value > 0) {
        return 'bg-orange-100 text-orange-800 border border-orange-200'
    } else {
        return 'bg-red-100 text-red-800 border border-red-200'
    }
}
const formatVehicleType = (type) => {
    if (!type) return 'N/A';
    return type
        .split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};


onMounted(async () => {
    try {
        if(document.referrer.includes('/sales/restore')) {
            referrer.value = '/sales/restore'
        } else if(document.referrer.includes('/sales/all')){
            referrer.value = '/sales/all'
        } else if (localStorage.getItem('saleReferrer')){
            referrer.value = localStorage.getItem('saleReferrer')
        }

        localStorage.setItem('saleReferrer', referrer.value)

        if(!saleId.value || isNaN(saleId.value)){
            throw new Error('Invalid sale ID')
        }

        const token = localStorage.getItem('X-API-TOKEN')

        const response = await axios.get(`/api/sales/${saleId.value}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        console.log('API Response:', response)

        if(!response.data){
            throw new Error('Sale data not found in response')
        }

        sale.value = response.data.data

        if (sale.value.shipments && Array.isArray(sale.value.shipments)) {
            totalShippingCost.value = sale.value.shipments.reduce((total, shipment) => {
                return total + Number(shipment.shipping_cost || 0)
            }, 0)
        }


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

