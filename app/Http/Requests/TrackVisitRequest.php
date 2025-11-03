<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TrackVisitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'visitorKey' => ['nullable','string','max:191'],
            'url'        => ['required','url','max:2048'],
            'referrer'   => ['nullable','url','max:2048'],
            'ua'         => ['nullable','string','max:2000'],
            'ts'         => ['nullable','integer','min:0'],
        ];
    }
}
