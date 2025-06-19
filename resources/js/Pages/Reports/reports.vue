<template>
    <div class="min-h-screen">
        <Sidebar>
            <div class="max-w-7xl mx-auto space-y-8">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard Reports</h1>
                    <p class="text-lg text-gray-600">Generate comprehensive business reports with detailed insights</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Products</p>
                                <p class="text-2xl font-bold text-gray-900">{{ dashboardData?.total_products || 0 }}</p>
                            </div>
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Sales This Month</p>
                                <p class="text-2xl font-bold text-gray-900">{{ formatCurrency(dashboardData?.sales_this_month) }}</p>
                            </div>
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Total Customers</p>
                                <p class="text-2xl font-bold text-gray-900">{{ dashboardData?.total_customers || 0 }}</p>
                            </div>
                            <div class="p-3 bg-yellow-100 rounded-full">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Low Stock Alert</p>
                                <p class="text-2xl font-bold text-gray-900">{{ dashboardData?.low_stock_products || 0 }}</p>
                            </div>
                            <div class="p-3 bg-red-100 rounded-full">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Export Reports</h2>
                        <p class="text-gray-600">Generate detailed PDF reports for your business analysis</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="group relative overflow-hidden rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl" style="background-color: #3B82F6;">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 rounded-lg" style="background-color: rgba(255, 255, 255, 0.2);">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Dashboard Summary</h3>
                                <p class="mb-4" style="color: rgba(255, 255, 255, 0.8);">Complete business overview with key metrics</p>
                                <button @click="exportDashboardSummary"
                                        class="w-full backdrop-blur-sm border rounded-lg py-3 px-4 font-semibold transition-all duration-200"
                                        style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;">
                                    Generate Report
                                </button>
                            </div>
                        </div>

                        <div class="group relative overflow-hidden rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl" style="background-color: #22C55E;">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 rounded-lg" style="background-color: rgba(255, 255, 255, 0.2);">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Products & Inventory</h3>
                                <p class="mb-4" style="color: rgba(255, 255, 255, 0.8);">Stock levels and product performance</p>
                                <button @click="exportProductsReport"
                                        class="w-full backdrop-blur-sm border rounded-lg py-3 px-4 font-semibold transition-all duration-200"
                                        style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;">
                                    Generate Report
                                </button>
                            </div>
                        </div>

                        <div class="group relative overflow-hidden rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl" style="background-color: #A855F7;">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 rounded-lg" style="background-color: rgba(255, 255, 255, 0.2);">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Sales Summary</h3>
                                <p class="mb-4" style="color: rgba(255, 255, 255, 0.8);">Monthly sales performance and trends</p>
                                <button @click="exportSalesSummary"
                                        class="w-full backdrop-blur-sm border rounded-lg py-3 px-4 font-semibold transition-all duration-200"
                                        style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;">
                                    Generate Report
                                </button>
                            </div>
                        </div>

                        <div class="group relative overflow-hidden rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl" style="background-color: #F97316;">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 rounded-lg" style="background-color: rgba(255, 255, 255, 0.2);">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Top Products</h3>
                                <p class="mb-4" style="color: rgba(255, 255, 255, 0.8);">Best selling products analysis</p>
                                <button @click="exportTopProductsReport"
                                        class="w-full backdrop-blur-sm border rounded-lg py-3 px-4 font-semibold transition-all duration-200"
                                        style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;">
                                    Generate Report
                                </button>
                            </div>
                        </div>

                        <div class="group relative overflow-hidden rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl" style="background-color: #14B8A6;">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 rounded-lg" style="background-color: rgba(255, 255, 255, 0.2);">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Financial Summary</h3>
                                <p class="mb-4" style="color: rgba(255, 255, 255, 0.8);">Revenue, expenses and profit analysis</p>
                                <button @click="exportFinancialSummary"
                                        class="w-full backdrop-blur-sm border rounded-lg py-3 px-4 font-semibold transition-all duration-200"
                                        style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;">
                                    Generate Report
                                </button>
                            </div>
                        </div>

                        <div class="group relative overflow-hidden rounded-xl p-6 text-white transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-2xl" style="background-color: #6366F1;">
                            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="p-3 rounded-lg" style="background-color: rgba(255, 255, 255, 0.2);">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold mb-2">Detailed Reports</h3>
                                <p class="mb-4" style="color: rgba(255, 255, 255, 0.8);">Sales & Purchase detailed reports</p>
                                <div class="space-y-2">
                                    <button @click="exportSalesToPDF"
                                            class="w-full backdrop-blur-sm border rounded-lg py-2 px-4 font-semibold transition-all duration-200 text-sm"
                                            style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;">
                                        Sales Report
                                    </button>
                                    <button @click="exportPurchaseToPDF"
                                            class="w-full backdrop-blur-sm border rounded-lg py-2 px-4 font-semibold transition-all duration-200 text-sm"
                                            style="background-color: rgba(255, 255, 255, 0.2); border-color: rgba(255, 255, 255, 0.3); color: white;">
                                        Purchase Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Sidebar>
    </div>
