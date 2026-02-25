<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

class IndexMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
