<script setup>
import { computed } from 'vue';
import { formatExpiry, formatPercent } from '../format.js';

const props = defineProps({
    coupon: {
        type: Object,
        required: true,
    },
});

const uses = computed(() => {
    if (props.coupon.usage_limit === null || props.coupon.usage_limit === undefined) {
        return 'sem limite';
    }

    return `${props.coupon.times_used} / ${props.coupon.usage_limit}`;
});
</script>

<template>
    <article class="ticket" aria-label="Cupom emitido">
        <div class="ticket-main">
            <p class="ticket-code">{{ coupon.code }}</p>
            <p class="ticket-percent">{{ formatPercent(coupon.percentage) }}</p>
        </div>
        <div class="ticket-rip" aria-hidden="true"></div>
        <dl class="ticket-stub">
            <div>
                <dt>Validade</dt>
                <dd>{{ formatExpiry(coupon.expires_at) }}</dd>
            </div>
            <div>
                <dt>Usos</dt>
                <dd>{{ uses }}</dd>
            </div>
        </dl>
    </article>
</template>
