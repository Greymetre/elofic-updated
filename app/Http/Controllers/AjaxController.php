<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\{Pincode, City, District, State, Country, Customers, Category, Product, Address, Attendance, Order, Status, Settings, Tasks, ProductDetails, Sales, UserReporting, CheckIn, CustomerDetails, GiftModel, GiftSubcategory, Notes, SchemeDetails, Subcategory};
use App\Models\User;
use Carbon\Carbon;
use App\Models\UserLiveLocation;
use App\Models\UserActivity;

class AjaxController extends Controller
{


    public function getState(Request $request)
    {
        try {
            $country = $request->input('country_id');
            $states = State::where(function ($query) use ($country) {
                if (isset($country)) {
                    $query->where('country_id', '=', $country);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'state_name')->orderBy('state_name', 'asc')->get();
            return response()->json($states);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getDistrict(Request $request)
    {
        try {
            $state = $request->input('state_id');
            $district = District::where(function ($query) use ($state) {
                if (isset($state)) {
                    $query->where('state_id', '=', $state);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'district_name')->orderBy('district_name', 'asc')->get();
            return response()->json($district);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCity(Request $request)
    {
        try {
            $district = $request->input('district_id');
            $cities = City::where(function ($query) use ($district) {
                if (isset($district)) {
                    $query->where('district_id', '=', $district);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'city_name')->orderBy('city_name', 'asc')->get();
            return response()->json($cities);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getPincode(Request $request)
    {
        try {
            $city = $request->input('city_id');
            $cities = Pincode::where(function ($query) use ($city) {
                if (isset($city)) {
                    $query->where('city_id', '=', $city);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'pincode')->orderBy('pincode', 'asc')->get();
            return response()->json($cities);
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function getAddressData(Request $request)
    {
        try {
            $pincode = $request->input('pincode_id');
            $data = Pincode::with('cityname', 'cityname.districtname', 'cityname.districtname.statename', 'cityname.districtname.statename.countryname')->where('id', '=', $pincode)->select('id', 'city_id')->first();
            $address = collect([
                'city_id' => isset($data['city_id']) ? $data['city_id'] : '',
                'city_name' => isset($data['cityname']['city_name']) ? $data['cityname']['city_name'] : '',
                'district_id' => isset($data['cityname']['district_id']) ? $data['cityname']['district_id'] : '',
                'district_name' => isset($data['cityname']['districtname']['district_name']) ? $data['cityname']['districtname']['district_name'] : '',
                'state_id' => isset($data['cityname']['districtname']['state_id']) ? $data['cityname']['districtname']['state_id'] : '',
                'state_name' => isset($data['cityname']['districtname']['statename']['state_name']) ? $data['cityname']['districtname']['statename']['state_name'] : '',
                'country_id' => isset($data['cityname']['districtname']['statename']['country_id']) ? $data['cityname']['districtname']['statename']['country_id'] : '',
                'country_name' => isset($data['cityname']['districtname']['statename']['countryname']['country_name']) ? $data['cityname']['districtname']['statename']['countryname']['country_name'] : '',
            ]);
            return response()->json($address);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getAddressInfo(Request $request)
    {
        try {
            $address_id = $request->input('address_id');
            $data = Address::with('cityname', 'districtname', 'statename', 'countryname', 'pincodename')->where('id', '=', $address_id)->select('id', 'address1', 'address2', 'landmark', 'locality', 'country_id', 'state_id', 'district_id', 'city_id', 'pincode_id')->first();
            $address = $data['address1'] . ' ' . $data['address2'] . ' ' . $data['landmark'] . ' ' . $data['locality'] . ' ' . $data['cityname']['city_name'] . ' ' . $data['districtname']['district_name'] . ' ' . $data['statename']['state_name'] . ' ' . $data['countryname']['country_name'] . $data['pincodename']['pincode'];
            return response()->json($address);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUserInfo(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $data = User::where('id', '=', $user_id)
                ->select('id', 'name', 'mobile')
                ->first();

            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerData(Request $request)
    {
        try {
            $customer_id = $request->input('customer_id');
            $data = Customers::with('customeraddress', 'addresslists')
                ->where('id', '=', $customer_id)
                ->select('id', 'name', 'first_name', 'last_name', 'mobile', 'email')
                ->first();
            $addresslists = collect([]);
            if ($data['addresslists']) {
                foreach ($data['addresslists'] as $key => $rows) {
                    $addresslists->push([
                        'address_id' => $rows['id'],
                        'address1' => $rows['address1'],
                        'address2' => $rows['address2'],
                        'landmark' => $rows['landmark'],
                        'locality' => $rows['locality'],
                    ]);
                }
            }
            $customer = collect([
                'name' => isset($data['name']) ? $data['name'] : '',
                'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
                'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
                'mobile' => isset($data['mobile']) ? $data['mobile'] : '',
                'email' => isset($data['email']) ? $data['email'] : '',
                'address1' => isset($data['customeraddress']['address1']) ? $data['customeraddress']['address1'] . ' ' . $data['customeraddress']['address2'] . ' ' . $data['customeraddress']['landmark'] . ' ' . $data['customeraddress']['locality'] : '',
                'address2' => isset($data['customeraddress']['cityname']['city_name']) ? $data['customeraddress']['cityname']['city_name'] . ', ' . $data['customeraddress']['pincodename']['pincode'] : '',
                'addresslists' => $addresslists
            ]);
            return response()->json($customer);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCategoryData(Request $request)
    {
        try {
            $data = Category::where(function ($query) {
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'category_name', 'category_image')
                ->orderBy('category_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getSubCategoryData(Request $request)
    {
        try {
            $data = Subcategory::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('category_id', $request->cat_id);
            }
            $data = $data->select('id', 'subcategory_name')
                ->orderBy('subcategory_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductData(Request $request)
    {
        try {
            $sub_category = $request->sub_cat;
            $data = Product::where(function ($query) use ($sub_category) {
                if ($sub_category && $sub_category != null) {
                    $query->where('subcategory_id', $sub_category);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'product_name', 'product_image', 'display_name')
                ->orderBy('product_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function getUserList(Request $request)
    {
        try {
            $beat_id = $request->input('beat_id');
            $userids = getUsersReportingToAuth();
            $data = User::where(function ($query) use ($beat_id, $userids) {
                if (isset($beat_id)) {
                    $query->whereHas('userbeats', function ($query) use ($beat_id) {
                        $query->where('beat_id', '=', $beat_id);
                    });
                }
                if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                    $query->whereIn('id', $userids);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'name', 'mobile', 'first_name', 'last_name')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getRetailerlist(Request $request)
    {
        try {
            $state = $request->input('state_id');
            $district = $request->input('district_id');
            $city = $request->input('city_id');
            $users = $request->input('user_id');
            $data = Customers::where(function ($query) use ($users) {
                if (isset($users)) {
                    $query->whereIn('executive_id', $users);
                }
                $query->where('active', '=', 'Y');
            })
                ->whereHas('customeraddress', function ($query) use ($state, $district, $city) {
                    if (isset($state)) {
                        $query->where('state_id', '=', $state);
                    }
                    if (isset($district)) {
                        $query->where('district_id', '=', $district);
                    }
                    if (isset($city)) {
                        $query->where('city_id', '=', $city);
                    }
                })
                ->select('id', 'name', 'mobile', 'first_name', 'last_name')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductInfo(Request $request)
    {
        try {
            $product_id = $request->input('product_id');
            $data = Product::with('productdetails')
                ->where(function ($query) use ($product_id) {
                    if (isset($product_id)) {
                        $query->where('id', '=', $product_id);
                    }
                    $query->where('active', '=', 'Y');
                })
                ->select('id', 'product_name', 'product_image', 'display_name')
                ->orderBy('product_name', 'asc')
                ->first();
            $product = collect([
                'product_name' => isset($data['product_name']) ? $data['product_name'] : '',
                'product_image' => isset($data['product_image']) ? $data['product_image'] : '',
                'display_name' => isset($data['display_name']) ? $data['display_name'] : '',
                'mrp' => isset($data['productdetails'][0]['mrp']) ? $data['productdetails'][0]['mrp'] : '',
                'price' => isset($data['productdetails'][0]['price']) ? $data['productdetails'][0]['price'] : '',
                'selling_price' => isset($data['productdetails'][0]['selling_price']) ? $data['productdetails'][0]['selling_price'] : '',
                'gst' => isset($data['productdetails'][0]['gst']) ? $data['productdetails'][0]['gst'] : '',
                'discount' => isset($data['productdetails'][0]['discount']) ? $data['productdetails'][0]['discount'] : '',
                'max_discount' => ($data['productdetails'][0]['max_discount']) ? $data['productdetails'][0]['max_discount'] : 0.00,
                'productdetails' => $data['productdetails']
            ]);


            return response()->json($product);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductDetailInfo(Request $request)
    {
        try {
            $productdetail_id = $request->input('productdetail_id');
            $data = ProductDetails::where('id', '=', $productdetail_id)
                ->select('id', 'detail_title', 'mrp', 'price', 'discount', 'max_discount', 'selling_price', 'gst')
                ->first();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getOrderInfo(Request $request)
    {
        try {
            $order_id = $request->input('order_id');
            $data = Order::with('buyers', 'sellers', 'orderdetails')->where('id', '=', $order_id)->select('id', 'buyer_id', 'seller_id', 'orderno', 'order_date', 'total_gst', 'total_discount', 'extra_discount', 'extra_discount_amount', 'sub_total', 'grand_total', 'address_id')->first();
            $order = collect([
                'buyer_id' => isset($data['buyer_id']) ? $data['buyer_id'] : '',
                'buyer_name' => isset($data['buyers']['name']) ? $data['buyers']['name'] : '',
                'seller_id' => isset($data['seller_id']) ? $data['seller_id'] : '',
                'seller_name' => isset($data['sellers']['name']) ? $data['sellers']['name'] : '',
                'orderno' => isset($data['orderno']) ? $data['orderno'] : '',
                'order_date' => isset($data['order_date']) ? $data['order_date'] : '',
                'total_gst' => isset($data['total_gst']) ? $data['total_gst'] : '',
                'total_discount' => isset($data['total_discount']) ? $data['total_discount'] : '',
                'extra_discount' => isset($data['extra_discount']) ? $data['extra_discount'] : '',
                'extra_discount_amount' => isset($data['extra_discount_amount']) ? $data['extra_discount_amount'] : '',
                'sub_total' => isset($data['sub_total']) ? $data['sub_total'] : '',
                'grand_total' => isset($data['grand_total']) ? $data['grand_total'] : '',
                'address_id' => isset($data['address_id']) ? $data['address_id'] : '',
                'address_name' => isset($data['address']['address1']) ? $data['address']['address1'] . ' ' . $data['address']['address2'] . ' ' . $data['address']['landmark'] . ' ' . $data['address']['locality'] . ' ' . $data['address']['cityname']['city_name'] . ' ' . $data['address']['districtname']['district_name'] . ' ' . $data['address']['statename']['state_name'] . ' ' . $data['address']['countryname']['country_name'] . $data['address']['pincodename']['pincode'] : '',
            ]);
            return response()->json($order);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function uniqueValidation(Request $request)
    {
        $data = DB::table($request['table'])
            ->where(function ($query) use ($request) {
                if (!empty($request['id'])) {
                    $query->where('id', '!=', $request['id']);
                }
                if (!empty($request['customer_id'])) {
                    $query->where('customer_id', '!=', $request['customer_id']);
                }
                $query->where($request['column'], $request['value']);
            })
            ->first();
        if ($data) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    public function getCustomerLatLong(Request $request)
    {
        try {
            $data = Customers::where(function ($query) {
                $query->where('active', '=', 'Y');
            })
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id', 'name', 'latitude', 'longitude')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUppaidInvouces(Request $request)
    {
        try {
            $customer_id = $request->input('customer_id');
            $data = Sales::where('buyer_id', '=', $customer_id)
                ->whereIn('status_id', [4, 5])
                ->select('id', 'invoice_date', 'invoice_no', 'grand_total', 'order_id', 'status_id', 'paid_amount')
                ->get();
            $sales = collect([]);
            if (!empty($data)) {
                foreach ($data as $key => $rows) {
                    $sales->push([
                        'id' => isset($rows['id']) ? $rows['id'] : '',
                        'invoice_date' => isset($rows['invoice_date']) ? $rows['invoice_date'] : '',
                        'invoice_no' => isset($rows['invoice_no']) ? $rows['invoice_no'] : '',
                        'grand_total' => isset($rows['grand_total']) ? $rows['grand_total'] : '',
                        'amount_unpaid' => isset($rows['paid_amount']) ? $rows['grand_total'] - $rows['paid_amount'] : $rows['grand_total'],
                        'order_id' => isset($rows['order_id']) ? $rows['order_id'] : '',
                        'status_id' => isset($rows['status_id']) ? $rows['status_id'] : '',
                    ]);
                }
            }
            return response()->json($sales);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function dashboardActivity(Request $request)
    {
        try {
            $reporting = UserReporting::where('userid', '=', Auth::user()->id)->select('users')->first();
            if (!empty($request->users)) {
                $users = $request->users;
            } else {
                $users = (!empty($reporting)) ? json_decode($reporting['users']) : [];
                array_push($users, Auth::user()->id);
            }
            $activities = collect([]);
            $customeractivity = Customers::where(function ($query) use ($users) {
                $query->whereDate('created_at', date('Y-m-d'));
                $query->whereIn('created_by', $users);
            })
                ->select('id', 'created_by', 'name', 'created_at')
                ->get();
            if ($customeractivity->isNotEmpty()) {
                $customeractivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('H:i', strtotime($item->created_at)),
                        'profile' => isset($item['createdbyname']['profile_image']) ? $item['createdbyname']['profile_image'] : '',
                        'user_name' => isset($item['createdbyname']['name']) ? $item['createdbyname']['name'] : '',
                        'description' => $item['name'] . 'is Added in CRM',
                    ]);
                });
            }
            $checkinactivity = CheckIn::where(function ($query) use ($users) {
                $query->whereDate('checkin_date', date('Y-m-d'));
                $query->whereIn('user_id', $users);
            })
                ->select('customer_id', 'checkin_time', 'user_id')
                ->get();
            if ($checkinactivity->isNotEmpty()) {
                $checkinactivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('G:i', strtotime($item->checkin_time)),
                        'profile' => isset($item['users']['profile_image']) ? $item['users']['profile_image'] : '',
                        'user_name' => isset($item['users']['name']) ? $item['users']['name'] : '',
                        'description' => $item['customers']['name'] . ' Counter Visited',
                    ]);
                });
            }
            $ordersactivity = Order::where(function ($query) use ($users) {
                $query->whereDate('order_date', date('Y-m-d'));
                $query->whereIn('created_by', $users);
            })
                ->select('id', 'grand_total', 'created_at', 'buyer_id', 'seller_id', 'created_by')
                ->get();
            if ($ordersactivity->isNotEmpty()) {
                $ordersactivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('H:i', strtotime($item->created_at)),
                        'profile' => isset($item['createdbyname']['profile_image']) ? $item['createdbyname']['profile_image'] : '',
                        'user_name' => isset($item['createdbyname']['name']) ? $item['createdbyname']['name'] : '',
                        'description' => 'Received Order from ' . $item['buyers']['name']
                    ]);
                });
            }
            $sorted = $activities->sortByDesc('time');
            $collection = $sorted->values()->all();
            return response()->json($collection);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUserLocationData(Request $request)
    {
        try {
            $users = $request->input('user_id');
            $date = !empty($request->input('date')) ? $request->input('date') : date('Y-m-d');
            $collection = UserLiveLocation::where(function ($query) use ($users, $date) {
                $query->whereDate('time', $date);
                $query->where('userid', $users);
            })
                ->select('address', 'time', 'latitude', 'longitude')
                ->get();
            return response()->json($collection);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function getUserActivityData(Request $request)
    {
        try {
            // $users = !empty($request->input('user_id')) ? $request->input('user_id') :Auth::user()->id ;
            // $date = !empty($request->input('date')) ? $request->input('date') : date('Y-m-d');
            // $collections = UserActivity::with('customers','users')->where(function($query) use($users, $date) {
            //                         $query->whereDate('created_at','=', date('Y-m-d',strtotime($date)));
            //                         $query->where('userid', $users);
            //                     })
            //                     ->select('customerid','latitude','longitude','time','address','description','type','userid')
            //                     ->get();
            $date = date('Y-m-d', strtotime($request->input('date')));
            $user_id = $request->input('user_id');

            $punchInOut = Attendance::where('user_id', $user_id)->where('punchin_date', $date)->get();
            $checkInOut = CheckIn::with('visitreports')->with('customers')->where('user_id', $user_id)->where('checkin_date', $date)->get();
            $orders = Order::with('buyers')->where('created_by', $user_id)->whereRaw('DATE(created_at)="' . $date . '"')->get();
            $customer_add = Customers::with('customeraddress')->where('created_by', $user_id)->whereRaw('DATE(created_at)="' . $date . '"')->get();
            $customer_update = Customers::with('customeraddress')->where('created_by', $user_id)->whereColumn('updated_at', '>', 'created_at')->whereRaw('DATE(updated_at)="' . $date . '"')->get();

            $punchInData = array();
            $punchOutData = array();
            $checkInData = array();
            $checkOutData = array();
            $orderData = array();
            $customerAddData = array();
            $customerUpdateData = array();

            foreach ($punchInOut as $k => $val) {
                if ($val->punchin_time != null) {
                    $punch_in_city = getLatLongToCity($val->punchin_latitude, $val->punchin_longitude);
                    $punchInData[$k]['title'] = 'Punchin';
                    $punchInData[$k]['time'] = $val->punchin_time;
                    $punchInData[$k]['latitude'] = $val->punchin_latitude != null ? $val->punchin_latitude : '';
                    $punchInData[$k]['longitude'] = $val->punchin_longitude != null ? $val->punchin_longitude : '';
                    $punchInData[$k]['msg'] = $val->punchin_summary . ' - ' . $punch_in_city;
                }
                if ($val->punchout_time != null) {
                    $punchOutData[$k]['title'] = 'Punchout';
                    $punchOutData[$k]['time'] = $val->punchout_time;
                    $punchOutData[$k]['latitude'] = $val->punchout_latitude != null ? $val->punchout_latitude : '';
                    $punchOutData[$k]['longitude'] = $val->punchout_longitude != null ? $val->punchout_longitude : '';
                    $punchOutData[$k]['msg'] = $val->punchout_address;
                }
            }

            foreach ($checkInOut as $k => $val) {
                if ($val->checkin_time != null) {
                    $check_in_city = getLatLongToCity($val->checkin_latitude, $val->checkin_longitude);
                    $checkInData[$k]['title'] = 'Checkin';
                    $checkInData[$k]['time'] = $val->checkin_time;
                    $checkInData[$k]['latitude'] = $val->checkin_latitude != null ? $val->checkin_latitude : '';
                    $checkInData[$k]['longitude'] = $val->checkin_longitude != null ? $val->checkin_longitude : '';
                    $checkInData[$k]['msg'] = $val->customers->name . ' - ' . $check_in_city;
                }
                if ($val->checkout_time != null) {
                    $check_out_city = getLatLongToCity($val->checkout_latitude, $val->checkout_longitude);
                    $checkOutData[$k]['title'] = 'Checkout';
                    $checkOutData[$k]['time'] = $val->checkout_time;
                    $checkOutData[$k]['latitude'] = $val->checkout_latitude != null ? $val->checkout_latitude : '';
                    $checkOutData[$k]['longitude'] = $val->checkout_longitude != null ? $val->checkout_longitude : '';
                    $checkOutData[$k]['msg'] = $val->customers->name . ' - ' . $check_out_city . '<br>Remark - ' . $val->visitreports->description;
                }
            }

            foreach ($orders as $k => $val) {
                $orderData[$k]['title'] = 'Order';
                $orderData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
                $orderData[$k]['latitude'] = '';
                $orderData[$k]['longitude'] = '';
                $orderData[$k]['msg'] = $val->buyers->name . ' - ' . $val->buyers->customeraddress->cityname->city_name . ',<br>Qty : ' . $val->orderdetails->sum('quantity') . ',<br>Total : ' . $val->grand_total;
            }

            foreach ($customer_add as $k => $val) {
                $customerAddData[$k]['title'] = 'New Customer Registration';
                $customerAddData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
                $customerAddData[$k]['latitude'] = $val->latitude;
                $customerAddData[$k]['longitude'] = $val->longitude;
                if ($val->customeraddress->cityname != null) {
                    $customerAddData[$k]['msg'] = $val->name . ' - ' . $val->customeraddress->cityname->city_name;
                } else {
                    $customerAddData[$k]['msg'] = $val->name . ' - City not enter';
                }
            }

            foreach ($customer_update as $k => $val) {
                $customerUpdateData[$k]['title'] = 'Customer Edit';
                $customerUpdateData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
                $customerUpdateData[$k]['latitude'] = $val->latitude;
                $customerUpdateData[$k]['longitude'] = $val->longitude;
                $customerUpdateData[$k]['msg'] = $val->name . ' - ' . $val->customeraddress->cityname->city_name;
            }

            $collections = array_merge($punchInData, $punchOutData, $checkInData, $checkOutData, $orderData, $customerAddData, $customerUpdateData);

            usort($collections, function ($a, $b) {
                return strtotime($a['time']) - strtotime($b['time']);
            });
            foreach ($collections as $k => $val) {
                $collections[$k]['time'] = date('h:i A', strtotime($val['time']));
            }
            return response()->json($collections);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerActivityData(Request $request)
    {
        try {
            $notes = Notes::with('customerinfo', 'users')
                ->where(function ($query) use ($request) {
                    if (!empty($request['customer_id'])) {
                        $query->where('customer_id', $request['customer_id']);
                    }
                })
                ->select('id', 'user_id', 'customer_id', 'note', 'purpose', 'status_id', 'created_at', 'callstatus')
                ->latest()
                ->get();
            return response()->json($notes);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function removeSchemesdetails(Request $request)
    {
        try {
            $scheme_details = SchemeDetails::find($request->id);
            $scheme_details->delete();

            return response()->json(["status" => true]);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);
            
            $coins = Customers::select("id as id", "name as text")->where('name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }


    public function changeDocumnetStatus(Request $request)
    {
        if ($request->ajax()) {
            $column = $request->type;
            $customer_id = $request->customer_id;
            $status = $request->status;
            $update = CustomerDetails::where('customer_id', $customer_id)->update([$column => $status]);
            if($update){
                if($status == 1){
                    $msg = "Verified Successfully !!";
                }elseif($status == 2){
                    $msg = "Rejected Successfully !!";
                }else{
                    $msg = "";
                }
                $results = array(
                    "status" => true,
                    "msg" => $msg
                );
            }else{
                $results = array(
                    "status" => false,
                    "msg" => "Somthing went wrong"
                );
            }
            return response()->json($results);
        }
    }

    public function getGiftSubCategoryData(Request $request)
    {
        try {
            $data = GiftSubcategory::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('category_id', $request->cat_id);
            }
            $data = $data->select('id', 'subcategory_name')
                ->orderBy('subcategory_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getGiftModelData(Request $request)
    {
        try {
            $data = GiftModel::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('sub_category_id', $request->cat_id);
            }
            $data = $data->select('id', 'model_name')
                ->orderBy('model_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
}
