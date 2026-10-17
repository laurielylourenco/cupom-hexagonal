<?php

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ADAPTADOR DE ENTRADA — validação HTTP (Form Request).
 *
 * Valida o body de POST /coupons/{code}/apply.
 * O código do cupom vem da rota ({code}); aqui só validamos o valor da compra.
 */
class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // Valor da compra sobre o qual o desconto será calculado
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
