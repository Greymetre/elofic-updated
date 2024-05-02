<x-app-layout>
  <style>
    .table.new-table th,
    .table.new-table td {
      border-top: 0px !important;
    }
  </style>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-9">

        @if(Session::has('success'))
        <div class="alert alert-success" id="hide_div">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('success') !!}</strong>
        </div>
        @endif

        @if(Session::has('danger'))
        <div class="alert alert-danger" id="hide_danger">
          <button type="button" class="close" data-dismiss="alert">×</button>
          <strong>{!! session('danger') !!}</strong>
        </div>
        @endif


        <div class="alert" style="display: none;" id="hide_check">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <strong class="message"></strong>
        </div>



        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-4">
                <h3 class="card-title pb-3">Complaint View</h3>
              </div>
              <div class="col-8">


                @if(auth()->user()->can(['compalint_change_status']))

                @if($complaint->complaint_status=='0')
                <button type="button" class="btn btn-warning pending_status">Pending</button>
                @elseif($complaint->complaint_status=='1')
                <button type="button" class="btn btn-info done_status">Work Done</button>
                @elseif($complaint->complaint_status=='2')
                <button type="button" class="btn btn-success complete_status">Completed</button>
                @elseif($complaint->complaint_status=='3')
                <button type="button" class="btn btn-primary close_status">Closed</button>
                @endif

                @endif

                @if($complaint->complaint_status=='5')
                <button type="button" class="btn btn-danger" disabled>Canceled</button>
                @else
                <button type="button" class="btn btn-danger cancel_status">Cancel</button>
                @endif


                <?php
                if (auth()->user()->can(['complaint_edit'])) { ?>

                  <a class="btn btn-warning" href="{{route('complaints.edit', $complaint->id)}}" role="button">Edit</a>

                <?php } ?>

                <a class="btn btn-primary" href="{{route('complaints.index')}}" role="button">Back</a>


              </div>
              <!-- /.col -->
            </div>
            <input type="hidden" id="complaint_id" name="expense" value="{{$complaint['id']}}">
            <hr>

            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-4">
                  <h4>
                    <small class="float-left">COMPLAINT <p style="font-size: 22px; color:#5252b7">#{!! $complaint['complaint_number'] !!}</p></small>
                  </h4>
                </div>

                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info mt-5">
                <div class="col-md-6">
                  <h4><em>Complaint From</em></h4>
                  <h6 style="color: #5252b7;">{{$complaint->customer->customer_name}}</h6>
                  <p>At - {{$complaint->customer->customer_address}} {{$complaint->customer->customer_place}} Po - {{$complaint->customer->customer_city}} District - {{$complaint->customer->customer_district}} State - {{$complaint->customer->customer_state}} Pin - {{$complaint->customer->pincodeDetails->pincode}}</p>
                  <p>{{$complaint->customer->customer_district}}, {{$complaint->customer->customer_state}}, {{$complaint->customer->customer_country}}</p>
                  <p>{{$complaint->customer->customer_number}}</p>
                </div>
                <div class="col-md-6 text-right">
                  <p>Complaint Status :
                    @if($complaint->complaint_status == '0')
                    <span class="badge badge-secondary">Open</span>
                    @elseif($complaint->complaint_status == '1')
                    <span class="badge badge-warning">Pending</span>
                    @elseif($complaint->complaint_status == '2')
                    <span class="badge badge-info">Work Done</span>
                    @elseif($complaint->complaint_status == '3')
                    <span class="badge badge-success">Completed</span>
                    @elseif($complaint->complaint_status == '4')
                    <span class="badge badge-primary">Closed</span>
                    @elseif($complaint->complaint_status == '5')
                    <span class="badge badge-danger">Cancel</span>
                    @endif
                  </p>
                  <p>Complaint Date : {{date('d-m-Y', strtotime($complaint->complaint_date))}}</p>
                  <p>Created By : {{$complaint->createdbyname?$complaint->createdbyname->name:'-'}}</p>
                  <p>Closed Date : -</p>
                </div>
              </div>

              <hr>

              <div class="row invoice-info">
                <div class="col-md-4"></div>
                <div class="col-md-4 text-center" style="line-height: 0px;">
                  <h5><em>Complaint Type</em></h5>
                  <p><b>{{$complaint->complaint_type_details->name}}</b></p>
                </div>
                <div class="col-md-4"></div>
              </div>

              <table class="table responsive border-0 mt-4 new-table">
                <tr>
                  <td><em>Category</em></td>
                  <td><em>Product/Model(Code)</em></td>
                  <td><em>Description</em></td>
                </tr>
                <tr>
                  <th class="pt-0">{{$complaint->product_details?$complaint->product_details->categories->category_name:'-'}}</th>
                  <th class="pt-0">{{$complaint->product_code??'-'}}</th>
                  <th class="pt-0">{{$complaint->product_details?$complaint->product_details->description:'-'}}</th>
                </tr>

                <tr>
                  <td><em>Product Serial Number</em></td>
                  <td><em>HP</em></td>
                  <td><em>Stage</em></td>
                </tr>
                <tr>
                  <th class="pt-0">{{$complaint->product_serail_number??'-'}}</th>
                  <th class="pt-0">{{$complaint->product_details?$complaint->product_details->specification:'-'}}</th>
                  <th class="pt-0">{{$complaint->product_details?$complaint->product_details->product_no:''}}</th>
                </tr>

                <tr>
                  <td><em>Phase</em></td>
                  <td><em>Warranty/Customer Bill Date</em></td>
                  <td><em>Service Paid/Free</em></td>
                </tr>
                <tr>
                  <th class="pt-0">{{($complaint->product_details && $complaint->product_details->phase != '')?$complaint->product_details->phase:'-'}}</th>
                  <th class="pt-0">{{$complaint->customer_bill_date?date('d-m-Y', strtotime($complaint->customer_bill_date)):'-'}}</th>
                  <th class="pt-0">{{$complaint->service_type??'-'}}</th>
                </tr>

                <tr>
                  <td><em>Seller</em></td>
                </tr>
                <tr>
                  <th class="pt-0">{{($complaint->seller_details && $complaint->seller_details->name != '')?'['.$complaint->seller_details->id.'] '.$complaint->seller_details->name:'-'}}</th>
                </tr>
              </table>

              <div class="row invoice-info">
                <div class="col-md-4"></div>
                <div class="col-md-4 text-center" style="line-height: 0px;">
                  <h5><em>Complaint Feedback Type</em></h5>
                  <p><b>-</b></p>
                </div>
                <div class="col-md-4"></div>

                <div class="col-md-4"></div>
                <div class="col-md-4 text-center" style="line-height: 0px;">
                  <h6><em>Feedback</em></h6>
                  <p><b>-</b></p>
                </div>
                <div class="col-md-4"></div>
              </div>

              <table class="table responsive border-0 mt-4 new-table">
                <tr>
                  <td><em>Replacement Tag</em></td>
                  <td><em>Replacement Tag Serial Number</em></td>
                  <td><em>Replacement Tag Description</em></td>
                </tr>
                <tr>
                  <th class="pt-0">-</th>
                  <th class="pt-0">-</th>
                  <th class="pt-0">-</th>
                </tr>
              </table>

              <hr class="mb-4">

              <div class="row invoice-info">
                <div class="col-md-6 mb-3">
                  <div class="row invoice-info" style="align-items: center;border:1px solid lightgrey;border-radius: 5px;padding: 0px 10px;width: 90%;">
                    @if($complaint->warranty_details)
                    @if($complaint->warranty_details->exists && $complaint->warranty_details->getMedia('warranty_activation_attach')->count() > 0 && file_exists($complaint->warranty_details->getFirstMedia('warranty_activation_attach')->getPath()))
                    <a href="{!! $complaint->warranty_details->getMedia('warranty_activation_attach')[0]->getFullUrl() !!}" data-lightbox="mygallery" data-title="Invoice">
                      <img width="50" style="height: 50px !important;" src="{!! $complaint->warranty_details->getMedia('warranty_activation_attach')[0]->getFullUrl() !!}" class="img-fluid rounded"></a>
                    <h6 class="ml-2 mb-0">{{$complaint->warranty_details->getMedia('warranty_activation_attach')[0]->name}}</h6>
                    @else
                    <img width="50" src="{!! url('/').'/'.asset('assets/img/placeholder.jpg') !!}" class="imagepreview1">
                    @endif
                    @endif
                  </div>
                </div>
                @if($complaint->exists && $complaint->getMedia('complaint_attach')->count() > 0 && file_exists($complaint->getFirstMedia('complaint_attach')->getPath()))
                @foreach($complaint->getMedia('complaint_attach') as $k=>$media)
                <div class="col-md-6 mb-2">
                  <div class="row invoice-info" style="align-items: center;border:1px solid lightgrey;border-radius: 5px;padding: 0px 10px;width: 90%;">
                    <a href="{{$media->getFullUrl()}}" data-lightbox="mygallery">
                      <img width="50" style="height: 50px !important;" class="img-fluid rounded" src="{!! $media->getFullUrl() !!}">
                    </a>
                    <h6 class="ml-2 mb-0">{{$media->name}}</h6>
                  </div>
                </div>
                @endforeach
                @endif
              </div>

              <hr class="mt-4">
              <h4>ATTACHMENT</h4>
              <table class="table responsive border-0 mt-4 new-table">
                <tr>
                  <th class="pb-0">SITE/SERVICE</th>
                  <th class="pb-0">BRANCH</th>
                  <th class="pb-0">Parchased Party Name</th>
                  <th class="pb-0">Warranty / Bill</th>
                </tr>
                <tr>
                  <td class="pt-0">-</td>
                  <td class="pt-0">{{$complaint->purchased_branch_details?$complaint->purchased_branch_details->branch_name:'-'}}</td>
                  <td class="pt-0">{{$complaint->party?$complaint->party->name:'-'}}</td>
                  <td class="pt-0">{{$complaint->warranty_bill??'-'}}</td>
                </tr>

                <tr>
                  <th class="pb-0">Customer Bill No.</th>
                  <th class="pb-0">Warranty/Custtomer Bill Date</th>
                  <th class="pb-0">Under Warranty</th>
                  <th class="pb-0">Serbvice Paid/Free</th>
                </tr>
                <tr>
                  <td class="pt-0">{{$complaint->customer_bill_no??'-'}}</td>
                  <td class="pt-0">{{$complaint->customer_bill_date?date('d-m-Y', strtotime($complaint->customer_bill_date)):'-'}}</td>
                  <td class="pt-0">{{$complaint->under_warranty??'-'}}</td>
                  <td class="pt-0">{{$complaint->service_type??'-'}}</td>
                </tr>

                <tr>
                  <th class="pb-0">Company Sale Bill No.</th>
                  <th class="pb-0">Company Sale Bill Date</th>
                  <th class="pb-0">Service Centre Remarks</th>
                  <th class="pb-0">Fault Type</th>
                </tr>
                <tr>
                  <td class="pt-0">{{$complaint->company_sale_bill_no??'-'}}</td>
                  <td class="pt-0">{{$complaint->company_sale_bill_date?date('d-m-Y', strtotime($complaint->company_sale_bill_date)):'-'}}</td>
                  <td class="pt-0">{{$complaint->service_centre_remark??'-'}}</td>
                  <td class="pt-0">{{$complaint->fault_type??'-'}}</td>
                </tr>

                <tr>
                  <th class="pb-0">Remark</th>
                  <th class="pb-0">Complaint Register By</th>
                  <th class="pb-0">Division</th>
                  <th class="pb-0"> </th>
                </tr>
                <tr>
                  <td class="pt-0">{{$complaint->remark??'-'}}</td>
                  <td class="pt-0">{{$complaint->register_by??'-'}}</td>

                  <td class="pt-0">{{$complaint->division_details?$complaint->division_details->division_name:'-'}}</td>
                  <td class="pt-0"> </td>
                </tr>


              </table>

              <hr class="mt-3 mb-3">

              <div class="row invoice-info mt-4">
                <div class="col-md-4">
                  <h4><b>Work Done Detail</b></h4>
                </div>
                <div class="col-md-4 text-center">
                </div>
                <div class="col-md-4"></div>
              </div>

              <table class="table responsive border-0 new-table">
                <tr>
                  <td><em>Action Done by ASC</em></td>
                  <td><em>Service centre remark</em></td>
                  <td><em>Work Done At</em></td>
                </tr>
                <tr>
                  <th class="pt-0">-</th>
                  <th class="pt-0">{{$complaint->service_centre_remark??'-'}}</th>
                  <th class="pt-0">-</th>
                </tr>
              </table>

              <hr class="mt-3 mb-3">

              <div class="row invoice-info mt-4">
                <div class="col-md-4">
                  <h4><b>Warranty Details</b></h4>
                </div>
                <div class="col-md-4 text-center">
                </div>
                <div class="col-md-4"></div>
              </div>

              <table class="table table-striped responsive">
                <tr>
                  <th>Product Serial No.</th>
                  <th>Product</th>
                  <th>Warranty Start Date</th>
                  <th>Warranty Upto</th>
                  <th>Warranty Status</th>
                </tr>
                <tr>
                  <td>{{strtoupper($complaint->product_serail_number)}}</td>
                  <td>[{{$complaint->product_code}}] {{$complaint->product_name}}</td>
                  <td>
                    @if ($complaint->customer_bill_date)
                    {{ date('d-m-Y', strtotime($complaint->customer_bill_date)) }}
                    @else
                    -
                    @endif
                  </td>
                  <td>
                    @if ($complaint->customer_bill_date)
                    @php
                    $today = Carbon\Carbon::today();
                    $date = Carbon\Carbon::parse($complaint->customer_bill_date);
                    if ($date !== false) {
                    $date->addMonths(18);
                    } else {
                    $date = null;
                    }
                    @endphp
                    @if ($date)
                    {{ $date->format('d-m-Y') }}
                    @else
                    Invalid date
                    @endif
                    @else
                    -
                    @endif
                  </td>
                  <td>
                    @if ($complaint->customer_bill_date)
                    @if ($date)
                    @if ($date->gt($today))
                    <span class="badge badge-success">In Warranty</span>
                    @else
                    <span class="badge badge-danger">Out Of Warranty</span>
                    @endif
                    @else
                    Invalid date
                    @endif
                    @else
                    -
                    @endif
                  </td>
                </tr>

              </table>

              <hr class="mt-3 mb-3">

              <div class="row invoice-info mt-4">
                <div class="col-md-4">
                  <h4><b>Service Bill Details</b></h4>
                </div>
                <div class="col-md-4 text-center">
                </div>
                <div class="col-md-4"></div>
              </div>

              <table class="table table-striped responsive">
                <tr>
                  <th>Serial No.</th>
                  <th>Total Amount</th>
                  <th>Service Bill Status</th>
                </tr>
                <tr>
                  <td>{{strtoupper($complaint->product_serail_number)}}</td>
                  <td>-</td>
                  <td>-</td>
                </tr>

              </table>


              <!-- /.row -->
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>



      <div class="col-3">
        <!-- <h4>Time Line</h4> -->

        <div class="card">
          <div class="card-body">
            <!-- <h4>Time Line</h4> -->

            <div class="row">
              <div class="col-12">
                <h3 class="card-title pb-3">Time Line</h3>
                <hr>
                <p class="lead"></p>


                <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                  <b> #{!! $complaint['complaint_number'] !!} Created on {{date("d M Y, h:i a", strtotime($complaint->created_at));}}
                </p>



              </div>

            </div>
          </div>
        </div>
      </div>


      <!-- /.row -->


      <!-- new model for reject status -->

      <div class="modal fade bd-example-modal-lg" id="reject_expense" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content card">
            <div class="card-header card-header-icon card-header-theme">
              <div class="card-icon">
                <i class="material-icons">perm_identity</i>
              </div>
              <h4 class="card-title">
                <span class="modal-title">Submit </span> Reject <span class="pull-right">
                  <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                    <i class="material-icons">clear</i>
                  </a>
                </span>
              </h4>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ route('cancelComplaint') }}" enctype="multipart/form-data" id="createleadstagesForm_new"> @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-group input-group-outline my-3">
                      <label class="form-label">Reason</label>
                      <input type="text" name="reason" id="reason" class="form-control" value="{!! old( 'reason') !!}" required> <br><br>
                      <input type="text" name="cancel_complaint_id" id="cancel_complaint_id" class="form-control" hidden>
                    </div>
                  </div>
                </div>
                <button class="btn btn-info save">Reject</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- end model for status -->


      <!-- new model for approve status -->

      <div class="modal fade bd-example-modal-lg" id="approve_expense" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content card">
            <div class="card-header card-header-icon card-header-theme">
              <div class="card-icon">
                <i class="material-icons">perm_identity</i>
              </div>
              <h4 class="card-title">
                <span class="modal-title">Submit </span> Approve <span class="pull-right">
                  <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
                    <i class="material-icons">clear</i>
                  </a>
                </span>
              </h4>
            </div>
            <div class="modal-body">
              <form method="POST" action="{{ route('approveExpense') }}" enctype="multipart/form-data" id="createleadstagesForms"> @csrf
                <div class="row">
                  <div class="col-md-6">
                    <div class="input-group input-group-outline my-3">
                      <label class="form-label">Approve Amount</label>
                      <input type="text" name="approve_amnt" id="approve_amnt" class="form-control" value="{!! old( 'reason') !!}" required> <br><br>
                      <input type="text" name="expense_new_id" id="expense_new_id" class="form-control" hidden>
                    </div>

                    <div class="input-group input-group-outline my-3">
                      <label class="form-label">Reason</label>
                      <input type="text" name="reasons" id="reasons" class="form-control" value="{!! old( 'reasons') !!}"> <br><br>
                    </div>


                  </div>
                </div>
                <button class="btn btn-info save">Approve</button>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- end model for status -->

      <!-- Custom styles for this page -->
      <link rel="stylesheet" href="{{ url('/').'/'.asset('lightboxx/css/lightbox.min.css') }}">

      <script src="{{ url('/').'/'.asset('lightboxx/js/lightbox-plus-jquery.min.js') }}"></script>




      <script type="text/javascript">
        $('body').on('click', '.cancel_status', function() {
          var id = $('#complaint_id').val();
          var token = $("meta[name='csrf-token']").attr("content");
          if (!confirm("Are You sure do you want to cancel the complaint")) {
            return false;
          } else {
            $('#cancel_complaint_id').val(id);
            $("#reject_expense").modal();
          }

        });
      </script>


      <script type="text/javascript">
        $('body').on('click', '.approve_status', function() {
          var id = $('#expenseid').val();
          var claimamount = $('#claim_new_amount').val();
          var token = $("meta[name='csrf-token']").attr("content");
          if (!confirm("Are You sure do you want to approve expense status")) {
            return false;
          } else {
            $('#expense_new_id').val(id);
            $('#approve_amnt').val(claimamount);
            $("#approve_expense").modal();
          }

        });
      </script>


      <!-- for checked -->
      <script type="text/javascript">
        $('body').on('click', '.pending_status', function() {
          var id = $('#complaint_id').val();
          var token = $("meta[name='csrf-token']").attr("content");
          if (!confirm("Are You sure do you want to Pending Complaint status")) {
            return false;
          } else {
            $.ajax({
              url: "{{ url('complaint-pending') }}",
              type: 'POST',
              data: {
                _token: token,
                id: id
              },
              success: function(data) {
                $('.message').empty();
                $('.alert').show();
                if (data.status == 'success') {
                  $('.alert').addClass("alert-success");
                  setTimeout(function() {
                    location.reload();
                  }, 3000);

                } else {
                  $('.alert').addClass("alert-danger");
                  setTimeout(function() {
                    location.reload();
                  }, 3000);
                }
                $('.message').append(data.message);

              },
            });


          }

        });
      </script>



      <!-- for unchecked -->
      <script type="text/javascript">
        $('body').on('click', '.unchecked_status', function() {
          var id = $('#expenseid').val();
          var token = $("meta[name='csrf-token']").attr("content");
          if (!confirm("Are You sure do you want to unchecked expense status")) {
            return false;
          } else {

            $.ajax({
              url: "{{ url('expenses-uncheck') }}",
              type: 'POST',
              data: {
                _token: token,
                id: id
              },
              success: function(data) {
                $('.message').empty();
                $('.alert').show();
                if (data.status == 'success') {
                  $('.alert').addClass("alert-success");
                  setTimeout(function() {
                    location.reload();
                  }, 3000);

                } else {
                  $('.alert').addClass("alert-danger");
                  setTimeout(function() {
                    location.reload();
                  }, 3000);
                }
                $('.message').append(data.message);

              },
            });


          }

        });
      </script>



      <script type="text/javascript">
        $("document").ready(function() {
          setTimeout(function() {
            $("#hide_div").remove();
          }, 3000); // 3 secs

        });
      </script>

      <script type="text/javascript">
        $("document").ready(function() {
          setTimeout(function() {
            $("#hide_danger").remove();
          }, 3000);
        });
      </script>


      <script type="text/javascript">
        //     $("document").ready(function(){
        //     setTimeout(function(){
        //        $("#hide_check").remove();
        //     }, 3000 ); 
        // });
      </script>

  </section>
  <!-- /.content -->
</x-app-layout>