</template>

<script setup>
import Swal from "sweetalert2";
import axios from "axios";
import { jsPDF } from "jspdf";
import autoTable from "jspdf-autotable";
import { ref, onMounted } from "vue";
import Sidebar from "@/Components/Sidebar.vue";

const dashboardData = ref(null);

const searchQuery = ref("");
const selectedStatus = ref("");
const selectedPaymentStatus = ref("");
const saleDateStart = ref("");
const saleDateEnd = ref("");
const dueDateStart = ref("");
const dueDateEnd = ref("");
const downpaymentIssueDateStart = ref("");
const downpaymentIssueDateEnd = ref("");

const purchaseSearchQuery = ref("");
const purchaseSelectedStatus = ref("");
const purchaseSelectedPaymentStatus = ref("");
const purchaseStartDate = ref("");
const purchaseEndDate = ref("");
const purchaseDueDateStart = ref("");
const purchaseDueDateEnd = ref("");

const formatDate = (dateString) => {
    if (!dateString) return "-";
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, "0");
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
};

const formatCurrency = (value) => {
    if (value == null || value === 0) return "Rp 0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const capitalizeFirstLetter = (str) => {
    if (!str) return "-";
    return str.charAt(0).toUpperCase() + str.slice(1);
};


// 4. Format payment status (e.g., "partial" → "Partially Paid")
const formatPaymentStatus = (status) => {
    if (!status) return "-";
    switch (status.toLowerCase()) {
        case "partial":
        case "partially_paid":
            return "Partially Paid";
        case "paid":
            return "Paid";
        case "unpaid":
            return "Unpaid";
        default:
            return capitalizeFirstLetter(status);
    }
};

// 5. Build filter text for Sales based on local state values
const buildSalesFilterText = (filters) => {
    const parts = [];
    if (filters.startDate) parts.push(`Date From: ${formatDate(filters.startDate)}`);
    if (filters.endDate) parts.push(`Date To: ${formatDate(filters.endDate)}`);
    if (filters.dueDateStart) parts.push(`Due From: ${formatDate(filters.dueDateStart)}`);
    if (filters.dueDateEnd) parts.push(`Due To: ${formatDate(filters.dueDateEnd)}`);
    if (filters.searchQuery?.trim())
        parts.push(`Search: "${filters.searchQuery.trim()}"`);
    if (filters.status) parts.push(`Status: ${capitalizeFirstLetter(filters.status)}`);
    if (filters.paymentStatus)
        parts.push(`Payment Status: ${formatPaymentStatus(filters.paymentStatus)}`);
    if (filters.dpIssueStart) parts.push(`DP Issue From: ${formatDate(filters.dpIssueStart)}`);
    if (filters.dpIssueEnd) parts.push(`DP Issue To: ${formatDate(filters.dpIssueEnd)}`);
    return parts.length > 0 ? `Filters: ${parts.join(", ")}` : "All Records";
};

// 6. Build filter text for Purchase based on local state values
const buildPurchaseFilterText = (filters) => {
    const parts = [];
    if (filters.startDate) parts.push(`Purchase Date From: ${formatDate(filters.startDate)}`);
    if (filters.endDate) parts.push(`Purchase Date To: ${formatDate(filters.endDate)}`);
    if (filters.dueDateStart) parts.push(`Due Date From: ${formatDate(filters.dueDateStart)}`);
    if (filters.dueDateEnd) parts.push(`Due Date To: ${formatDate(filters.dueDateEnd)}`);
    if (filters.searchQuery?.trim())
        parts.push(`Search: "${filters.searchQuery.trim()}"`);
    if (filters.status) parts.push(`Status: ${capitalizeFirstLetter(filters.status)}`);
    if (filters.paymentStatus)
        parts.push(`Payment Status: ${formatPaymentStatus(filters.paymentStatus)}`);
    return parts.length > 0 ? `Filters: ${parts.join(", ")}` : "All Purchases";
};

// Fetch dashboard data
const fetchDashboardData = async () => {
    try {
        const response = await axios.get('/api/dashboard/summary');
        dashboardData.value = response.data.data;
    } catch (error) {
        console.error('Error fetching dashboard data:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to fetch dashboard data',
            icon: 'error'
        });
    }
};

