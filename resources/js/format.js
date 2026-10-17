const brl = new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL',
});

/**
 * @param {number|string} value
 */
export function formatBrl(value) {
    return brl.format(Number(value));
}

/**
 * @param {number|string|null|undefined} value
 */
export function formatPercent(value) {
    if (value === '' || value === null || value === undefined || Number.isNaN(Number(value))) {
        return '–';
    }

    const formatted = new Intl.NumberFormat('pt-BR', {
        maximumFractionDigits: 2,
    }).format(Number(value));

    return `${formatted}%`;
}

/**
 * @param {string} iso
 */
export function formatExpiry(iso) {
    const date = new Date(iso);

    if (Number.isNaN(date.getTime())) {
        return iso;
    }

    return new Intl.DateTimeFormat('pt-BR', {
        dateStyle: 'short',
        timeStyle: 'short',
    }).format(date);
}
