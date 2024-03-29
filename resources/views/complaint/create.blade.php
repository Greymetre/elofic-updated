<x-app-layout>
   <style>
      .select2-results__options {
         overflow: auto;
         max-height: 200px !important;
      }

      .select2-results,
      .select2-search--dropdown,
      .select2-dropdown--above {
         min-width: 250px !important;
      }

      .select2-container {
         border-bottom: 1px solid lightgray;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        Complaint Creation
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('complaints') }}">
                                 <i class="material-icons">next_plan</i> {!! trans('panel.complaint.title') !!}
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                        </ul>
                        @endif
                     </h4>
                  </div>
               </div>
            </div>
            <div class="card-body">
               @if(count($errors) > 0)
               <div class="alert alert-danger">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <i class="material-icons">close</i>
                  </button>
                  <span>
                     @foreach($errors->all() as $error)
                     <li>{{$error}}</li>
                     @endforeach
                  </span>
               </div>
               @endif
               {!! Form::model($complaints,[
               'route' => $complaints->exists ? ['complaints.update', encrypt($complaints->id) ] : 'complaints.store',
               'method' => $complaints->exists ? 'PUT' : 'POST',
               'id' => 'storeComplaintData',
               'files'=>true
               ]) !!}
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="bmd-label-floating">Search By Contact Number or Searial Number </label>
                        <input type="text" name="serail_number" id="serail_number" class="form-control" value="{!! old( 'serail_number', $complaints['serail_number']) !!}">
                        <p class="text-danger d-none" id="search_error"></p>
                        @if ($errors->has('serail_number'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('serail_number') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-2">
                     <button type="button" class="btn btn-success ml-5" id="go-search">Go Search</button>
                  </div>
               </div>
               <div class="row d-none" id="search_data">
                  <h3>Complaint History</h3>
                  <table class="table table-striped table-bordered">
                     <thead>
                        <tr>
                           <th scope="col">Complaint No</th>
                           <th scope="col">Complaint date</th>
                           <th scope="col">Claim Amount</th>
                           <th scope="col">Complaint Status</th>
                           <th scope="col">Product Serial No</th>
                           <th scope="col">Service Center</th>
                           <th scope="col">Seller (Company billed Party )</th>
                           <th scope="col">Purchased Party Name </th>
                        </tr>
                     </thead>
                     <tbody>

                     </tbody>
                  </table>
               </div>
               <div class="basic_details mt-5">
                  <h4>COMPLAINT REGISTRATION</h4>
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Complaint Number </label>
                           <input type="text" readonly name="complaint_number" class="form-control" value="{!! old( 'complaint_number', $complaints['complaint_number']) ?? $newComplaintNumber !!}">
                           @if ($errors->has('complaint_number'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('complaint_number') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Complaint Date </label>
                           <input type="text" readonly name="complaint_date" id="complaint_date" class="form-control datepicker" value="{!! old( 'complaint_date', $complaints['complaint_date']) !!}">
                           @if ($errors->has('complaint_date'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('complaint_date') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Complaint Number</label> -->
                           <select name="seller" id="seller" class="select2 form-control"></select>
                           @if ($errors->has('seller'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('seller') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Complaint Number</label> -->
                           <select name="party_name" id="party_name" class="select2 form-control"></select>
                           @if ($errors->has('party_name'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('party_name') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Assign User </label> -->
                           <select name="product_laying" id="product_laying" class="select2 form-control">
                              <option value="">Product Laying at</option>
                              <option value="Customer">Customer</option>
                              <option value="Dealer">Dealer</option>
                              <option value="Distributor">Distributor</option>
                              <option value="Retailer">Retailer</option>
                              <option value="ASC">ASC</option>
                              <option value="Branch">Branch</option>
                           </select>
                           @if ($errors->has('product_laying'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('product_laying') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Service Center </label> -->
                           <select name="service_center" id="service_center" class="select2 form-control">
                              <option value="">Select Service Center</option>
                              @if($service_centers)
                              @foreach($service_centers as $service_center)
                              <option value="{{$service_center->id}}">[{{$service_center->id}}] {{$service_center->name}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('service_center'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('service_center') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Assign User </label> -->
                           <select name="assign_user" id="assign_user" class="select2 form-control">
                              <option value="">Select User</option>
                              @if($assign_users)
                              @foreach($assign_users as $assign_user)
                              <option value="{{$assign_user->id}}">[{{$assign_user->id}}] {{$assign_user->name}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('assign_user'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('assign_user') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Product Searial Number </label>
                        <input type="text" name="product_serail_number" id="product_serail_number" class="form-control" value="{!! old( 'product_serail_number', $complaints['product_serail_number']) !!}">
                        <input type="hidden" name="product_id" id="product_id" value="">
                        @if ($errors->has('product_serail_number'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('product_serail_number') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Product/Model(Code) </label>
                        <input readonly type="text" name="product_code" id="product_code" class="form-control" value="{!! old( 'product_code', $complaints['product_code']) !!}">
                        @if ($errors->has('product_code'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('product_code') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Category </label>
                        <input readonly type="text" name="category" id="category" class="form-control" value="{!! old( 'category', $complaints['category']) !!}">
                        @if ($errors->has('category'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('category') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">HP </label>
                        <input readonly type="text" name="specification" id="specification" class="form-control" value="{!! old( 'specification', $complaints['specification']) !!}">
                        @if ($errors->has('specification'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('specification') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Stage </label>
                        <input readonly type="text" name="product_no" id="product_no" class="form-control" value="{!! old( 'product_no', $complaints['product_no']) !!}">
                        @if ($errors->has('product_no'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('product_no') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Phase </label>
                        <input readonly type="text" name="phase" id="phase" class="form-control" value="{!! old( 'phase', $complaints['phase']) !!}">
                        @if ($errors->has('phase'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('phase') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <!-- <label class="bmd-label-floating">Assign User </label> -->
                        <select disabled name="seller_branch" id="seller_branch" class="select2 form-control">
                           <option value="">Seller Branch</option>
                           @if($branchs)
                           @foreach($branchs as $branch)
                           <option value="{{$branch->id}}">[{{$branch->branch_code}}] {{$branch->branch_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('seller_branch'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('seller_branch') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <!-- <label class="bmd-label-floating">Assign User </label> -->
                        <select name="purchased_branch" id="purchased_branch" class="select2 form-control">
                           <option value="">Purchased Branch</option>
                           @if($branchs)
                           @foreach($branchs as $branch)
                           <option value="{{$branch->id}}">[{{$branch->branch_code}}] {{$branch->branch_name}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('purchased_branch'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('purchased_branch') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Product Group </label>
                        <input readonly type="text" name="product_group" class="form-control" value="{!! old( 'product_group', $complaints['product_group']) !!}">
                        @if ($errors->has('product_group'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('product_group') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Company Sale Bill NO </label>
                        <input type="text" name="company_sale_bill_no" class="form-control" value="{!! old( 'company_sale_bill_no', $complaints['company_sale_bill_no']) !!}">
                        @if ($errors->has('company_sale_bill_no'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('company_sale_bill_no') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Company Sale Bill Date </label>
                        <input type="text" name="company_sale_bill_date" id="company_sale_bill_date" class="form-control datepicker" value="{!! old( 'company_sale_bill_date', $complaints['company_sale_bill_date']) !!}">
                        @if ($errors->has('company_sale_bill_date'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('company_sale_bill_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Warranty / Customer Bill Date </label>
                        <input type="text" name="customer_bill_date" id="customer_bill_date" class="form-control datepicker" value="{!! old( 'customer_bill_date', $complaints['customer_bill_date']) !!}">
                        @if ($errors->has('customer_bill_date'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('customer_bill_date') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Customer Bill No.</label>
                        <input type="text" name="customer_bill_no" class="form-control" value="{!! old( 'customer_bill_no', $complaints['customer_bill_no']) !!}">
                        @if ($errors->has('customer_bill_no'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('customer_bill_no') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Company Bill Date Month </label>
                        <input readonly type="text" name="company_bill_date_month" id="company_bill_date_month" class="form-control" value="{!! old( 'company_bill_date_month', $complaints['company_bill_date_month']) !!}">
                        @if ($errors->has('company_bill_date_month'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('company_bill_date_month') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <!-- <label class="bmd-label-floating">Assign User </label> -->
                        <select name="under_warranty" id="under_warranty" class="select2 form-control">
                           <option value="">Under Warranty</option>
                           <option value="Yes">Yes</option>
                           <option value="No">No</option>
                        </select>
                        @if ($errors->has('under_warranty'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('under_warranty') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <!-- <label class="bmd-label-floating">Assign User </label> -->
                        <select name="service_type" id="service_type" class="select2 form-control">
                           <option value="">Service Paid/Free</option>
                           <option value="Paid">Paid</option>
                           <option value="Free">Free</option>
                           <option value="later_update">F&A Later Update</option>
                        </select>
                        @if ($errors->has('service_type'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('service_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label class="bmd-label-floating">Customer Bill Date Month </label>
                        <input readonly type="text" name="customer_bill_date_month" id="customer_bill_date_month" class="form-control" value="{!! old( 'customer_bill_date_month', $complaints['customer_bill_date_month']) !!}">
                        @if ($errors->has('customer_bill_date_month'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('customer_bill_date_month') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <!-- <label class="bmd-label-floating">Assign User </label> -->
                        <select name="warranty_bill" id="warranty_bill" class="select2 form-control">
                           <option value="">Warranty/Bill</option>
                           <option value="Yes">Yes</option>
                           <option value="No">No</option>
                        </select>
                        @if ($errors->has('warranty_bill'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('warranty_bill') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <!-- <label class="bmd-label-floating">Assign User </label> -->
                        <select name="fault_type" id="fault_type" class="select2 form-control">
                           <option value="">Fault Type</option>
                           <option value="Site">Site</option>
                           <option value="Company">Company</option>
                        </select>
                        @if ($errors->has('fault_type'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('fault_type') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="bmd-label-floating">Service Centre Remarks </label>
                        <textarea type="text" name="service_centre_remark" cols="20" rows="3" class="form-control"> {!! old( 'service_centre_remark', $complaints['service_centre_remark']) !!} </textarea>
                        @if ($errors->has('service_centre_remark'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('service_centre_remark') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group">
                        <label class="bmd-label-floating">Remarks </label>
                        <textarea type="text" name="remark" cols="20" rows="3" class="form-control"> {!! old( 'remark', $complaints['remark']) !!} </textarea>
                        @if ($errors->has('remark'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('remark') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
               </div>
               <div class="contact_details mt-5">
                  <h4>Contact Details</h4>
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Customer Number Search</label>
                           <input type="number" name="customer_number" id="customer_number" class="form-control" value="{!! old( 'customer_number', $complaints['customer_number']) !!}" required>
                           <input type="hidden" name="end_user_id" id="end_user_id">
                           @if ($errors->has('customer_number'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_number') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>

                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating"> Customer Name </label>
                           <input type="text" readonly name="customer_name" id="customer_name" class="form-control" value="{!! old( 'customer_name', $complaints['customer_name']) !!}">
                           @if ($errors->has('customer_name'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>

                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Email </label>
                           <input type="text" readonly name="customer_email" id="customer_email" class="form-control" value="{!! old( 'customer_email', $complaints['customer_email']) !!}">
                           @if ($errors->has('customer_email'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_email') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Address </label>
                           <input type="text" name="customer_address" id="customer_address" class="form-control" value="{!! old( 'customer_address', $complaints['customer_address']) !!}">
                           @if ($errors->has('customer_address'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Place </label>
                           <input type="text" name="customer_place" id="customer_place" class="form-control" value="{!! old( 'customer_place', $complaints['customer_place']) !!}">
                           @if ($errors->has('customer_place'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_place') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Pincode </label> -->
                           <select name="customer_pindcode" id="customer_pindcode" placeholder="Select Pincode" class="select2 form-control" required>
                              <option value="" disabled selected>Select Pincode</option>
                              @if($pincodes && count($pincodes) > 0)
                              @foreach($pincodes as $pincode)
                              <option value="{{$pincode->id}}">{{$pincode->pincode}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('customer_pindcode'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_pindcode') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">Country </label>
                           <input type="text" readonly name="customer_country" id="customer_country" class="form-control" value="{!! old( 'customer_country', $complaints['customer_country']) !!}">
                           @if ($errors->has('customer_country'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_country') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">State </label>
                           <input type="text" readonly name="customer_state" id="customer_state" class="form-control" value="{!! old( 'customer_state', $complaints['customer_state']) !!}">
                           @if ($errors->has('customer_state'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_state') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">District </label>
                           <input type="text" readonly name="customer_district" id="customer_district" class="form-control" value="{!! old( 'customer_district', $complaints['customer_district']) !!}">
                           @if ($errors->has('customer_district'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_district') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <label class="bmd-label-floating">City </label>
                           <input type="text" readonly name="customer_city" id="customer_city" class="form-control" value="{!! old( 'customer_city', $complaints['customer_city']) !!}">
                           @if ($errors->has('customer_city'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_city') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Pincode </label> -->
                           <select name="division" id="division" placeholder="Select Division" class="select2 form-control">
                              <option value="" disabled selected>Division</option>
                              @if($divisions && count($divisions) > 0)
                              @foreach($divisions as $division)
                              <option value="{{$division->id}}" {!! old( 'division' , $complaints['division'])==$division->id?'selected':'' !!}>{{$division->division_name}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('division'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('division') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-3">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Assign User </label> -->
                           <select name="register_by" id="register_by" class="select2 form-control">
                              <option value="">Complaint Register By</option>
                              <option value="Dealer">Dealer</option>
                              <option value="Distributor">Distributor</option>
                              <option value="Retailer">Retailer</option>
                              <option value="Marketing Team">Marketing Team</option>
                              <option value="ASC">ASC</option>
                              <option value="Service Enginer">Service Enginer</option>
                           </select>
                           @if ($errors->has('register_by'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('register_by') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="bmd-label-floating">Description / CRM Remark </label>
                           <textarea type="text" name="description" cols="30" rows="7" class="form-control"> {!! old( 'description', $complaints['description']) !!} </textarea>
                           @if ($errors->has('description'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('description') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">
                           <!-- <label class="bmd-label-floating">Pincode </label> -->
                           <select name="complaint_type" id="complaint_type" placeholder="Select Pincode" class="select2 form-control" required>
                              <option value="" disabled selected>Complaint Type</option>
                              @if($complaint_types && count($complaint_types) > 0)
                              @foreach($complaint_types as $complaint_type)
                              <option value="{{$complaint_type->id}}" {!! old( 'complaint_type' , $complaints['complaint_type'])==$complaint_type->id?'selected':'' !!}>{{$complaint_type->name}}</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('complaint_type'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('complaint_type') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               <div class="card-footer pull-right mt-5">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   <script>
      setTimeout(() => {
         var $customerSelect = $('#party_name').select2({
            placeholder: 'Purchased Party Name ',
            allowClear: true,
            ajax: {
               url: "{{ route('getRetailerDataSelect') }}",
               dataType: 'json',
               delay: 250,
               data: function(params) {
                  return {
                     term: params.term || '',
                     page: params.page || 1
                  }
               },
               cache: true
            }
         });
      }, 2000);
      setTimeout(() => {
         var $customerSelect = $('#seller').select2({
            placeholder: 'Seller (Company billed Party )',
            allowClear: true,
            ajax: {
               url: "{{ route('getDealerDisDataSelect') }}",
               dataType: 'json',
               delay: 250,
               data: function(params) {
                  return {
                     term: params.term || '',
                     page: params.page || 1
                  }
               },
               cache: true
            }
         });
      }, 2000);
      $(document).ready(function() {
         $('#company_sale_bill_date').datepicker({
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
         });
         $('#customer_bill_date').datepicker({
            maxDate: 0,
            dateFormat: 'yy-mm-dd',
         });
         $('#complaint_date').datepicker({
            minDate: 0,
            dateFormat: 'yy-mm-dd',
         });
      });
      $("#company_sale_bill_date").on('change', function() {
         var selectedDate = moment($(this).val());
         var today = moment();
         var diffMonths = today.diff(selectedDate, 'months');
         selectedDate.add(diffMonths, 'months');
         var diffDays = today.diff(selectedDate, 'days');

         $("#company_bill_date_month").focus();
         $("#company_bill_date_month").val(diffMonths + " Month " + diffDays + " Day");
      });
      $("#customer_bill_date").on('change', function() {
         var selectedDate = moment($(this).val());
         var today = moment();
         var diffMonths = today.diff(selectedDate, 'months');
         selectedDate.add(diffMonths, 'months');
         var diffDays = today.diff(selectedDate, 'days');

         $("#customer_bill_date_month").focus();
         $("#customer_bill_date_month").val(diffMonths + " Month " + diffDays + " Day");
      });
      $("#product_serail_number").on("keyup", function() {
         var serial_no = $(this).val();
         $.ajax({
            url: "{{ url('getProductInfoBySerialNo') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               serial_no: serial_no
            },
            success: function(res) {
               if (res.status === true) {
                  $("#product_code").val(res.data.product_code);
                  $("#product_id").val(res.data.id);
                  $("#category").val(res.data.categories.category_name);
                  $("#specification").val(res.data.specification);
                  $("#product_no").val(res.data.product_no);
                  $("#phase").val(res.data.phase);
                  $("#product_code").prop('readonly', true);
                  $("#category").prop('readonly', true);
                  $("#specification").prop('readonly', true);
                  $("#product_no").prop('readonly', true);
                  $("#phase").prop('readonly', true);
               } else {
                  $("#product_code").val(" ");
                  $("#product_id").val(" ");
                  $("#category").val(" ");
                  $("#specification").val(" ");
                  $("#product_no").val(" ");
                  $("#phase").val(" ");
                  $("#product_code").prop('readonly', false);
                  $("#category").prop('readonly', false);
                  $("#specification").prop('readonly', false);
                  $("#product_no").prop('readonly', false);
                  $("#phase").prop('readonly', false);
               }
            }
         });
      });

      $("#customer_number").on("keyup", function() {
         var customer_number = $(this).val();
         $.ajax({
            url: "{{ url('getEndUserData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               customer_number: customer_number
            },
            success: function(res) {
               if (res.status === true) {
                  $("#customer_name").val(res.data.customer_name);
                  $("#end_user_id").val(res.data.id);
                  $("#customer_email").val(res.data.customer_email);
                  $("#customer_address").val(res.data.customer_address);
                  $("#customer_place").val(res.data.customer_place);
                  $("#customer_pindcode").val(res.data.customer_pindcode).trigger("change");;
                  $("#customer_state").val(res.data.customer_state);
                  $("#customer_district").val(res.data.customer_district);
                  $("#customer_city").val(res.data.customer_city);

                  $("#customer_name").prop('readonly', true);
                  $("#customer_email").prop('readonly', true);
                  $("#customer_address").prop('readonly', true);
                  $("#customer_place").prop('readonly', true);
                  $("#customer_pindcode").prop('disabled', true);
                  $("#customer_state").prop('readonly', true);
                  $("#customer_district").prop('readonly', true);
                  $("#customer_city").prop('readonly', true);

               } else {
                  $("#customer_name").val("");
                  $("#end_user_id").val("");
                  $("#customer_email").val("");
                  $("#customer_address").val("");
                  $("#customer_place").val("");
                  $("#customer_pindcode").val("").trigger("change");;
                  $("#customer_state").val("");
                  $("#customer_district").val("");
                  $("#customer_city").val("");

                  $("#customer_name").prop('readonly', false);
                  $("#customer_email").prop('readonly', false);
                  $("#customer_address").prop('readonly', false);
                  $("#customer_place").prop('readonly', false);
                  $("#customer_pindcode").prop('readonly', false);
                  $("#customer_state").prop('readonly', false);
                  $("#customer_district").prop('readonly', false);
                  $("#customer_city").prop('readonly', false);
                  $("#customer_name").prop('readonly', false);
                  $("#customer_email").prop('readonly', false);
               }
            }
         });
      });
      $(document).on("change", "#customer_pindcode", function() {
         var customer_pindcode = $(this).val();
         $.ajax({
            url: "{{ url('getAddressData') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               pincode_id: customer_pindcode
            },
            success: function(res) {
               console.log(res);
               $("#customer_country").val(res.country_name);
               $("#customer_state").val(res.state_name);
               $("#customer_district").val(res.district_name);
               $("#customer_city").val(res.city_name);
            }
         });
      });
      $("#go-search").on('click', function() {
         var search = $("#serail_number").val();
         if (!search || search == '' || search == null) {
            $("#serail_number").focus();
            $("#search_error").html("Please enter Contact Number or Searial Number");
            $("#search_error").removeClass("d-none");
         } else {
            $("#search_error").addClass("d-none");
            $("#search_data").removeClass("d-none");
            $.ajax({
               url: "{{ url('getComplaintsData') }}",
               dataType: "json",
               type: "POST",
               data: {
                  _token: "{{csrf_token()}}",
                  search: search
               },
               success: function(res) {
                  if(res.status === true){
                     $("#search_data table tbody").html(res.data);
                  }else{
                     if(res.data){
                        $("#search_data table tbody").html(res.data);
                     }
                  }
               }
            });
         }
      });
   </script>
</x-app-layout>