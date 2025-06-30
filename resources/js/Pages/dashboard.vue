<template>
    <div class="bg-gray-100">
        <Sidebar>
            <!-- Main Content -->
            <div class="">
                <!-- Header with Month/Year Selector -->
                <div class="bg-white border-b rounded-lg border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                            <p class="text-sm text-gray-600 mt-1">Business performance overview</p>
                        </div>

                        <!-- Month/Year Selector -->
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-700">Period:</label>
                                <select
                                    v-model="selectedMonth"
                                    @change="fetchDashboardData"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary bg-white"
                                >
                                    <option v-for="(month, index) in monthNames" :key="index" :value="index + 1">
                                        {{ month }}
                                    </option>
                                </select>

                                <select
                                    v-model="selectedYear"
                                    @change="fetchDashboardData"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary bg-white"
                                >
                                    <option v-for="year in availableYears" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>

                            <!-- Refresh Button -->
                            <button
                                @click="refreshData"
                                :disabled="loading"
                                class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors duration-200 flex items-center space-x-2 disabled:opacity-50"
                            >
                                <svg class="w-4 h-4" :class="{ 'animate-spin': loading }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="text-sm">{{ loading ? 'Loading...' : 'Sync' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Content -->
                <main class="pt-6">
                    <!-- Error State -->
                    <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-red-800 text-sm">{{ error }}</p>
                            <button @click="fetchDashboardData" class="ml-auto text-red-600 hover:text-red-800 text-sm font-medium">
                                Try Again
                            </button>
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div v-if="loading" class="space-y-6">
                        <!-- Summary Cards Loading -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <div v-for="i in 8" :key="i" class="bg-white rounded-xl shadow-sm p-6 animate-pulse">
                                <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                                <div class="h-8 bg-gray-200 rounded w-1/2 mb-2"></div>
                                <div class="h-3 bg-gray-200 rounded w-full"></div>
                            </div>
                        </div>

                        <!-- Charts Loading -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl shadow-sm p-6 animate-pulse">
                                <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
                                <div class="h-64 bg-gray-200 rounded"></div>
                            </div>
                            <div class="bg-white rounded-xl shadow-sm p-6 animate-pulse">
                                <div class="h-6 bg-gray-200 rounded w-1/3 mb-4"></div>
                                <div class="h-64 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Dashboard Content -->
                    <div v-else class="space-y-6">
                        <!-- Key Metrics Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Total Products -->
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-gray-100 group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                                        <p class="text-3xl font-bold text-gray-900 mb-1">{{ formatNumber(dashboardData?.total_products || 0) }}</p>
                                        <p class="text-xs text-gray-500">In inventory</p>
                                    </div>
                                    <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Low Stock Alert -->
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-gray-100 group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Low Stock Alert</p>
                                        <p class="text-3xl font-bold text-red-600 mb-1">{{ formatNumber(dashboardData?.low_stock_products || 0) }}</p>
                                        <p class="text-xs text-red-500">Needs attention</p>
                                    </div>
                                    <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Customers -->
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-gray-100 group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Total Customers</p>
                                        <p class="text-3xl font-bold text-purple-600 mb-1">{{ formatNumber(dashboardData?.total_customers || 0) }}</p>
                                        <p class="text-xs text-gray-500">Registered</p>
                                    </div>
                                    <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Suppliers -->
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-gray-100 group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Total Suppliers</p>
                                        <p class="text-3xl font-bold text-indigo-600 mb-1">{{ formatNumber(dashboardData?.total_suppliers || 0) }}</p>
                                        <p class="text-xs text-gray-500">Active partners</p>
                                    </div>
                                    <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Overview -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Sales This Month -->
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-green-200 group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-green-700 mb-1">Sales Revenue</p>
                                        <p class="text-2xl font-bold text-green-800 mb-1">{{ formatCurrency(dashboardData?.sales_this_month || 0) }}</p>
                                        <p class="text-xs text-green-600">{{ currentPeriodText }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-green-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Purchases This Month -->
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-blue-200 group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-700 mb-1">Purchase Costs</p>
                                        <p class="text-2xl font-bold text-blue-800 mb-1">{{ formatCurrency(dashboardData?.purchases_this_month || 0) }}</p>
                                        <p class="text-xs text-blue-600">{{ currentPeriodText }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-blue-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Expenses This Month -->
                            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 p-6 border border-orange-200 group">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-orange-700 mb-1">Expenses</p>
                                        <p class="text-2xl font-bold text-orange-800 mb-1">{{ formatCurrency(dashboardData?.expenses_this_month || 0) }}</p>
                                        <p class="text-xs text-orange-600">{{ currentPeriodText }}</p>
                                    </div>
                                    <div class="w-12 h-12 bg-orange-200 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-6 h-6 text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Financial Overview Chart -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Financial Overview</h3>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                        <span class="text-xs text-gray-600">Sales</span>
                                        <div class="w-3 h-3 bg-blue-500 rounded-full ml-3"></div>
                                        <span class="text-xs text-gray-600">Purchases</span>
                                        <div class="w-3 h-3 bg-orange-500 rounded-full ml-3"></div>
                                        <span class="text-xs text-gray-600">Expenses</span>
                                    </div>
                                </div>
                                <div class="h-64 relative">
                                    <canvas ref="financialChart"></canvas>
                                </div>
                            </div>

                            <!-- Status Overview Chart -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Order Status Overview</h3>
                                    <div class="text-xs text-gray-500">Distribution by status</div>
                                </div>
                                <div class="h-64 relative">
                                    <canvas ref="statusChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Status Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Sales Indent -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Sales Indent</p>
                                        <p class="text-2xl font-bold text-yellow-600">{{ formatNumber(dashboardData?.sales_indent || 0) }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Sales Shipped -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Sales Shipped</p>
                                        <p class="text-2xl font-bold text-green-600">{{ formatNumber(dashboardData?.sales_shipped || 0) }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Purchases Shipped -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Purchases Shipped</p>
                                        <p class="text-2xl font-bold text-blue-600">{{ formatNumber(dashboardData?.purchases_shipped || 0) }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Users -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600 mb-1">Active Users</p>
                                        <p class="text-2xl font-bold text-primary">{{ formatNumber(dashboardData?.users_active || 0) }}</p>
                                    </div>
                                    <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Top Products Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Top Products All Time -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Products (All Time)</h3>
                                <div class="space-y-3">
                                    <div v-if="!dashboardData?.top_products_all_time?.length" class="text-center py-8 text-gray-500">
                                        No data available
                                    </div>
                                    <div v-else v-for="(product, index) in dashboardData.top_products_all_time" :key="product.id"
                                         class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                {{ index + 1 }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ product.name }}</span>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ formatNumber(product.sales_details_sum_quantity || 0) }} sold</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Top Products This Month -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Products ({{ currentPeriodText }})</h3>
                                <div class="space-y-3">
                                    <div v-if="!dashboardData?.top_products_this_month?.length" class="text-center py-8 text-gray-500">
                                        No data available for this period
                                    </div>
                                    <div v-else v-for="(product, index) in dashboardData.top_products_this_month" :key="product.id"
                                         class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                {{ index + 1 }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ product.name }}</span>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ formatNumber(product.total_sold_this_month || 0) }} sold</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Partially Paid Sales -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Payment Status</h3>
                                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-700">Partially Paid Sales</span>
                                        <span class="text-lg font-bold text-orange-600">{{ formatNumber(dashboardData?.partially_paid_sales || 0) }}</span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-700">Partially Paid Purchases</span>
                                        <span class="text-lg font-bold text-red-600">{{ formatNumber(dashboardData?.partially_paid_purchases || 0) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Courier Status -->
                            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900">Courier Status</h3>
                                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <div class="text-center py-8">
                                    <div class="text-4xl font-bold text-green-600 mb-2">{{ formatNumber(dashboardData?.couriers_available || 0) }}</div>
                                    <p class="text-gray-600">Available Couriers</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </Sidebar>
    </div>
</template>

<script setup>
import Sidebar from '../Components/Sidebar.vue'
import axios from 'axios'
import { ref, onMounted, computed, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import { Chart, registerables } from 'chart.js'


Chart.register(...registerables)

const user = ref(null)
const dashboardData = ref(null)
const loading = ref(true)
const error = ref(null)

const financialChart = ref(null)
const statusChart = ref(null)
let financialChartInstance = null
let statusChartInstance = null

const selectedMonth = ref(new Date().getMonth() + 1)
const selectedYear = ref(new Date().getFullYear())

const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
]

const availableYears = computed(() => {
    const currentYear = new Date().getFullYear()
    const years = []
    for (let i = currentYear - 5; i <= currentYear; i++) {
        years.push(i)
    }
    return years
})

const currentPeriodText = computed(() => {
    return `${monthNames[selectedMonth.value - 1]} ${selectedYear.value}`
})

const formatNumber = (num) => {
    return new Intl.NumberFormat('id-ID').format(num)
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount)
}

const createFinancialChart = () => {
    if (!financialChart.value || !dashboardData.value) return

    if (financialChartInstance) {
        financialChartInstance.destroy()
    }

    const ctx = financialChart.value.getContext('2d')

    const data = {
        labels: ['Sales', 'Purchases', 'Expenses'],
        datasets: [{
            label: 'Amount (IDR)',
            data: [
                dashboardData.value.sales_this_month || 0,
                dashboardData.value.purchases_this_month || 0,
                dashboardData.value.expenses_this_month || 0
            ],
            backgroundColor: [
                'rgba(34, 197, 94, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(245, 158, 11, 0.8)'
            ],
            borderColor: [
                'rgb(34, 197, 94)',
                'rgb(59, 130, 246)',
                'rgb(245, 158, 11)'
            ],
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    }

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return formatCurrency(context.parsed.y)
                        }
                    }
                }
            },
            datasets: {
                bar: {
                    barPercentage: 0.5,
                    categoryPercentage: 0.8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value)
                        },
                        color: '#6B7280',
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6B7280',
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    }

    financialChartInstance = new Chart(ctx, config)
}

