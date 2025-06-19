<template>
    <div v-if="loading" class="py-10 text-gray-500">
        <div class="bg-gray-100">
            <sidebar>
                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">Purchase Return Detail</span>
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

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-28 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-28 bg-gray-200 rounded"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px]"></div>
                                </div>
                            </div>

                            <div class="mt-6 w-full">
                                <div class="h-5 w-40 bg-gray-200 rounded mb-2"></div> <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <div class="h-10 w-full bg-gray-200 rounded-t-[10px]"></div> <div class="h-12 w-full bg-gray-100 border-b border-gray-200"></div> <div class="h-12 w-full bg-gray-100 border-b border-gray-200"></div> <div class="h-12 w-full bg-gray-100 rounded-b-[10px]"></div>    </div>
                            </div>

                            <div class="mt-6 w-full">
                                <div class="h-5 w-20 bg-gray-200 rounded mb-2"></div> <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <div class="h-10 w-full bg-gray-200 rounded-t-[10px]"></div> <div class="h-12 w-full bg-gray-100 border-b border-gray-200"></div> <div class="h-12 w-full bg-gray-100 rounded-b-[10px]"></div>    </div>
                            </div>

                            <div class="mt-6 w-full">
                                <div class="h-5 w-28 bg-gray-200 rounded mb-2"></div> <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <div class="h-10 w-full bg-gray-200 rounded-t-[10px]"></div> <div class="h-12 w-full bg-gray-100 border-b border-gray-200"></div> <div class="h-12 w-full bg-gray-100 rounded-b-[10px]"></div>    </div>
                            </div>

                            <div class="mt-6 w-full">
                                <div class="flex items-center gap-2 mb-4"> <div class="w-5 h-5 bg-gray-300 rounded-full"></div>
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
                                <div class="h-5 w-24 bg-gray-200 rounded mb-2"></div> <div class="overflow-x-auto rounded-lg border border-gray-200">
                                <div class="h-10 w-full bg-gray-200 rounded-t-[10px]"></div> <div class="h-12 w-full bg-gray-100 rounded-b-[10px]"></div>    </div>
                            </div>
                        </div>
                        <div class="w-[186px] h-[48px] bg-gray-300 rounded-[10px]"></div> </div>
                </div>
            </sidebar>
        </div>
    </div>

    <div v-else>
        <div class="bg-gray-100">
            <Sidebar>
                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">Purchase Return Detail</span>
                </div>

                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Invoice Purchase</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v2H7V5zm0 4h6v2H7V9zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                            {{ purchaseReturn?.invoice_number ?? '-' }}
                                        </p>
                                    </div>
                                </div>


                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Return Date</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                      <span class="text-[#2E2E2E] text-[15px] leading-6">
                                        {{ formatDate(purchaseReturn?.return_date) ?? '-' }}
                                      </span>
                                    </div>
                                </div>


                            </div>

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Invoice Return</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v2H7V5zm0 4h6v2H7V9zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                            {{ purchaseReturn?.invoice_number_returns ?? '-' }}
                                        </p>
                                    </div>
                                </div>


                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Delivery Number Return</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v2H7V5zm0 4h6v2H7V9zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                            {{ purchaseReturn?.delivery_number_returns ?? '-' }}
                                        </p>
                                    </div>
                                </div>


                            </div>


                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Actual Arrival Date</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                        {{ formatDate(purchaseReturn?.actual_arrival_date) ?? '-' }}
                                      </span>
                                    </div>
                                </div>


                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">Estimated Arrival Date</p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">

                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                        {{ formatDate(purchaseReturn?.estimated_arrival_date) ?? '-' }}
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
                                                getStatusClass(purchaseReturn?.status)
                                            ]"
                                        >
                                            {{ capitalizeFirstLetter(purchaseReturn?.status) }}
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
                                                getPaymentStatusClass(purchaseReturn?.payment_status)
                                            ]"
                                        >
                                            {{ formatPaymentStatus(purchaseReturn?.payment_status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Compensation Method
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ capitalizeFirstLetter(purchaseReturn?.compensation_method) ?? '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                        <p class="text-[#000000] text-sm font-medium leading-6">
                                            Reason
                                        </p>
                                        <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5L6 11H5a1 1 0 000 2h1.282l2.576 4.114A1 1 0 0010 17h2.828l1.414 1.414a1 1 0 001.414-1.414L15.414 15H17a1 1 0 000-2h-1.282l-2.576-4.114A1 1 0 0010 7z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ purchaseReturn?.reason ?? '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>


                            <div class="mt-6 w-full">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Returned Products</h3>
                                <div class="overflow-x-auto rounded-lg border border-gray-200">
                                    <table class="w-full">
                                        <thead class="bg-lp-green bg-opacity-20">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-medium">NO.</th>
                                            <th class="px-4 py-3 text-left font-medium">PRODUCT</th>
                                            <th class="px-4 py-3 text-left font-medium">QUANTITY</th>
                                            <th class="px-4 py-3 text-left font-medium">UNIT PRICE</th>
                                            <th class="px-4 py-3 text-left font-medium">SUBTOTAL</th>
                                            <th class="px-4 py-3 text-left font-medium">NOTE</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(detail, index) in purchaseReturn?.purchase_details" :key="detail.id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">{{ detail.product_name || 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ detail.quantity }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(detail.unit_price) }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(detail.sub_total) }}</td>
                                            <td class="px-4 py-3">{{ detail.note || '-' }}</td>
                                        </tr>
                                        <tr v-if="!purchaseReturn?.purchase_details || purchaseReturn.purchase_details.length === 0">
                                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">No returned products.</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-6 w-full" v-if="purchaseReturn?.taxes && purchaseReturn.taxes.length > 0">
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
                                        <tr v-for="(tax, index) in purchaseReturn.taxes" :key="tax.id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3">{{ index + 1 }}</td>
                                            <td class="px-4 py-3">{{ tax.name || 'N/A' }}</td>
                                            <td class="px-4 py-3">{{ tax.rate ?? 'N/A' }}%</td>
                                            <td class="px-4 py-3">{{ formatCurrency(tax?.pivot?.amount ?? tax.amount) }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-6 w-full" v-if="purchaseReturn?.purchase_return_payments && purchaseReturn.purchase_return_payments.length > 0">
                                <h3 class="text-[#000000] text-sm font-medium mb-2">Refunds/Payments</h3>
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
                                        <tr v-for="(payment, index) in purchaseReturn.purchase_return_payments" :key="payment.id" class="border-b hover:bg-gray-50">
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

                            <div class="mt-6 w-full mx-auto">
                                <div class="mb-6">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="p-2 bg-emerald-100 rounded-lg">
                                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ isRefundScenario ? 'Return Refund Summary' : 'Replacement Compensation Summary' }}
                                        </h3>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        {{ isRefundScenario ? 'Overview of refund status and financial breakdown' : 'Overview of costs for replacement and compensation status' }}
                                    </p>
                                </div>

                                <div class="border-0 shadow-lg bg-gradient-to-br from-emerald-50 via-white to-emerald-50 rounded-lg">
                                    <div class="p-8">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                            <div class="border border-blue-200 bg-white shadow-sm hover:shadow-md transition-shadow rounded-lg">
                                                <div class="p-6">
                                                    <div class="flex items-start justify-between">
                                                        <div class="space-y-2">
                                                            <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">
                                                                {{ isRefundScenario ? 'Total Refund Amount' : 'Total Compensation Cost' }}
                                                            </p>
                                                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(effectiveTotalAmount) }}</p>
                                                            <p class="text-xs text-gray-500">
                                                                {{ isRefundScenario ? 'Total amount to be refunded' : 'Total cost for replacement (shipping)' }}
                                                            </p>
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
                                                            <p class="text-sm font-medium text-emerald-600 uppercase tracking-wide">
                                                                {{ isRefundScenario ? 'Total Refunded' : 'Total Paid by Customer' }}
                                                            </p>
                                                            <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(totalMoneyTransferred) }}</p>
                                                            <p class="text-xs text-gray-500">
                                                                {{ isRefundScenario ? 'Amount already paid back' : 'Amount paid for replacement' }}
                                                            </p>
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
                                                            :style="{ width: Math.min(progressPercentage, 100) + '%' }"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="bg-white shadow-sm hover:shadow-md transition-shadow rounded-lg border"
                                                :class="remainingAmount > 0 ? 'border-amber-200' : 'border-emerald-200'"
                                            >
                                                <div class="p-6">
                                                    <div class="flex items-start justify-between">
                                                        <div class="space-y-2">
                                                            <p
                                                                class="text-sm font-medium uppercase tracking-wide"
                                                                :class="remainingAmount > 0 ? 'text-amber-600' : 'text-emerald-600'"
                                                            >
                                                                {{ isRefundScenario ? 'Outstanding Refund' : 'Outstanding Payment' }}
                                                            </p>
                                                            <p
                                                                class="text-2xl font-bold"
                                                                :class="remainingAmount > 0 ? 'text-gray-900' : 'text-emerald-600'"
                                                            >
                                                                {{ formatCurrency(remainingAmount) }}
                                                            </p>
                                                            <p class="text-xs text-gray-500">
                                                                {{ isRefundScenario ? 'Amount still to be refunded' : 'Amount still to be paid by customer' }}
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="p-3 rounded-xl"
                                                            :class="remainingAmount > 0 ? 'bg-amber-50' : 'bg-emerald-50'"
                                                        >
                                                            <svg
                                                                v-if="remainingAmount > 0"
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
                                                            :class="remainingAmount > 0 ? 'bg-amber-500' : 'bg-emerald-500'"
                                                            :style="{ width: (remainingAmount > 0 ? (100 - progressPercentage) : 100) + '%' }"
                                                        ></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                                            <div class="flex items-center justify-between mb-4">
                                                <div class="flex items-center gap-3">
                                                    <h4 class="text-base font-semibold text-gray-900">
                                                        {{ isRefundScenario ? 'Refund Progress' : 'Compensation Progress' }}
                                                    </h4>
                                                    <span
                                                        class="text-xs font-medium px-2 py-1 rounded-full"
                                                        :class="getOverallProgressStatusClass()">
                                                    {{ getOverallProgressStatus() }}
                                                  </span>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-2xl font-bold text-gray-900">{{ progressPercentage }}%</p>
                                                    <p class="text-xs text-gray-500">Completed</p>
                                                </div>
                                            </div>

                                            <div class="space-y-3">
                                                <div class="w-full bg-gray-100 rounded-full h-3">
                                                    <div
                                                        class="h-3 rounded-full transition-all duration-300 ease-in-out"
                                                        :class="progressPercentage >= 100 ? 'bg-emerald-500' : 'bg-blue-500'"
                                                        :style="{ width: Math.min(progressPercentage, 100) + '%' }"
                                                    ></div>
                                                </div>

                                                <div class="flex justify-between items-center text-xs">
                                                    <span class="text-gray-500 font-medium">0%</span>
                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            v-if="progressPercentage >= 100"
                                                            class="text-emerald-600 font-semibold flex items-center gap-1"
                                                        >
                                                          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                          </svg>
                                                          {{ isRefundScenario ? 'Refund Complete' : 'Compensation Complete' }}
                                                        </span>
                                                        <span
                                                            v-else-if="progressPercentage > 0 && progressPercentage < 100"
                                                            class="text-blue-600 font-semibold"
                                                        >
                                                          {{ formatCurrency(remainingAmount) }} remaining
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="text-amber-600 font-semibold"
                                                        >
                                                          {{ isRefundScenario ? 'Refund pending' : 'Payment pending' }}
                                                        </span>
                                                    </div>
                                                    <span class="text-gray-500 font-medium">100%</span>
                                                </div>
                                            </div>

                                            <div class="mt-6 pt-6 border-t border-gray-100">
                                                <div class="grid grid-cols-2 gap-6">
                                                    <div class="space-y-2">
                                                        <p class="text-sm font-medium text-gray-600">
                                                            {{ isRefundScenario ? 'Refund Ratio' : 'Payment Ratio' }}
                                                        </p>
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                                            <span class="text-sm text-gray-700">
                                                            {{ isRefundScenario ? 'Refunded' : 'Paid' }}: {{ ((totalMoneyTransferred / (effectiveTotalAmount || 1)) * 100).toFixed(1) }}%
                                                          </span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-3 h-3 bg-gray-300 rounded-full"></div>
                                                            <span class="text-sm text-gray-700">
                                                                Outstanding: {{ ((remainingAmount / (effectiveTotalAmount || 1)) * 100).toFixed(1) }}%
                                                              </span>
                                                        </div>
                                                    </div>

                                                    <div class="space-y-2">
                                                        <p class="text-sm font-medium text-gray-600">Next Action</p>
                                                        <p
                                                            class="text-sm font-medium"
                                                            :class="remainingAmount > 0 ? 'text-amber-600' : 'text-emerald-600'"
                                                        >
                                                            {{ remainingAmount > 0 ? (isRefundScenario ? 'Process outstanding refund' : 'Follow up on outstanding payment') : (isRefundScenario ? 'Refund completed successfully' : 'Compensation completed successfully') }}
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
                                            <td class="px-4 py-3">{{ formatCurrency(purchaseReturn?.total_amount) }}</td>
                                            <td class="px-4 py-3">{{ formatCurrency(purchaseReturn?.total_discount) }}</td>
                                            <td class="px-4 py-3">
                                                {{ formatCurrency(purchaseReturn?.taxes?.reduce((sum, tax) => sum + Number(tax.pivot?.amount ?? tax.amount ?? 0), 0)) }}
                                            </td>
                                            <td class="px-4 py-3">{{ formatCurrency(purchaseReturn?.shipping_amount) }}</td>
                                            <td class="px-4 py-3 font-semibold text-lp-green  rounded-r-md">
                                                {{ formatCurrency(purchaseReturn?.grand_total) }}
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
const purchaseReturn = ref(null)
const loading = ref(true)
const error = ref(null)
const referrer = ref('/purchase-returns/all')

