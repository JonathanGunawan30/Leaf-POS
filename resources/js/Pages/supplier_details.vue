<template>

    <div v-if="loading" class="py-10 text-gray-500">
        <div class="bg-gray-100">
            <sidebar>
                <div class="bg-white rounded-lg p-5 mb-4 shadow-sm">
                    <span class="text-black font-medium">Customer Detail</span>
                </div>

                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-white rounded-[10px] animate-pulse">
                    <div class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                </div>

                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                </div>

                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                </div>

                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-between items-start flex-row gap-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                </div>

                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                </div>
                            </div>

                            <div class="flex self-stretch justify-start items-start flex-row gap-6 mt-4">
                                <div class="flex flex-1 flex-col gap-2.5">
                                    <div class="flex flex-col gap-[5px]">
                                        <div class="h-5 w-24 bg-gray-200 rounded animate-pulse"></div>
                                        <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                    </div>

                                    <div class="flex flex-col gap-[5px] mt-2">
                                        <div class="h-5 w-32 bg-gray-200 rounded animate-pulse"></div>
                                        <div class="w-full h-[213px] bg-gray-200 rounded-[10px] animate-pulse"></div>
                                        <div class="h-4 w-3/4 bg-gray-200 rounded animate-pulse mt-1"></div>
                                    </div>
                                </div>

                                <div class="flex flex-1 flex-col gap-2.5">
                                    <div class="flex flex-col gap-[5px]">
                                        <div class="h-5 w-20 bg-gray-200 rounded animate-pulse"></div>
                                        <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                    </div>
                                    <div class="flex flex-col gap-[5px]">
                                        <div class="h-5 w-16 bg-gray-200 rounded animate-pulse"></div>
                                        <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                    </div>
                                    <div class="flex flex-col gap-[5px]">
                                        <div class="h-5 w-16 bg-gray-200 rounded animate-pulse"></div>
                                        <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                    </div>
                                    <div class="flex flex-col gap-[5px]">
                                        <div class="h-5 w-28 bg-gray-200 rounded animate-pulse"></div>
                                        <div class="h-[45px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-row gap-2.5 mt-4">
                                <div class="flex flex-1 flex-col gap-[5px]">
                                    <div class="h-5 w-20 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-[120px] w-full bg-gray-200 rounded-[10px] animate-pulse"></div>
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
                    <span class="text-black font-medium">Customer Detail</span>
                </div>

                <div class="flex justify-start items-start flex-col gap-2.5 py-[15px] px-[58px] bg-[#FFFFFF] rounded-[10px]">
                    <div
                        class="flex self-stretch justify-start items-start flex-col gap-[30px]">
                        <div class="flex self-stretch justify-start items-start flex-col gap-2.5">
                            <div
                                class="flex self-stretch justify-start items-start flex-col gap-2.5">
                                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">Name</p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div
                                            class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                            <p class="flex-1 text-[#2E2E2E] text-[15px] leading-6">
                                                {{ supplier?.name ?? '-' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">Company Name</p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v2H7V5zm0 4h6v2H7V9zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                          <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{ supplier?.company_name ?? '-' }}
                                          </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="flex self-stretch justify-between items-start flex-row gap-4">

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">
                                                Email
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{supplier?.email ?? '-'}}
                                        </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">
                                                Phone
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{supplier?.phone ?? '-'}}
                                        </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="flex self-stretch justify-between items-start flex-row gap-4">

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">
                                                Bank Account
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 2a2 2 0 00-2 2v2H5a2 2 0 000 4h3v2H6a2 2 0 100 4h2v2a2 2 0 104 0v-2h2a2 2 0 100-4h-2v-2h3a2 2 0 000-4h-3V4a2 2 0 00-2-2z"/>
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                            <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                {{ supplier?.bank_account ?? '-' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">
                                                Bank Name
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 3a1 1 0 011-1h10a1 1 0 011 1v2H4V3zM3 7h14v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7zm3 2v2h2V9H6zm3 0v2h2V9H9z"/>
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                            <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                {{ supplier?.bank_name ?? '-' }}
                                            </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="flex self-stretch justify-between items-start flex-row gap-4">

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">
                                                Country
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{supplier?.country ?? '-'}}
                                        </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">
                                                Province
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{supplier?.province ?? '-'}}
                                        </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="flex self-stretch justify-between items-start flex-row gap-4">

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">
                                                City
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{supplier?.city ?? '-'}}
                                        </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="text-[#000000] text-sm font-medium leading-6">
                                                Postal Code
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm0 2h8v12H6V4z" clip-rule="evenodd" />
                                                <path d="M8 6h4v2H8V6zM8 10h4v2H8v-2z" />
                                            </svg>
                                        </div>
                                        <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6">
                                            {{supplier?.postal_code ?? '-'}}
                                        </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="flex self-stretch justify-start items-start flex-row gap-6">

                                    <div class="flex flex-1 justify-start items-start flex-col gap-2.5">

                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">

                                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                                <p class="self-stretch text-[#000000] text-sm font-medium leading-6">
                                                    Address
                                                </p>
                                                <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm3 2h6v2H7V4zm0 4h6v2H7V8zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>


                                            <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                                <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                    {{ supplier?.address ?? '-' }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Google Maps Section -->
                                        <div v-if="supplier?.address" class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                                <p class="text-[#000000] text-sm font-medium leading-6">
                                                    Location Map
                                                </p>
                                                <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="w-full bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] overflow-hidden shadow-sm">
                                                <iframe
                                                    width="100%"
                                                    height="300"
                                                    frameborder="0"
                                                    style="border:0; border-radius: 10px; max-height: 213px;"
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                    :src="`https://www.google.com/maps/embed/v1/place?q=${encodeURIComponent(getFullAddress())}&key=${googleMapsKey}`"
                                                    allowfullscreen>
                                                </iframe>
                                            </div>
                                            <p class="text-[#666666] text-xs leading-4 mt-1">
                                                {{ getFullAddress() }}
                                            </p>
                                        </div>

                                    </div>

                                    <div class="flex flex-1 justify-start items-start flex-col gap-2.5">

                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                                <p class="text-[#000000] text-sm font-medium leading-6">
                                                    NPWP
                                                </p>
                                                <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2v8h12V6H4z" clip-rule="evenodd" />
                                                    <path d="M6 8h8v2H6V8z" />
                                                </svg>
                                            </div>
                                            <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                                <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                    {{ supplier?.npwp_number ?? '-' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                                <p class="text-[#000000] text-sm font-medium leading-6">
                                                    NIB
                                                </p>
                                                <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm0 2h8v12H6V4z" clip-rule="evenodd" />
                                                    <path d="M8 6h4v2H8V6zM8 10h4v2H8v-2z" />
                                                </svg>
                                            </div>
                                            <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                                <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                    {{ supplier?.nib_number ?? '-' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                                <p class="text-[#000000] text-sm font-medium leading-6">
                                                    SIUP
                                                </p>
                                                <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                                <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                    {{ supplier?.siup_number ?? '-' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                            <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                                <p class="text-[#000000] text-sm font-medium leading-6">
                                                    Business Type
                                                </p>
                                                <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm3 2h6v2H7V4zm0 4h6v2H7V8zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="h-[45px] flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px]">
                                                <span class="text-[#2E2E2E] text-[15px] leading-6">
                                                    {{ supplier?.business_type ?? '-' }}
                                                </span>
                                            </div>
                                        </div>

                                    </div>

                                </div>


                                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <div class="flex self-stretch justify-start items-start flex-row gap-[5px]">
                                            <p class="self-stretch text-[#000000] text-sm font-medium leading-6">
                                                Notes
                                            </p>
                                            <svg class="w-4 h-4 text-[#2F8451] mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 2a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2H4zm3 2h6v2H7V4zm0 4h6v2H7V8zm0 4h6v2H7v-2z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="h-[120px] flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border-solid border-[#AEADAD] border rounded-[10px] overflow-y-auto">
                                        <span class="text-[#2E2E2E] text-[15px] leading-6 whitespace-pre-wrap">
                                            {{ supplier?.note ?? '-' }}
                                        </span>
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

<script setup>

import {ref, onMounted, computed} from 'vue'
import axios from 'axios'
import {usePage, router} from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const page = usePage()
const supplier = ref(null)
const loading = ref(true)
const error = ref(null)
const referrer = ref('/suppliers/all')

const googleMapsKey = ref(import.meta.env.VITE_GOOGLE_MAPS_API_KEY)

const supplierId = computed(() => {
    return page.props.supplier?.id ||
        page.props.route?.params?.id ||
        window.location.pathname.replace(/\/$/, '').split('/').pop()
})

const getFullAddress = () => {
    if (!supplier.value) return 'Tangerang, Indonesia'

    const addressParts = [
        supplier.value.address,
        supplier.value.city,
        supplier.value.province,
        supplier.value.country
    ].filter(Boolean)

    return addressParts.length > 0 ? addressParts.join(', ') : 'Tangerang, Indonesia'
}

const googleMapsUrl = computed(() => {
    const address = getFullAddress()
    return `https://www.google.com/maps/embed/v1/place?q=${encodeURIComponent(address)}&key=${googleMapsKey.value}`
})

const openInGoogleMaps = () => {
    const address = getFullAddress()
    const url = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(address)}`
    window.open(url, '_blank')
}

onMounted(async () => {
    try {
        if(document.referrer.includes('/suppliers/restore')) {
            referrer.value = '/suppliers/restore'
        } else if(document.referrer.includes('/suppliers/all')){
            referrer.value = '/suppliers/all'
        } else if (localStorage.getItem('supplierReferrer')){
            referrer.value = localStorage.getItem('supplierReferrer')
        }

        localStorage.setItem('supplierReferrer', referrer.value)

        if(!supplierId.value || isNaN(supplierId.value)){
            throw new Error('Invalid supplier ID')
        }

        const token = localStorage.getItem('X-API-TOKEN')

        const response = await axios.get(`/api/suppliers/${supplierId.value}`, {
            headers: {
                'Authorization': `Bearer ${token}`
            }
        })
        console.log('API Response:', response)

        if(!response.data){
            throw new Error('Customer data not found in response')
        }

        supplier.value = response.data.data

        console.log('Customer address:', getFullAddress())

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
