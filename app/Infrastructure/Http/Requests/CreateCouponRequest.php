<?php

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ADAPTADOR DE ENTRADA — validação HTTP (Form Request).
 *
 * Antes de o controller chamar o use case, o Laravel valida o JSON/form
 * com estas regras. Assim o núcleo (Application/Domain) só recebe dados
 * já no formato esperado e não precisa conhecer HTTP nem regras de request.
 *
 * Se a validação falhar, o Laravel responde 422 automaticamente — sem
 * chegar no CreateCouponUseCase.
 */
class CreateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Estudo sem autenticação: qualquer cliente pode criar cupom.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50'],
            'percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'expires_at' => ['required', 'date'],
            // null = sem limite de usos; se informado, precisa ser inteiro >= 1
            'usage_limit' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
