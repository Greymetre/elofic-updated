 <x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">{!! trans('panel.expenses.title_singular') !!} {!! trans('panel.global.list') !!}
              <span class="pull-right">
                <div class="btn-group">

        
              @if(auth()->user()->can(['expense_download']))
              <form method="GET" action="{{ URL::to('expenses-download') }}">
                  <div class="d-flex flex-row">

                  <div class="p-2" style="width:150px;">
                    <select class="selectpicker" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                     <option value="">Select Branch</option>
                    @if(@isset($branches ))
                        @foreach($branches as $branch)
                          <option value="{!! $branch['id'] !!}">{!! $branch['name'] !!}</option>
                        @endforeach
                    @endif
                   </select>
                  </div>


                  <div class="p-2" style="width:150px;">
                    <select class="selectpicker" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                       <option value="">Select User</option>
                      @if(@isset($users ))
                      @foreach($users as $user)
                       <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>

                  <div class="p-2" style="width:195px;">
                    <select class="selectpicker"  name="payroll" id="payroll" data-style="select-with-transition">
                     <!-- <option value="">Select Payroll</option> -->
                        @foreach($pay_rolls as $key=>$payroll)
                          <option value="{!! $key !!}">{!! $payroll !!}</option>
                       @endforeach
                   </select>
                  </div>



                  <div class="p-2" style="width:195px;">
                    <select class="selectpicker"  name="expenses_type" id="expenses_type" data-style="select-with-transition" >
                     <!-- <option value="">Select Expense Type</option>
                    @if(@isset($expense_types ))
                        @foreach($expense_types as $expense_type)
                          <option value="{!! $expense_type['id'] !!}">{!! $expense_type['name'] !!}</option>
                        @endforeach
                    @endif -->
                   </select>

                  </div>


                  <div class="p-2" style="width:160px;">
                    <select class="selectpicker"  name="status" id="status" data-style="select-with-transition" title="Select Status">
                     <option value="">Select Status</option>
                      <option value="3">Checked</option>
                      <option value="1">Approved</option>
                      <option value="2">Rejected</option>
                      <option value="0">Pending</option>
                   </select>
                  </div>


                  <div class="p-2" style="width:140px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:140px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.customers.title') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
              </form>
              @endif



                  @if(auth()->user()->can(['role_download']))
                  <a href="{{ URL::to('roles-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.role.title') !!}"><i class="material-icons">cloud_download</i></a>
                  @endif

                  @if(auth()->user()->can(['role_template']))
                  <a href="{{ URL::to('roles-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.role.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['expenses_create'])) 
                  <a href="{{ route('expenses.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.role.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
                </div>
              </span>
          </h4>
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
        @if(session('message_success'))
        <div class="alert alert-success">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span>
                <li>{{session('message_success')}}</li>
          </span>
        </div>
        @endif
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>

        <div class="table-responsive">
           <table id="getexpensestype" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.expenses.fields.expense_id') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>{!! trans('panel.expenses.fields.user') !!}</th>
              <th>{!! trans('panel.expenses.fields.expense_type') !!}</th>
              <th>{!! trans('panel.expenses.fields.date') !!}</th>
              <th>{!! trans('panel.expenses.fields.claim_amount') !!}</th>
              <th>{!! trans('panel.expenses.fields.expense_status') !!}</th>
              <th>{!! trans('panel.expenses.fields.note') !!}</th>
              <th>{!! trans('panel.expenses.fields.created_at') !!}</th>
              <th>{!! trans('panel.expenses.fields.branch') !!}</th>
              <th>{!! trans('panel.expenses.fields.total_km') !!}</th>
             
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function() {
    oTable = $('#getexpensestype').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [ [0, 'desc'] ],

    
         ajax: {
          url: "{{ route('expenses.index') }}",
          data: function (d) { 
                d.executive_id = $('#executive_id').val(),
                d.expenses_type = $('#expenses_type').val(),
                d.branch_id = $('#branch_id').val(),
                d.status = $('#status').val(),
                d.start_date = $('#start_date').val(),
                d.end_date = $('#end_date').val()
    
            }
        },

        columns: [
            // {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
            {data: 'id',name: 'id',orderable: false,searchable: false},
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'users.name',name: 'users.name',orderable: false,searchable: false},
            {data: 'expense_type.name',name: 'expense_type.name',orderable: false,searchable: false},
            {data: 'date',name: 'date',orderable: false,searchable: false},
            {data: 'claim_amount',name: 'claim_amount',orderable: false,searchable: false},
            {data: 'checker_status', name: 'checker_status', orderable: false, searchable: false },
            {data: 'note', name: 'note', orderable: false, searchable: false },
            {data: 'date_create', name: 'date_create', orderable: false, searchable: false },
            {data: 'users.getbranch.branch_name', name: 'users.getbranch.branch_name', orderable: false, searchable: false },
            {data: 'total_km', name: 'total_km', orderable: false, searchable: false },
           
        ]
    });

    $('#branch_id').change(function(){
        oTable.draw();
    });
    $('#status').change(function(){
        oTable.draw();
    });

    $('#executive_id').change(function(){
        oTable.draw();
    });

    $('#expenses_type').change(function(){
        oTable.draw();
    });
    $('#start_date').change(function(){
        oTable.draw();
    });
    $('#end_date').change(function(){
        oTable.draw();
    });


});


   

$('body').on('click', '.activeRecord', function () {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if(active == '1')
        {
          status = 'Incative ?';
        }
        else
        {
           status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want "+status)) {
           return false;
        }
        $.ajax({
          url: "{{ url('expenses-active') }}",
            type: 'POST',
            data: {_token: token,id: id,active:active},
            success: function (data) {
              $('.message').empty();
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
              oTable.draw();
            },
        });
    });

</script>


<script type="text/javascript">

    // for get expense type
 $('#payroll').change(function() {
    var payroll = $(this).val();
    
    $.post("{{ route('getexpenseType') }}",{
        'payroll':payroll,
        '_token':"{{ csrf_token() }}"
        },function(response){

          var select = $('#expenses_type');
          select.empty();
          select.append(response);
          select.selectpicker('refresh');              

    })
   
 }).trigger('change');
    
</script>


</x-app-layout>
