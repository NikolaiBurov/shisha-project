<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'term' => 'required|min:2',
            'per_page' => 'required|int',
            'current_page' => 'required|int',
        ];
    }

    /**
     * @return array
     */
    public function onlyInRules(): array
    {
        return $this->only(array_keys($this->rules()));
    }
}
