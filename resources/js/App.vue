<script setup>
import ApplyCouponForm from './components/ApplyCouponForm.vue';
import CreateCouponForm from './components/CreateCouponForm.vue';
</script>

<template>
    <div class="min-h-screen bg-cream text-ink">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
            <header class="mb-8">
                <p class="text-xs font-medium uppercase tracking-[0.22em] text-ink/55">Balcão</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight">Cupons</h1>
            </header>

            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-2">
                <CreateCouponForm />
                <ApplyCouponForm />
            </div>
        </div>
    </div>
</template>