// Export Dashboard Summary Report
const exportDashboardSummary = async () => {
    if (!dashboardData.value) {
        await Swal.fire({
            title: 'No Data',
            text: 'Dashboard data is not available',
            icon: 'warning'
        });
        return;
    }

    const loadingSwal = Swal.fire({
        title: 'Generating Dashboard Summary',
        html: 'Please wait while we prepare your dashboard summary report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const doc = new jsPDF({
            orientation: 'portrait'
        });

        // Title
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.text('Dashboard Summary Report', 14, 20);

        // Date
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.text(`Generated: ${formatDate(new Date().toISOString())}`, 14, 30);

        // Business Overview Section
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text('Business Overview', 14, 45);

        const overviewData = [
            ['Total Products', dashboardData.value.total_products.toString()],
            ['Low Stock Products', dashboardData.value.low_stock_products.toString()],
            ['Total Customers', dashboardData.value.total_customers.toString()],
            ['Total Suppliers', dashboardData.value.total_suppliers.toString()],
            ['Active Users', dashboardData.value.users_active.toString()],
            ['Available Couriers', dashboardData.value.couriers_available.toString()]
        ];

        doc.autoTable({
            head: [['Metric', 'Value']],
            body: overviewData,
            startY: 50,
            margin: { left: 14, right: 14 },
            styles: { fontSize: 10 },
            headStyles: { fillColor: [59, 130, 246], textColor: 255 },
            columnStyles: {
                0: { cellWidth: 80 },
                1: { cellWidth: 40, halign: 'center' }
            }
        });

        // Financial Summary Section
        let finalY = doc.lastAutoTable.finalY + 15;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text('Financial Summary', 14, finalY);

        const financialData = [
            ['Sales This Month', formatCurrency(dashboardData.value.sales_this_month)],
            ['Purchases This Month', formatCurrency(dashboardData.value.purchases_this_month)],
            ['Expenses This Month', formatCurrency(dashboardData.value.expenses_this_month)]
        ];

        doc.autoTable({
            head: [['Category', 'Amount']],
            body: financialData,
            startY: finalY + 5,
            margin: { left: 14, right: 14 },
            styles: { fontSize: 10 },
            headStyles: { fillColor: [16, 185, 129], textColor: 255 },
            columnStyles: {
                0: { cellWidth: 80 },
                1: { cellWidth: 60, halign: 'right' }
            }
        });

        // Sales Status Section
        finalY = doc.lastAutoTable.finalY + 15;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text('Sales & Purchase Status', 14, finalY);

        const statusData = [
            ['Sales Indent', dashboardData.value.sales_indent.toString()],
            ['Sales Shipped', dashboardData.value.sales_shipped.toString()],
            ['Purchases Shipped', dashboardData.value.purchases_shipped.toString()],
            ['Partially Paid Sales', dashboardData.value.partially_paid_sales.toString()],
            ['Partially Paid Purchases', dashboardData.value.partially_paid_purchases.toString()]
        ];

        doc.autoTable({
            head: [['Status', 'Count']],
            body: statusData,
            startY: finalY + 5,
            margin: { left: 14, right: 14 },
            styles: { fontSize: 10 },
            headStyles: { fillColor: [147, 51, 234], textColor: 255 },
            columnStyles: {
                0: { cellWidth: 80 },
                1: { cellWidth: 40, halign: 'center' }
            }
        });

        await loadingSwal.close();

        const timestamp = new Date().toISOString().slice(0, 10);
        doc.save(`Dashboard_Summary_${timestamp}.pdf`);

        Swal.fire({
            title: 'Success!',
            text: 'Dashboard summary report has been generated successfully',
            icon: 'success'
        });

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();
        Swal.fire({
            title: 'Export Failed',
            text: 'An error occurred while generating the PDF.',
            icon: 'error'
        });
    }
};

