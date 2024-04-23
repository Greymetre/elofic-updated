<x-app-layout>
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
                  <h4>Complaint From</h4>
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
                  <p>Created By : {{$complaint->createdbyname->name}}</p>
                  <p>Closed Date : </p>
                </div>
              </div>

              <hr>

              <div class="row invoice-info">

              </div>





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