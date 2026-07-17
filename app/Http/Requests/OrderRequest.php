<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Gate;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $type = strtoupper((string) $this->input('type'));
        $type = $type === 'DISTRIBUTER' ? 'DISTRIBUTOR' : $type;
        $sellerId = $this->input('seller_id');
        $sellerType = strtoupper((string) $this->input('seller_type'));

        if (is_string($sellerId) && str_contains($sellerId, ':')) {
            [$sellerType, $sellerId] = array_pad(explode(':', $sellerId, 2), 2, null);
        }

        if ($type === 'RETAILER') {
            $sellerType = 'DISTRIBUTOR';
        }

        $this->merge([
            'type' => $type,
            'seller_id' => $sellerId,
            'seller_type' => $sellerType ?: null,
        ]);
    }

    public function authorize()
    {
        abort_if(Gate::denies('order_create') || Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }


    public function rules()
    {
        $type = strtoupper((string) $this->input('type'));
        $type = $type === 'DISTRIBUTER' ? 'DISTRIBUTOR' : $type;

        $buyerRules = $type === 'DISTRIBUTOR'
            ? ['required', 'integer', 'exists:master_distributors,id']
            : [
                'required',
                'integer',
                Rule::exists('secondary_customers', 'id')->where(fn ($query) => $query
                    ->where('type', $type)
                    ->where(function ($activeQuery) {
                        $activeQuery->where('active', 'Y')->orWhere('status', 'Active');
                    })),
            ];

        $sellerRules = $type === 'DISTRIBUTOR'
            ? ['nullable']
            : ['required', 'integer'];

        if ($type === 'RETAILER') {
            $sellerRules[] = 'exists:master_distributors,id';
        } elseif ($type !== 'DISTRIBUTOR') {
            $sellerRules[] = function ($attribute, $value, $fail) {
                $valid = $this->input('seller_type') === 'DISTRIBUTOR'
                    ? \App\Models\MasterDistributor::whereKey($value)->where('business_status', 'Active')->exists()
                    : ($this->input('seller_type') === 'RETAILER'
                        && \App\Models\SecondaryCustomer::whereKey($value)->where('type', 'RETAILER')->where('active', 'Y')->exists());

                if (!$valid) {
                    $fail('The selected parent must be an active Retailer or Distributor.');
                }
            };
        }

        $customerRules = [
            'type' => ['required', Rule::in(['RETAILER', 'WORKSHOP', 'MECHANIC', 'GARAGE', 'DISTRIBUTOR'])],
            'buyer_id' => $buyerRules,
            'seller_id' => $sellerRules,
            'seller_type' => $type === 'DISTRIBUTOR'
                ? ['nullable']
                : ['required', Rule::in($type === 'RETAILER' ? ['DISTRIBUTOR'] : ['DISTRIBUTOR', 'RETAILER'])],
            'orderdetail.*.subcategory_id' => [
                'nullable',
                'integer',
                Rule::exists('subcategories', 'id')->where(fn ($query) => $query->where('active', 'Y')),
            ],
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