// Export Products Report
const exportProductsReport = async () => {
    if (!dashboardData.value) {
        await Swal.fire({
            title: 'No Data',
            text: 'Dashboard data is not available',
            icon: 'warning'
        });
        return;
    }

    const loadingSwal = Swal.fire({
        title: 'Generating Products Report',
        html: 'Please wait while we prepare your products report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const doc = new jsPDF({
            orientation: 'portrait'
        });

        // Title
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.text('Products & Inventory Report', 14, 20);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.text(`Generated: ${formatDate(new Date().toISOString())}`, 14, 30);

        // Product Summary
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text('Product Summary', 14, 45);

        const productSummary = [
            ['Total Products', dashboardData.value.total_products.toString()],
            ['Low Stock Alert', dashboardData.value.low_stock_products.toString()],
            ['Stock Status', dashboardData.value.low_stock_products > 0 ? 'Attention Required' : 'Good']
        ];

        doc.autoTable({
            head: [['Metric', 'Value']],
            body: productSummary,
            startY: 50,
            margin: { left: 14, right: 14 },
            styles: { fontSize: 10 },
            headStyles: { fillColor: [34, 197, 94], textColor: 255 }
        });

        // Top Products All Time
        if (dashboardData.value.top_products_all_time && dashboardData.value.top_products_all_time.length > 0) {
            let finalY = doc.lastAutoTable.finalY + 15;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(14);
            doc.text('Top Products (All Time)', 14, finalY);

            const topProductsData = dashboardData.value.top_products_all_time.map((product, index) => [
                (index + 1).toString(),
                product.name || 'N/A',
                product.sku || 'N/A',
                product.stock?.toString() || '0',
                formatCurrency(product.selling_price),
                product.sales_details_sum_quantity || '0'
            ]);

            doc.autoTable({
                head: [['#', 'Product Name', 'SKU', 'Stock', 'Price', 'Total Sold']],
                body: topProductsData,
                startY: finalY + 5,
                margin: { left: 14, right: 14 },
                styles: { fontSize: 8 },
                headStyles: { fillColor: [34, 197, 94], textColor: 255 },
                columnStyles: {
                    0: { cellWidth: 10, halign: 'center' },
                    1: { cellWidth: 50 },
                    2: { cellWidth: 30 },
                    3: { cellWidth: 20, halign: 'center' },
                    4: { cellWidth: 35, halign: 'right' },
                    5: { cellWidth: 25, halign: 'center' }
                }
            });
        }

        await loadingSwal.close();

        const timestamp = new Date().toISOString().slice(0, 10);
        doc.save(`Products_Report_${timestamp}.pdf`);

        Swal.fire({
            title: 'Success!',
            text: 'Products report has been generated successfully',
            icon: 'success'
        });

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();
        Swal.fire({
            title: 'Export Failed',
            text: 'An error occurred while generating the PDF.',
            icon: 'error'
        });
    }
};

// Export Sales Summary Report
const exportSalesSummary = async () => {
    if (!dashboardData.value) {
        await Swal.fire({
            title: 'No Data',
            text: 'Dashboard data is not available',
            icon: 'warning'
        });
        return;
    }

    const loadingSwal = Swal.fire({
        title: 'Generating Sales Summary',
        html: 'Please wait while we prepare your sales summary report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const doc = new jsPDF({
            orientation: 'portrait'
        });

        // Title
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.text('Sales Summary Report', 14, 20);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.text(`Generated: ${formatDate(new Date().toISOString())}`, 14, 30);

        // Sales Performance
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text('Sales Performance', 14, 45);

        const salesData = [
            ['Sales This Month', formatCurrency(dashboardData.value.sales_this_month)],
            ['Sales Indent', dashboardData.value.sales_indent.toString()],
            ['Sales Shipped', dashboardData.value.sales_shipped.toString()],
            ['Partially Paid Sales', dashboardData.value.partially_paid_sales.toString()]
        ];

        doc.autoTable({
            head: [['Metric', 'Value']],
            body: salesData,
            startY: 50,
            margin: { left: 14, right: 14 },
            styles: { fontSize: 10 },
            headStyles: { fillColor: [147, 51, 234], textColor: 255 }
        });

        // Top Products This Month
        if (dashboardData.value.top_products_this_month && dashboardData.value.top_products_this_month.length > 0) {
            let finalY = doc.lastAutoTable.finalY + 15;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(14);
            doc.text('Top Products This Month', 14, finalY);

            const monthlyProductsData = dashboardData.value.top_products_this_month.map((product, index) => [
                (index + 1).toString(),
                product.name || 'N/A',
                product.sku || 'N/A',
                formatCurrency(product.selling_price),
                product.total_sold_this_month || '0'
            ]);

            doc.autoTable({
                head: [['#', 'Product Name', 'SKU', 'Price', 'Sold This Month']],
                body: monthlyProductsData,
                startY: finalY + 5,
                margin: { left: 14, right: 14 },
                styles: { fontSize: 9 },
                headStyles: { fillColor: [147, 51, 234], textColor: 255 },
                columnStyles: {
                    0: { cellWidth: 10, halign: 'center' },
                    1: { cellWidth: 60 },
                    2: { cellWidth: 40 },
                    3: { cellWidth: 40, halign: 'right' },
                    4: { cellWidth: 30, halign: 'center' }
                }
            });
        }

        await loadingSwal.close();

        const timestamp = new Date().toISOString().slice(0, 10);
        doc.save(`Sales_Summary_${timestamp}.pdf`);

        Swal.fire({
            title: 'Success!',
            text: 'Sales summary report has been generated successfully',
            icon: 'success'
        });

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();
        Swal.fire({
            title: 'Export Failed',
            text: 'An error occurred while generating the PDF.',
            icon: 'error'
        });
    }
};

