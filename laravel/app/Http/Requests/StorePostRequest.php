<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'   => 'required|integer',
            'caption'   => 'required|string',
            'media_url' => 'required|url',
        ];
    }
}