const createStatusChart = () => {
    if (!statusChart.value || !dashboardData.value) return

    if (statusChartInstance) {
        statusChartInstance.destroy()
    }

    const ctx = statusChart.value.getContext('2d')

    const data = {
        labels: ['Sales Indent', 'Sales Shipped', 'Purchases Shipped', 'Available Couriers'],
        datasets: [{
            data: [
                dashboardData.value.sales_indent || 0,
                dashboardData.value.sales_shipped || 0,
                dashboardData.value.purchases_shipped || 0,
                dashboardData.value.couriers_available || 0
            ],
            backgroundColor: [
                'rgba(245, 158, 11, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(47, 132, 81, 0.8)'
            ],
            borderColor: [
                'rgb(245, 158, 11)',
                'rgb(16, 185, 129)',
                'rgb(59, 130, 246)',
                'rgb(47, 132, 81)'
            ],
            borderWidth: 2,
            hoverOffset: 10
        }]
    }

    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 11
                        },
                        color: '#6B7280'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: 'white',
                    bodyColor: 'white',
                    borderColor: 'rgba(255, 255, 255, 0.1)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0)
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0
                            return `${context.label}: ${formatNumber(context.parsed)} (${percentage}%)`
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    }

    statusChartInstance = new Chart(ctx, config)
}

