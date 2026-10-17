<script setup>
import { computed, ref } from 'vue';
import { formatPercent } from '../format.js';
import { postJson, validationMessages } from '../http.js';
import CouponTicket from './CouponTicket.vue';

const code = ref('');
const percentage = ref('');
const expiresAt = ref('');
const usageLimit = ref('');
const pending = ref(false);
const errors = ref([]);
const coupon = ref(null);

const preview = computed(() => formatPercent(percentage.value));

function onCodeInput(event) {
    const upper = event.target.value.toUpperCase();
    event.target.value = upper;
    code.value = upper;
}

async function submit() {
    pending.value = true;
    errors.value = [];
    coupon.value = null;

    const body = {
        code: code.value.trim(),
        percentage: percentage.value === '' ? '' : Number(percentage.value),
        expires_at: expiresAt.value,
    };

    if (usageLimit.value !== '') {
        body.usage_limit = Number(usageLimit.value);
    }

    try {
        const result = await postJson('/api/coupons', body);

        if (result.status === 201) {
            coupon.value = result.data;

            return;
        }

        errors.value = validationMessages(result.data);
    } catch {
        errors.value = ['Não foi possível falar com o balcão.'];
    } finally {
        pending.value = false;
    }
}
</script>

<template>
    <section class="flex flex-col gap-5" aria-labelledby="create-heading">
        <form class="paper-card" @submit.prevent="submit">
            <h2 id="create-heading">Novo cupom</h2>

            <label class="field">
                <span>Código</span>
                <input
                    :value="code"
                    class="code-input"
                    type="text"
                    name="code"
                    maxlength="50"
                    autocomplete="off"
                    spellcheck="false"
                    @input="onCodeInput"
                >
            </label>

            <div class="percentage-row">
                <label class="field">
                    <span>Porcentagem</span>
                    <input
                        v-model="percentage"
                        type="number"
                        name="percentage"
                        min="0"
                        max="100"
                        step="any"
                        inputmode="decimal"
                    >
                </label>
                <div class="mini-ticket" aria-hidden="true">
                    <strong :class="{ 'is-empty': percentage === '' }">{{ preview }}</strong>
                </div>
            </div>

            <label class="field">
                <span>Validade</span>
                <input v-model="expiresAt" type="datetime-local" name="expires_at">
            </label>

            <label class="field">
                <span>Limite de usos</span>
                <input
                    v-model="usageLimit"
                    type="number"
                    name="usage_limit"
                    min="1"
                    step="1"
                    inputmode="numeric"
                    placeholder="Sem limite"
                >
            </label>

            <button class="btn" type="submit" :disabled="pending">Emitir cupom</button>

            <p v-for="message in errors" :key="message" class="stamp" role="alert">{{ message }}</p>
        </form>

        <CouponTicket v-if="coupon" :coupon="coupon" />
    </section>
</template>
