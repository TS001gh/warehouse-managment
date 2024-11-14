<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutboundRequest extends FormRequest
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
        return [
            'item_id' => 'required|exists:items,id', // ensures item exists
            'quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    // dd($value);
                    $item = \App\Models\Item::find($this->item_id);
                    if ($item && ($item->current_quantity - $value) < $item->min_quantity) {
                        $fail("إن الكمية المخصصة للخارج سوف تجعل المخزون أقل من الحد الأدنى لهذا العنصر، الحد الأدنى المسموح به هو : " . number_format($item->min_quantity) . " وحدة.");
                    }
                },
            ],
            'date' => [
                'required',
                'date',
                'after_or_equal:today', // Ensures the date is today or later
            ],
            'customer_id' => 'required|exists:customers,id', // ensures customer exists
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