const fetchDashboardData = async () => {
    loading.value = true
    error.value = null

    try {
        const response = await axios.get('/api/dashboard/summary', {
            params: {
                month: selectedMonth.value,
                year: selectedYear.value
            }
        })
        dashboardData.value = response.data.data
        console.log('Dashboard data updated:', dashboardData.value)

        await nextTick()

        setTimeout(() => {
            createFinancialChart()
            createStatusChart()
        }, 100)
    } catch (err) {
        error.value = 'Failed to fetch dashboard data. Please try again.'
        console.error('Dashboard API error:', err)
    } finally {
        loading.value = false
    }
}

const refreshData = () => {
    fetchDashboardData()
}

watch([selectedMonth, selectedYear], () => {
    fetchDashboardData()
})

onMounted(async () => {
    const token = localStorage.getItem('X-API-TOKEN')
    if (!token) {
        router.visit('/')
        return
    }

    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    try {
        const userResponsePromise = axios.get('/api/users/current', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        await fetchDashboardData()

        user.value = (await userResponsePromise).data.data
        console.log('User data:', user.value)

    } catch (error) {
        console.error('Authentication error or initial data fetch error:', error)
        router.visit('/')
    }
})
</script>

<style scoped>
.text-primary {
    color: #2F8451;
}

.bg-primary {
    background-color: #2F8451;
}

.bg-primary\/10 {
    background-color: rgba(47, 132, 81, 0.1);
}

.bg-primary\/90 {
    background-color: rgba(47, 132, 81, 0.9);
}

.hover\:bg-primary\/90:hover {
    background-color: rgba(47, 132, 81, 0.9);
}

.hover\:text-primary\/80:hover {
    color: rgba(47, 132, 81, 0.8);
}

.border-primary {
    border-color: #2F8451;
}

.focus\:ring-primary:focus {
    --tw-ring-color: rgba(47, 132, 81, 0.5);
}

.focus\:border-primary:focus {
    border-color: #2F8451;
}

canvas {
    width: 100% !important;
    height: 100% !important;
}
</style>
