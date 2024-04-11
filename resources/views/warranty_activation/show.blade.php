<x-app-layout>
   <style>
      .theadl {
         font-weight: 900;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                        Warranty Activation > {{$warrantyactivation->customer->customer_name}}
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
               <div class="row">
                  <div class="col-md-12">
                     <h6>Activation Status</h6>
                     @if($warrantyactivation->status == '0')
                     <p>Pending Activation'</p>
                     @else
                     <p>Activated</p>
                     @endif
                  </div>
                  <div class="col-md-9">
                     <h3>Contact Details</h3>
                     <hr>
                     <div class="row">
                        <div class="col-md-4">
                           <h6>Name</h6>
                           <p>{{$warrantyactivation->customer->customer_name}}</p>
                        </div>
                        <div class="col-md-4">
                           <h6>Email</h6>
                           <p>{{$warrantyactivation->customer->customer_email}}</p>
                        </div>
                        <div class="col-md-4">
                           <h6>Contact</h6>
                           <p>{{$warrantyactivation->customer->customer_number}}</p>
                        </div>
                     </div>
                     <h3 class="mt-4">Warranty Details</h3>
                     <hr>
                     <div class="row">
                        @php
                        $expire_count = $warrantyactivation['product_details']?$warrantyactivation['product_details']['expiry_interval_preiod']:"18";
                        $expire_type = $warrantyactivation['product_details']?strtolower($warrantyactivation['product_details']['expiry_interval'].'s'):"months";
                        @endphp
                        <div class="col-md-3">
                           <h6>Product Serail Number</h6>
                           <p>{{$warrantyactivation->product_serail_number}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Product</h6>
                           <p>{{$warrantyactivation->product_details?$warrantyactivation->product_details->product_name:''}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Start Date</h6>
                           <p>{{date('d M Y', strtotime($warrantyactivation->created_at))}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>End Date</h6>
                           <p>{{date('d M Y', strtotime($warrantyactivation['created_at'] . ' +'.$expire_count.' '.$expire_type))}}</p>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-3">
                           <h6>Seller</h6>
                           @if($warrantyactivation->seller_details)
                           <p><a href="{{route('customers.show', encrypt($warrantyactivation->seller_details->id))}}">[{{$warrantyactivation->seller_details->id}}] {{$warrantyactivation->seller_details->name}}</a></p>
                           <p>{{$warrantyactivation->seller_details->mobile}}</p>
                           @endif
                        </div>
                        <div class="col-md-3">

                        </div>
                     </div>
                     <h3 class="mt-4">Warranty Details</h3>
                     <hr>
                     <div class="row">
                        <div class="col-md-3">
                           <h6>Dealer</h6>
                           <p>-</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Sale Bill Date</h6>
                           <p>{{date('d M Y', strtotime($warrantyactivation->sale_bill_date))}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Dealer Waranty Date</h6>
                           <p>{{date('d M Y', strtotime($warrantyactivation->warranty_date))}}</p>
                        </div>
                        <div class="col-md-3">
                           <h6>Co Sale Bill No.</h6>
                           <p>{{$warrantyactivation->sale_bill_no}}</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3 rounded">
                     <h5 class="theadl">Timeline</h5>
                     <div class="bg-secondar" style="border-top: 1px dashed #a7a3a3;background-color: #f1e7e7;padding: 10px;">
                        <h5 class="theadl">PLANNED</h5>
                        <p class="text-center text-wrap">
                           You don't have any Schedule activity.
                        </p>
                        <h5 class="theadl mt-5">PAST</h5>
                        <div class="d-flex"><i class="material-icons">military_tech</i>
                           @if($warrantyactivation->created_by)
                           <p>Warranty <span class="theadl">{{$warrantyactivation->product_serail_number}}</span> generated by <span class="theadl">{{$warrantyactivation->createdByName->name}}</span> on {{date('d M Y', strtotime($warrantyactivation->created_at))}}</p>
                           @else
                           <p>Warranty <span class="theadl">{{$warrantyactivation->product_serail_number}}</span> generated by <span class="theadl">{{$warrantyactivation->seller_details->name}}</span> on {{date('d M Y', strtotime($warrantyactivation->created_at))}}</p>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</x-app-layout>