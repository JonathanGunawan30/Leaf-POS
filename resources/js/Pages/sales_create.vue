<template>
    <div class="bg-gray-100">
        <sidebar>
            <div class="flex justify-start items-start flex-col gap-2.5">
                <div class="flex self-stretch justify-start items-center flex-row gap-2.5 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <span class="text-[#000000] font-medium leading-6">Create Sale</span>
                </div>

                <div class="flex self-stretch justify-start items-stretch flex-row gap-2.5">

                    <div class="flex flex-1 flex-col">

                        <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Sale Information</p>
                            <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                                <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Sale Date <span class="text-red-500">*</span></p>
                                        <input v-model="saleForm.sale_date" type="date"
                                               class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                               required/>
                                    </div>
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Total Discount <span class="text-red-500">*</span></p>
                                        <input v-model="formattedTotalDiscount" type="text"
                                               class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                               placeholder="Enter total discount" required/>
                                    </div>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Status <span class="text-red-500">*</span></p>
                                    <select v-model="saleForm.status" required
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                        <option value="" disabled selected>Select status</option>
                                        <option value="indent">Indent</option>
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="shipped">Shipped</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Note</p>
                                    <textarea v-model="saleForm.note" placeholder="Enter sale note"
                                              class="flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[80px] text-[#000000] text-[15px] leading-6 outline-none resize-none w-full"></textarea>
                                </div>
                            </div>


                        </div>

                        <div class="flex w-full justify-start items-start mt-2.5 flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Important Dates</p>
                            <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                                <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Due Date</p>
                                    <input v-model="saleForm.due_date" type="date"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                                </div>
                            </div>
                        </div>

                    </div>




                    <div class="flex flex-1 flex-col gap-2.5">
                        <div class="flex w-full justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Customer</p>
                            <div class="flex self-stretch w-full justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                                <div class="flex self-stretch w-full justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Select Customer <span class="text-red-500">*</span></p>
                                    <select
                                        v-model="saleForm.customer_id"
                                        required
                                        class="flex w-full justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                    >
                                        <option value="" disabled selected>Select customer</option>
                                        <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                            {{ customer.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="flex self-stretch w-full justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Customer Address</p>
                                    <iframe
                                        width="100%"
                                        height="250"
                                        frameborder="0"
                                        style="border:0; border-radius: 10px"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        :src="googleMapsSrc"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                        </div>



                        <div class="flex w-full justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                            <p class="self-stretch text-[#000000] font-medium leading-6">Shipping Details</p>
                            <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                                <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Calculated Shipping Distance (KM)</p>
                                        <input :value="formattedShippingDistance" type="text" readonly
                                               class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-gray-100 border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none cursor-not-allowed"
                                               placeholder="Distance"/>
                                    </div>
                                    <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                        <p class="self-stretch text-[#000000] text-xs leading-6">Calculated Shipping Amount</p>
                                        <input :value="formattedShippingAmount" type="text" readonly
                                               class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-gray-100 border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none cursor-not-allowed"
                                               placeholder="Shipping Amount"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex justify-between items-center w-full">
                        <p class="text-[#000000] font-medium leading-6">Products</p>
                        <button @click="addProduct" type="button"
                                class="flex justify-center items-center gap-2.5 py-[5px] px-[15px] bg-[#2F8451] hover:bg-[#256F43] rounded-[10px] transition-all duration-200">
                            <span class="text-white text-sm font-medium leading-6">+ Add Product</span>
                        </button>
                    </div>

                    <div v-if="saleForm.sale_details.length === 0"
                         class="flex justify-center items-center p-[40px] border-2 border-dashed border-gray-300 rounded-[10px] w-full">
                        <p class="text-gray-500">No products added yet. Click "Add Product" to start.</p>
                    </div>

                    <div v-for="(product, index) in saleForm.sale_details" :key="index"
                         class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                        <div class="flex justify-between items-center w-full">
                            <h4 class="text-[#000000] font-medium">Product {{ index + 1 }}</h4>
                            <button @click="removeProduct(index)" type="button"
                                    class="text-red-500 hover:text-red-700 font-medium">Remove</button>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Product <span class="text-red-500">*</span></p>
                                <select v-model="product.product_id" required
                                        @change="updateSalePrice(index)" class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select product</option>
                                    <option v-for="prod_option in products" :key="prod_option.id" :value="prod_option.id">
                                        {{ prod_option.name }} </option>
                                </select>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Quantity <span class="text-red-500">*</span></p>
                                <input v-model.number="product.quantity" type="number" min="1" required
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       placeholder="Enter quantity"/>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Sale Price (Each)</p>
                                <input :value="formatToRupiah(product.selling_price)" type="text" readonly
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-gray-100 border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none cursor-not-allowed"
                                       placeholder="Price"/>
                            </div>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                            <p class="self-stretch text-[#000000] text-xs leading-6">Note</p>
                            <textarea v-model="product.note" placeholder="Enter product note"
                                      class="flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[80px] text-[#000000] text-[15px] leading-6 outline-none resize-none w-full"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex self-stretch justify-start items-start flex-row gap-2.5">
                    <div class="flex flex-1 justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                        <p class="self-stretch text-[#000000] font-medium leading-6">Payment Information</p>
                        <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Payment Date <span class="text-red-500">*</span></p>
                                    <input v-model="saleForm.sale_payments[0].payment_date" type="date"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none" required/>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Amount <span class="text-red-500">*</span></p>
                                    <input v-model="formattedPaymentAmount" type="text"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                           placeholder="Enter payment amount" required/>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Payment Due Date</p>
                                    <input v-model="saleForm.sale_payments[0].due_date" type="date"
                                           class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Payment Method <span class="text-red-500">*</span></p>
                                    <select v-model="saleForm.sale_payments[0].payment_method" required
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                        <option value="" disabled selected>Select payment method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="giro">Giro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex self-stretch justify-start items-row gap-6">
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                    <p class="self-stretch text-[#000000] text-xs leading-6">Payment Status <span class="text-red-500">*</span></p>
                                    <select v-model="saleForm.sale_payments[0].status" required
                                            class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                        <option value="" disabled selected>Select payment status</option>
                                        <option value="unpaid">Unpaid</option>
                                        <option value="paid">Paid</option>
                                        <option value="failed">Failed</option>
                                    </select>
                                </div>
                                <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                </div>
                            </div>
                            <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Payment Note</p>
                                <textarea v-model="saleForm.sale_payments[0].note" placeholder="Enter payment note"
                                          class="flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[80px] text-[#000000] text-[15px] leading-6 outline-none resize-none w-full"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] rounded-[10px]">
                    <div class="flex justify-between items-center w-full">
                        <p class="text-[#000000] font-medium leading-6">Shipments</p>
                        <button @click="addShipment" type="button"
                                class="flex justify-center items-center gap-2.5 py-[5px] px-[15px] bg-[#2F8451] hover:bg-[#256F43] rounded-[10px] transition-all duration-200">
                            <span class="text-white text-sm font-medium leading-6">+ Add Shipment</span>
                        </button>
                    </div>

                    <div v-if="saleForm.shipments.length === 0" class="flex justify-center w-full items-center p-[40px] border-2 border-dashed border-gray-300 rounded-[10px]">
                        <p class="text-gray-500">No shipments added yet. Click "Add Shipment" to start.</p>
                    </div>

                    <div v-for="(shipment, index) in saleForm.shipments" :key="index"
                         class="flex self-stretch justify-start items-start flex-col gap-4 p-[20px] bg-[#FFFFFF] border rounded-[10px]">
                        <div class="flex justify-between items-center w-full">
                            <h4 class="text-[#000000] font-medium">Shipment {{ index + 1 }}</h4>
                            <button @click="removeShipment(index)" type="button"
                                    class="text-red-500 hover:text-red-700 font-medium">Remove</button>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Courier <span class="text-red-500">*</span></p>
                                <select v-model="shipment.courier_id" required
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select courier</option>
                                    <option v-for="courier in couriers" :key="courier.id" :value="courier.id">
                                        {{ courier.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Vehicle Type <span class="text-red-500">*</span></p>
                                <select v-model="shipment.vehicle_type" required
                                        class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                    <option value="" disabled selected>Select vehicle type</option>
                                    <option value="motorcycle">Motorcycle</option>
                                    <option value="car_sedan">Car Sedan</option>
                                    <option value="car_van">Car Van</option>
                                    <option value="car_pickup">Car Pickup</option>
                                    <option value="truck_small">Truck Small</option>
                                    <option value="truck_medium">Truck Medium</option>
                                    <option value="truck_large">Truck Large</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Vehicle Number <span class="text-red-500">*</span></p>
                                <input v-model="shipment.vehicle_number" type="text" required
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"
                                       placeholder="Enter vehicle number"/>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Shipping Date <span class="text-red-500">*</span></p>
                                <input v-model="shipment.shipping_date" type="date" required
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                            </div>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-row gap-6">
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Estimated Arrival Date <span class="text-red-500">*</span></p>
                                <input v-model="shipment.estimated_arrival_date" type="date" required
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                            </div>
                            <div class="flex flex-1 justify-start items-start flex-col gap-[5px]">
                                <p class="self-stretch text-[#000000] text-xs leading-6">Actual Arrival Date</p>
                                <input v-model="shipment.actual_arrival_date" type="date"
                                       class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none"/>
                            </div>
                        </div>

                        <div class="flex self-stretch justify-start items-col flex-col gap-[5px]">
                            <p class="self-stretch text-[#000000] text-xs leading-6">Status <span class="text-red-500">*</span></p>
                            <select v-model="shipment.status" required
                                    class="flex self-stretch justify-start items-center flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[45px] text-[#000000] text-[15px] leading-6 outline-none">
                                <option value="" disabled selected>Select status</option>
                                <option value="pending">Pending</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="processing">Processing</option>
                                <option value="in transit">In Transit</option>
                                <option value="on hold">On Hold</option>
                                <option value="delivered partially">Delivered Partially</option>
                            </select>
                        </div>

                        <div class="flex self-stretch justify-start items-start flex-col gap-[5px]">
                            <p class="self-stretch text-[#000000] text-xs leading-6">Note</p>
                            <textarea v-model="shipment.note" placeholder="Enter shipment note"
                                      class="flex self-stretch justify-start items-start flex-row gap-2.5 py-[9px] px-[15px] bg-[#FFFFFF] border border-gray-300 rounded-[10px] h-[80px] text-[#000000] text-[15px] leading-6 outline-none resize-none w-full"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex self-stretch justify-start items-center pt-4">
                    <button @click="createSale" :disabled="loading"
                            class="flex justify-center items-center gap-2.5 py-[5px] px-[30px] bg-[#2F8451] hover:!bg-[#256F43] rounded-[10px] w-[186px] h-[48px] transition-all duration-200 hover:scale-105">
                        <span v-if="!loading" class="text-white text-sm font-medium leading-6">Create Sale</span>
                        <div v-else class="flex items-center justify-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-white text-sm font-medium leading-6">Processing...</span>
                        </div>
                    </button>
                </div>
            </div>
        </sidebar>
    </div>
</template>

<script setup>
import Sidebar from '@/Components/Sidebar.vue'
import {onMounted, ref, computed, watch} from 'vue'
import axios from 'axios'
import Swal from "sweetalert2";
const googleMapsKey = import.meta.env.VITE_Maps_API_KEY;

const token = localStorage.getItem('X-API-TOKEN')

const api = axios.create({
    baseURL: '/api',
    timeout: 20000,
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
});

const saleForm = ref({
    sale_date: '',
    total_discount: '',
    status: '',
    due_date: '',
    note: '',
    customer_id: '',
    shipping_amount: 0,
    shipping_distance_km: 0,
    sale_details: [],
    sale_payments: [
        {
            payment_date: '',
            amount: '',
            due_date: '',
            status: '',
            note: '',
            payment_method: ''
        }
    ],
    shipments: []
})

const selectedCustomerAddress = ref('Tangerang, Indonesia');


const formatToRupiah = (rawValue) => {
    if (rawValue === '' || rawValue === null || rawValue === undefined) {
        return '';
    }
    const number = Number(rawValue);
    if (isNaN(number)) {
        return '';
    }
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(number);
};

const parseRupiah = (formattedString) => {
    if (typeof formattedString !== 'string' || !formattedString) return 0;
    const number = Number(String(formattedString).replace(/[^0-9]/g, ''));
    return isNaN(number) ? 0 : number;
};

const formattedTotalDiscount = computed({
    get: () => formatToRupiah(saleForm.value.total_discount),
    set: (val) => {
        saleForm.value.total_discount = parseRupiah(val);
    }
});

const formattedPaymentAmount = computed({
    get: () => {
        if (saleForm.value.sale_payments && saleForm.value.sale_payments[0]) {
            return formatToRupiah(saleForm.value.sale_payments[0].amount);
        }
        return formatToRupiah('');
    },
    set: (val) => {
        if (!saleForm.value.sale_payments) {
            saleForm.value.sale_payments = [];
        }
        if (!saleForm.value.sale_payments[0]) {
            saleForm.value.sale_payments[0] = {
                payment_date: '', amount: 0, due_date: '', status: '', note: '', payment_method: ''
            };
        }
        saleForm.value.sale_payments[0].amount = parseRupiah(val);
    }
});

const formattedShippingAmount = computed({
    get: () => formatToRupiah(saleForm.value.shipping_amount),
    set: (val) => {
        saleForm.value.shipping_amount = parseRupiah(val);
    }
});

const formattedShippingDistance = computed({
    get: () => {
        if (saleForm.value.shipping_distance_km === '' || saleForm.value.shipping_distance_km === null || saleForm.value.shipping_distance_km === undefined) {
            return '';
        }
        return `${saleForm.value.shipping_distance_km} KM`;
    },
    set: (val) => {
        const number = Number(String(val).replace(/[^0-9.]/g, ''));
        saleForm.value.shipping_distance_km = isNaN(number) ? 0 : number;
    }
});

const googleMapsSrc = computed(() => {
    if (selectedCustomerAddress.value && googleMapsKey) {
        const encodedAddress = encodeURIComponent(selectedCustomerAddress.value);
        return `https://maps.google.com/maps?q=${encodedAddress}&z=15&output=embed&key=${googleMapsKey}`;
    }
    return '';
});

const customers = ref([])
const products = ref([])
const couriers = ref([])
const loading = ref(false)

const addProduct = () => {
    saleForm.value.sale_details.push({
        product_id: '',
        quantity: 1,
        note: '',
        selling_price: 0
    })
}

const removeProduct = (index) => {
    saleForm.value.sale_details.splice(index, 1);
}

const addPayment = () => {
    saleForm.value.sale_payments.push({
        payment_date: '',
        amount: '',
        due_date: '',
        status: '',
        note: '',
        payment_method: ''
    });
};

const removePayment = (index) => {
    if (saleForm.value.sale_payments.length > 1) {
        saleForm.value.sale_payments.splice(index, 1);
    } else if (index === 0 && saleForm.value.sale_payments.length === 1) {
    }
};

const addShipment = () => {
    saleForm.value.shipments.push({
        courier_id: '',
        vehicle_type: '',
        vehicle_number: '',
        shipping_date: '',
        estimated_arrival_date: '',
        actual_arrival_date: '',
        status: '',
        note: ''
    });
};

const removeShipment = (index) => {
    saleForm.value.shipments.splice(index, 1);
};

const updateSalePrice = (index) => {
    const selectedProductId = saleForm.value.sale_details[index].product_id;
    const selectedProductData = products.value.find(p => p.id === selectedProductId);

    if (selectedProductData) {
        saleForm.value.sale_details[index].selling_price = Number(selectedProductData.selling_price) || 0;
    } else {
        saleForm.value.sale_details[index].selling_price = 0;
    }
}

const fetchData = async () => {
    try {
        loading.value = true
        const apiToken = localStorage.getItem('X-API-TOKEN');
        if(!apiToken) {
            return;
        }

        const headers = { 'Authorization': `Bearer ${apiToken}` };

        const [customerResponse, productResponse, courierResponse] = await Promise.all([
            axios.get('/api/customers?per_page=100000000', { headers }),
            axios.get('/api/products?per_page=100000000', { headers }),
            axios.get('/api/couriers?per_page=100000000&status=available', { headers })
        ]);

        customers.value = customerResponse.data.data.map(customer => ({
            ...customer,
            address: customer.address || ''
        }));
        products.value = productResponse.data.data.map(p => ({...p, selling_price: Number(p.selling_price) || 0 }));
        couriers.value = courierResponse.data.data;
    } catch (error) {
        console.log('Error fetching data: ', error);
    } finally {
        loading.value = false;
    }
}

const fetchShippingCost = async (customerId) => {
    if (!customerId) {
        saleForm.value.shipping_amount = 0;
        saleForm.value.shipping_distance_km = 0;
        selectedCustomerAddress.value = '';
        return;
    }
    try {
        const apiToken = localStorage.getItem('X-API-TOKEN');
        if(!apiToken) return;

        const headers = { 'Authorization': `Bearer ${apiToken}` };
        const response = await api.get(`/sales/shipping-cost/${customerId}`, { headers });
        saleForm.value.shipping_amount = response.data.shipping_cost;
        saleForm.value.shipping_distance_km = response.data.distance_km;

        const customer = customers.value.find(c => c.id === customerId);
        selectedCustomerAddress.value = customer ? customer.address : '';

    } catch (error) {
        console.error('Error fetching shipping cost:', error.response?.data || error.message);
        saleForm.value.shipping_amount = 0;
        saleForm.value.shipping_distance_km = 0;
        selectedCustomerAddress.value = '';
        Swal.fire({
            title: 'Shipping Error',
            text: 'Could not calculate shipping cost for the selected customer. Please ensure the customer address is valid.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
    }
};

watch(() => saleForm.value.customer_id, (newCustomerId) => {
    fetchShippingCost(newCustomerId);
});


const createSale = async () => {
    try {
        loading.value = true
        const apiToken = localStorage.getItem('X-API-TOKEN');
        if(!apiToken) return;

        const formData = new FormData();

        formData.append('sale_date', saleForm.value.sale_date);
        formData.append('total_discount', saleForm.value.total_discount || 0);
        formData.append('status', saleForm.value.status);
        formData.append('due_date', saleForm.value.due_date);
        formData.append('note', saleForm.value.note);
        formData.append('customer_id', saleForm.value.customer_id);
        formData.append('shipping_amount', saleForm.value.shipping_amount || 0);
        formData.append('shipping_distance_km', saleForm.value.shipping_distance_km || 0);


        saleForm.value.sale_details.forEach((detail, index) => {
            Object.keys(detail).forEach(key => {
                let value = detail[key];
                if (key === 'selling_price') {
                    value = Number(value) || 0;
                }
                formData.append(`sale_details[${index}][${key}]`, value !== null && value !== undefined ? value : '');
            });
        });

        saleForm.value.sale_payments.forEach((payment, index) => {
            Object.keys(payment).forEach(key => {
                let value = payment[key];
                if (key === 'amount') {
                    value = Number(value) || 0;
                }
                formData.append(`sale_payments[${index}][${key}]`, value !== null && value !== undefined ? value : '');
            });
        });

        saleForm.value.shipments.forEach((shipment, index) => {
            Object.keys(shipment).forEach(key => {
                formData.append(`shipments[${index}][${key}]`, shipment[key] !== null && shipment[key] !== undefined ? shipment[key] : '');
            });

            formData.set(`shipments[${index}][shipping_cost]`, saleForm.value.shipping_amount);
            formData.set(`shipments[${index}][shipping_distance_km]`, saleForm.value.shipping_distance_km);
        });

        console.log('--- FormData Content for Sale ---');
        for (var pair of formData.entries()) {
            console.log(pair[0]+ ': ' + pair[1]);
        }
        console.log('--- End FormData Content for Sale ---');


        const response = await api.post('/sales', formData, {
            headers: {
                'Authorization': `Bearer ${apiToken}`,
            }
        });

        Swal.fire({
            title: 'Success',
            text: 'Sale data has been created successfully.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-md focus:outline-none transition-colors duration-200'
            },
        })

        saleForm.value = {
            sale_date: '',
            total_discount: '',
            status: '',
            due_date: '',
            note: '',
            customer_id: '',
            shipping_amount: 0,
            shipping_distance_km: 0,
            sale_details: [],
            sale_payments: [
                {
                    payment_date: '',
                    amount: '',
                    due_date: '',
                    status: '',
                    note: '',
                    payment_method: ''
                }
            ],
            shipments: []
        }
        selectedCustomerAddress.value = 'Tangerang, Indonesia';

    } catch (error) {
        console.error('Failed to create sale:', error.response?.data || error.message)

        let errorMessage = 'Failed to add sale. Please check the data or re-login.';
        let useHtml = false;

        const message = error.response?.data?.errors?.message;

        if (message) {
            if (typeof message === 'object' && !Array.isArray(message)) {
                const formattedErrors = Object.entries(message)
                    .map(([field, msgs]) => `<strong>${field}</strong>: ${Array.isArray(msgs) ? msgs.join(', ') : msgs}`)
                    .join('<br>');

                if (formattedErrors) {
                    errorMessage = formattedErrors;
                    useHtml = true;
                }
            } else if (typeof message === 'string') {
                errorMessage = message;
            }
        }


        Swal.fire({
            title: 'Failed!',
            html: useHtml ? errorMessage : undefined,
            text: useHtml ? undefined : errorMessage,
            icon: 'error',
            confirmButtonText: 'OK'
        })
    } finally {
        loading.value = false;
    }
}

onMounted(async () => {
    await fetchData();
    if (saleForm.value.customer_id) {
        await fetchShippingCost(saleForm.value.customer_id);
    }
})
</script>
