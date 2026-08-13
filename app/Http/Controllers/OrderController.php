<?php

    namespace App\Http\Controllers;

    use App\Models\Order;
    use Illuminate\Http\Request;
    use App\Models\OrderDetails;
    use App\Models\Product;
    use App\Models\Status;
    use App\Models\City;
    use App\Models\User;
    use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Support\Facades\Redirect;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;


    use DataTables;
    use Validator;
    use Gate;
    use Excel;
    use App\DataTables\OrderDataTable;
    use App\Exports\OrderEmailExport;
    use App\Imports\OrderImport;
    use App\Exports\OrderExport;
    use App\Exports\OrderTemplate;
    use App\Http\Requests\OrderRequest;
    use App\Mail\OrderMailWithAttachment;
    use App\Models\Category;
    use App\Models\Division;
    use App\Models\Sales;
    use App\Models\MasterDistributor;
    use App\Models\SecondaryCustomer;
    use App\Models\SalesDetails;
    use App\Models\Subcategory;
    // use App\Models\Customers;
    use Dompdf\Dompdf;
    use Dompdf\Options;
    use Illuminate\Support\Facades\Mail;

    class OrderController extends Controller
    {
        public function __construct()
        {
            $this->middleware('auth');
            $this->orders = new Order();
        }

        public function index(OrderDataTable $dataTable)
        {
            abort_if(Gate::denies('order_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $sellers_ids = $this->orders->distinct()->pluck('seller_id');
            $createdByIds = $this->orders->whereNotNull('created_by')->distinct()->pluck('created_by');
            $executiveIds = $this->orders->whereNotNull('executive_id')->distinct()->pluck('executive_id');
            $user_ids = $createdByIds->merge($executiveIds)->filter()->unique()->values();
            $buyer_ids = $this->orders->distinct()->pluck('buyer_id');
            $divisions = Category::where('active', 'Y')->get();
            $retailers = SecondaryCustomer::whereIn("id", $buyer_ids)->get();
            $distributors = MasterDistributor::whereIn("id", $sellers_ids)->get();
            $customer_types = ['RETAILER', 'WORKSHOP', 'MECHANIC', 'GARAGE', 'DISTRIBUTOR'];
            $users = User::whereIn('id', $user_ids)->orderBy('name')->get();
            return $dataTable->render('orders.index', compact('divisions', 'retailers', 'distributors', 'customer_types' , 'users'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return \Illuminate\Http\Response
         */
        public function create()
        {
            abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $products = Product::with('productdetails')
                ->where('active', 'Y')
                ->select(
                    'id',
                    'product_name',
                    'product_image',
                    'display_name',
                    'product_code'
                )
                ->orderBy('product_name', 'asc')
                ->get();

            $userids = getUsersReportingToAuth();
            $sellers = array();
            $masterDistributors = MasterDistributor::select('id','legal_name')->get();

            $secondaryCustomers = SecondaryCustomer::select('id','shop_name','owner_name')->get();

            // $buyers = Customers::whereIn('customertype', ['1', '3', '4', '5', '6'])
            //     ->where(function ($query) use ($userids) {
            //         if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            //             $query->whereIn('executive_id', $userids);
            //         }
            //     })
            //     ->where('active', '=', 'Y')
            //     ->whereNotNull('sap_code')
            //     ->select('id', 'name', 'mobile', 'sap_code')
            //     ->get();

            $users = User::where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('id', $userids);
                }
            })->whereNull('customerid')->whereHas('roles', function ($query) {
                $query->whereNot('id', ['29']);
            })->select('id', 'name')->orderBy('id', 'desc')->get();
            $subcategories = Subcategory::where('active', 'Y')
                ->orderBy('subcategory_name')
                ->get(['id', 'subcategory_name']);

            $category = Category::where('active', 'Y')->get();
            // dd($products,$masterDistributors,$secondaryCustomers,$users,$category,$subcategories);
            return view('orders.create', compact(
                'products',
                'masterDistributors',
                'secondaryCustomers',
                'users',
                'category',
                'subcategories'
            ))->with('orders', $this->orders);        
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @return \Illuminate\Http\Response
         */
        public function store(OrderRequest $request)
        {
            // dd($request);
            try {
                
                abort_if(Gate::denies('order_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
                $request['created_by'] = Auth::user()->id;
                $request['orderno'] = isset($request['orderno']) ? $request['orderno'] : date('Ymd') . '_' . autoIncrementId('Order', 'id');


                $customerType = strtoupper((string) $request->type);
                $customerType = $customerType === 'DISTRIBUTER' ? 'DISTRIBUTOR' : $customerType;
                $request->merge(['type' => $customerType]);

                // if (!empty($request['buyer_id'])) {
                //     $buyer = $request['buyer_id'];
                // } else {
                //     $buyer = $request['seller_id'];
                // }


                // $request['buyer_id'] = isset($request['seller_id']) ? $request['seller_id'] : null;
                // $request['seller_id'] = $buyer;
                // Order type mapping
                if ($customerType === 'DISTRIBUTOR') {

                    $request['order_type'] = 'MASTER_DISTRIBUTOR';

                    // Distributor order
                    $request['seller_id'] = $request['buyer_id'];
                    $request['seller_type'] = 'DISTRIBUTOR';
                    $request['retailer_id'] = null;

                } else {

                    $request['order_type'] = 'SECONDARY_CUSTOMER';

                    // Retailer / Workshop / Garage / Mechanic order
                    $request['seller_id'] = $request['seller_id'];
                    $request['buyer_id'] = $request['buyer_id'];
                    $request['seller_type'] = $request['seller_type'];

                }



                $request['cluster_amount'] = $request['extra_cluster_discount'];
                $request['deal_discount'] = $request['extra_discount'] ?? NULL;
                $request['deal_amount'] = $request['extra_discount_amount'] ?? NULL;
                $request['distributor_amount'] = $request['distributor_discount_amount'];
                $request['frieght_amount'] = $request['frieght_discount_amount'];
                $request['special_amount'] = $request['special_discount_amount'];
                $request['ebd_amount'] = $request['extra_ebd_discount'];
                $request['gst5_amt'] = $request['5_gst'];
                $request['gst12_amt'] = $request['12_gst'];
                $request['gst18_amt'] = $request['18_gst'];
                $request['gst28_amt'] = $request['28_gst'];
                // Order type mapping
                if ($customerType === 'DISTRIBUTOR') {

                    $request['order_type'] = 'MASTER_DISTRIBUTOR';

                    // optional
                    $request['customer_type'] = 'DISTRIBUTOR';

                } else {

                    $request['order_type'] = 'SECONDARY_CUSTOMER';

                    // Store actual customer type
                    $request['customer_type'] = strtoupper($request->type);

                }

                

                $response =  $this->orders->save_data($request);
                // dd($response);
                if ($response['status'] == 'success') {
                    $orderdetail = collect([]);
                    foreach ($request['orderdetail'] as $key => $rows) {
                        $tax = $rows['tax_amount'] ?? 0;
                        $single_product_amount = $rows['line_total'] + $tax;
                        // $single_product_amount = $rows['line_total'] + $rows['tax_amount'];
                        $single_product_amount = number_format((float)$single_product_amount, 2, '.', '');

                        $orderdetail->push([
                            'active' => 'Y',
                            'order_id' => isset($response['order_id']) ? $response['order_id'] : null,
                            'product_id' => isset($rows['product_id']) ? $rows['product_id'] : null,
                            'product_detail_id' => isset($rows['product_detail']) ? $rows['product_detail'] : null,
                            'quantity' => isset($rows['quantity']) ? $rows['quantity'] : 0,
                            'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] : 0,
                            'price' => isset($rows['mrp']) ? $rows['mrp'] : 0.00,
                            'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] : 0.00,
                            'line_total' => isset($rows['line_total']) ? $rows['line_total'] : 0.00,
                            'gst' => isset($rows['gst']) ? $rows['gst'] : 0.00,
                            'gst_amount' => $single_product_amount ?? 0.00,
                            'discount' => isset($rows['discount']) ? $rows['discount'] : 0.00,
                            'scheme_discount' => isset($rows['scheme_dis']) ? $rows['scheme_dis'] : 0.00,
                            'scheme_name' => isset($rows['scheme_name']) ? $rows['scheme_name'] : null,
                            'scheme_amount' => isset($rows['scheme_amount']) ? $rows['scheme_amount'] : 0.00,
                            'cluster_discount' => isset($rows['clustered_dis']) ? $rows['clustered_dis'] : 0.00,
                            'cluster_amount' => isset($rows['clus_amounts']) ? $rows['clus_amounts'] : 0.00,
                            'distributor_discount' => isset($rows['distributot_dis']) ? $rows['distributot_dis'] : 0.00,
                            'distributor_amount' => isset($rows['distributot_amounts']) ? $rows['distributot_amounts'] : 0.00,
                            'deal_discount' => isset($rows['deal_dis']) ? $rows['deal_dis'] : 0.00,
                            'deal_amount' => isset($rows['deal_amounts']) ? $rows['deal_amounts'] : 0.00,
                            'ebd_dis' => isset($rows['ebd_dis']) ? $rows['ebd_dis'] : 0.00,
                            'ebd_amount' => isset($rows['ebd_amounts']) ? $rows['ebd_amounts'] : 0.00,
                            'special_dis' => isset($rows['special_dis']) ? $rows['special_dis'] : 0.00,
                            'special_amounts' => isset($rows['special_amounts']) ? $rows['special_amounts'] : 0.00,
                            'frieght_discount' => isset($rows['frieght_dis']) ? $rows['frieght_dis'] : 0.00,
                            'frieght_amount' => isset($rows['frieght_amounts']) ? $rows['frieght_amounts'] : 0.00,
                            'agri_standard_dis' => isset($rows['agri_standard_dis']) ? $rows['agri_standard_dis'] : 0.00,
                            'agri_standard_dis_amounts' => isset($rows['agri_standard_dis_amounts']) ? $rows['agri_standard_dis_amounts'] : 0.00,
                            'created_at' => getcurentDateTime(),
                        ]);
                    }

                    OrderDetails::insert($orderdetail->toArray());

                    $order = null;

                    if (!empty($response['order_id'])) {
                        $order = Order::find($response['order_id']);
                    }
                    if ($order) {

                        $newData = [

                        'orderno'         => $order->orderno,
                    'buyer_id'        => $order->buyer_id,
                    'seller_id'       => $order->seller_id,
                    'retailer_id'     => $order->retailer_id,
                    'executive_id'    => $order->executive_id ?? $request->executive_id ?? Auth::id(),
                    'order_type'      => $order->order_type,
                    'customer_type'   => $order->customer_type,
                    'sub_total'       => round((float) $order->sub_total, 2),
                    'total_gst'       => round((float) $order->total_gst, 2),
                    'grand_total'     => round((float) $order->grand_total, 2),
                    'total_qty'       => $order->total_qty,
                    'order_remark'    => $order->order_remark,
                    'order_date'      => $order->order_date,
                    'products'        => $orderdetail->toArray(),


                            // 'order_no'   => $order->orderno ?? null,
                            // 'order_type' => $order->order_type ?? null,
                            // 'seller_id'  => $order->seller_id ?? null,
                            // 'buyer_id'   => $order->buyer_id ?? null,
                            // 'total'      => $order->total_amount ?? null,
                            // 'items'      => count($request['orderdetail'] ?? []),
                        ];

                        // 👇 Dynamic module_id
                        $moduleId = $order->order_type === 'SECONDARY_CUSTOMER'
                            ? $order->buyer_id
                            : $order->seller_id;

                        logActivity(
                            'order',
                            $moduleId, // ✅ FIXED
                            'created',
                            'transaction',
                            null,
                            $newData,
                            $order->order_type
                        );
                    }
                    $exportData = new Request();
                    $exportData->merge([
                        'order_id' => $response['order_id'],
                    ]);

                    Excel::store(new OrderEmailExport($exportData), '/assets/orderDetails.xlsx', 'local');

                    $user = User::find($request->executive_id);

                    if ($user?->userinfo?->order_mails) {
                        $mail_id_array = explode(',', $user->userinfo->order_mails);
                        $buyer = MasterDistributor::find($request['seller_id']);
                        $seller = SecondaryCustomer::find($request['buyer_id']);
                        // $buyer = Customers::find($request['buyer_id']);
                        // $seller = Customers::find($request['seller_id']);
                        $attachmentPath = base_path('storage/app/assets/orderDetails.xlsx');

                        // foreach ($mail_id_array as $k => $val) {
                        //  Mail::to($val)->send(new OrderMailWithAttachment($attachmentPath, $orderdetail, Order::find($response['order_id'])));
                        // }
                    }

                    return Redirect::to('orders')->with('message_success', 'Order Store Successfully');
                }
                return redirect()->back()->with('message_danger', 'Error in Purchases Store')->withInput();
            } catch (\Exception $e) {

                    
                return redirect()->back()->withErrors($e->getMessage())->withInput();
            }
        }

        /**
         * Display the specified resource.
         *
         * @param  \App\Models\Order  $order
         * @return \Illuminate\Http\Response
         */
        public function show($id, Request $request)
        {
            abort_if(Gate::denies('order_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $id = decrypt($id);
            $orders = $this->orders->with('sellers', 'createdbyname', 'buyers')->find($id);
            $orders?->resolveCustomerRelations();
            $orderdetails = OrderDetails::with('products')->where('order_id', '=', $id)->get();
            if ($orders->product_cat_id == '1') {
                $totalLP = 0;
                foreach ($orderdetails as $key => $value) {
                    $totalLP += $value->price * $value->quantity;
                }
                if ($totalLP > 0) {
                    $ttdis = number_format(((1 - ($orders->sub_total / $totalLP)) * 100), 2);
                } else {
                    $ttdis = false;
                }
            } else {
                $ttdis = false;
                $totalLP = false;
            }

            // dd($orders,$orderdetails);


            return view('orders.show', compact('orderdetails', 'orders', 'ttdis', 'totalLP'));

        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  \App\Models\Order  $order
         * @return \Illuminate\Http\Response
         */
       public function edit($id)
        {

        
            
            abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            
            $id = decrypt($id);
            $userids = getUsersReportingToAuth();
            $orders = $this->orders->with([
                'buyers.city',
                'buyers.state',
                'buyers.district',
                'buyers.pincode',
                'sellers',
                'orderdetails.products'
            ])->find($id);
            $orders?->resolveCustomerRelations();
            $orders->type = in_array($orders->order_type, ['MASTER_DISTRIBUTER', 'MASTER_DISTRIBUTOR'], true)
                ? 'DISTRIBUTOR'
                : strtoupper($orders->customer_type ?: ($orders->buyers->type ?? 'RETAILER'));
            $orders->customer_type = $orders->type;

            // $orderdetail = OrderDetails::with('products')->where('order_id', '=', $id)->get();
            $products = Product::where('active', '=', 'Y')->select('id', 'display_name', 'product_image')->get();
            $subcategories = Subcategory::where('active', 'Y')
                ->orderBy('subcategory_name')
                ->get(['id', 'subcategory_name']);
            $cities = \App\Models\City::pluck('city_name', 'id');
            $states = \App\Models\State::pluck('state_name', 'id');
            $districts = \App\Models\District::pluck('district_name', 'id');
            $pincodes = \App\Models\Pincode::pluck('pincode', 'id');
            // $sellers = Customers::where(function ($query) use ($userids) {
            //     if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            //         if (Auth::user()->hasRole('Accounts Order')) {
            //             $userids = User::whereIn('branch_id', explode(',', Auth::user()->branch_show))->pluck('id');
            //         }
            //         $query->whereIn('executive_id', $userids)
            //             ->orWhereIn('created_by', $userids);
            //     }
            // })
            //     ->where('active', '=', 'Y')
            //     ->select('id', 'name', 'mobile')
            //     ->get();

            // $buyers = Customers::whereIn('customertype', ['1', '3', '4', '5', '6'])
            //     ->where(function ($query) use ($userids) {
            //         if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            //             $query->whereIn('executive_id', $userids)
            //                 ->orWhereIn('created_by', $userids);
            //         }
            //     })
            //     ->where('active', '=', 'Y')
            //     ->select('id', 'name', 'mobile')
            //     ->get();

            $users = User::where(function ($query) use ($userids) {
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('id', $userids);
                }
            })->whereNull('customerid')->whereHas('roles', function ($query) {
                $query->whereNot('id', ['29']);
            })->select('id', 'name')->orderBy('id', 'desc')->get();

            $category = Category::where('active', 'Y')->get();
            // dd($orders,$products,$cities,$states);
            return view('orders.edit', compact('orders','products', 'users', 'category','subcategories','cities',
    'states',
    'districts',
    'pincodes'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  \Illuminate\Http\Request  $request
         * @param  \App\Models\Order  $order
         * @return \Illuminate\Http\Response
         */
        public function update(OrderRequest $request, $id)
        {

        // dd($request);
            abort_if(Gate::denies('order_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $id = decrypt($id);

            $request['cluster_amount'] = $request['extra_cluster_discount'];
            $request['deal_discount'] = $request['extra_discount'] ?? NULL;
            $request['deal_amount'] = $request['extra_discount_amount'] ?? NULL;
            $request['distributor_amount'] = $request['distributor_discount_amount'];
            $request['frieght_amount'] = $request['frieght_discount_amount'];
            $request['special_amount'] = $request['special_discount_amount'];
            $request['ebd_amount'] = $request['extra_ebd_discount'];
            $request['gst5_amt'] = $request['5_gst'];
            $request['gst12_amt'] = $request['12_gst'];
            $request['gst28_amt'] = $request['18_gst'];
            $request['gst18_amt'] = $request['28_gst'];
            $customerType = strtoupper((string) $request->type);
            $customerType = $customerType === 'DISTRIBUTER' ? 'DISTRIBUTOR' : $customerType;

            $orders = Order::with('orderdetails.products.subcategories','retailers')->find($id);
            $oldOrderData = $orders->toArray();
            $orders->buyer_id = $request->buyer_id;
            $orders->seller_id = $customerType === 'DISTRIBUTOR' ? $request->buyer_id : $request->seller_id;
            $orders->seller_type = $customerType === 'DISTRIBUTOR' ? 'DISTRIBUTOR' : $request->seller_type;
            $orders->customer_type = $customerType;
            $orders->order_type = $customerType === 'DISTRIBUTOR'
                ? 'MASTER_DISTRIBUTOR'
                : 'SECONDARY_CUSTOMER';
            $orders->retailer_id = null;

            //$orders->buyer_id = isset($request['buyer_id']) ? $request['buyer_id'] :null ;
            $orders->executive_id = isset($request['executive_id']) ? $request['executive_id'] : null;
            //$orders->seller_id = isset($request['seller_id']) ? $request['seller_id'] :null ;
            $orders->order_date = isset($request['order_date']) ? $request['order_date'] : null;
            $orders->order_remark = $request->input('order_remark');
            $orders->total_gst = isset($request['total_gst']) ? $request['total_gst'] : 0.00;
            $orders->total_discount = isset($request['total_discount']) ? $request['total_discount'] : 0.00;
            $orders->extra_discount = isset($request['extra_discount']) ? $request['extra_discount'] : 0.00;
            $orders->gst_amount = isset($request['gst_amount']) ? $request['gst_amount'] : null;
            $orders->schme_amount = isset($request['schme_amount']) ? $request['schme_amount'] : null;
            $orders->ebd_discount = isset($request['ebd_discount']) ? $request['ebd_discount'] : null;
            $orders->ebd_amount = isset($request['ebd_amount']) ? $request['ebd_amount'] : null;
            $orders->special_discount = isset($request['special_discount']) ? $request['special_discount'] : null;
            $orders->special_amount = isset($request['special_amount']) ? $request['special_amount'] : null;
            $orders->cluster_discount = isset($request['cluster_discount']) ? $request['cluster_discount'] : null;
            $orders->cluster_amount = isset($request['cluster_amount']) ? $request['cluster_amount'] : null;
            $orders->deal_discount = isset($request['deal_discount']) ? $request['deal_discount'] : null;
            $orders->deal_amount = isset($request['deal_amount']) ? $request['deal_amount'] : null;
            $orders->distributor_discount = isset($request['distributor_discount']) ? $request['distributor_discount'] : null;
            $orders->distributor_amount = isset($request['distributor_amount']) ? $request['distributor_amount'] : null;
            $orders->frieght_discount = isset($request['frieght_discount']) ? $request['frieght_discount'] : null;
            $orders->frieght_amount = isset($request['frieght_amount']) ? $request['frieght_amount'] : null;
            $orders->product_cat_id = isset($request['product_cat_id']) ? $request['product_cat_id'] : null;
            $orders->dod_discount = isset($request['dod_discount']) ? $request['dod_discount'] : null;
            $orders->cash_discount = isset($request['cash_discount']) ? $request['cash_discount'] : null;
            $orders->special_distribution_discount = isset($request['special_distribution_discount']) ? $request['special_distribution_discount'] : null;
            $orders->distribution_margin_discount = isset($request['distribution_margin_discount']) ? $request['distribution_margin_discount'] : null;
            $orders->total_fan_discount = isset($request['total_fan_discount']) ? $request['total_fan_discount'] : null;
            $orders->total_fan_discount_amount = isset($request['total_fan_discount_amount']) ? $request['total_fan_discount_amount'] : null;

            $orders->gst5_amt = isset($request['gst5_amt']) ? $request['gst5_amt'] : null;
            $orders->gst12_amt = isset($request['gst12_amt']) ? $request['gst12_amt'] : null;
            $orders->gst18_amt = isset($request['gst18_amt']) ? $request['gst18_amt'] : null;
            $orders->gst28_amt = isset($request['gst28_amt']) ? $request['gst28_amt'] : null;
            $orders->sub_total = isset($request['sub_total']) ? $request['sub_total'] : 0.00;
            $orders->grand_total = isset($request['grand_total']) ? $request['grand_total'] : 0.00;
            $orders->order_taking = isset($request['order_taking']) ? $request['order_taking'] : '';
            $orders->suc_del = isset($request['suc_del']) ? $request['suc_del'] : '';
            $orders->updated_by = Auth::user()->id;
            if ($orders->save()) {
                // foreach ($request['orderdetail'] as $key => $rows) {
                //         OrderDetails::updateOrCreate(['product_id' => $request['product_id'], 'order_id' => $id], [
                //             'order_id' => $id,
                //             'product_id' => isset($rows['product_id']) ? $rows['product_id'] :null,
                //             'product_detail_id' => isset($rows['product_detail']) ? $rows['product_detail'] :null,
                //             'quantity' => isset($rows['quantity']) ? $rows['quantity'] :0,
                //             'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] :0,
                //             'price' => isset($rows['price']) ? $rows['price'] :0.00,
                //             'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] :0.00,
                //             'line_total' => isset($rows['line_total']) ? $rows['line_total'] :0.00,
                //             'created_at' => getcurentDateTime(),
                //         ]);
                //     }


                $existingIds = OrderDetails::where('order_id', $id)
                    ->pluck('id')
                    ->toArray();

                $incomingIds = [];
                // foreach ($request['orderdetail'] as $key => $rows) {
                //     $check = OrderDetails::updateOrCreate(['product_id' => $rows['product_id'], 'order_id' => $id], [
                //         'order_id' => $id,
                //         'product_id' => isset($rows['product_id']) ? $rows['product_id'] : null,
                //         'product_detail_id' => isset($rows['product_detail']) ? $rows['product_detail'] : null,
                //         'quantity' => isset($rows['quantity']) ? $rows['quantity'] : 0,
                //         'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] : 0,
                //         'price' => isset($rows['price']) ? $rows['price'] : 0.00,
                //         'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] : 0.00,
                //         'line_total' => isset($rows['line_total']) ? $rows['line_total'] : 0.00,
                //         'gst' => isset($rows['gst']) ? $rows['gst'] : 0.00,
                //         'gst_amount' => $single_product_amount ?? 0.00,
                //         'discount' => isset($rows['discount']) ? $rows['discount'] : 0.00,
                //         'scheme_discount' => isset($rows['scheme_dis']) ? $rows['scheme_dis'] : 0.00,
                //         'scheme_name' => isset($rows['scheme_name']) ? $rows['scheme_name'] : null,
                //         'scheme_amount' => isset($rows['scheme_amount']) ? $rows['scheme_amount'] : 0.00,
                //         'cluster_discount' => isset($rows['clustered_dis']) ? $rows['clustered_dis'] : 0.00,
                //         'cluster_amount' => isset($rows['clus_amounts']) ? $rows['clus_amounts'] : 0.00,
                //         'distributor_discount' => isset($rows['distributot_dis']) ? $rows['distributot_dis'] : 0.00,
                //         'distributor_amount' => isset($rows['distributot_amounts']) ? $rows['distributot_amounts'] : 0.00,
                //         'deal_discount' => isset($rows['deal_dis']) ? $rows['deal_dis'] : 0.00,
                //         'deal_amount' => isset($rows['deal_amounts']) ? $rows['deal_amounts'] : 0.00,
                //         'ebd_dis' => isset($rows['ebd_dis']) ? $rows['ebd_dis'] : 0.00,
                //         'ebd_amount' => isset($rows['ebd_amounts']) ? $rows['ebd_amounts'] : 0.00,
                //         'special_dis' => isset($rows['special_dis']) ? $rows['special_dis'] : 0.00,
                //         'special_amounts' => isset($rows['special_amounts']) ? $rows['special_amounts'] : 0.00,
                //         'frieght_discount' => isset($rows['frieght_dis']) ? $rows['frieght_dis'] : 0.00,
                //         'frieght_amount' => isset($rows['frieght_amounts']) ? $rows['frieght_amounts'] : 0.00,
                //         'created_at' => getcurentDateTime(),
                //     ]);
                // }
                
                $deletedProducts = [];

                foreach ($request['orderdetail'] as $rows) {

                    $data = [
                        'order_id' => $id,
                        'product_id' => $rows['product_id'] ?? null,
                        'product_detail_id' => $rows['product_detail'] ?? null,
                        'quantity' => $rows['quantity'] ?? 0,
                        'shipped_qty' => $rows['shipped_qty'] ?? 0,
                        'price' => $rows['price'] ?? 0,
                        'tax_amount' => $rows['tax_amount'] ?? 0,
                        'line_total' => $rows['line_total'] ?? 0,
                        'gst' => $rows['gst'] ?? 0,
                        'discount' => $rows['discount'] ?? 0,
                    ];

                    if (!empty($rows['id'])) {
                        
                        OrderDetails::where('id', $rows['id'])->update($data);
                        $incomingIds[] = $rows['id'];
                    } else {
                     
                        $new = OrderDetails::create($data);
                        $incomingIds[] = $new->id;
                    }
                }

               
                $deleteIds = array_diff($existingIds, $incomingIds);

                if (!empty($deleteIds)) {

                    $deletedProducts = OrderDetails::whereIn('id', $deleteIds)
                        ->get()
                        ->toArray();

                    OrderDetails::whereIn('id', $deleteIds)->delete();
                }

                $newOrderData = Order::with('orderdetails')
                    ->find($id)
                    ->toArray();

                    $changesOld = [];
                    $changesNew = [];
                    
                    foreach ($newOrderData as $field => $newValue) {
                    
                        if (in_array($field, [
                            'updated_at',
                            'created_at',
                            'orderdetails',
                            'retailers'
                        ])) {
                            continue;
                        }
                    
                        $oldValue = $oldOrderData[$field] ?? null;
                    
                        // Normalize values
                        if (is_null($oldValue)) $oldValue = '';
                        if (is_null($newValue)) $newValue = '';
                    
                        // Convert arrays to json
                        if (is_array($oldValue)) {
                            $oldValue = json_encode($oldValue);
                        }
                    
                        if (is_array($newValue)) {
                            $newValue = json_encode($newValue);
                        }
                    
                        // Trim + cast
                        $oldValue = trim((string)$oldValue);
                        $newValue = trim((string)$newValue);
                    
                        // Compare
                        if ($oldValue != $newValue) {
                    
                            $changesOld[$field] = $oldOrderData[$field] ?? null;
                            $changesNew[$field] = $newOrderData[$field] ?? null;
                        }
                    }
                    
                    if (!empty($deletedProducts)) {

                        $changesOld['deleted_products'] = $deletedProducts;
                        $changesNew['deleted_products'] = [];
                    }
                    
                    if (!empty($changesNew)) {
                    
                        logActivity(
                            'order',
                            $request->buyer_id,
                            'updated',
                            'web',
                            $changesOld,
                            $changesNew,
                            strtoupper($orders->customer_type ?? 'ORDER')
                        );
                    }
                return Redirect::to('orders')->with('message_success', 'Order update Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Purchases Store')->withInput();
        }

        public function destroy($id)
        {
            abort_if(Gate::denies('order_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            OrderDetails::where('order_id', $id)->delete();
            $product = Order::find($id);
            if ($product->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Order deleted successfully!']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in User Delete!']);
        }

        public function active(Request $request)
        {
            if (Order::where('id', $request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' : 'Y'])) {
                $message = ($request['active'] == 'Y') ? 'Inactive' : 'Active';
                return response()->json(['status' => 'success', 'message' => 'Order ' . $message . ' Successfully!']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Status Update']);
        }

        public function upload(Request $request)
        {
            abort_if(Gate::denies('order_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();
            Excel::import(new OrderImport, request()->file('import_file'));
            return back();
        }
        public function download(Request $request)
        {
            abort_if(Gate::denies('order_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            $request->validate([
                'customer_type_id' => 'nullable|in:RETAILER,WORKSHOP,MECHANIC,GARAGE,DISTRIBUTOR',
            ]);
            if (ob_get_contents()) ob_end_clean();
            ob_start();
            return Excel::download(new OrderExport($request), 'orders.xlsx');
        }
        public function template()
        {
            abort_if(Gate::denies('order_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
            if (ob_get_contents()) ob_end_clean();
            ob_start();
            return Excel::download(new OrderTemplate, 'orders.xlsx');
        }

        public function ordersInfo(Request $request)
        {
            if ($request->ajax()) {
                $data = Order::with('sellers', 'buyers')
                    ->latest();
                    
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($data) {
                        return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
                    })
                    ->editColumn('order_date', function ($data) {
                        return isset($data->order_date) ? showdateformat($data->order_date) : '';
                    })
                    ->addColumn('action', function ($query) {
                        $btn = '';
                        if (auth()->user()->can(['order_show'])) {
                            $btn = $btn . '<a href="' . url("orders/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.orders.title_singular') . '">
                                                <i class="material-icons">visibility</i>
                                            </a>';
                        }
                        return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                            ' . $btn . '
                                        </div>';
                    })
                    ->filter(function ($query) use ($request) {
                        if (!empty($request['buyer_id'])) {
                            $query->where('buyer_id', $request['buyer_id'])->orWhere('seller_id', $request['buyer_id']);
                        }
                        if (!empty($request['seller_id'])) {
                            $query->where('seller_id', $request['seller_id'])->orWhere('buyer_id', $request['buyer_id']);
                        }
                        if (!empty($request['seller_id'])) {
                            $query->where('seller_id', $request['seller_id'])->orWhere('buyer_id', $request['buyer_id']);
                        }
                        if (!empty($request['created_by'])) {
                            $query->where('created_by', $request['created_by']);
                        }
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }

        public function ordertopoint()
        {

            $orders = Order::with('orderdetails')->whereNotNull(['buyer_id', 'seller_id'])->select('orderno as invoice_no', 'id', 'buyer_id', 'seller_id', 'grand_total', 'order_date as invoice_date', 'id as order_id', 'total_qty', 'shipped_qty', 'total_gst', 'status_id')->get();

            foreach ($orders as $key => $order) {
                $details = collect([]);
                $data = collect([
                    'order_id' => isset($order['order_id']) ? $order['order_id'] : null,
                    'invoice_no' => isset($order['invoice_no']) ? $order['invoice_no'] : null,
                    'buyer_id' => isset($order['buyer_id']) ? $order['buyer_id'] : null,
                    'seller_id' => isset($order['seller_id']) ? $order['seller_id'] : null,
                    'grand_total' => isset($order['grand_total']) ? $order['grand_total'] : 0.00,
                    'invoice_date' => isset($order['invoice_date']) ? $order['invoice_date'] : null,
                    'order_id' => isset($order['order_id']) ? $order['order_id'] : null,
                    'total_qty' => isset($order['total_qty']) ? $order['total_qty'] : null,
                    'shipped_qty' => isset($order['shipped_qty']) ? $order['shipped_qty'] : null,
                    'total_gst' => isset($order['total_gst']) ? $order['total_gst'] : null,
                    'status_id' => isset($order['status_id']) ? $order['status_id'] : null,
                ]);
                if (!empty($order['orderdetails'])) {
                    foreach ($order['orderdetails'] as $key => $rows) {
                        $details->push([
                            'order_id' => isset($rows['order_id']) ? $rows['order_id'] : null,
                            'product_id' => isset($rows['product_id']) ? $rows['product_id'] : null,
                            'quantity' => isset($rows['quantity']) ? $rows['quantity'] : 0,
                            'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] : 0,
                            'price' => isset($rows['price']) ? $rows['price'] : 0.00,
                            'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] : 0.00,
                            'line_total' => isset($rows['line_total']) ? $rows['line_total'] : 0.00,
                        ]);
                    }
                }
                $data['saledetail'] =  $details;
                $finaldata = collect([$data]);
                insertSales($finaldata);
            }
        }

        public function orderDispatched($orderid)
        {
            // $orderid = decrypt($orderid);
            // $status_id = Status::where('status_name','=','Dispatched')->pluck('id')->first();
            // Order::where('id','=',$orderid)->update(['status_id' => $status_id]);
            // $orders = $this->orders->with('orderdetails')->find($orderid);
            // $orders['invoice_date'] = date('Y-m-d');
            // $orders['invoice_no'] = $orderid.'-'.autoIncrementId('Sales','id') ;
            // $orders['order_id'] = $orderid ;
            // $orders['saledetail'] = $orders['orderdetails'];
            // $data = collect([$orders]);
            // $response = insertSales($data);
            // if($response['status'] == 'success')
            // {
            //     OrderDetails::where('order_id','=',$orderid)->update(['status_id' => $status_id]);
            //   return Redirect::to('orders')->with('message_success', 'Sales Store Successfully');
            // }
            // else
            // {
            //     Order::where('id','=',$orderid)->update(['status_id' => null]);
            // }

            $orderid = decrypt($orderid);
            $orders = $this->orders->with('orderdetails')->find($orderid);
            $orders?->resolveCustomerRelations();
            $category = Category::where('active', 'Y')->get();
            return view('orders.full_dispatched', compact('category'))->with('orders', $orders);
        }

        public function submitFullyDispatched(Request $request)
        {
            //$orderid = decrypt($orderid);
            try {

                $validator = Validator::make($request->all(), [
                    'invoice_no'       => 'required',
                    'order_id'         => 'required',
                    'invoice_date'     => 'required',
                    // 'transport_name'   => 'required',
                    'lr_no'            => 'required',
                    'dispatch_date'    => 'required'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $orderid = $request['order_id'];
                $status_id = Status::where('status_name', '=', 'Dispatched')->pluck('id')->first();
                Order::where('id', '=', $orderid)->update([
                    'status_id' => $status_id,
                    'order_remark' => $request->order_remark,
                ]);
                $orders = $this->orders->with('orderdetails')->find($orderid);
                $orders['invoice_date'] = $request['invoice_date'];
                $orders['invoice_no'] = $request['invoice_no'];
                // $orders['transport_name'] = $request['transport_name'];
                $orders['lr_no'] = $request['lr_no'];
                $orders['dispatch_date'] = $request['dispatch_date'];
                $orders['transport_details'] = $request['transport_details'];
                $orders['order_id'] = $orderid;
                $orders['saledetail'] = $orders['orderdetails'];
                $data = collect([$orders]);
                // dd($data);
                $response = insertSales($data);
                if ($response['status'] == 'success') {

                    $status_id = Status::where('status_name', '=', 'Dispatched')->pluck('id')->first();
                    $partiallystatus = Status::where('status_name', '=', 'Partially Dispatched')->pluck('id')->first();

                    if ($request['orderdetail']) {
                        foreach ($request['orderdetail'] as $key => $rows) {
                            // code chnanges
                            // $orderdetail = OrderDetails::where('order_id', '=', $request['order_id'])
                            //     ->where('product_detail_id', '=', $rows['product_detail'])->first();
                            $orderdetail = OrderDetails::where('order_id', '=', $request['order_id'])
                                ->where('product_id', '=', ($rows['product_id'] ?? ''))->first();

                            if (isset($orderdetail)) {
                                if ($orderdetail['shipped_qty'] + $rows['quantity'] == $orderdetail['quantity']) {
                                    $orderdetail->status_id = $status_id;
                                } else {
                                    $orderdetail->status_id = $partiallystatus;
                                }
                                $orderdetail->increment('shipped_qty', $rows['quantity']);
                                $orderdetail->save();
                            }
                        }
                    }

                    if (OrderDetails::where('order_id', '=', $request['order_id'])->where('status_id', '=', $partiallystatus)->exists()) {
                        Order::where('id', '=', $request['order_id'])->update(['status_id' => $partiallystatus]);
                    } else {
                        Order::where('id', '=', $request['order_id'])->update([
                            'status_id' => $status_id,
                            'completed_date' => $request->dispatch_date,
                        ]);
                    }
                    return Redirect::to('sales')->with('message_success', 'Sales Store Successfully');

                    // OrderDetails::where('order_id','=',$orderid)->update(['status_id' => $status_id]);
                    // return Redirect::to('orders')->with('message_success', 'Sales Store Successfully');
                } else {
                    Order::where('id', '=', $orderid)->update(['status_id' => null]);
                }
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage())->withInput();
            }
        }




        public function orderPartiallyDispatched($orderid)
        {
            $orderid = decrypt($orderid);
            $orders = $this->orders->find($orderid);
            $orders?->resolveCustomerRelations();
            $category = Category::where('active', 'Y')->get();
            return view('orders.dispatched', compact('category'))->with('orders', $orders);
        }

        public function orderCancle($orderid, Request $request)
        {
            $orderid = decrypt($orderid);
            $orders = $this->orders->with('orderdetails')->find($orderid);
            if ($orders) {
                $orders->status_id = '4';
                $orders->order_remark = $request->remark;
                $orders->save();
                return response()->json(['status' => 'success', 'message' => 'Order cancle successfully !!']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Order not found !!']);
            }
        }

        public function orderPendding($orderid, Request $request)
        {
            $orderid = decrypt($orderid);
            $orders = $this->orders->find($orderid);
            if ($orders) {
                $orders->status_id = NULL;
                // $orders->order_remark = $request->remark;
                $orders->save();
                OrderDetails::where('order_id', $orderid)->update(['shipped_qty' => '0']);
                $sales = Sales::where('order_id', $orderid)->first();
                SalesDetails::where('sales_id', $sales->id)->delete();
                $sales->delete();
                return response()->json(['status' => 'success', 'message' => 'Order pendding successfully !!']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Order not found !!']);
            }
        }

        public function submitDispatched(Request $request)
        {
            try {
                $request['active'] = 'Y';
                $request['created_by'] = Auth::user()->id;
                $validator = Validator::make($request->all(), [
                    'buyer_id' => 'required',
                    'seller_id' => 'required',
                    'invoice_no' => 'required',
                    'order_id' => 'required',
                    'grand_total' => 'required',
                    'lr_no'            => 'required',
                    'dispatch_date'    => 'required'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }
                $request['saledetail'] = $request['orderdetail'];
                $request['status_id'] = 2;
                $data = collect([$request]);
                $response = insertSales($data);
                if ($response['status'] == 'success') {
                    $partiallystatus = 2;
                    if (isset($request['orderdetail'])) {
                        foreach ($request['orderdetail'] as $key => $rows) {
                            $orderdetail = OrderDetails::where('order_id', '=', $request['order_id'])
                                ->where('product_id', '=', ($rows['product_id'] ?? ''))->first();
                            if (isset($orderdetail)) {
                                $orderdetail->status_id = $partiallystatus;
                                $orderdetail->increment('shipped_qty', $rows['quantity']);
                                $orderdetail->save();
                            }
                        }
                    }
                    Order::where('id', '=', $request['order_id'])->update([
                        'status_id' => $partiallystatus,
                        'order_remark' => $request->order_remark,
                    ]);
                    return Redirect::to('sales')->with('message_success', 'Sales Store Successfully');
                }
                return redirect()->back()->with('message_danger', 'Error in Sales Store')->withInput();
            } catch (\Exception $e) {
                return redirect()->back()->withErrors($e->getMessage())->withInput();
            }
        }

        public function expectedDelivery(Request $request)
        {
            $cities = City::select('id', 'city_name')->get();
            $palaces = PlaceDispatch::select('city_name', 'pincode', 'days')->get();
            return view('orders.delivery', compact('cities', 'palaces'));
        }

        public function submitExpectedDelivery(Request $request)
        {
            foreach ($request['detail'] as $key => $rows) {
                if (!empty($rows['pincode'])) {
                    PlaceDispatch::updateOrCreate(['pincode' => $rows['pincode']], [
                        'city_name'      => isset($rows['city_name']) ? $rows['city_name'] : null,
                        'pincode'      => isset($rows['pincode']) ? $rows['pincode'] : null,
                        'days'      => isset($rows['days']) ? $rows['days'] : null,
                    ]);
                }
            }
            return Redirect::to('expected-delivery')->with('message_success', 'PlaceDispatch Update Successfully');
        }

        public function deleteOrderDtails(Request $request)
        {
            OrderDetails::where('id', $request->detailID)->delete();

            return response()->json(['status' => 'success']);
        }

    
    }
