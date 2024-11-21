<x-app-layout>
  <style>
    .table.new-table th,
    .table.new-table td {
      border-top: 0px !important;
    }

    b {
      font-weight: 600;
    }

    .all-attach {
      align-items: center;
      border: 1px solid lightgrey;
      border-radius: 5px;
      padding: 5px 10px;
      width: 90%;
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
                <h3 class="card-title pb-3">Resignation View</h3>
              </div>
              <div class="col-8 text-right">


                @if(auth()->user()->can(['compalint_change_status']))

                @if($resignation->status=='0')
                <a href="#" type="button" class="btn btn-sm btn-success done_status"><b>Accept</b></a>
                <a href="#" type="button" class="btn btn-sm btn-danger done_status"><b>Reject</b></a>
                <a href="#" type="button" class="btn btn-sm btn-info done_status"><b>Revoke</b></a>
                @elseif($resignation->status=='1')
                <button type="button" class="btn btn-sm btn-success open_status"><b>Open Complaint</b></button>
                <button type="button" class="btn btn-sm btn-danger cancel_status"><b>Cancel Complaint</b></button>
                @elseif($resignation->status=='2')
                <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending Complaint</b></button>
                <button type="button" class="btn btn-sm btn-success open_status"><b>Open Complaint</b></button>
                <button type="button bg-primary" class="btn btn-sm complete_status"><b>Complete Complaint</b></button>
                @elseif($resignation->status=='3')
                <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending Complaint</b></button>
                <button type="button" class="btn btn-sm btn-success open_status"><b>Open Complaint</b></button>
                <button type="button" class="btn btn-sm btn-info close_status"><b>Close Complaint</b></button>
                @elseif($resignation->status=='5')
                <button type="button" class="btn btn-sm btn-warning pending_status"><b>Pending Complaint</b></button>
                <button type="button" class="btn btn-sm btn-success open_status"><b>Open Complaint</b></button>
                @endif

                @endif

                <a class="btn btn-primary btn-sm" href="{{route('resignations.index')}}"><b>Back</b></a>


              </div>
              <!-- /.col -->
            </div>
            <input type="hidden" id="resignation_id" name="expense" value="{{$resignation['id']}}">

            <hr>

            <div class="invoice p-3 mb-1">
              <!-- title row -->
              <div class="row">
                <div class="col-4">
                  <h4>
                    <small class="float-left">Submit Date <p style="font-size: 22px; color:#5252b7">{!! date('d M Y',strtotime($resignation['submit_date'])) !!}</p></small>
                  </h4>
                </div>
              </div>
              <!-- info row -->
              <div class="row">
                <div class="col-4">
                    <h5>Name</h5>
                    <h6>{{$resignation->user->name}}</h6>
                </div>
              </div>

            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>


      <!-- for checked -->
      <script type="text/javascript">
        var token = $("meta[name='csrf-token']").attr("content");

        $('body').on('click', '.complete_status', function() {
          var id = $('#complaint_id').val();
          $.ajax({
            url: "{{ url('check-complaint-complete') }}",
            type: 'POST',
            data: {
              _token: token,
              id: id
            },
            success: function(data) {
              if (data.status == 'success') {
                Swal.fire({
                  title: 'Enter your remark',
                  input: 'text',
                  inputPlaceholder: 'Remark',
                  showCancelButton: true,
                  inputValidator: (value) => {
                    if (!value) {
                      return 'You need to write something!';
                    }
                  }
                }).then((result) => {
                  console.log(result);
                  if (result.value) {
                    $.ajax({
                      url: "{{ url('complaint-complete') }}",
                      type: 'POST',
                      data: {
                        _token: token,
                        id: id,
                        remark: result.value
                      },
                      success: function(data) {
                        if (data.status == 'success') {
                          $('.message').empty();
                          $('.alert').show();
                          $('.alert').addClass("alert-success");
                          $('.message').append(data.message);
                          setTimeout(function() {
                            location.reload();
                          }, 700);
                        } else {
                          $('.message').empty();
                          $('.alert').show();
                          $('.alert').addClass("alert-danger");
                          $('.message').append(data.message);
                        }
                      }
                    })
                  }
                });
              } else {
                $('.message').empty();
                $('.alert').show();
                $('.alert').addClass("alert-danger");
                $('.message').append(data.message);
              }
            },
          });
        });
      </script>
  </section>
  <!-- /.content -->
</x-app-layout>