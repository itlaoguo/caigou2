<?php

namespace Modules\Base\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class LanguageRequest extends Request
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
'code' => 'required|string',

        ];
    }


    /**
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}
