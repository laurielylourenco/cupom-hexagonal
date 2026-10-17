/**
 * Cliente HTTP da página. A API não usa CSRF.
 *
 * @param {string} url
 * @param {Record<string, unknown>} body
 * @returns {Promise<{ ok: boolean, status: number, data: Record<string, any> }>}
 */
export async function postJson(url, body) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(body),
    });

    const data = await response.json().catch(() => ({}));

    return {
        ok: response.ok,
        status: response.status,
        data,
    };
}

/**
 * Mensagens de 409 (message) ou 422 (errors do Laravel).
 *
 * @param {Record<string, any>} data
 * @returns {string[]}
 */
export function validationMessages(data) {
    if (data?.errors && typeof data.errors === 'object') {
        return Object.values(data.errors).flat().map(String);
    }

    if (typeof data?.message === 'string' && data.message !== '') {
        return [data.message];
    }

    return ['Não foi possível concluir o pedido.'];
}