// Export Top Products Report
const exportTopProductsReport = async () => {
    if (!dashboardData.value) {
        await Swal.fire({
            title: 'No Data',
            text: 'Dashboard data is not available',
            icon: 'warning'
        });
        return;
    }

    const loadingSwal = Swal.fire({
        title: 'Generating Top Products Report',
        html: 'Please wait while we prepare your top products report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const doc = new jsPDF({
            orientation: 'landscape'
        });

        // Title
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.text('Top Products Analysis Report', 14, 20);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.text(`Generated: ${formatDate(new Date().toISOString())}`, 14, 30);

        // Top Products All Time
        if (dashboardData.value.top_products_all_time && dashboardData.value.top_products_all_time.length > 0) {
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(14);
            doc.text('Top Products (All Time)', 14, 45);

            const allTimeData = dashboardData.value.top_products_all_time.map((product, index) => [
                (index + 1).toString(),
                product.name || 'N/A',
                product.sku || 'N/A',
                product.brand || 'N/A',
                product.stock?.toString() || '0',
                formatCurrency(product.purchase_price),
                formatCurrency(product.selling_price),
                product.sales_details_sum_quantity || '0',
                product.barcode || 'N/A'
            ]);

            doc.autoTable({
                head: [['#', 'Product Name', 'SKU', 'Brand', 'Stock', 'Purchase Price', 'Selling Price', 'Total Sold', 'Barcode']],
                body: allTimeData,
                startY: 50,
                margin: { left: 14, right: 14 },
                styles: { fontSize: 8 },
                headStyles: { fillColor: [249, 115, 22], textColor: 255 },
                columnStyles: {
                    0: { cellWidth: 15, halign: 'center' },
                    1: { cellWidth: 50 },
                    2: { cellWidth: 35 },
                    3: { cellWidth: 25 },
                    4: { cellWidth: 20, halign: 'center' },
                    5: { cellWidth: 35, halign: 'right' },
                    6: { cellWidth: 35, halign: 'right' },
                    7: { cellWidth: 15, halign: 'center' },
                    8: { cellWidth: 30 }
                }
            });
        }

        // Top Products This Month
        if (dashboardData.value.top_products_this_month && dashboardData.value.top_products_this_month.length > 0) {
            let finalY = doc.lastAutoTable.finalY + 15;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(14);
            doc.text('Top Products (This Month)', 14, finalY);

            const monthlyData = dashboardData.value.top_products_this_month.map((product, index) => [
                (index + 1).toString(),
                product.name || 'N/A',
                product.sku || 'N/A',
                product.brand || 'N/A',
                product.stock?.toString() || '0',
                formatCurrency(product.selling_price),
                product.total_sold_this_month || '0'
            ]);

            doc.autoTable({
                head: [['#', 'Product Name', 'SKU', 'Brand', 'Current Stock', 'Selling Price', 'Sold This Month']],
                body: monthlyData,
                startY: finalY + 5,
                margin: { left: 14, right: 14 },
                styles: { fontSize: 9 },
                headStyles: { fillColor: [249, 115, 22], textColor: 255 },
                columnStyles: {
                    0: { cellWidth: 15, halign: 'center' },
                    1: { cellWidth: 50 },
                    2: { cellWidth: 40 },
                    3: { cellWidth: 40 },
                    4: { cellWidth: 30, halign: 'center' },
                    5: { cellWidth: 50, halign: 'right' },
                    6: { cellWidth: 40, halign: 'center' }
                }
            });
        }

        await loadingSwal.close();

        const timestamp = new Date().toISOString().slice(0, 10);
        doc.save(`Top_Products_Report_${timestamp}.pdf`);

        Swal.fire({
            title: 'Success!',
            text: 'Top products report has been generated successfully',
            icon: 'success'
        });

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();
        Swal.fire({
            title: 'Export Failed',
            text: 'An error occurred while generating the PDF.',
            icon: 'error'
        });
    }
};

