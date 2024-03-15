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
                        Warranty Activation Creation
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('warranty_activation') }}">
                                 <i class="material-icons">next_plan</i> Warranty Activation
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
               {!! Form::model($warranty_activation,[
               'route' => $warranty_activation->exists ? ['warranty_activation.update', encrypt($warranty_activation->id) ] : 'warranty_activation.store',
               'method' => $warranty_activation->exists ? 'PUT' : 'POST',
               'id' => 'storeTransactionHistoryData',
               'files'=>true
               ]) !!}
               <div class="form-group">
                  <div>
                     <h5>Warranty Details</h5>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="serial_no" class="form-control">Product Serial Number</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="serial_no" id="serial_no" class="form-control" required>
                           @if ($errors->has('serial_no'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('serial_no') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="product_id" class="form-control">Product</label>
                        </div>
                        <div class="col-md-4">
                           <select name="product_id" id="product_id" placeholder="Select Product" class="select2 form-control">
                           </select>
                           @if ($errors->has('product_id'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('product_id') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="branch_id" class="form-control">Branch</label>
                        </div>
                        <div class="col-md-4">
                           <select name="branch_id" placeholder="Select Branch" class="select2 form-control" required>
                              <option value="" disabled selected>Select Branch</option>
                              @if($branches && count($branches) > 0)
                              @foreach($branches as $branche)
                              <option value="{{$branche->id}}" {!! old( 'branch_id' , $warranty_activation['branch_id'])==$branche->id?'selected':'' !!}>{{$branche->branch_name}}({{$branche->branch_code}})</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('branch_id'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="status" class="form-control">Status</label>
                        </div>
                        <div class="col-md-4">
                           <input type="radio" name="status" id="status" value="0"> Inactive
                           <input type="radio" name="status" id="status" value="1"> active
                           @if ($errors->has('status'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('status') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="pt-4">
                     <h5>Contact Details</h5>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_number" class="form-control">Customer Number</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="customer_number" id="customer_number" class="form-control">
                           @if ($errors->has('customer_number'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_number') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="customer_name" class="form-control">Customer Name</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="customer_name" id="customer_name" class="form-control">
                           @if ($errors->has('customer_name'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_name') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_email" class="form-control">Email</label>
                        </div>
                        <div class="col-md-4">
                           <input type="email" name="customer_email" id="customer_email" class="form-control">
                           @if ($errors->has('customer_email'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_email') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="customer_registered_number" class="form-control">Registered Number</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="customer_registered_number" id="customer_registered_number" class="form-control">
                           @if ($errors->has('customer_registered_number'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_registered_number') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_address" class="form-control">Address</label>
                        </div>
                        <div class="col-md-4">
                           <input type="email" name="customer_address" id="customer_address" class="form-control">
                           @if ($errors->has('customer_address'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_address') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="customer_pindcode" class="form-control">Pincode</label>
                        </div>
                        <div class="col-md-4">
                           <select name="customer_pindcode" id="customer_pindcode" placeholder="Select Pincode" class="select2 form-control" required>
                              <option value="" disabled selected>Select Pincode</option>
                              @if($pincodes && count($pincodes) > 0)
                              @foreach($pincodes as $pincode)
                              <option value="{{$pincode->id}}" {!! old( 'customer_pindcode' , $warranty_activation['customer_pindcode'])==$pincode->id?'selected':'' !!}>{{$pincode->pincode}}</option>
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
                     <div class="row">
                        <div class="col-md-1">
                           <label for="customer_state" class="form-control">State</label>
                        </div>
                        <div class="col-md-3">
                           <input type="text" name="customer_state" id="customer_state" class="form-control">
                           @if ($errors->has('customer_state'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_state') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-1">
                           <label for="customer_district" class="form-control">District</label>
                        </div>
                        <div class="col-md-3">
                           <input type="text" name="customer_district" id="customer_district" class="form-control">
                           @if ($errors->has('customer_district'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_district') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-1">
                           <label for="customer_city" class="form-control">City</label>
                        </div>
                        <div class="col-md-3">
                           <input type="text" name="customer_city" id="customer_city" class="form-control">
                           @if ($errors->has('customer_city'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_city') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div class="pt-4">
                     <h5>Other Details</h5>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="customer_id" class="form-control">Seller</label>
                        </div>
                        <div class="col-md-4">
                           <select name="customer_id" placeholder="Select Customers" class="select2 form-control" required>
                              <option value="" disabled selected>Select Customer</option>
                              @if($customers && count($customers) > 0)
                              @foreach($customers as $customer)
                              <option value="{{$customer->id}}" {!! old( 'customer_id' , $warranty_activation['customer_id'])==$customer->id?'selected':'' !!}>{{$customer->name}}({{$customer->mobile}})</option>
                              @endforeach
                              @endif
                           </select>
                           @if ($errors->has('customer_id'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('customer_id') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="sale_bill_date" class="form-control">Sale Bill Date</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="sale_bill_date" id="sale_bill_date" class="datepicker form-control" placeholder="Sale Bill Date" autocomplete="off" readonly>
                           @if ($errors->has('sale_bill_date'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('sale_bill_date') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                     <div class="col-md-2">
                           <label for="warranty_date" class="form-control">Warranty Date</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="warranty_date" id="warranty_date" class="datepicker form-control" placeholder="Warranty Date" autocomplete="off" readonly>
                           @if ($errors->has('warranty_date'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('warranty_date') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="sale_bill_no" class="form-control">Co Sale Bill No.</label>
                        </div>
                        <div class="col-md-4">
                           <input type="text" name="sale_bill_no" id="sale_bill_no" class="form-control">
                           @if ($errors->has('sale_bill_no'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('sale_bill_no') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>

               <div class="card-footer pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
      $(document).on("keyup", "#serial_no", function() {
         var serial_no = $(this).val();
         $.ajax({
            url: "{{ url('getProductByCoupon') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               serial_no: serial_no
            },
            success: function(res) {
               if (res.status == true) {
                  $('#product_id').html(res.html);
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
               $("#customer_state").val(res.state_name);
               $("#customer_district").val(res.district_name);
               $("#customer_city").val(res.city_name);
               console.log(res);
            }
         });
      });
   </script>
</x-app-layout>