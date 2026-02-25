<?php

namespace App\Http\Requests\Room;

use Illuminate\Foundation\Http\FormRequest;

class IndexRoomRequest extends FormRequest
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
