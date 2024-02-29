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
                      <h3 class="card-title pb-3">Approve View</h3>
                    </div>
                    <div class="col-8">
                        
                        @if($expense->checker_status=='3')
                        <button type="button" class="btn btn-dark unchecked_status">Unchecked</button>
                        <button type="button" class="btn btn-success approve_status">Approved</button>
                        <button type="button" class="btn btn-danger reject_status">Rejected</button>
                        @elseif($expense->checker_status=='1') 
                         <button type="button" class="btn btn-danger reject_status">Rejected</button>
                        @else
                         <button type="button" class="btn btn-dark checked_status">Checked</button>
                         <button type="button" class="btn btn-danger reject_status">Rejected</button>
                        @endif 


                        <a class="btn btn-warning" href="{{route('expenses.edit', ['expense' => $expense->id])}}" role="button">Edit</a>
                        <a class="btn btn-primary" href="{{route('expenses.index')}}" role="button">Back</a>
                        
                    </div>
                    <!-- /.col -->
                  </div>
                  <input type="hidden"  id="expenseid"  name="expense" value="{{$expense['id']}}">
                  <hr>

                <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-4">
                  <h4>
                    <small class="float-left">{{ trans('panel.expenses.title') }} #{!! $expense['id'] !!}</small>
                  </h4>
                </div>
               
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                 <div class="col-sm-4 invoice-col">
                  From
                  <address>
                    <strong>{!! isset($expense['users']['name']) ? $expense['users']['name'] :'' !!} </strong><br>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  Expense Type
                  <address>
                    <strong>{!! $expense['expense_type']['name'] !!} 
                             ( {!! isset($expense['expense_type']['allowance_type_id'])?config('constants.allowance_type.'.$expense['expense_type']['allowance_type_id']):''!!} )
                           </strong><br>
                  </address>

                
                </div>

                 <div class="col-sm-4 invoice-col">
                  Date
                  <address>
                    <strong>{!! $expense['date'] !!}</strong><br>
                  </address>
                </div>
              </div>

             <div class="row invoice-info">
                 <div class="col-sm-4 invoice-col">
                  Expense Status
                  <address>
                    <strong> @if($expense->checker_status=='1') 
                              Approve
                             @elseif($expense->checker_status=='2')  
                             Reject
                             @else
                             Pending
                             @endif

                     </strong><br>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  Status Change Reason
                  <address>
                    <strong>test</strong><br>
                  </address>
                </div>
              </div>

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th>{{ trans('panel.expenses.fields.start_km') }}</th>
                      <th>{{ trans('panel.expenses.fields.stop_km') }}</th>
                      <th>{{ trans('panel.expenses.fields.total_km') }}</th>
                      <th>{{ trans('panel.expenses.fields.rate') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                          <td>{!! $expense['start_km']??0 !!}</td>
                          <td>{!! $expense['stop_km']??0 !!}</td>
                          <td>{!! $expense['total_km']??0 !!}</td>
                          <td>{!! $expense['expense_type']['rate']??0 !!}</td>
                        </tr> 
                    </tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

            <div class="row">
              <div class="col-6">
                <div class="table-responsive">
                    <table class="table">
                      <tbody>
                        <tr>
                        <th style="width:20%">Claim Amount:</th>
                        <td>{{$expense['claim_amount']??0}}</td>
                         <input type="text" name="claims" id="claim_new_amount" value="{{$expense['claim_amount']??0}}" hidden>
                      </tr>
                    </tbody>
                 </table>
                  </div>
              </div>

              <div class="col-6">
                <div class="table-responsive">
                    <table class="table">
                      <tbody>
                      <tr>
                        <th style="width:25%">Approved Amount</th>
                        <td>{{$expense['approve_amount']??0}}</td>
                      </tr>
                    </tbody>
                 </table>
                  </div>
              </div>
           </div>     

              <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                  <p class="lead"></p>
                  <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                   <strong> Note:</strong>
                    {!! $expense['note'] !!}
                  </p>
                </div>
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

                     @foreach($logdetails as $logdetail)
                  <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    {{ trans('panel.expenses.title') }}  <b> #{!! $expense['id'] !!}  {{$logdetail->status_type??''}}</b>, by <b>{{$logdetail->logusers->employee_codes??''}} {{$logdetail->logusers->name??''}}</b> on {{date("d-m-Y g:i a", strtotime($logdetail->created_at));}}
                  </p>
                    @endforeach


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
        <form method="POST" action="{{ route('rejectExpense') }}" enctype="multipart/form-data" id="createleadstagesForm_new"> @csrf 
          <div class="row">
            <div class="col-md-6">
              <div class="input-group input-group-outline my-3">
                <label class="form-label">Reason</label>
                <input type="text" name="reason" id="reason" class="form-control" value="{!! old( 'reason') !!}" required> <br><br>
                <input type="text" name="expense_id" id="expense_id" class="form-control" hidden>
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
                <input type="text" name="reasons" id="reasons" class="form-control" value="{!! old( 'reasons') !!}" required> <br><br>
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



<script type="text/javascript">
        $('body').on('click', '.reject_status', function () {
        var id = $('#expenseid').val();
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure do you want to reject expense status")) {
           return false;
        }else{
          $('#expense_id').val(id);
          $("#reject_expense").modal();
        }

    }); 
</script>


<script type="text/javascript">
        $('body').on('click', '.approve_status', function () {
        var id = $('#expenseid').val();
        var claimamount = $('#claim_new_amount').val();
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure do you want to approve expense status")) {
           return false;
        }else{
          $('#expense_new_id').val(id);
          $('#approve_amnt').val(claimamount);
          $("#approve_expense").modal();
        }

    }); 
</script>


  <!-- for checked -->
<script type="text/javascript">

        $('body').on('click', '.checked_status', function () {
        var id = $('#expenseid').val();
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure do you want to checked expense status")) {
           return false;
        }else{

          $.ajax({
          url: "{{ url('expenses-active') }}",
            type: 'POST',
            data: {_token: token,id: id},
            success: function (data) {
              $('.message').empty();
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
                setTimeout(function(){
                    location.reload();
                 }, 3000 ); 
                
              }
              else
              {
                $('.alert').addClass("alert-danger");
                 setTimeout(function(){
                    location.reload();
                 }, 3000 ); 
              }
              $('.message').append(data.message);
             
            },
        });

          
       }

    }); 
</script>



  <!-- for unchecked -->
<script type="text/javascript">

        $('body').on('click', '.unchecked_status', function () {
        var id = $('#expenseid').val();
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure do you want to unchecked expense status")) {
           return false;
        }else{

          $.ajax({
          url: "{{ url('expenses-uncheck') }}",
            type: 'POST',
            data: {_token: token,id: id},
            success: function (data) {
              $('.message').empty();
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
                setTimeout(function(){
                    location.reload();
                 }, 3000 ); 
                
              }
              else
              {
                $('.alert').addClass("alert-danger");
                 setTimeout(function(){
                    location.reload();
                 }, 3000 ); 
              }
              $('.message').append(data.message);
             
            },
        });

          
       }

    }); 
</script>



<script type="text/javascript">
    $("document").ready(function(){
    setTimeout(function(){
       $("#hide_div").remove();
    }, 3000 ); // 3 secs

});
</script>

<script type="text/javascript">
    $("document").ready(function(){
    setTimeout(function(){
       $("#hide_danger").remove();
    }, 3000 ); 
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