const purchaseReturnId = computed(() => {
    return page.props.purchaseReturn?.id ||
        page.props.route?.params?.id ||
        window.location.pathname.replace(/\/$/, '').split('/').pop()
})


const isRefundScenario = computed(() => {
    return purchaseReturn.value?.compensation_method === 'refund';
});


const effectiveTotalAmount = computed(() => {
    if (!purchaseReturn.value) return 0;

    const totalAmountFromDetails = Number(purchaseReturn.value.total_amount || 0);
    const totalDiscount = Number(purchaseReturn.value.total_discount || 0);
    const shippingAmount = Number(purchaseReturn.value.shipping_amount || 0);
    const totalTaxes = purchaseReturn.value.taxes?.reduce((sum, tax) => sum + Number(tax.pivot?.amount ?? tax.amount ?? 0), 0) || 0;

    if (isRefundScenario.value) {
        return totalAmountFromDetails - totalDiscount + shippingAmount + totalTaxes;
    } else {
        return shippingAmount;
    }
});

const totalMoneyTransferred = computed(() => {
    if (!purchaseReturn.value?.purchase_return_payments) return 0
    return purchaseReturn.value.purchase_return_payments
        .filter(payment => payment.status === 'paid' || payment.status === 'completed' || payment.status === 'refunded')
        .reduce((sum, payment) => sum + Number(payment.amount || 0), 0)
});