// Export Financial Summary Report
const exportFinancialSummary = async () => {
    if (!dashboardData.value) {
        await Swal.fire({
            title: 'No Data',
            text: 'Dashboard data is not available',
            icon: 'warning'
        });
        return;
    }

    const loadingSwal = Swal.fire({
        title: 'Generating Financial Summary',
        html: 'Please wait while we prepare your financial summary report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const doc = new jsPDF({
            orientation: 'portrait'
        });

        // Title
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(18);
        doc.text('Financial Summary Report', 14, 20);

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);
        doc.text(`Generated: ${formatDate(new Date().toISOString())}`, 14, 30);

        // Financial Overview
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text('Monthly Financial Overview', 14, 45);

        const salesAmount = parseFloat(dashboardData.value.sales_this_month) || 0;
        const purchasesAmount = parseFloat(dashboardData.value.purchases_this_month) || 0;
        const expensesAmount = parseFloat(dashboardData.value.expenses_this_month) || 0;
        const grossProfit = salesAmount - purchasesAmount;
        const netProfit = grossProfit - expensesAmount;

        const financialData = [
            ['Sales This Month', formatCurrency(salesAmount)],
            ['Purchases This Month', formatCurrency(purchasesAmount)],
            ['Expenses This Month', formatCurrency(expensesAmount)],
            ['Gross Profit', formatCurrency(grossProfit)],
            ['Net Profit', formatCurrency(netProfit)]
        ];

        doc.autoTable({
            head: [['Category', 'Amount']],
            body: financialData,
            startY: 50,
            margin: { left: 14, right: 14 },
            styles: { fontSize: 11 },
            headStyles: { fillColor: [20, 184, 166], textColor: 255 },
            columnStyles: {
                0: { cellWidth: 80 },
                1: { cellWidth: 60, halign: 'right' }
            },
            didParseCell: function (data) {
                if (data.row.index === 4) { // Net Profit row
                    if (netProfit >= 0) {
                        data.cell.styles.textColor = [34, 197, 94]; // Green for profit
                    } else {
                        data.cell.styles.textColor = [239, 68, 68]; // Red for loss
                    }
                    data.cell.styles.fontStyle = 'bold';
                }
            }
        });

        // Payment Status Analysis
        let finalY = doc.lastAutoTable.finalY + 15;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text('Payment Status Analysis', 14, finalY);

        const paymentData = [
            ['Partially Paid Sales', dashboardData.value.partially_paid_sales.toString()],
            ['Partially Paid Purchases', dashboardData.value.partially_paid_purchases.toString()],
            ['Sales Shipped', dashboardData.value.sales_shipped.toString()],
            ['Purchases Shipped', dashboardData.value.purchases_shipped.toString()]
        ];

        doc.autoTable({
            head: [['Payment Status', 'Count']],
            body: paymentData,
            startY: finalY + 5,
            margin: { left: 14, right: 14 },
            styles: { fontSize: 10 },
            headStyles: { fillColor: [20, 184, 166], textColor: 255 },
            columnStyles: {
                0: { cellWidth: 80 },
                1: { cellWidth: 40, halign: 'center' }
            }
        });

        // Financial Health Indicators
        finalY = doc.lastAutoTable.finalY + 15;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(14);
        doc.text('Financial Health Indicators', 14, finalY);

        const profitMargin = salesAmount > 0 ? ((netProfit / salesAmount) * 100).toFixed(2) : '0.00';
        const grossMargin = salesAmount > 0 ? ((grossProfit / salesAmount) * 100).toFixed(2) : '0.00';

        const healthData = [
            ['Gross Profit Margin', `${grossMargin}%`],
            ['Net Profit Margin', `${profitMargin}%`],
            ['Financial Status', netProfit >= 0 ? 'Profitable' : 'Loss'],
            ['Cash Flow Indicator', dashboardData.value.partially_paid_sales > 0 ? 'Pending Receivables' : 'Good']
        ];

        doc.autoTable({
            head: [['Indicator', 'Value']],
            body: healthData,
            startY: finalY + 5,
            margin: { left: 14, right: 14 },
            styles: { fontSize: 10 },
            headStyles: { fillColor: [20, 184, 166], textColor: 255 },
            columnStyles: {
                0: { cellWidth: 80 },
                1: { cellWidth: 60, halign: 'center' }
            }
        });

        await loadingSwal.close();

        const timestamp = new Date().toISOString().slice(0, 10);
        doc.save(`Financial_Summary_${timestamp}.pdf`);

        Swal.fire({
            title: 'Success!',
            text: 'Financial summary report has been generated successfully',
            icon: 'success'
        });

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();
        Swal.fire({
            title: 'Export Failed',
            text: 'An error occurred while generating the PDF.',
            icon: 'error'
        });
    }
};

