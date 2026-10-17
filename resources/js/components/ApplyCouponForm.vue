<script setup>
import { ref } from 'vue';
import { postJson } from '../http.js';
import Receipt from './Receipt.vue';

const code = ref('');
const amount = ref('');
const pending = ref(false);
const error = ref('');
const receipt = ref(null);

function onCodeInput(event) {
    const upper = event.target.value.toUpperCase();
    event.target.value = upper;
    code.value = upper;
}

async function submit() {
    pending.value = true;
    error.value = '';
    receipt.value = null;

    const trimmed = code.value.trim();

    try {
        const result = await postJson(`/api/coupons/${encodeURIComponent(trimmed)}/apply`, {
            amount: amount.value === '' ? '' : Number(amount.value),
        });

        const discount = Number(result.data?.discount);

        if (result.status === 200 && Number.isFinite(discount)) {
            receipt.value = {
                amount: Number(amount.value),
                discount,
            };

            return;
        }

        error.value = typeof result.data?.message === 'string' && result.data.message !== ''
            ? result.data.message
            : 'Não foi possível aplicar o cupom.';
    } catch {
        error.value = 'Não foi possível falar com o balcão.';
    } finally {
        pending.value = false;
    }
}
</script>

<template>
    <section class="flex flex-col gap-5" aria-labelledby="apply-heading">
        <form class="paper-card" @submit.prevent="submit">
            <h2 id="apply-heading">Na compra</h2>

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

            <label class="field">
                <span>Valor da compra</span>
                <input
                    v-model="amount"
                    type="number"
                    name="amount"
                    min="0"
                    step="0.01"
                    inputmode="decimal"
                >
            </label>

            <p class="apply-note">Aplicar calcula o desconto e registra um uso.</p>

            <button class="btn" type="submit" :disabled="pending">Aplicar cupom</button>

            <p v-if="error" class="stamp" role="alert">{{ error }}</p>
        </form>

        <Receipt v-if="receipt" :amount="receipt.amount" :discount="receipt.discount" />
    </section>
</template>
