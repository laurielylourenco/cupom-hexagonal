<script setup>
import { computed } from 'vue';
import { formatBrl } from '../format.js';

const props = defineProps({
    amount: {
        type: Number,
        required: true,
    },
    discount: {
        type: Number,
        required: true,
    },
});

const due = computed(() => props.amount - props.discount);
</script>

<template>
    <article class="receipt" aria-label="Recibo">
        <p>
            <span>Valor da compra</span>
            <strong>{{ formatBrl(amount) }}</strong>
        </p>
        <p class="receipt-discount">
            <span>Desconto</span>
            <strong>{{ formatBrl(discount) }}</strong>
        </p>
        <p class="receipt-due">
            <span>A pagar</span>
            <strong>{{ formatBrl(due) }}</strong>
        </p>
    </article>
</template>
