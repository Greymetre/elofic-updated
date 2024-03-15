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
                        Redemption
                        @if($redemption->exists)
                        Edit
                        @else
                        Create
                        @endif
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('redemptions') }}">
                                 <i class="material-icons">next_plan</i> Redemptions
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
               <p class="badge d-none" id="bank-details-error"></p>
               {!! Form::model($redemption,[
               'route' => $redemption->exists ? ['redemptions.update', $redemption->id ] : 'redemptions.store',
               'method' => $redemption->exists ? 'PUT' : 'POST',
               'id' => 'storeRedemptionData',
               'files'=>true
               ]) !!}
               <div class="form-group">
                  <div class="row">
                     <div class="col-md-2">
                        <label for="customer_id" class="form-control">Customer</label>
                     </div>
                     <div class="col-md-4">
                        <select name="customer_id" id="customer_id" placeholder="Select Customers" class="select2 form-control" required>
                           <option value="" disabled selected>Select Customer</option>
                           @if($customers && count($customers) > 0)
                           @foreach($customers as $customer)
                           <option value="{{$customer->id}}" {!! (old('customer_id', $redemption['customer_id'])==$customer->id) ?'selected':'' !!}>{{$customer->name}}({{$customer->mobile}})</option>
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
                        <label for="redeem_mode" class="form-control">Redeem Mode</label>
                     </div>
                     <div class="col-md-4">
                        <select name="redeem_mode" id="redeem_mode" placeholder="Select Redeem Mode" class="select2 form-control" required>
                           <option value="" disabled selected>Select Redeem Mode</option>
                           @if($redeem_modes && count($redeem_modes) > 0)
                           @foreach($redeem_modes as $k=>$redeem_mode)
                           <option value="{{$k}}" {!! (old( 'redeem_mode' , $redemption['redeem_mode'])==$k) ?'selected':'' !!}>{{$redeem_mode}}</option>
                           @endforeach
                           @endif
                        </select>
                        @if ($errors->has('redeem_mode'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('redeem_mode') }}</p>
                        </div>
                        @endif
                     </div>
                     <div class="col-md-2">
                        <label for="total_point" class="form-control">Total Point</label>
                     </div>
                     <div class="col-md-2">
                        <input readonly type="number" value="{!! old( 'total_point' ) !!}" name="total_point" id="total_point" class="form-control">
                        @if ($errors->has('total_point'))
                        <div class="error col-lg-12">
                           <p class="text-danger">{{ $errors->first('total_point') }}</p>
                        </div>
                        @endif
                     </div>
                  </div>
                  <div id="bank_details">
                     <div class="row">
                        <div class="col-md-2">
                           <label for="account_holder" class="form-control">Account Holder Name</label>
                        </div>
                        <div class="col-md-4">
                           <input readonly type="text" name="account_holder" value="{!! old( 'account_holder' , $redemption['account_holder']) !!}" id="account_holder" class="form-control">
                           @if ($errors->has('account_holder'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('account_holder') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="account_number" class="form-control">Account Number</label>
                        </div>
                        <div class="col-md-4">
                           <input readonly type="number" value="{!! old( 'account_number' , $redemption['account_number']) !!}" name="account_number" id="account_number" class="form-control">
                           @if ($errors->has('account_number'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('account_number') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="bank_name" class="form-control">Bank Name</label>
                        </div>
                        <div class="col-md-4">
                           <input readonly type="text" name="bank_name" id="bank_name" class="form-control" value="{!! old( 'bank_name' , $redemption['bank_name']) !!}">
                           @if ($errors->has('bank_name'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('bank_name') }}</p>
                           </div>
                           @endif
                        </div>
                        <div class="col-md-2">
                           <label for="ifsc_code" class="form-control">IFSC Code</label>
                        </div>
                        <div class="col-md-4">
                           <input readonly type="text" name="ifsc_code" id="ifsc_code" value="{!! old( 'ifsc_code' , $redemption['ifsc_code']) !!}" class="form-control">
                           @if ($errors->has('ifsc_code'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('ifsc_code') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-2">
                           <label for="redeem_amount" class="form-control">Redeem Amount</label>
                        </div>
                        <div class="col-md-6">
                           <input type="number" value="{!! old( 'redeem_amount' , $redemption['redeem_amount']) !!}" name="redeem_amount" id="redeem_amount" class="form-control">
                           @if ($errors->has('redeem_amount'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('redeem_amount') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
                  </div>
                  <div id="gift_catalogue">
                     <div class="table-responsive">
                        <table id="getproduct" class="table table-striped- table-bordered table-hover table-checkable">
                           <thead class=" text-primary">
                              <th>Gift {!! trans('panel.global.code') !!}</th>
                              <th>{!! trans('panel.gift.fields.product_image') !!}</th>
                              <th>{!! trans('panel.gift.fields.category_name') !!}</th>
                              <th>{!! trans('panel.gift.fields.product_name') !!}</th>
                              <th>{!! trans('panel.gift.fields.points') !!}</th>
                              <th>Select Gift</th>
                           </thead>
                           <tbody>
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>

               <div class="card-footer pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme', 'id' => 'submit-button')) }}
               </div>
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
      $(document).ready(function() {
         $("#bank_details").hide();
         $("#gift_catalogue").hide();
      });
      $("#customer_id").on('change', function() {
         var cust_id = $(this).val();
         if (cust_id != '' && cust_id != null) {
            $.ajax({
               url: "/getBankdetailandPoints",
               data: {
                  'cust_id': cust_id
               },
               success: function(data) {
                  $("#total_point").val(data.Total_points);
                  $("#account_holder").val(data.bank_details.account_holder);
                  $("#account_number").val(data.bank_details.account_number);
                  $("#bank_name").val(data.bank_details.bank_name);
                  $("#ifsc_code").val(data.bank_details.ifsc_code);
                  var redeem_mode = $("#redeem_mode").val();
                  if (redeem_mode != '' && redeem_mode != null) {
                     if (redeem_mode == '1') {
                        if (!data.shop_img || data.shop_img == '' || data.shop_img == null || data.aadhar_details.aadhar_no_status != '1' || data.Total_points < '1') {
                           $("#submit-button").prop('disabled', true);
                           $("#bank-details-error").removeClass('d-none');
                           $("#bank-details-error").removeClass('badge-success');
                           $("#bank-details-error").addClass('badge-danger');
                           $("#bank-details-error").html('Note : Your KYC is not completed or you don\'t have sufficient point For Gift Redemption.');
                        } else {
                           $("#submit-button").prop('disabled', false);
                           $("#bank-details-error").removeClass('d-none');
                           $("#bank-details-error").removeClass('badge-danger');
                           $("#bank-details-error").addClass('badge-success');
                           $("#bank-details-error").html('Note : Your KYC are verified For Gift Redemption !!.');
                        }
                        oTable = $('#getproduct').DataTable({
                           "processing": true,
                           "serverSide": true,
                           "bDestroy": true,
                           "order": [
                              [0, 'desc']
                           ],
                           "ajax": {
                              "url": "{{ route('redemptions.gift-catalogue') }}",
                              "data": {
                                 "Total_points": data.Total_points,
                                 "data": '{!! json_encode($redemption) !!}'
                              }
                           },
                           "columns": [{
                                 data: 'id',
                                 name: 'id',
                                 orderable: false,
                                 searchable: false
                              },
                              {
                                 data: 'image',
                                 name: 'image',
                                 "defaultContent": '',
                                 orderable: false,
                                 searchable: false
                              },
                              {
                                 data: 'categories.category_name',
                                 name: 'categories.category_name',
                                 "defaultContent": ''
                              },
                              {
                                 data: 'display_name',
                                 name: 'display_name',
                                 "defaultContent": ''
                              },

                              {
                                 data: 'points',
                                 name: 'points',
                                 "defaultContent": ''
                              },
                              {
                                 data: 'action',
                                 name: 'action',
                                 "defaultContent": '',
                                 orderable: false,
                                 searchable: false
                              },
                           ]
                        });
                     } else if (redeem_mode == '2') {
                        if (!data.shop_img || data.shop_img == '' || data.shop_img == null || data.aadhar_details.aadhar_no_status != '1' || data.bank_details.bank_status != '1' || data.Total_points < '1') {
                           $("#submit-button").prop('disabled', true);
                           $("#bank-details-error").removeClass('d-none');
                           $("#bank-details-error").removeClass('badge-success');
                           $("#bank-details-error").addClass('badge-danger');
                           $("#bank-details-error").html('Note : Your KYC is not completed or you don\'t have sufficient point For NEFT Redemption.');
                        } else {
                           $("#submit-button").prop('disabled', false);
                           $("#bank-details-error").removeClass('d-none');
                           $("#bank-details-error").removeClass('badge-danger');
                           $("#bank-details-error").addClass('badge-success');
                           $("#bank-details-error").html('Note : Your KYC are verified For NEFT Redemption !!.');
                        }
                     }
                  }

               }
            });
         }
      }).trigger('change');

      $("#redeem_mode").on('change', function() {
         var redeem_mode = $(this).val();
         var cust_id = $("#customer_id").val();
         if (cust_id != '' && cust_id != null) {
            $.ajax({
               url: "/getBankdetailandPoints",
               data: {
                  'cust_id': cust_id
               },
               success: function(data) {
                  if (redeem_mode == '2') {
                     if (!data.shop_img || data.shop_img == '' || data.shop_img == null || data.aadhar_details.aadhar_no_status != '1' || data.bank_details.bank_status != '1' || data.Total_points < '1') {
                        $("#submit-button").prop('disabled', true);
                        $("#bank-details-error").removeClass('d-none');
                        $("#bank-details-error").removeClass('badge-success');
                        $("#bank-details-error").addClass('badge-danger');
                        $("#bank-details-error").html('Note : Your KYC is not completed or you don\'t have sufficient point For NEFT Redemption.');
                     } else {
                        $("#submit-button").prop('disabled', false);
                        $("#bank-details-error").removeClass('d-none');
                        $("#bank-details-error").removeClass('badge-danger');
                        $("#bank-details-error").addClass('badge-success');
                        $("#bank-details-error").html('Note : Your KYC are verified For NEFT Redemption !!.');
                     }
                  } else if (redeem_mode == '1') {
                     if (!data.shop_img || data.shop_img == '' || data.shop_img == null || data.aadhar_details.aadhar_no_status != '1' || data.Total_points < '1') {
                        $("#submit-button").prop('disabled', true);
                        $("#bank-details-error").removeClass('d-none');
                        $("#bank-details-error").removeClass('badge-success');
                        $("#bank-details-error").addClass('badge-danger');
                        $("#bank-details-error").html('Note : Your KYC is not completed or you don\'t have sufficient point For Gift Redemption.');
                     } else {
                        $("#submit-button").prop('disabled', false);
                        $("#bank-details-error").removeClass('d-none');
                        $("#bank-details-error").removeClass('badge-danger');
                        $("#bank-details-error").addClass('badge-success');
                        $("#bank-details-error").html('Note : Your KYC are verified For Gift Redemption !!.');
                     }
                  }
               }
            });
         }
         setTimeout(() => {
            if (redeem_mode == '2') {
               $("#bank_details").show();
               $("#gift_catalogue").hide();
            } else if (redeem_mode == '1') {
               $("#bank_details").hide();
               $("#gift_catalogue").show();
               var Total_points = $("#total_point").val();
               if (cust_id != '' && cust_id != null) {
                  oTable = $('#getproduct').DataTable({
                     "processing": true,
                     "serverSide": true,
                     "bDestroy": true,
                     "order": [
                        [0, 'desc']
                     ],
                     //"dom": 'Bfrtip',
                     "ajax": {
                        "url": "{{ route('redemptions.gift-catalogue') }}",
                        'data': {
                           "Total_points": Total_points,
                           "data": '{!! json_encode($redemption) !!}'
                        }
                     },
                     "columns": [{
                           data: 'id',
                           name: 'id',
                           orderable: false,
                           searchable: false
                        },
                        {
                           data: 'image',
                           name: 'image',
                           "defaultContent": '',
                           orderable: false,
                           searchable: false
                        },
                        {
                           data: 'categories.category_name',
                           name: 'categories.category_name',
                           "defaultContent": ''
                        },
                        {
                           data: 'display_name',
                           name: 'display_name',
                           "defaultContent": ''
                        },

                        {
                           data: 'points',
                           name: 'points',
                           "defaultContent": ''
                        },
                        {
                           data: 'action',
                           name: 'action',
                           "defaultContent": '',
                           className: 'td-actions text-center',
                           orderable: false,
                           searchable: false
                        },
                     ]
                  });
               }
            }
         }, 1000);
      }).trigger('change');
   </script>
</x-app-layout>