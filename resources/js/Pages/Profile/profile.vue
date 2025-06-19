<template>
    <div class="bg-gray-100 min-h-screen">
        <Sidebar :auth="auth">
            <div class="p-8">
                <div class="max-w-6xl mx-auto">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">User Profile</h1>
                        <p class="text-gray-600">Manage your account settings and personal information</p>
                    </div>

                    <div v-if="loading" class="flex justify-center items-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#2F8451]"></div>
                    </div>

                    <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h3 class="text-red-800 font-medium">Error Loading Profile</h3>
                                <p class="text-red-600 text-sm mt-1">{{ error }}</p>
                            </div>
                        </div>
                        <button
                            @click="fetchUserProfile"
                            class="mt-4 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors"
                        >
                            Try Again
                        </button>
                    </div>

                    <div v-else-if="user" class="space-y-8">
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                            <div class="h-40 relative bg-cover bg-center" :style="{ backgroundImage: `url('/storage/profile/daun.jpg')` }">
                                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                            </div>

                            <div class="relative px-10 pb-10">
                                <div class="flex justify-center -mt-20">
                                    <div class="w-36 h-36 rounded-full border-4 border-white shadow-lg bg-[#2F8451] flex items-center justify-center">
                                        <span class="text-4xl font-bold text-white">{{ getInitials(user.name) }}</span>
                                    </div>
                                </div>

                                <div class="text-center mt-6">
                                    <h2 class="text-3xl font-bold text-gray-900">{{ user.name }}</h2>
                                    <p class="text-lg text-gray-700 mt-1">{{ user.email }}</p>
                                    <span class="inline-flex items-center mt-3 px-4 py-1.5 rounded-full text-sm font-medium bg-[#2F8451] bg-opacity-20 text-[#2F8451]">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    {{ user.role.name }} - {{ user.status }}
                                  </span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div class="bg-white rounded-xl shadow-lg p-8">
                                <div class="flex items-center justify-between mb-8">
                                    <h3 class="text-2xl font-semibold text-gray-800 flex items-center">
                                        <svg class="w-7 h-7 text-[#2F8451] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Personal Information
                                    </h3>
                                </div>

                                <div class="space-y-5">
                                    <div class="flex justify-between items-center py-4 border-b border-gray-100">
                                        <span class="text-gray-600 font-medium text-lg">User ID</span>
                                        <span class="text-gray-800 text-lg">#{{ user.id }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-4 border-b border-gray-100">
                                        <span class="text-gray-600 font-medium text-lg">Full Name</span>
                                        <span class="text-gray-800 text-lg">{{ user.name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-4 border-b border-gray-100">
                                        <span class="text-gray-600 font-medium text-lg">Email</span>
                                        <span class="text-gray-800 text-lg">{{ user.email }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-4 border-b border-gray-100">
                                        <span class="text-gray-600 font-medium text-lg">Role</span>
                                        <span class="text-gray-800 text-lg">{{ user.role.name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-4">
                                        <span class="text-gray-600 font-medium text-lg">Status</span>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium"
                                              :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                          {{ user.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl shadow-lg p-8">
                                <div class="flex items-center justify-between mb-8">
                                    <h3 class="text-2xl font-semibold text-gray-800 flex items-center">
                                        <svg class="w-7 h-7 text-[#2F8451] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Account Settings
                                    </h3>
                                </div>

                                <div class="space-y-5">
                                    <div class="flex items-center justify-between p-5 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors"
                                         @click="showChangePasswordModal = true">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-[#2F8451] bg-opacity-10 rounded-lg flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-[#2F8451]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 text-lg">Change Password</p>
                                                <p class="text-base text-gray-600">Update your password</p>
                                            </div>
                                        </div>
                                        <button class="text-[#2F8451] hover:text-[#245a3d] transition-colors">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>

<!--                                    <div class="flex items-center justify-between p-5 bg-gray-50 rounded-lg">-->
<!--                                        <div class="flex items-center">-->
<!--                                            <div class="w-12 h-12 bg-[#2F8451] bg-opacity-10 rounded-lg flex items-center justify-center mr-4">-->
<!--                                                <svg class="w-6 h-6 text-[#2F8451]" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h10a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"></path>-->
<!--                                                </svg>-->
<!--                                            </div>-->
<!--                                            <div>-->
<!--                                                <p class="font-medium text-gray-800 text-lg">Email Notifications</p>-->
<!--                                                <p class="text-base text-gray-600">Manage notification preferences</p>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <label class="relative inline-flex items-center cursor-pointer">-->
<!--                                            <input type="checkbox" checked class="sr-only peer">-->
<!--                                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#2F8451] peer-focus:ring-opacity-25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-[#2F8451]"></div>-->
<!--                                        </label>-->
<!--                                    </div>-->

<!--                                    <div class="flex items-center justify-between p-5 bg-gray-50 rounded-lg">-->
<!--                                        <div class="flex items-center">-->
<!--                                            <div class="w-12 h-12 bg-[#2F8451] bg-opacity-10 rounded-lg flex items-center justify-center mr-4">-->
<!--                                                <svg class="w-6 h-6 text-[#2F8451]" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
<!--                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>-->
<!--                                                </svg>-->
<!--                                            </div>-->
<!--                                            <div>-->
<!--                                                <p class="font-medium text-gray-800 text-lg">Two-Factor Authentication</p>-->
<!--                                                <p class="text-base text-gray-600">Add extra security to your account</p>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <button class="bg-[#2F8451] text-white px-5 py-2.5 rounded-lg hover:bg-[#245a3d] transition-colors text-base font-medium">-->
<!--                                            Enable-->
<!--                                        </button>-->
<!--                                    </div>-->
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-lg p-8">
                            <h3 class="text-2xl font-semibold text-gray-800 mb-8 flex items-center">
                                <svg class="w-7 h-7 text-[#2F8451] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Quick Actions
                            </h3>

                            <div class="grid grid-cols-3 gap-6">
                                <button
                                    @click="showEditProfileModal = true"
                                    class="p-6 border border-gray-200 rounded-lg hover:border-[#2F8451] hover:bg-[#2F8451] hover:bg-opacity-5 transition-all group">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-[#2F8451] bg-opacity-10 rounded-lg flex items-center justify-center mx-auto mb-4 group-hover:bg-[#2F8451] group-hover:bg-opacity-20">
                                            <svg class="w-8 h-8 text-[#2F8451]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </div>
                                        <p class="font-medium text-gray-800 text-lg">Edit Profile</p>
                                        <p class="text-base text-gray-600 mt-2">Update your information</p>
                                    </div>
                                </button>

                                <button
                                    @click="downloadUserData"
                                    class="p-6 border border-gray-200 rounded-lg hover:border-[#2F8451] hover:bg-[#2F8451] hover:bg-opacity-5 transition-all group">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-[#2F8451] bg-opacity-10 rounded-lg flex items-center justify-center mx-auto mb-4 group-hover:bg-[#2F8451] group-hover:bg-opacity-20">
                                            <svg class="w-8 h-8 text-[#2F8451]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <p class="font-medium text-gray-800 text-lg">Export Data</p>
                                        <p class="text-base text-gray-600 mt-2">Download your data</p>
                                    </div>
                                </button>

                                <button
                                    @click="showHelpCenterModal = true"
                                    class="p-6 border border-gray-200 rounded-lg hover:border-[#2F8451] hover:bg-[#2F8451] hover:bg-opacity-5 transition-all group">
                                    <div class="text-center">
                                        <div class="w-16 h-16 bg-[#2F8451] bg-opacity-10 rounded-lg flex items-center justify-center mx-auto mb-4 group-hover:bg-[#2F8451] group-hover:bg-opacity-20">
                                            <svg class="w-8 h-8 text-[#2F8451]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="font-medium text-gray-800 text-lg">Help Center</p>
                                        <p class="text-base text-gray-600 mt-2">Get support</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showChangePasswordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-semibold text-gray-800">Change Password</h3>
                            <button @click="closeChangePasswordModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div v-if="passwordChangeSuccess" class="mb-6 p-5 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="text-green-800 text-base">Password changed successfully!</p>
                            </div>
                        </div>

                        <div v-if="passwordChangeError" class="mb-6 p-5 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-red-800 text-base">{{ passwordChangeError }}</p>
                            </div>
                        </div>

                        <form @submit.prevent="changePassword" class="space-y-5">
                            <div>
                                <label for="current_password" class="block text-base font-medium text-gray-700 mb-2">
                                    Current Password
                                </label>
                                <div class="relative">
                                    <input
                                        :type="passwordFieldType.current"
                                        id="current_password"
                                        v-model="passwordForm.current_password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451] focus:border-transparent text-base pr-12"
                                        required
                                    >
                                    <button
                                        type="button"
                                        @click="togglePasswordVisibility('current')"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-sm leading-5"
                                    >
                                        <svg class="h-6 w-6" :class="passwordFieldType.current === 'password' ? 'text-[#2F8451]' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path v-if="passwordFieldType.current === 'text'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path v-if="passwordFieldType.current === 'text'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            <path v-if="passwordFieldType.current === 'password'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.843A12.001 12.001 0 0112 21c-4.478 0-8.268-2.943-9.542-7 .983-3.003 3.031-5.183 5.402-6.538m7.447-3.038A9.999 9.999 0 0112 3c4.478 0 8.268 2.943 9.542 7-.472 1.447-1.558 2.78-2.909 3.844M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 0a.75.75 0 100-1.5.75.75 0 000 1.5z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="new_password" class="block text-base font-medium text-gray-700 mb-2">
                                    New Password
                                </label>
                                <div class="relative">
                                    <input
                                        :type="passwordFieldType.new"
                                        id="new_password"
                                        v-model="passwordForm.new_password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451] focus:border-transparent text-base pr-12"
                                        required
                                    >
                                    <button
                                        type="button"
                                        @click="togglePasswordVisibility('new')"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-sm leading-5"
                                    >
                                        <svg class="h-6 w-6" :class="passwordFieldType.new === 'password' ? 'text-[#2F8451]' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path v-if="passwordFieldType.new === 'text'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path v-if="passwordFieldType.new === 'text'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            <path v-if="passwordFieldType.new === 'password'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.843A12.001 12.001 0 0112 21c-4.478 0-8.268-2.943-9.542-7 .983-3.003 3.031-5.183 5.402-6.538m7.447-3.038A9.999 9.999 0 0112 3c4.478 0 8.268 2.943 9.542 7-.472 1.447-1.558 2.78-2.909 3.844M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 0a.75.75 0 100-1.5.75.75 0 000 1.5z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-base font-medium text-gray-700 mb-2">
                                    Confirm New Password
                                </label>
                                <div class="relative">
                                    <input
                                        :type="passwordFieldType.confirm"
                                        id="new_password_confirmation"
                                        v-model="passwordForm.new_password_confirmation"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451] focus:border-transparent text-base pr-12"
                                        required
                                    >
                                    <button
                                        type="button"
                                        @click="togglePasswordVisibility('confirm')"
                                        class="absolute inset-y-0 right-0 pr-4 flex items-center text-sm leading-5"
                                    >
                                        <svg class="h-6 w-6" :class="passwordFieldType.confirm === 'password' ? 'text-[#2F8451]' : 'text-gray-500'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path v-if="passwordFieldType.confirm === 'text'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path v-if="passwordFieldType.confirm === 'text'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            <path v-if="passwordFieldType.confirm === 'password'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.843A12.001 12.001 0 0112 21c-4.478 0-8.268-2.943-9.542-7 .983-3.003 3.031-5.183 5.402-6.538m7.447-3.038A9.999 9.999 0 0112 3c4.478 0 8.268 2.943 9.542 7-.472 1.447-1.558 2.78-2.909 3.844M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 0a.75.75 0 100-1.5.75.75 0 000 1.5z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-4 pt-6">
                                <button
                                    type="button"
                                    @click="closeChangePasswordModal"
                                    class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors text-base font-medium"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="passwordChangeLoading"
                                    class="px-6 py-3 bg-[#2F8451] text-white rounded-lg hover:bg-[#245a3d] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center text-base font-medium"
                                >
                                    <svg v-if="passwordChangeLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ passwordChangeLoading ? 'Changing...' : 'Change Password' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div v-if="showEditProfileModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-semibold text-gray-800">Edit Profile</h3>
                            <button @click="closeEditProfileModal" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div v-if="profileUpdateSuccess" class="mb-6 p-5 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <p class="text-green-800 text-base">Profile updated successfully!</p>
                            </div>
                        </div>

                        <div v-if="profileUpdateError" class="mb-6 p-5 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-red-800 text-base">{{ profileUpdateError }}</p>
                            </div>
                        </div>

                        <form @submit.prevent="updateProfile" class="space-y-5">
                            <div>
                                <label for="name" class="block text-base font-medium text-gray-700 mb-2">
                                    Full Name
                                </label>
                                <input
                                    type="text"
                                    id="name"
                                    v-model="profileForm.name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451] focus:border-transparent text-base"
                                    required
                                >
                            </div>

                            <div>
                                <label for="email" class="block text-base font-medium text-gray-700 mb-2">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    v-model="profileForm.email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451] focus:border-transparent text-base"
                                    required
                                >
                            </div>

                            <div class="flex justify-end space-x-4 pt-6">
                                <button
                                    type="button"
                                    @click="closeEditProfileModal"
                                    class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors text-base font-medium"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="profileUpdateLoading"
                                    class="px-6 py-3 bg-[#2F8451] text-white rounded-lg hover:bg-[#245a3d] transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center text-base font-medium"
                                >
                                    <svg v-if="profileUpdateLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ profileUpdateLoading ? 'Updating...' : 'Update Profile' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div v-if="showHelpCenterModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
                    <div class="flex h-full">
                        <div class="w-80 bg-gray-50 border-r border-gray-200">
                            <div class="bg-gradient-to-r from-[#2F8451] to-[#3a9d63] p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-xl font-bold">Help Center</h3>
                                    <button @click="closeHelpCenterModal" class="text-white hover:text-gray-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                <p class="mt-2 text-sm text-gray-100">Get help and support</p>
                            </div>

                            <div class="p-4">
                                <nav class="space-y-2">
                                    <button
                                        @click="activeHelpTab = 'overview'"
                                        :class="activeHelpTab === 'overview' ? 'bg-[#2F8451] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                        class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left"
                                    >
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                                        </svg>
                                        Overview
                                    </button>

                                    <button
                                        @click="activeHelpTab = 'tickets'"
                                        :class="activeHelpTab === 'tickets' ? 'bg-[#2F8451] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                        class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left"
                                    >
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                        Support Tickets
                                        <span v-if="tickets.filter(t => t.status !== 'closed').length > 0"
                                              class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                          {{ tickets.filter(t => t.status !== 'closed').length }}
                                        </span>
                                    </button>

                                    <button
                                        @click="activeHelpTab = 'create'"
                                        :class="activeHelpTab === 'create' ? 'bg-[#2F8451] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                        class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left"
                                    >
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Submit Ticket
                                    </button>

                                    <button
                                        @click="activeHelpTab = 'contact'"
                                        :class="activeHelpTab === 'contact' ? 'bg-[#2F8451] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                        class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left"
                                    >
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Contact Us
                                    </button>

                                    <button
                                        @click="activeHelpTab = 'faq'"
                                        :class="activeHelpTab === 'faq' ? 'bg-[#2F8451] text-white' : 'text-gray-700 hover:bg-gray-100'"
                                        class="w-full flex items-center px-4 py-3 rounded-lg transition-colors text-left"
                                    >
                                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        FAQ
                                    </button>
                                </nav>
                            </div>
                        </div>

                        <div class="flex-1 overflow-y-auto">
                            <div v-if="activeHelpTab === 'overview'" class="p-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6">Welcome to Help Center</h2>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                    <div class="bg-gradient-to-r from-[#2F8451] to-[#3a9d63] rounded-xl p-6 text-white">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm opacity-90">Open Tickets</p>
                                                <p class="text-3xl font-bold">{{ tickets.filter(t => t.status === 'open').length }}</p>
                                            </div>
                                            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm opacity-90">In Progress</p>
                                                <p class="text-3xl font-bold">{{ tickets.filter(t => t.status === 'in_progress').length }}</p>
                                            </div>
                                            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm opacity-90">Resolved</p>
                                                <p class="text-3xl font-bold">{{ tickets.filter(t => t.status === 'resolved').length }}</p>
                                            </div>
                                            <svg class="w-12 h-12 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl border border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Tickets</h3>
                                    <div v-if="tickets.length === 0" class="text-center py-8 text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                        <p>No tickets found</p>
                                        <button @click="activeHelpTab = 'create'" class="mt-4 bg-[#2F8451] text-white px-4 py-2 rounded-lg hover:bg-[#245a3d] transition-colors">
                                            Create Your First Ticket
                                        </button>
                                    </div>
                                    <div v-else class="space-y-3">
                                        <div v-for="ticket in tickets.slice(0, 5)" :key="ticket.id"
                                             class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
                                             @click="viewTicket(ticket)">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                                     :class="getStatusColor(ticket.status)">
                                                    <span class="text-xs font-bold text-white">#{{ ticket.id }}</span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ ticket.subject }}</p>
                                                    <p class="text-sm text-gray-600">{{ ticket.category }} • {{ formatDate(ticket.created_at) }}</p>
                                                </div>
                                            </div>
                                            <span class="px-3 py-1 rounded-full text-xs font-medium"
                                                  :class="getStatusBadgeColor(ticket.status)">
                                                {{ formatStatus(ticket.status) }}
                                              </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="activeHelpTab === 'tickets'" class="p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <h2 class="text-2xl font-bold text-gray-800">Support Tickets</h2>
                                    <button @click="activeHelpTab = 'create'"
                                            class="bg-[#2F8451] text-white px-4 py-2 rounded-lg hover:bg-[#245a3d] transition-colors flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        New Ticket
                                    </button>
                                </div>

                                <div class="flex space-x-4 mb-6">
                                    <select v-model="ticketFilter" @change="fetchTickets" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451]">
                                        <option value="all">All Tickets</option>
                                        <option value="open">Open</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="closed">Closed</option>
                                    </select>

                                    <select v-model="priorityFilter" @change="fetchTickets" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451]">
                                        <option value="all">All Priorities</option>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>

                                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                    <div v-if="ticketLoading" class="flex justify-center items-center py-12">
                                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#2F8451]"></div>
                                    </div>
                                    <div v-else-if="ticketError" class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                                        <p class="text-red-800">{{ ticketError }}</p>
                                        <button @click="fetchTickets" class="mt-4 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Try Again</button>
                                    </div>
                                    <div v-else-if="filteredTickets.length === 0" class="text-center py-12 text-gray-500">
                                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                        </svg>
                                        <p>No tickets found with the current filters.</p>
                                    </div>
                                    <div v-else>
                                        <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                                            <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-600">
                                                <div class="col-span-1">ID</div>
                                                <div class="col-span-4">Subject</div>
                                                <div class="col-span-2">Category</div>
                                                <div class="col-span-2">Priority</div>
                                                <div class="col-span-2">Status</div>
                                                <div class="col-span-1">Date</div>
                                            </div>
                                        </div>
                                        <div class="divide-y divide-gray-200">
                                            <div v-for="ticket in filteredTickets" :key="ticket.id"
                                                 class="px-6 py-4 hover:bg-gray-50 transition-colors cursor-pointer"
                                                 @click="viewTicket(ticket)">
                                                <div class="grid grid-cols-12 gap-4 items-center">
                                                    <div class="col-span-1">
                                                        <span class="font-mono text-sm text-gray-600">#{{ ticket.id }}</span>
                                                    </div>
                                                    <div class="col-span-4">
                                                        <p class="font-medium text-gray-800 truncate">{{ ticket.subject }}</p>
                                                        <p class="text-sm text-gray-600 truncate">{{ ticket.description }}</p>
                                                    </div>
                                                    <div class="col-span-2">
                                                        <span class="text-sm text-gray-600">{{ ticket.category }}</span>
                                                    </div>
                                                    <div class="col-span-2">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                                          :class="getPriorityColor(ticket.priority)">
                                                      {{ formatPriority(ticket.priority) }}
                                                    </span>
                                                    </div>
                                                    <div class="col-span-2">
                                                    <span class="px-2 py-1 rounded-full text-xs font-medium"
                                                          :class="getStatusBadgeColor(ticket.status)">
                                                      {{ formatStatus(ticket.status) }}
                                                    </span>
                                                    </div>
                                                    <div class="col-span-1">
                                                        <span class="text-sm text-gray-600">{{ formatDate(ticket.created_at) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="activeHelpTab === 'create'" class="p-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6">Submit Support Ticket</h2>

                                <div class="bg-white rounded-xl border border-gray-200 p-6">
                                    <div v-if="ticketSubmitSuccess" class="mb-6 p-5 bg-green-50 border border-green-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <p class="text-green-800 text-base">Ticket submit successfully! Redirecting to your tickets...</p>
                                        </div>
                                    </div>

                                    <div v-if="ticketSubmitError" class="mb-6 p-5 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-red-800 text-base">{{ ticketSubmitError }}</p>
                                        </div>
                                    </div>

                                    <form @submit.prevent="createTicket" class="space-y-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                                <select v-model="newTicket.category" required
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451]">
                                                    <option value="">Select Category</option>
                                                    <option value="Technical">Technical Support</option>
                                                    <option value="Billing">Billing & Payment</option>
                                                    <option value="Account">Account Issues</option>
                                                    <option value="Feature">Feature Request</option>
                                                    <option value="Bug">Bug Report</option>
                                                    <option value="General">General Inquiry</option>
                                                    <option value="Hardware Support">Hardware Support</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                                                <select v-model="newTicket.priority" required
                                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451]">
                                                    <option value="">Select Priority</option>
                                                    <option value="low">Low</option>
                                                    <option value="medium">Medium</option>
                                                    <option value="high">High</option>
                                                    <option value="critical">Critical</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                            <input type="text" v-model="newTicket.subject" required
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451]"
                                                   placeholder="Brief description of your issue">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                            <textarea v-model="newTicket.description" required rows="6"
                                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451]"
                                                      placeholder="Please provide detailed information about your issue..."></textarea>
                                        </div>

                                        <div class="flex justify-end space-x-4">
                                            <button type="button" @click="resetTicketForm"
                                                    class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                                                Reset
                                            </button>
                                            <button type="submit" :disabled="ticketSubmitting"
                                                    class="px-6 py-3 bg-[#2F8451] text-white rounded-lg hover:bg-[#245a3d] transition-colors disabled:opacity-50 flex items-center">
                                                <svg v-if="ticketSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                {{ ticketSubmitting ? 'Submitting...' : 'Submit Ticket' }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div v-if="activeHelpTab === 'contact'" class="p-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6">Contact Us</h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                                        <div class="flex items-center mb-4">
                                            <div class="w-12 h-12 bg-[#2F8451] bg-opacity-10 rounded-full flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-[#2F8451]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <h4 class="text-xl font-semibold text-gray-800">Email Support</h4>
                                        </div>
                                        <p class="text-gray-600 mb-6">Send us an email and we'll get back to you within 24 hours.</p>
                                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <span class="text-gray-700 truncate">app.leaf.pos@gmail.com</span>
                                            <a
                                                href="mailto:app.leaf.pos@gmail.com"
                                                class="bg-[#2F8451] text-white px-4 py-2 rounded-lg hover:bg-[#245a3d] transition-colors text-sm font-medium whitespace-nowrap ml-4"
                                            >
                                                Send Email
                                            </a>
                                        </div>
                                    </div>

                                    <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                                        <div class="flex items-center mb-4">
                                            <div class="w-12 h-12 bg-[#25D366] bg-opacity-10 rounded-full flex items-center justify-center mr-4">
                                                <svg class="w-6 h-6 text-[#25D366]" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </div>
                                            <h4 class="text-xl font-semibold text-gray-800">WhatsApp Support</h4>
                                        </div>
                                        <p class="text-gray-600 mb-6">Get instant support via WhatsApp message.</p>
                                        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg border border-gray-200">
                                            <span class="text-gray-700 truncate">+62 812-8348-7502</span>
                                            <a
                                                href="https://wa.me/6281283487502"
                                                target="_blank"
                                                class="bg-[#25D366] text-white px-4 py-2 rounded-lg hover:bg-[#128C7E] transition-colors text-sm font-medium whitespace-nowrap ml-4"
                                            >
                                                Chat Now
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 bg-white rounded-xl border border-gray-200 p-6">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">Business Hours</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-600">Email Support</p>
                                            <p class="text-gray-800">24/7 - We respond within 24 hours</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-600">WhatsApp Support</p>
                                            <p class="text-gray-800">Monday - Friday: 9:00 AM - 6:00 PM (WIB)</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="activeHelpTab === 'faq'" class="p-8">
                                <h2 class="text-2xl font-bold text-gray-800 mb-6">Frequently Asked Questions</h2>

                                <div class="space-y-4">
                                    <div v-for="(faq, index) in faqs" :key="index" class="bg-white rounded-xl border border-gray-200">
                                        <div class="p-6 cursor-pointer" @click="toggleFaq(index + 1)">
                                            <div class="flex justify-between items-center">
                                                <h5 class="font-medium text-gray-800">{{ faq.question }}</h5>
                                                <svg class="w-5 h-5 text-gray-500 transition-transform"
                                                     :class="{ 'transform rotate-180': openFaq === index + 1 }"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                            <div v-if="openFaq === index + 1" class="mt-4 text-gray-600">
                                                <p>{{ faq.answer }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="showTicketDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between p-6 border-b border-gray-200">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Ticket #{{ selectedTicket?.id }}</h3>
                            <p class="text-sm text-gray-600">{{ selectedTicket?.subject }}</p>
                        </div>
                        <button @click="closeTicketDetail" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 overflow-y-auto flex-1">
                        <div v-if="selectedTicket" class="space-y-6">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Status</p>
                                    <select v-model="selectedTicket.status"
                                            @change="updateTicketStatus(selectedTicket.id, selectedTicket.status)"
                                            class="px-3 py-1 rounded-full text-sm font-medium"
                                            :class="getStatusBadgeColor(selectedTicket.status)">
                                        <option value="open">Open</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="resolved">Resolved</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                    <p v-if="statusUpdateError" class="text-red-500 text-xs mt-1">{{ statusUpdateError }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Priority</p>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                                          :class="getPriorityColor(selectedTicket.priority)">
                                {{ formatPriority(selectedTicket.priority) }}
                            </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Category</p>
                                    <p class="text-gray-800">{{ selectedTicket.category }}</p>
                                </div>

                                <div>
                                    <p class="text-sm font-medium text-gray-600">User</p>
                                    <p class="text-gray-800">{{ selectedTicket.user?.name || 'N/A' }}</p>
                                </div>
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-800 mb-2">Description</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 whitespace-pre-wrap">{{ selectedTicket.description }}</p>
                                </div>
                            </div>

                            <div v-if="selectedTicket.responses && selectedTicket.responses.length > 0">
                                <h4 class="font-medium text-gray-800 mb-4">Responses</h4>
                                <div class="space-y-4">
                                    <div v-for="response in selectedTicket.responses" :key="response.id"
                                         class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="font-medium text-gray-800">{{ response.author_name || 'System' }}</span>
                                            <span class="text-sm text-gray-600">{{ formatDate(response.created_at) }}</span>
                                        </div>
                                        <p class="text-gray-700">{{ response.message }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="p-6 border-t border-gray-200">
                        <h4 class="font-medium text-gray-800 mb-3">Add a Response</h4>
                        <div v-if="responseError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-800 text-sm">
                            {{ responseError }}
                        </div>
                        <form @submit.prevent="addTicketResponse" class="flex flex-col space-y-4">
                         <textarea v-model="newResponseMessage" rows="3" placeholder="Type your response here..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2F8451]" required></textarea>
                            <div class="flex justify-end">
                                <button type="submit" :disabled="submittingResponse"
                                        class="bg-[#2F8451] text-white px-5 py-2.5 rounded-lg hover:bg-[#245a3d] transition-colors disabled:opacity-50 flex items-center">
                                    <svg v-if="submittingResponse" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    {{ submittingResponse ? 'Sending...' : 'Send Response' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Sidebar>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Sidebar from "@/Components/Sidebar.vue"
import { jsPDF } from 'jspdf'
import Swal from 'sweetalert2';

const props = defineProps({
    auth: {
        type: Object,
        default: () => ({})
    }
})

const getAuthToken = () => {
    return localStorage.getItem('X-API-TOKEN')
}

if (getAuthToken()) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${getAuthToken()}`
}

const user = ref(null)
const loading = ref(true)
const error = ref(null)

const showChangePasswordModal = ref(false)
const passwordChangeLoading = ref(false)
const passwordChangeSuccess = ref(false)
const passwordChangeError = ref(null)
const statusUpdateError = ref(null)
const passwordForm = ref({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
})

const passwordFieldType = ref({
    current: 'password',
    new: 'password',
    confirm: 'password'
})

const showEditProfileModal = ref(false)
const profileUpdateLoading = ref(false)
const profileUpdateSuccess = ref(false)
const profileUpdateError = ref(null)
const profileForm = ref({
    name: '',
    email: ''
})

const showHelpCenterModal = ref(false)
const activeHelpTab = ref('overview')
const openFaq = ref(null)

// Ticket System State
const tickets = ref([])
const ticketLoading = ref(false)
const ticketError = ref(null)
const newTicket = ref({
    category: '',
    priority: '',
    subject: '',
    description: ''
})
const ticketSubmitting = ref(false)
const ticketSubmitSuccess = ref(false)
const ticketSubmitError = ref(null)

const showTicketDetail = ref(false)
const selectedTicket = ref(null)
const newResponseMessage = ref('')
const submittingResponse = ref(false)
const responseError = ref(null)

const faqs = ref([
    {
        question: 'How do I reset my password?',
        answer: 'You can reset your password by clicking on the "Change Password" option in your profile settings. You will need to enter your current password and then your new password twice.'
    },
    {
        question: 'How can I update my profile information?',
        answer: 'To update your profile information, click on "Edit Profile" under Quick Actions. You can change your full name and email address there.'
    },
    {
        question: 'Where can I view my support tickets?',
        answer: 'All your submitted support tickets can be viewed under the "Support Tickets" tab in the Help Center. You can filter them by status and priority.'
    },
    {
        question: 'How long does it take to get a response to a support ticket?',
        answer: 'We strive to respond to all support tickets within 24-48 business hours. Critical issues are prioritized and addressed more quickly.'
    },
    {
        question: 'Can I attach files to my support tickets?',
        answer: 'Currently, direct file attachments are not supported through the ticket creation form. If you need to send files, please mention it in your ticket description, and our support team will provide instructions.'
    }
])

const ticketFilter = ref('all')
const priorityFilter = ref('all')


const filteredTickets = computed(() => {

    let filtered = tickets.value ?? [];

    if (ticketFilter.value !== 'all') {
        filtered = filtered.filter(ticket => ticket.status === ticketFilter.value)
    }

    if (priorityFilter.value !== 'all') {
        filtered = filtered.filter(ticket => ticket.priority === priorityFilter.value)
    }

    return filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

watch([ticketFilter, priorityFilter], () => {
    fetchTickets();
});


const fetchUserProfile = async () => {
    try {
        loading.value = true
        error.value = null

        const token = getAuthToken()
        if (!token) {
            console.warn('API token not found. User profile might not be fetched. Redirecting to login...')
            error.value = 'Authentication required. Please log in.'
            return
        }

        const response = await axios.get('/api/users/current', {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })
        user.value = response.data.data

        profileForm.value = {
            name: user.value.name,
            email: user.value.email
        }
    } catch (err) {
        console.error('Error fetching user profile:', err)
        error.value = err.response?.data?.message || 'Failed to load user profile'
        if (err.response?.status === 401 || err.response?.status === 403) {
            console.log("Authentication failed. Redirecting to login if applicable.");
        }
    } finally {
        loading.value = false
    }
}

const getInitials = (name) => {
    if (!name) return 'U'

    const words = name.trim().split(' ')
    if (words.length === 1) {
        return words[0].charAt(0).toUpperCase()
    }

    return words.map(word => word.charAt(0).toUpperCase()).join('').substring(0, 2)
}

const changePassword = async () => {
    try {
        passwordChangeLoading.value = true
        passwordChangeError.value = null
        passwordChangeSuccess.value = false

        if (passwordForm.value.new_password !== passwordForm.value.new_password_confirmation) {
            passwordChangeError.value = 'New password and confirmation do not match.'
            return
        }

        if (passwordForm.value.new_password.length < 8) {
            passwordChangeError.value = 'New password must be at least 8 characters long.'
            return
        }

        const token = getAuthToken()
        if (!token) {
            passwordChangeError.value = 'Authentication token missing. Please log in again.'
            return
        }

        const response = await axios.patch('/api/users/change-password', passwordForm.value, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        passwordChangeSuccess.value = true

        passwordForm.value = {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
        }

        passwordFieldType.value = {
            current: 'password',
            new: 'password',
            confirm: 'password'
        }

        setTimeout(() => {
            closeChangePasswordModal()
        }, 2000)

    } catch (err) {
        console.error('Error changing password:', err)

        if (err.response) {
            if (err.response.status === 401) {
                passwordChangeError.value = 'Unauthorized: Invalid current password or session expired.'
            } else if (err.response.status === 403) {
                passwordChangeError.value = 'Forbidden: You do not have permission to change the password.'
            } else if (err.response.status === 422 && err.response.data.errors) {
                const errors = err.response.data.errors
                let errorMessage = ''
                for (const key in errors) {
                    errorMessage += errors[key].join(', ') + ' '
                }
                passwordChangeError.value = errorMessage.trim()
            } else {
                passwordChangeError.value = err.response?.data?.message || 'Failed to change password.'
            }
        } else {
            passwordChangeError.value = 'Network error or server is unreachable.'
        }
    } finally {
        passwordChangeLoading.value = false
    }
}

const updateProfile = async () => {
    try {
        profileUpdateLoading.value = true
        profileUpdateError.value = null
        profileUpdateSuccess.value = false

        if (!profileForm.value.name.trim()) {
            profileUpdateError.value = 'Name cannot be empty.'
            return
        }

        if (!profileForm.value.email.trim()) {
            profileUpdateError.value = 'Email cannot be empty.'
            return
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        if (!emailRegex.test(profileForm.value.email)) {
            profileUpdateError.value = 'Please enter a valid email address.'
            return
        }

        const token = getAuthToken()
        if (!token) {
            profileUpdateError.value = 'Authentication token missing. Please log in again.'
            return
        }

        const response = await axios.patch('/api/users/change-email-or-username', {
            name: profileForm.value.name,
            email: profileForm.value.email
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        user.value.name = profileForm.value.name
        user.value.email = profileForm.value.email

        profileUpdateSuccess.value = true

        setTimeout(() => {
            closeEditProfileModal()
        }, 2000)

    } catch (err) {
        console.error('Error updating profile:', err)

        if (err.response) {
            if (err.response.status === 401) {
                profileUpdateError.value = 'Unauthorized: Session expired. Please log in again.'
            } else if (err.response.status === 403) {
                profileUpdateError.value = 'Forbidden: You do not have permission to update this profile.'
            } else if (err.response.status === 422 && err.response.data.errors) {
                const errors = err.response.data.errors
                let errorMessage = ''

                if (errors.name) {
                    errorMessage += errors.name.join(', ') + ' '
                }

                if (errors.email) {
                    errorMessage += errors.email.join(', ')
                }

                profileUpdateError.value = errorMessage.trim()
            } else {
                profileUpdateError.value = err.response?.data?.message || 'Failed to update profile.'
            }
        } else {
            profileUpdateError.value = 'Network error or server is unreachable.'
        }
    } finally {
        profileUpdateLoading.value = false
    }
}

const downloadUserData = () => {
    if (!user.value) return

    try {
        const doc = new jsPDF()

        doc.setFillColor(47, 132, 81)
        doc.rect(0, 0, 210, 40, 'F')

        doc.setTextColor(255, 255, 255)
        doc.setFontSize(22)
        doc.setFont('helvetica', 'bold')
        doc.text('User Profile Data', 105, 20, { align: 'center' })

        doc.setFontSize(12)
        doc.setFont('helvetica', 'normal')
        doc.text('Generated on: ' + new Date().toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' }), 105, 30, { align: 'center' })

        doc.setTextColor(0, 0, 0)
        doc.setFontSize(14)
        doc.setFont('helvetica', 'bold')
        doc.text('Personal Information', 20, 60)

        doc.setDrawColor(47, 132, 81)
        doc.setLineWidth(0.5)
        doc.line(20, 65, 190, 65)

        doc.setFontSize(12)
        doc.setFont('helvetica', 'normal')

        const details = [
            { label: 'User ID', value: '#' + user.value.id },
            { label: 'Full Name', value: user.value.name },
            { label: 'Email', value: user.value.email },
            { label: 'Role', value: user.value.role.name },
            { label: 'Status', value: user.value.status }
        ]

        let yPos = 80
        details.forEach(detail => {
            doc.setFont('helvetica', 'bold')
            doc.text(detail.label + ':', 20, yPos)
            doc.setFont('helvetica', 'normal')
            doc.text(detail.value, 70, yPos)
            yPos += 10
        })

        const pageCount = doc.internal.getNumberOfPages()
        doc.setFontSize(10)
        doc.setTextColor(150, 150, 150) // Gray text
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i)
            doc.text('Page ' + i + ' of ' + pageCount, 105, 290, { align: 'center' })
            doc.text('This document contains confidential information.', 105, 295, { align: 'center' })
        }

        doc.save(`user-profile-${user.value.id}.pdf`)
    } catch (error) {
        console.error('Error generating PDF:', error)
        alert('Failed to generate PDF. Please try again.')
    }
}

const togglePasswordVisibility = (field) => {
    passwordFieldType.value[field] = passwordFieldType.value[field] === 'password' ? 'text' : 'password'
}

const toggleFaq = (id) => {
    openFaq.value = openFaq.value === id ? null : id
}

const closeChangePasswordModal = () => {
    showChangePasswordModal.value = false
    passwordChangeSuccess.value = false
    passwordChangeError.value = null
    passwordForm.value = {
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
    }
    passwordFieldType.value = {
        current: 'password',
        new: 'password',
        confirm: 'password'
    }
}

const closeEditProfileModal = () => {
    showEditProfileModal.value = false
    profileUpdateSuccess.value = false
    profileUpdateError.value = null

    if (user.value) {
        profileForm.value = {
            name: user.value.name,
            email: user.value.email
        }
    }
}

const closeHelpCenterModal = () => {
    showHelpCenterModal.value = false
    activeHelpTab.value = 'overview'
}

const fetchTickets = async () => {
    ticketLoading.value = true
    ticketError.value = null
    try {
        const token = getAuthToken()
        if (!token) {
            ticketError.value = 'Authentication required to fetch tickets.'
            return
        }

        const params = {};
        if (ticketFilter.value !== 'all') {
            params.status = ticketFilter.value;
        }
        if (priorityFilter.value !== 'all') {
            params.priority = priorityFilter.value;
        }

        const response = await axios.get('/api/tickets', {
            headers: {
                Authorization: `Bearer ${token}`
            },
            params: params
        })

        tickets.value = response.data.data.ticket

    } catch (err) {
        console.error('Error fetching tickets:', err)
        ticketError.value = err.response?.data?.message || 'Failed to load tickets.'
        if (err.response?.status === 401 || err.response?.status === 403) {
        }
    } finally {
        ticketLoading.value = false
    }
}

const createTicket = async () => {
    ticketSubmitting.value = true
    ticketSubmitSuccess.value = false
    ticketSubmitError.value = null

    try {
        if (!newTicket.value.category || !newTicket.value.priority || !newTicket.value.subject || !newTicket.value.description) {
            ticketSubmitError.value = 'Please fill in all required fields.'
            return
        }

        const token = getAuthToken()
        if (!token) {
            ticketSubmitError.value = 'Authentication token missing. Please log in again.'
            return
        }

        const response = await axios.post('/api/tickets', newTicket.value, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        })

        ticketSubmitSuccess.value = true
        resetTicketForm()
        fetchTickets()

        activeHelpTab.value = 'tickets'
        setTimeout(() => {
            ticketSubmitSuccess.value = false
        }, 3000);

    } catch (err) {
        console.error('Error creating ticket:', err)
        if (err.response && err.response.status === 422) {
            const errors = err.response.data.errors
            let errorMessage = ''
            for (const key in errors) {
                errorMessage += (errors[key].join(', ') + ' ')
            }
            ticketSubmitError.value = errorMessage.trim()
        } else {
            ticketSubmitError.value = err.response?.data?.message || 'Failed to submit ticket.'
        }
    } finally {
        ticketSubmitting.value = false
    }
}

const resetTicketForm = () => {
    newTicket.value = {
        category: '',
        priority: '',
        subject: '',
        description: ''
    }
}

const viewTicket = async (ticket) => {
    console.log('Attempting to view ticket with ID:', ticket.id);
    try {
        const token = getAuthToken();
        if (!token) {
            console.error('Authentication token missing for ticket detail.');

            Swal.fire({
                icon: 'error',
                title: 'Authentication Error',
                text: 'Authentication token missing. Please log in again.',
                confirmButtonColor: '#2F8451'
            });
            return;
        }

        selectedTicket.value = null;
        showTicketDetail.value = true;
        const response = await axios.get(`/api/tickets/${ticket.id}`, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });
        selectedTicket.value = response.data.data?.ticket || null;
    } catch (err) {
        console.error('Error fetching ticket detail:', err);
        selectedTicket.value = null;
        showTicketDetail.value = false;

        Swal.fire({
            icon: 'error',
            title: 'Error Loading Ticket',
            text: err.response?.data?.errors?.message || 'Failed to load ticket details. Please try again later.',
            confirmButtonColor: '#2F8451'
        });
    }
};

const closeTicketDetail = () => {
    showTicketDetail.value = false
    selectedTicket.value = null
    newResponseMessage.value = ''
    responseError.value = null
}

const updateTicketStatus = async (ticketId, newStatus) => {
    statusUpdateError.value = null;
    try {
        const token = getAuthToken();
        if (!token) {
            statusUpdateError.value = 'Authentication token missing. Please log in again.';
            return;
        }

        const response = await axios.patch(`/api/tickets/${ticketId}/status`, {
            status: newStatus
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        selectedTicket.value.status = response.data.data.status;

        fetchTickets();

    } catch (err) {
        console.error('Error updating ticket status:', err);
        statusUpdateError.value = err.response?.data?.message || 'Failed to update ticket status.';
        if (selectedTicket.value) {
        }
    }
};

const addTicketResponse = async () => {
    if (!newResponseMessage.value.trim() || !selectedTicket.value) {
        return;
    }

    submittingResponse.value = true;
    responseError.value = null;

    try {
        const token = getAuthToken();
        if (!token) {
            responseError.value = 'Authentication token missing. Please log in again.';
            return;
        }

        const response = await axios.post(`/api/tickets/${selectedTicket.value.id}/responses`, {
            message: newResponseMessage.value
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        selectedTicket.value.responses.push(response.data.data);
        newResponseMessage.value = '';

    } catch (err) {
        console.error('Error adding response:', err);
        responseError.value = err.response?.data?.message || 'Failed to add response.';
    } finally {
        submittingResponse.value = false;
    }
};


const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) {
        return dateString;
    }
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
    return date.toLocaleDateString('en-US', options)
}

const getStatusColor = (status) => {
    switch (status) {
        case 'open': return 'bg-red-500';
        case 'in_progress': return 'bg-blue-500';
        case 'resolved': return 'bg-purple-500';
        case 'closed': return 'bg-gray-400';
        default: return 'bg-gray-400';
    }
}

const getStatusBadgeColor = (status) => {
    switch (status) {
        case 'open': return 'bg-red-100 text-red-800';
        case 'in_progress': return 'bg-blue-100 text-blue-800';
        case 'resolved': return 'bg-purple-100 text-purple-800';
        case 'closed': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

const getPriorityColor = (priority) => {
    switch (priority) {
        case 'low': return 'bg-green-100 text-green-800';
        case 'medium': return 'bg-yellow-100 text-yellow-800';
        case 'high': return 'bg-orange-100 text-orange-800';
        case 'critical': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

const formatStatus = (status) => {
    if (!status) return 'N/A';
    return status.replace(/_/g, ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')
}

const formatPriority = (priority) => {
    if (!priority) return 'N/A';
    return priority.charAt(0).toUpperCase() + priority.slice(1)
}

const isAnyModalOpen = computed(() => {
    return showHelpCenterModal.value || showChangePasswordModal.value || showEditProfileModal.value || showTicketDetail.value;
});


watch(isAnyModalOpen, (newValue) => {
    if (newValue) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}, { immediate: true });


onMounted(() => {
    fetchUserProfile()
    fetchTickets()
})
</script>