const remainingAmount = computed(() => {
    return Math.max(0, effectiveTotalAmount.value - totalMoneyTransferred.value);
});

const progressPercentage = computed(() => {
    if (effectiveTotalAmount.value === 0) return 0;
    return Math.round((totalMoneyTransferred.value / effectiveTotalAmount.value) * 100);
});

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
        case 'approved':
            return 'bg-blue-100 text-blue-800'
        case 'completed':
            return 'bg-green-100 text-green-800'
        case 'rejected':
            return 'bg-red-100 text-red-800'
        case 'cancelled':
            return 'bg-gray-100 text-gray-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getPaymentStatusClass = (status) => {
    switch (status) {
        case 'paid':
        case 'refunded':
            return 'bg-green-100 text-green-800'
        case 'partially_paid':
        case 'pending_refund':
            return 'bg-blue-100 text-blue-800'
        case 'unpaid':
            return 'bg-red-100 text-red-800'
        case 'failed':
            return 'bg-red-100 text-red-800'
        case 'no_refund':
            return 'bg-gray-100 text-gray-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getOverallProgressStatus = () => {
    if (remainingAmount.value === 0 && effectiveTotalAmount.value > 0) {
        return 'Completed';
    } else if (totalMoneyTransferred.value > 0) {
        return 'Partially Completed';
    } else if (effectiveTotalAmount.value === 0 && totalMoneyTransferred.value === 0) {
        return 'No Amount Needed';
    }
    return 'Pending';
};

