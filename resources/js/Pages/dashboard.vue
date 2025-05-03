<template>
    <div>
        <Sidebar :auth="auth"></Sidebar>
    </div>
</template>
<script setup>
import Sidebar from '../Components/Sidebar.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const auth = page.props.auth
</script>