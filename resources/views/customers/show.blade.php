<x-app-layout>
<div class="row">
   <div class="col">
  <div class="card card-body">
     <div class="row">
        <div class="col-md-12">
           <span class="pull-right">
              <div class="btn-group">
                 @if(auth()->user()->can(['customer_edit']))
                 <a href="{{ url('customers/'.encrypt($customers->id).'/edit') }}"  class="btn btn-just-icon btn-theme"><i class="material-icons">edit</i></a>
                 @endif
              </div>
           </span>
        </div>
     </div>
     <div class="row gx-4 mb-2">
        <div class="col-auto">
           <div class="avatar avatar-xl position-relative">
              <img src="{!! !empty($customers['profile_image']) ? env('IMAGE_UPLOADS').$customers['profile_image'] : asset('public/assets/img/placeholder.jpg') !!}" alt="profile_image" class="w-100 border-radius-lg shadow-sm imageDisplayModel">
           </div>
        </div>
        <div class="col-auto my-auto">
           <div class="h-100">
              <h5 class="mb-1">
                 {!! isset($customers['name']) ? $customers['name'] : '' !!}
              </h5>
              <p class="mb-0 font-weight-normal text-sm">
                 {!! isset($customers['customertypes']['customertype_name']) ? $customers['customertypes']['customertype_name'] : '' !!}
              </p>
           </div>
        </div>
        <div class="col-lg-6 col-md-6 my-sm-auto ms-sm-auto me-sm-0 mx-auto mt-3">
           <div class="nav-wrapper position-relative end-0">
              <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" role="tablist">
                 <li class="nav-item">
                    <a class="nav-link active show" data-toggle="tab" href="#profile-tabs-detail" role="tablist">
                    <i class="material-icons">preview</i> Details
                    </a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link orderinfo" data-toggle="tab" href="#profile-tabs-orders" role="tablist">
                    <i class="material-icons">add_shopping_cart</i> Orders
                    </a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link pointsinfo" data-toggle="tab" href="#profile-tabs-sales" role="tablist">
                    <i class="material-icons">shopping_bag</i> Sales
                    </a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#profile-tabs-payments" role="tablist">
                    <i class="material-icons">currency_rupee</i> Payments
                    </a>
                 </li>
                 <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#profile-tabs-activity" role="tablist" onclick="getCustomerActivity()">
                    <i class="material-icons">add_task</i> Activity
                    </a>
                 </li>
              </ul>
           </div>
        </div>
     </div>
     <div class="row">
        <div class="tab-content tab-subcategories">
           <div class="tab-pane active show" id="profile-tabs-detail">
              <div class="row mt-3">
                 <div class="col-md-4 col-xl-4 mt-md-0 mt-4 position-relative">
                    <div class="card card-plain h-100">
                       <div class="card-header pb-0 p-3">
                          <div class="row">
                             <div class="col-md-8 d-flex align-items-center">
                                <h6 class="mb-0">Personal Information</h6>
                             </div>
                          </div>
                       </div>
                       <div class="card-body p-3">
                          <!-- <p class="text-sm">
                             </p> -->
                          <hr class="horizontal gray-light my-4">
                          <ul class="list-group">
                             <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; {!! isset($customers['first_name']) ? $customers['first_name'] : '' !!} {!! isset($customers['last_name']) ? $customers['last_name'] : '' !!}</li>
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Mobile:</strong> &nbsp; {!! isset($customers['mobile']) ? $customers['mobile'] : '' !!}</li>
                             @if(isset($customers['email']))
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; {!! $customers['email'] !!} </li>
                             @endif
                             @if(isset($customers['gender']) && $customers['gender'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Gender:</strong> &nbsp; {!! $customers['gender'] !!} </li>
                             @endif
                          </ul>
                          <hr class="horizontal gray-light my-4">
                          <hr class="horizontal gray-light my-4">
                          <h6 class="mb-0">Address Info</h6>
                          <hr class="horizontal gray-light my-4">
                          <ul class="list-group">
                             @if(isset($customers['customeraddress']['address1']) && $customers['customeraddress']['address1'] != '')
                             <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Address:</strong> &nbsp; {!! isset($customers['customeraddress']['address1']) ? $customers['customeraddress']['address1'] : '' !!} 
                                {!! isset($customers['customeraddress']['address2']) ? $customers['customeraddress']['address2'] : '' !!} 
                                {!! isset($customers['customeraddress']['landmark']) ? $customers['customeraddress']['landmark'] : '' !!}
                                {!! isset($customers['customeraddress']['locality']) ? $customers['customeraddress']['locality'] : '' !!}
                             </li>
                             @endif
                             @if(isset($customers['customeraddress']['cityname']['city_name']) && $customers['customeraddress']['cityname']['city_name'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">City:</strong> &nbsp; {!! $customers['customeraddress']['cityname']['city_name'] !!} </li>
                             @endif
                             @if(isset($customers['customeraddress']['districtname']['district_name']) && $customers['customeraddress']['districtname']['district_name'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">District:</strong> &nbsp; {!! $customers['customeraddress']['districtname']['district_name'] !!} </li>
                             @endif
                             @if(isset($customers['customeraddress']['statename']['state_name']) && $customers['customeraddress']['statename']['state_name'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">State:</strong> &nbsp; {!! $customers['customeraddress']['statename']['state_name'] !!} </li>
                             @endif
                             @if(isset($customers['customeraddress']['pincodename']['pincode']) && $customers['customeraddress']['pincodename']['pincode'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Pincode:</strong> &nbsp; {!! $customers['customeraddress']['pincodename']['pincode'] !!} </li>
                             @endif
                          </ul>
                       </div>
                    </div>
                    <hr class="vertical dark">
                 </div>
                 <div class="col-md-4 col-xl-4 mt-md-0 mt-4 position-relative">
                    <div class="card card-plain h-100">
                       <div class="card-header pb-0 p-3">
                          <div class="row">
                             <div class="col-md-8 d-flex align-items-center">
                                <h6 class="mb-0">Customer Information</h6>
                             </div>
                          </div>
                       </div>
                       <div class="card-body p-3">
                          <!-- <p class="text-sm"></p> -->
                          <hr class="horizontal gray-light my-4">
                          <ul class="list-group">
                             <!-- @if(isset($customers['employeename']['name']))
                             <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Executive:</strong> &nbsp; {!! $customers['employeename']['name'] !!} </li>
                             @endif -->
                             
                              @if(isset($customers->getemployeedetail))
                             <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Executive:</strong> &nbsp;<?php foreach($customers->getemployeedetail as $key_new => $customer_detail) {  
                                  echo $customer_detail->employee_detail->name.' '.',<br>';
                                 }  ?> </li>
                              @endif

                             @if(isset($customers['customer_code']))
                             <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Customer Code:</strong> &nbsp; {!! $customers['customer_code'] !!} </li>
                             @endif
                             @if(isset($customers['manager_name']))
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Manager Name:</strong> &nbsp; {!! $customers['manager_name'] !!} </li>
                             @endif
                             @if(isset($customers['manager_phone']))
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Manager Phone:</strong> &nbsp; {!! $customers['manager_phone'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['gstin_no']) && $customers['customerdetails']['gstin_no'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">GSTIN No:</strong> &nbsp; {!! $customers['customerdetails']['gstin_no'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['pan_no']) && $customers['customerdetails']['pan_no'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Pan No:</strong> &nbsp; {!! $customers['customerdetails']['pan_no'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['aadhar_no'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Aadhar No:</strong> &nbsp; {!! $customers['customerdetails']['aadhar_no'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['otherid_no']) && $customers['customerdetails']['otherid_no'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Other No:</strong> &nbsp; {!! $customers['customerdetails']['otherid_no'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['enrollment_date']) && $customers['customerdetails']['enrollment_date'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Enrollment Date:</strong> &nbsp; {!! $customers['customerdetails']['enrollment_date'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['approval_date']) && $customers['customerdetails']['approval_date'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Approval Date:</strong> &nbsp; {!! $customers['customerdetails']['approval_date'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['aadhar_no'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Aadhar No:</strong> &nbsp; {!! $customers['customerdetails']['aadhar_no'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['shop_image']) && $customers['customerdetails']['shop_image'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><img src="{!! !empty($customers['customerdetails']['shop_image']) ? env('IMAGE_UPLOADS').$customers['customerdetails']['shop_image'] : asset('public/assets/img/placeholder.jpg') !!}" alt="profile_image" class="w-100 border-radius-lg shadow-sm"> </li>
                             @endif
                             @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['visiting_card'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><img src="{!! $customers['customerdetails']['visiting_card'] !!}" alt="profile_image" class="w-100 border-radius-lg shadow-sm"> </li>
                             @endif
                             @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['grade'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Grade:</strong> &nbsp; {!! $customers['customerdetails']['grade'] !!} </li>
                             @endif
                             @if(isset($customers['customerdetails']['aadhar_no']) && $customers['customerdetails']['visit_status'] != '')
                             <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Visit Status:</strong> &nbsp; {!! $customers['customerdetails']['visit_status'] !!} </li>
                             @endif
                          </ul>
                          <hr class="horizontal gray-light my-4">
                          <h6 class="mb-0">Survey Information</h6>
                          <hr class="horizontal gray-light my-4">
                          <div id="accordion" role="tablist">
                             @if(!empty($customers['surveys']))
                             @foreach( $customers['surveys']  as $index => $survey )
                             <div class="card-collapse">
                                <div class="card-header" role="tab" id="heading{{$index}}">
                                   <h5 class="mb-0">
                                      <a data-toggle="collapse" href="#collapse{{$index}}" aria-expanded="false" aria-controls="collapse{{$index}}" class="collapsed">
                                      {!! isset($survey['fields']['label_name']) ? $survey['fields']['label_name'] : '' !!}
                                      <i class="material-icons">keyboard_arrow_down</i>
                                      </a>
                                   </h5>
                                </div>
                                <div id="collapse{{$index}}" class="collapse" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion" style="">
                                   <div class="card-body">
                                      {!! $survey['value'] !!}
                                   </div>
                                </div>
                             </div>
                             @endforeach
                             @endif
                          </div>
                       </div>
                    </div>
                    <hr class="vertical dark">
                 </div>
                 <div class="col-md-4 mt-md-0 mt-4">
                    <div class="card card-plain h-100">
                       <div class="card-header pb-0 p-3">
                          <h6 class="mb-0">Conversations</h6>
                       </div>
                       <div class="card-body p-3">
                          <ul class="timeline timeline-simple">
                             @if(isset($customers['visitsinfo']))
                             @foreach( $customers['visitsinfo'] as $visit )
                             @if(isset($visit['description']) && $visit['description'] != '')
                             <li class="timeline-inverted">
                                <div class="timeline-badge danger">
                                   <i class="material-icons">card_travel</i>
                                </div>
                                <div class="timeline-panel">
                                   <div class="timeline-heading">
                                      <span class="badge badge-pill badge-danger">{!! $visit['users']['name'] !!}</span>
                                   </div>
                                   <div class="timeline-body">
                                      <p>{!! $visit['description'] !!}</p>
                                   </div>
                                   <h6>
                                      <i class="ti-time"></i> {!! date("d-m-Y", strtotime($visit['created_at']))  !!}
                                   </h6>
                                </div>
                             </li>
                             @endif
                             @endforeach
                             @endif
                          </ul>
                       </div>
                    </div>
                 </div>
                 <div class="showCustomerLocationonMaps" style="display:none;">
                    <div id="map" style="width: 900px; height: 700px;"></div>
                </div>
                <div class="col-md-12">
                  <span class="pull-right">
                      <a href="javascript:void(0)" class="btn btn-just-icon btn-theme" onclick="showGoogleMaps()"><i class="material-icons">room</i></a>
                   </span>
                </div>
              </div>
           </div>
           <div class="tab-pane " id="profile-tabs-orders">
              <div class="row">
                 <div class="col-md-12">
                    <h4 class="mb-0">Orders List</h4>
                    <div class="table-responsive">
                       <table id="getorder" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                          <thead class="text-primary">
                             <th>{!! trans('panel.global.action') !!}</th>
                             <th>{!! trans('panel.global.seller') !!}</th>
                             <th>{!! trans('panel.global.buyer') !!}</th>
                             <th>{!! trans('panel.order.orderno') !!}</th>
                             <th>{!! trans('panel.order.order_date') !!}</th>
                             <th>{!! trans('panel.order.grand_total') !!}</th>
                          </thead>
                          <tbody>
                          </tbody>
                       </table>
                    </div>
                 </div>
              </div>
           </div>
           <div class="tab-pane " id="profile-tabs-sales">
              <h4 class="mb-0">Sales List</h4>
              <div class="table-responsive">
                 <table id="getsales" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                    <thead class=" text-primary">
                       <th>{!! trans('panel.global.action') !!}</th>
                       <th>{!! trans('panel.global.seller') !!}</th>
                       <th>{!! trans('panel.sale.fields.invoice_no') !!}</th>
                       <th>{!! trans('panel.sale.fields.invoice_date') !!}</th>
                       <th>{!! trans('panel.sale.fields.grand_total') !!}</th>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
              </div>
           </div>
           <div class="tab-pane " id="profile-tabs-payments">
              <h4 class="mb-0">Payments List</h4>
              <div class="table-responsive">
                 <table id="getPaymentList" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap" style="width: 100%">
                    <thead class=" text-primary">
                       <th>No</th>
                       <th>Date</th>
                       <th>Type</th>
                       <th>Mode</th>
                       <th>Amount</th>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
              </div>
           </div>
           <div class="tab-pane " id="profile-tabs-activity">
              <div class="row mt-3">
                 <div class="col-12">
                    <div class="card card-plain h-100">
                       <div class="card-header pb-0 p-3">
                          <h4 class="section-heading mb-3 h4 mt-0">
                             Activity List
                             <span class="pull-right">
                                <div class="btn-group">
                                   <a class="btn btn-just-icon btn-theme create" title="Create Notes" onclick="showCreateNotes()"><i class="material-icons">add_circle</i></a>
                                </div>
                             </span>
                          </h4>
                       </div>
                       <div class="card-body p-3">
                          <div class="row">
                             <div class="col-md-12 createCustomerActivity" style="display:none;">
                                {!! Form::open(['route' => 'notes.store']) !!}
                                <input type="hidden" name="customer_id" value="{!! $customers['id'] !!}">
                                <input type="hidden" name="id" id="note_id">
                                <div class="row">
                                   <div class="col-md-6">
                                      <div class="row">
                                         <label class="col-md-3 col-form-label">Purpose<span class="text-danger"> *</span></label>
                                         <div class="col-md-9">
                                            <div class="form-group has-default bmd-form-group">
                                               <select class="form-control select2" name="purpose" id="purpose" style="width: 100%;" required>
                                                  <option value="" disabled>Select Purpose</option>
                                                  <option value="Welcome Call">Welcome Call</option>
                                                  <option value="Product Launch">Product Launch</option>
                                               </select>
                                            </div>
                                            @if ($errors->has('purpose'))
                                            <div class="error col-lg-12">
                                               <p class="text-danger">{{ $errors->first('purpose') }}</p>
                                            </div>
                                            @endif
                                         </div>
                                      </div>
                                   </div>
                                   <div class="col-md-6">
                                      <div class="row">
                                         <label class="col-md-3 col-form-label">Call Status <span class="text-danger"> *</span></label>
                                         <div class="col-md-9">
                                            <div class="form-group has-default bmd-form-group">
                                               <select class="form-control select2" name="callstatus" id="callstatus" data-style="select-with-transition" title="Select Call Status" required style="width: 100%;">
                                                  <option value="" disabled>Select Call Status</option>
                                                  <option value="Switched Off">Switched Off</option>
                                                  <option value="Out Of Network">Out Of Network</option>
                                                  <option value="Did Not Pick">Did Not Pick</option>
                                                  <option value="Call Back Later">Call Back Later</option>
                                                  <option value="Call Done">Call Done</option>
                                                  <option value="Not Interested">Not Interested</option>
                                                  <option value="Language Issue">Language Issue</option>
                                                  <option value="Wrong Number">Wrong Number</option>
                                                  <option value="Call Disconnected">Call Disconnected</option>
                                                  
                                               </select>
                                            </div>
                                         </div>
                                      </div>
                                   </div>
                                   <div class="col-md-12">
                                      <div class="row">
                                         <label class="col-sm-1 col-form-label">Notes<span class="text-danger"> *</span></label>
                                         <div class="col-sm-11">
                                            <div class="form-group bmd-form-group">
                                               <textarea class="form-control" rows="4" name="note" id="note" required>{!! old( 'note') !!}</textarea>
                                            </div>
                                         </div>
                                      </div>
                                   </div>
                                   <div class="col-md-12 pull-right">
                                      {{ Form::submit('Submit', array('class' => 'btn btn-info pull-right')) }}
                                   </div>
                                   {{ Form::close() }}
                                </div>
                             </div>
                             <div class="col-md-12">
                                <ul class="timeline timeline-simple customerActivity">
                                </ul>
                             </div>
                          </div>
                       </div>
                    </div>
                 </div>
              </div>
           </div>
        </div>
     </div>
  </div>
   </div>
</div>
<script src="http://maps.google.com/maps/api/js?key=AIzaSyAVSDwHbKULnZa93kYpYINTqX4eaWy9q18" type="text/javascript"></script>
<script type="text/javascript">
   $(function () {
   $.ajaxSetup({
         headers: {
             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         }
   });
   var orderTable = $('#getorder').DataTable({
       "processing": true,
       "serverSide": true,
       "pageLength": 5,
       "searching": false,
       "ordering": false,
       "bLengthChange": false,
       "retrieve": true,
       ajax: {
         url: "{{ route('orders.info') }}",
         data: function (d) {
               d.buyer_id = "{!! $customers['id'] !!}"
           }
       },
       columns: [
           {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
           {data: 'sellers.name', name: 'sellers.name',"defaultContent": ''},
           {data: 'buyers.name', name: 'buyers.name',"defaultContent": ''},
           {data: 'orderno', name: 'orderno',"defaultContent": ''},
           {data: 'order_date', name: 'order_date',"defaultContent": ''},
           {data: 'grand_total', name: 'grand_total',"defaultContent": ''},
       ]
   });
   var salesTable = $('#getsales').DataTable({
       "processing": true,
       "serverSide": true,
       "pageLength": 5,
       "searching": false,
       "ordering": false,
       "bLengthChange": false,
       "retrieve": true,
       ajax: {
         url: "{{ route('sales.info') }}",
         data: function (d) {
               d.buyer_id = "{!! $customers['id'] !!}"
           }
       },
       columns: [
           {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
           {data: 'sellers.name', name: 'sellers.name',"defaultContent": ''},
           {data: 'invoice_no', name: 'invoice_no',"defaultContent": ''},
           {data: 'invoice_date', name: 'invoice_date',"defaultContent": ''},
           {data: 'grand_total', name: 'grand_total',"defaultContent": ''},
       ]
   });
   var pointsTable = $('#getpoints').DataTable({
       "processing": true,
       "serverSide": true,
       "pageLength": 5,
       "searching": false,
       "ordering": false,
       "bLengthChange": false,
       "retrieve": true,
       ajax: {
         url: "{{ route('wallets.info') }}",
         data: function (d) {
               d.customer_id = "{!! $customers['id'] !!}"
           }
       },
       columns: [
           { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
           {data: 'transaction_at', name: 'transaction_at',"defaultContent": ''},
           {data: 'point_type', name: 'point_type',"defaultContent": ''},
           {data: 'points', name: 'points',"defaultContent": ''},
       ]
   });
   var pointsTable = $('#getCoupons').DataTable({
       "processing": true,
       "serverSide": true,
       "pageLength": 5,
       "searching": false,
       "ordering": false,
       "bLengthChange": false,
       "retrieve": true,
       ajax: {
         url: "{{ route('wallets.info') }}",
         data: function (d) {
               d.customer_id = "{!! $customers['id'] !!}",
               d.coupon = "Yes"
           }
       },
       columns: [
           { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
           {data: 'transaction_at', name: 'transaction_at',"defaultContent": ''},
           {data: 'point_type', name: 'point_type',"defaultContent": ''},
           {data: 'points', name: 'points',"defaultContent": ''},
       ]
   });
   var pointsTable = $('#getRedeemed').DataTable({
       "processing": true,
       "serverSide": true,
       "pageLength": 5,
       "searching": false,
       "ordering": false,
       "bLengthChange": false,
       "retrieve": true,
       ajax: {
         url: "{{ route('wallets.info') }}",
         data: function (d) {
               d.customer_id = "{!! $customers['id'] !!}",
               d.redeem = "Yes"
           }
       },
       columns: [
           { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
           {data: 'transaction_at', name: 'transaction_at',"defaultContent": ''},
           {data: 'points', name: 'points',"defaultContent": ''},
       ]
   });
   
   var paymentTable = $('#getPaymentList').DataTable({
       "processing": true,
       "serverSide": true,
       "pageLength": 5,
       "searching": false,
       "ordering": false,
       "bLengthChange": false,
       "retrieve": true,
       ajax: {
         url: "{{ route('payments.info') }}",
         data: function (d) {
               d.customer_id = "{!! $customers['id'] !!}"
           }
       },
       columns: [
           { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
           {data: 'payment_date', name: 'payment_date',"defaultContent": ''},
           {data: 'payment_type', name: 'payment_type',"defaultContent": ''},
           {data: 'payment_mode', name: 'payment_mode',"defaultContent": ''},
           {data: 'amount', name: 'amount',"defaultContent": ''},
       ]
   });
   
   $('.orderinfo').change(function(){
       orderTable.draw();
   });
   $('.salesinfo').change(function(){
       salesTable.draw();
   });
   $('.pointsinfo').change(function(){
       pointsTable.draw();
   });
   });
   function getCustomerActivity()
   {
   $.ajax({
    url: "{{ url('getCustomerActivityData') }}",
    dataType: "json",
    type: "POST",
    data:{ _token: "{{csrf_token()}}", customer_id:"{{ $customers['id'] }}" },
    success: function(res){
     $(".customerActivity").empty();
       $.each(res, function(index,item) {  
          $(".customerActivity").append('<li class="timeline-inverted">'+
             '<div class="timeline-badge danger">'+
                '<i class="material-icons">card_travel</i>'+
             '</div>'+
             '<div class="timeline-panel">'+
                '<div class="timeline-heading">'+
                   '<div class="d-flex">'+
                     '<div class="mr-auto p-2">'+
                       '<span class="badge badge-pill badge-danger">'+item.callstatus+'</span>'+
                     '</div>'+
                     '<div class="mr-auto p-2">'+
                       '<span class="badge badge-pill badge-info">'+item.purpose+'</span>'+
                     '</div>'+
                     '<div class="p-2">'+
                       '<a href="javascript:void(0)" class="btn btn-info btn-link editNotesForm" rel="tooltip" onclick="editNotesForm('+item.id+')"><i class="material-icons">edit</i></a>'+
                     '</div>'+
                   '</div>'+
                '</div>'+
                '<div class="timeline-body">'+
                   '<p>'+item.note+'</p>'+
                '</div>'+
                '<h6>'+
                   '<i class="ti-time"></i> '+new Date(item.created_at).toLocaleDateString()+
                '</h6>'+
             '</div>'+
          '</li>');
       });
    }
   }) 
   }
   function showCreateNotes()
   {
   $('.createCustomerActivity').show();
   } 
   function showGoogleMaps()
   {
      var locations = [
       [
               "{{ $customers['name'] }}", "{{ $customers['latitude'] }}", "{{ $customers['longitude'] }}", 1
       ]
     ];
       var map = new google.maps.Map(document.getElementById('map'), {
         zoom: 12,
         center: new google.maps.LatLng("{{ $customers['latitude'] }}", "{{ $customers['longitude'] }}"),
         mapTypeId: google.maps.MapTypeId.ROADMAP
       });
       var infowindow = new google.maps.InfoWindow();
       var marker, i;
       for (i = 0; i < locations.length; i++) {  
         marker = new google.maps.Marker({
           position: new google.maps.LatLng(locations[i][1], locations[i][2]),
           map: map
         });
         google.maps.event.addListener(marker, 'click', (function(marker, i) {
           return function() {
             infowindow.setContent(locations[i][0]);
             infowindow.open(map, marker);
           }
         })(marker, i));
       }
     $('.showCustomerLocationonMaps').show();
   }
   
   function editNotesForm( id ){
   var base_url =$('.baseurl').data('baseurl');
   $.ajax({
   url: base_url + '/notes/'+id+'/edit',
   dataType:"json",
   success:function(data)
   {
     $('#purpose').val(data.purpose);
     $('#note').val(data.note);
     $('#callstatus').val(data.callstatus).change();
     $('#status_id').val(data.status_id).change();
     $('#note_id').val(data.id);
     showCreateNotes()
   }
   })
   }
</script>
</x-app-layout>