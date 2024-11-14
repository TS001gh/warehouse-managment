<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = request()->route('id');

        // dd(request()->all());
        return [
            'name' => 'required|string|unique:items,name,' . $id . '|max:255',
            'code' => 'required|string|unique:items,code,' . $id . '|max:50',
            'min_quantity' => 'required|integer|min:0',
            'current_quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|mimes:avif,jpeg,png,bmp,gif,svg,webp|max:16384',
            'is_active' => 'boolean',
            'group_id' => 'required|exists:groups,id', // ensures group exists
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