const exportPurchaseToPDF = async () => {
    const loadingSwal = Swal.fire({
        title: 'Preparing Purchase PDF',
        html: 'Please wait while we prepare your purchase report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await axios.get('/api/purchases', {
            params: {
                search: purchaseSearchQuery.value.trim(),
                status: purchaseSelectedStatus.value,
                payment_status: purchaseSelectedPaymentStatus.value,
                start_date: purchaseStartDate.value,
                end_date: purchaseEndDate.value,
                start_due_date: purchaseDueDateStart.value,
                end_due_date: purchaseDueDateEnd.value,
                per_page: 1000000
            }
        });

        const allPurchases = response.data.data;

        if (allPurchases.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no purchases to export with the current filters.',
                icon: 'info'
            });
            loadingSwal.close();
            return;
        }

        const doc = new jsPDF({
            orientation: 'portrait'
        });

        // Title
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('Purchase Report', 14, 20);

        // Filter information
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);

        const filterText = buildPurchaseFilterText({
            searchQuery: purchaseSearchQuery.value,
            status: purchaseSelectedStatus.value,
            paymentStatus: purchaseSelectedPaymentStatus.value,
            startDate: purchaseStartDate.value,
            endDate: purchaseEndDate.value,
            dueDateStart: purchaseDueDateStart.value,
            dueDateEnd: purchaseDueDateEnd.value,
        });

        doc.text(filterText, 14, 28);
        doc.text(`Generated: ${formatDate(new Date().toISOString())}`, 14, 36);
        doc.text(`Total Records: ${allPurchases.length}`, 14, 44);

        // Table headers
        const headers = [
            'No',
            'Invoice',
            'Supplier',
            'Purchase Date',
            'Due Date',
            'Grand Total',
            'Status',
            'Payment Status',
            'Created By'
        ];

        // Table data
        const tableData = allPurchases.map((purchase, index) => [
            index + 1,
            purchase.invoice_number || 'N/A',
            purchase.supplier?.label || 'N/A',
            formatDate(purchase.purchase_date),
            formatDate(purchase.due_date) || 'N/A',
            formatCurrency(purchase.grand_total),
            capitalizeFirstLetter(purchase.status),
            formatPaymentStatus(purchase.payment_status),
            purchase.user?.name || 'N/A'
        ]);

        // Column widths and alignment
        const columnStyles = {
            0: { cellWidth: 10, halign: 'center' }, // No
            1: { cellWidth: 25 }, // Invoice
            2: { cellWidth: 30 }, // Supplier
            3: { cellWidth: 15, halign: 'center' }, // Purchase Date
            4: { cellWidth: 15, halign: 'center' }, // Due Date
            5: { cellWidth: 28, halign: 'right' }, // Grand Total
            6: { cellWidth: 20, halign: 'center' }, // Status
            7: { cellWidth: 25, halign: 'center' }, // Payment Status
            8: { cellWidth: 20 }, // Created By
        };

        // Generate table
        doc.autoTable({
            head: [headers],
            body: tableData,
            startY: 50,
            margin: { top: 50, right: 14, bottom: 14, left: 14 },
            styles: {
                fontSize: 8,
                cellPadding: 2,
                overflow: 'linebreak',
                valign: 'middle',
            },
            headStyles: {
                fillColor: [47, 132, 81], // Leaf POS green
                textColor: 255,
                fontStyle: 'bold',
                halign: 'center',
            },
            bodyStyles: {
                textColor: [50, 50, 50],
            },
            columnStyles: columnStyles,
            didDrawPage: (data) => {
                // Footer (page number)
                doc.setFontSize(8);
                doc.setTextColor(150);
                const pageCount = doc.internal.getNumberOfPages();
                doc.text(
                    `Page ${data.pageNumber} of ${pageCount}`,
                    doc.internal.pageSize.width - data.settings.margin.right,
                    doc.internal.pageSize.height - 10,
                    { align: 'right' }
                );
            }
        });

        await loadingSwal.close();

        // Generate filename based on filters and timestamp
        const timestamp = new Date().toISOString().slice(0, 10);
        let fileName = 'Purchase_Report';
        if (purchaseStartDate.value) fileName += `_from_${purchaseStartDate.value}`;
        if (purchaseEndDate.value) fileName += `_to_${purchaseEndDate.value}`;
        if (purchaseDueDateStart.value) fileName += `_dueFrom_${purchaseDueDateStart.value}`;
        if (purchaseDueDateEnd.value) fileName += `_dueTo_${purchaseDueDateEnd.value}`;
        fileName += `_${timestamp}`;

        doc.save(`${fileName}.pdf`);

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();

        await Swal.fire({
            title: 'Export Failed',
            text: error.response?.data?.message || 'An error occurred while generating the PDF.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

// ------------------------------
// ▼ Export Sales Function ▼
// ------------------------------
const exportSalesToPDF = async () => {
    const loadingSwal = Swal.fire({
        title: 'Preparing Sales PDF',
        html: 'Please wait while we prepare your sales report...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await axios.get('/api/sales', {
            params: {
                search: searchQuery.value.trim(),
                status: selectedStatus.value,
                payment_status: selectedPaymentStatus.value,
                sale_date_start: saleDateStart.value,
                sale_date_end: saleDateEnd.value,
                due_date_start: dueDateStart.value,
                due_date_end: dueDateEnd.value,
                downpayment_issue_date_start: downpaymentIssueDateStart.value,
                downpayment_issue_date_end: downpaymentIssueDateEnd.value,
                per_page: 1000000
            }
        });

        const allSales = response.data.data;

        if (allSales.length === 0) {
            await Swal.fire({
                title: 'No Data',
                text: 'There are no sales to export with the current filters.',
                icon: 'info'
            });
            loadingSwal.close();
            return;
        }

        const doc = new jsPDF({
            orientation: 'portrait'
        });

        // Title
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(16);
        doc.text('Sales Report', 14, 20);

        // Filter information
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(10);

        const filterText = buildSalesFilterText({
            searchQuery: searchQuery.value,
            status: selectedStatus.value,
            paymentStatus: selectedPaymentStatus.value,
            startDate: saleDateStart.value,
            endDate: saleDateEnd.value,
            dueDateStart: dueDateStart.value,
            dueDateEnd: dueDateEnd.value,
            dpIssueStart: downpaymentIssueDateStart.value,
            dpIssueEnd: downpaymentIssueDateEnd.value,
        });

        doc.text(filterText, 14, 28);
        doc.text(`Generated: ${formatDate(new Date().toISOString())}`, 14, 36);
        doc.text(`Total Records: ${allSales.length}`, 14, 44);

        const headers = [
            'No',
            'Main Invoice',
            'DP Invoice',
            'Customer',
            'Sale Date',
            'Grand Total',
            'Status',
            'Payment Status',
            'Created By'
        ];

        const tableData = allSales.map((sale, index) => [
            index + 1,
            sale.invoice_number || 'N/A',
            sale.invoice_downpayment_number || 'N/A',
            sale.customer?.label || 'N/A',
            formatDate(sale.sale_date),
            formatCurrency(sale.grand_total),
            capitalizeFirstLetter(sale.status),
            formatPaymentStatus(sale.payment_status),
            sale.user?.name || 'N/A'
        ]);

        // Column widths and alignment
        const columnStyles = {
            0: { cellWidth: 10, halign: 'center' }, // No
            1: { cellWidth: 22 }, // Main Invoice
            2: { cellWidth: 22 }, // DP Invoice
            3: { cellWidth: 28 }, // Customer
            4: { cellWidth: 18, halign: 'center' }, // Sale Date
            5: { cellWidth: 25, halign: 'right' }, // Grand Total
            6: { cellWidth: 18, halign: 'center' }, // Status
            7: { cellWidth: 20, halign: 'center' }, // Payment Status
            8: { cellWidth: 20 }, // Created By
        };

        doc.autoTable({
            head: [headers],
            body: tableData,
            startY: 50,
            margin: { top: 50, right: 14, bottom: 14, left: 14 },
            styles: {
                fontSize: 8,
                cellPadding: 2,
                overflow: 'linebreak',
                valign: 'middle',
            },
            headStyles: {
                fillColor: [47, 132, 81], // Leaf POS green
                textColor: 255,
                fontStyle: 'bold',
                halign: 'center',
            },
            bodyStyles: {
                textColor: [50, 50, 50],
            },
            columnStyles: columnStyles,
            didDrawPage: (data) => {
                // Footer (page number)
                doc.setFontSize(8);
                doc.setTextColor(150);
                const pageCount = doc.internal.getNumberOfPages();
                doc.text(
                    `Page ${data.pageNumber} of ${pageCount}`,
                    doc.internal.pageSize.width - data.settings.margin.right,
                    doc.internal.pageSize.height - 10,
                    { align: 'right' }
                );
            }
        });

        await loadingSwal.close();

        // Generate filename based on filters and timestamp
        const timestamp = new Date().toISOString().slice(0, 10);
        let fileName = 'Sales_Report';
        if (saleDateStart.value) fileName += `_from_${saleDateStart.value}`;
        if (saleDateEnd.value) fileName += `_to_${saleDateEnd.value}`;
        if (dueDateStart.value) fileName += `_dueFrom_${dueDateStart.value}`;
        if (dueDateEnd.value) fileName += `_dueTo_${dueDateEnd.value}`;
        if (downpaymentIssueDateStart.value) fileName += `_dpFrom_${downpaymentIssueDateStart.value}`;
        if (downpaymentIssueDateEnd.value) fileName += `_dpTo_${downpaymentIssueDateEnd.value}`;
        fileName += `_${timestamp}`;

        doc.save(`${fileName}.pdf`);

    } catch (error) {
        console.error('PDF export error:', error);
        await loadingSwal.close();

        await Swal.fire({
            title: 'Export Failed',
            text: error.response?.data?.message || 'An error occurred while generating the PDF.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
};

// Initialize component
onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
/* Additional custom styles if needed */
.group:hover .group-hover\:opacity-10 {
    opacity: 0.1;
}
</style>
