<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('order_create') || Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }


    public function rules()
    {
        $type = strtoupper((string) $this->input('type'));
        $type = $type === 'DISTRIBUTER' ? 'DISTRIBUTOR' : $type;

        $customerRules = [
            'type' => ['required', Rule::in(['RETAILER', 'WORKSHOP', 'MECHANIC', 'GARAGE', 'DISTRIBUTOR'])],
            'seller_id' => ['required', 'integer', 'exists:master_distributors,id'],
            'buyer_id' => $type === 'DISTRIBUTOR'
                ? ['nullable']
                : [
                    'required',
                    'integer',
                    Rule::exists('secondary_customers', 'id')->where(fn ($query) => $query->where('type', $type)),
                ],
            'retailer_id' => in_array($type, ['GARAGE', 'WORKSHOP'], true)
                ? [
                    'required',
                    'integer',
                    Rule::exists('secondary_customers', 'id')->where(fn ($query) => $query->where('type', 'RETAILER')),
                ]
                : ['nullable'],
        ];

        $rules = [];
        switch($this) {
            case !empty($this->id) :
                $rules = [
                    // 'buyer_id'      => 'nullable|numeric|exists:customers,id',
                    // 'seller_id'     => 'nullable|numeric|exists:customers,id',
                    'total_qty'     => 'nullable|numeric',
                    'shipped_qty'   => 'nullable|numeric',
                    'orderno'       => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'order_date'    => 'nullable|date_format:Y-m-d',
                    'completed_date'=> 'nullable|date_format:Y-m-d',
                    'total_gst'     => 'nullable|numeric',
                    'total_discount'=> 'nullable|numeric',
                    'extra_discount'=> 'nullable|numeric',
                    'extra_discount_amount' => 'nullable|numeric',
                    'sub_total'     => 'nullable|numeric',
                    'grand_total'   => 'nullable|numeric',
                    'created_by'    => 'nullable|numeric|exists:users,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                ];
                break;
            default :
                $rules = [
                    // 'buyer_id'      => 'nullable|numeric|exists:customers,id',
                    // 'seller_id'     => 'nullable|numeric|exists:customers,id',
                    'total_qty'     => 'nullable|numeric',
                    'shipped_qty'   => 'nullable|numeric',
                    'orderno'       => 'nullable|min:2|max:100|string|regex:/[a-zA-Z0-9\s]+/',
                    'order_date'    => 'nullable|date_format:Y-m-d',
                    'completed_date'=> 'nullable|date_format:Y-m-d',
                    'total_gst'     => 'nullable|numeric',
                    'total_discount'=> 'nullable|numeric',
                    'extra_discount'=> 'nullable|numeric',
                    'extra_discount_amount' => 'nullable|numeric',
                    'sub_total'     => 'nullable|numeric',
                    'grand_total'   => 'nullable|numeric',
                    'created_by'    => 'nullable|numeric|exists:users,id',
                    'status_id'     => 'nullable|numeric|exists:statuses,id',
                ];
                break;
        }
        return array_merge($rules, $customerRules);
    }
}
