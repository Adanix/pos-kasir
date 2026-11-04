<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DemoQuickLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return config('demo.enabled', true);
    }

    public function rules(): array
    {
        $allowed = array_keys(config('demo.personas', []));

        return [
            'persona' => ['required', 'string', 'in:' . implode(',', $allowed)],
        ];
    }

    public function messages(): array
    {
        return [
            'persona.required' => 'Pilihan peran demo wajib diisi.',
            'persona.in' => 'Peran demo tidak valid atau tidak terdaftar.',
        ];
    }
}