const getOverallProgressStatusClass = () => {
    if (remainingAmount.value === 0 && effectiveTotalAmount.value > 0) {
        return 'bg-green-100 text-green-800 border border-green-200';
    } else if (totalMoneyTransferred.value > 0) {
        return 'bg-orange-100 text-orange-800 border border-orange-200';
    } else if (effectiveTotalAmount.value === 0 && totalMoneyTransferred.value === 0) {
        return 'bg-gray-100 text-gray-800 border border-gray-200';
    }
    return 'bg-red-100 text-red-800 border border-red-200';
};


onMounted(async () => {
    try {
        if(document.referrer.includes('/purchase-returns/restore')) {
            referrer.value = '/purchase-returns/restore'
        } else if(document.referrer.includes('/purchase-returns')){
            referrer.value = '/purchase-returns'
        } else if (localStorage.getItem('purchaseReturnReferrer')){
            referrer.value = localStorage.getItem('purchaseReturnReferrer')
        }

        localStorage.setItem('purchaseReturnReferrer', referrer.value)

        if(!purchaseReturnId.value || isNaN(purchaseReturnId.value)){
            throw new Error('Invalid purchase return ID')
        }

        const token = localStorage.getItem('X-API-TOKEN')

        const response = await axios.get(`/api/purchase-returns/${purchaseReturnId.value}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        console.log('API Response:', response)

        if(!response.data){
            throw new Error('Purchase return data not found in response')
        }

        purchaseReturn.value = response.data.data

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
