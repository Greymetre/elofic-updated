  <x-app-layout>
 <div class="row">
   <div class="col-md-12">
     <div class="card">
       <div class="card-header card-header-icon card-header-theme">
         <div class="card-icon">
           <i class="material-icons">perm_identity</i>
         </div>
         <h4 class="card-title ">{!! trans('panel.expenses.title_singular') !!} {!! trans('panel.global.list') !!}
               <span class="">
                 <div class="btn-group header-frm-btn">

         
               @if(auth()->user()->can(['expense_download']))
               <form method="GET" action="{{ URL::to('expenses-download') }}">


                <div class="d-flex flex-row">
                   <div class="p-2" style="width:195px;">
                     <select class="selectpicker"  name="payroll" id="payroll" data-style="select-with-transition">
                     
                         @foreach($pay_rolls as $key=>$payroll)
                           <option value="{!! $key !!}">{!! $payroll !!}</option>
                        @endforeach


                    </select>
                   </div>

                   <div class="p-2" style="width:195px;">
                     <select class="selectpicker"  name="expenses_type" id="expenses_type" data-style="select-with-transition">
                    </select>
                   </div>   

                   <div class="p-2" style="width:150px;">
                     <select class="selectpicker1 select2" name="expense_id" id="expense_id" data-style="select-with-transition" title="Select Expense">
                      <option value="">Select Expense Id</option>
                     @if(@isset($expense_ids ))
                         @foreach($expense_ids as $expense_id)
                           <option value="{!! $expense_id['id'] !!}">#{!! $expense_id['id'] !!}</option>
                         @endforeach
                     @endif
                    </select>
                   </div>


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
                     <select class="selectpicker" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division">
                      <option value="">Select Division</option>
                     @if(@isset($divisions ))
                         @foreach($divisions as $division)
                           <option value="{!! $division['id'] !!}">{!! $division['name'] !!}</option>
                         @endforeach
                     @endif
                    </select>
                   </div>
                   <div class="p-2" style="width:150px;">
                     <select class="select2" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                        <option value="">Select User</option>
                       @if(@isset($users ))
                       @foreach($users as $user)
                        <option value="{!! $user['id'] !!}"> ({!! $user['employee_codes']??'' !!}) {!! $user['name'] !!}</option>
                       @endforeach
                       @endif
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


                   <div class="p-2" style="width:140px;">
                     <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                   </div>
                     <div class="p-2" style="width:140px;">
                       <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                     </div>

                        <div class="p-2"><button type="button" class="btn btn-just-icon btn-theme" title="Reset Fliter" onclick="resetFilter();"><i class="fa fa-refresh" aria-hidden="true"></i></button></div>
                 
                     <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.customers.title') !!}"><i class="material-icons">cloud_download</i></button></div>

                     
                   </div> 
               </form>

            
               @endif

                <div class="next-btn">
                   @if(auth()->user()->can(['expenses_create'])) 
                   <a href="{{ route('expenses.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.expenses.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                   @endif
               </div>
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
               <th>{!! trans('panel.expenses.fields.date') !!}</th>
               <th>{!! trans('panel.expenses.fields.user') !!}</th>
               <th>{!! trans('panel.expenses.fields.designation') !!}</th>
               <th>{!! trans('panel.expenses.fields.expense_type') !!}</th>
               <th>{!! trans('panel.expenses.fields.claim_amount') !!}</th>
               <th>{!! trans('panel.expenses.fields.approve_amount') !!}</th>
               <th>{!! trans('panel.expenses.fields.expense_status') !!}</th>
               <th>{!! trans('panel.expenses.fields.note') !!}</th>
               <th>{!! trans('panel.expenses.fields.created_at') !!}</th>
               <th>{!! trans('panel.expenses.fields.branch') !!}</th>
               <th>{!! trans('panel.expenses.fields.total_km') !!}</th>
               <th>{!! trans('panel.global.action') !!}</th>
             </thead>
             <tbody>
             </tbody>
           </table>
         </div>
       </div>
     </div>
   </div>
 </div>

 <style type="text/css">
   
 .flex-row .p-2 {
     width: 20%!important;
 /*   overflow: hidden;*/
 }

 .flex-row {
     flex-direction: row !important;
     flex-wrap: wrap;
 }

 span.select2.select2-container.select2-container--default.select2-container--below.select2-container--focus {
 }

 span#select2-executive_id-container {
     color: #000;
     line-height: 43px;
 }

 span#select2-executive_id-container {
 }

 </style>
 <script>

 function resetFilter() {
    window.location.reload();
 }   
 function checkPageLoad() {
     if (performance.navigation.type === 1) {
         if (localStorage.getItem('payroll')) {
          localStorage.removeItem('payroll');
         }
         if (localStorage.getItem('executive_id')) {
         localStorage.removeItem('executive_id');
         }
         if (localStorage.getItem('expenses_type')) {
         localStorage.removeItem('expenses_type');
         }
         if (localStorage.getItem('branch_id')) {
         localStorage.removeItem('branch_id');
         }
         if (localStorage.getItem('division_id')) {
         localStorage.removeItem('division_id');
         }
         if (localStorage.getItem('expense_id')) {
         localStorage.removeItem('expense_id');
         }
         if (localStorage.getItem('status')) {
         localStorage.removeItem('status');
         }
         if (localStorage.getItem('start_date')) {
         localStorage.removeItem('start_date');
         }
         if (localStorage.getItem('end_date')) {
         localStorage.removeItem('end_date');
         }
     } else {
     }
 }
 document.addEventListener('DOMContentLoaded', function() {
   checkPageLoad();
 });
 </script>

 <script type="text/javascript">
 $(document).ready(function() {
    $('.selectpicker').selectpicker();
     oTable = $('#getexpensestype').DataTable({
         "processing": true,
         "serverSide": true,
         "order": [ [0, 'desc'] ],
         "stateSave": true,
         "bStateSave": true,

          ajax: {
           url: "{{ route('expenses.index') }}",
           data: function (d) { 
                 d.payroll = $('#payroll').val(),
                 d.executive_id = $('#executive_id').val(),
                 d.expenses_type = $('#expenses_type').val(),
                 d.branch_id = $('#branch_id').val(),
                 d.division_id = $('#division_id').val(),
                 d.expense_id = $('#expense_id').val(),
                 d.status = $('#status').val(),
                 d.start_date = $('#start_date').val(),
                 d.end_date = $('#end_date').val()
     
             }
         },

         columns: [
             // {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
             {data: 'id',name: 'id',orderable: false,searchable: false},
             {data: 'date',name: 'date',orderable: false,searchable: false},
             {data: 'users.name',name: 'users.name',orderable: false,searchable: false},
             {data: 'users.getdesignation.designation_name', name: 'users.getdesignation.designation_name', orderable: false, searchable: false },
             {data: 'expense_type.name',name: 'expense_type.name',orderable: false,searchable: false},
             {data: 'claim_amount',name: 'claim_amount',orderable: false,searchable: false},
             {data: 'approve_amount',name: 'approve_amount',orderable: false,searchable: false},
             {data: 'checker_status', name: 'checker_status', orderable: false, searchable: false },
             {data: 'note', name: 'note', orderable: false, searchable: false },
             {data: 'date_create', name: 'date_create', orderable: false, searchable: false },
             {data: 'users.getbranch.branch_name', name: 'users.getbranch.branch_name', orderable: false, searchable: false },
             {data: 'total_km', name: 'total_km', orderable: false, searchable: false },
             {data: 'action', name: 'action',"defaultContent": '', orderable: false, searchable: false},
         ]
     });
    
     $('#payroll').change(function(){
         localStorage.setItem("payroll", $(this).val());
         oTable.draw();
     });
     $('#branch_id').change(function(){
         localStorage.setItem("branch_id", $(this).val());
         oTable.draw();
     });
     $('#status').change(function(){
         localStorage.setItem("status", $(this).val());
         oTable.draw();
     });
     $('#executive_id').change(function(){
         localStorage.setItem("executive_id", $(this).val());
         oTable.draw();
     });

     $('#expenses_type').change(function(){
         localStorage.setItem("expenses_type", $(this).val());
         oTable.draw();
     });
     $('#start_date').change(function(){
         localStorage.setItem("start_date", $(this).val());
         oTable.draw();
     });
     $('#end_date').change(function(){
         localStorage.setItem("end_date", $(this).val());
         oTable.draw();
     });
     $('#division_id').change(function(){
         localStorage.setItem("division_id", $(this).val());
         oTable.draw();
     });
     $('#expense_id').change(function(){
         localStorage.setItem("expense_id", $(this).val());
         oTable.draw();
     });

 });


    $(document).ready(function() {
        
        var payroll = localStorage.getItem('payroll');
        var executive_id = localStorage.getItem('executive_id');
        var expenses_type = localStorage.getItem('expenses_type');
        var branch_id = localStorage.getItem('branch_id');
        var division_id = localStorage.getItem('division_id');
        var expense_id = localStorage.getItem('expense_id');
        var status = localStorage.getItem('status');
        var start_date = localStorage.getItem('start_date');
        var end_date = localStorage.getItem('end_date');

        if (payroll) {
            $('#payroll').val(payroll).trigger('change'); 
        }
        if (executive_id) {
             $('#executive_id').val(executive_id).trigger('change'); 
         }
        if (expenses_type) {
        // $('#expenses_type').val(expenses_type).trigger('change');
        $.post("{{ route('getexpenseType') }}", {
            'payroll': $('#payroll').val(),
            '_token': "{{ csrf_token() }}"
        }, function(response) {
            var select = $('#expenses_type');
                select.empty();
                select.append(response);
                select.val(expenses_type);
                select.selectpicker('refresh');
                oTable.draw();
            });
        }
         if (branch_id) {
             $('#branch_id').val(branch_id).trigger('change');
         }
         if (division_id) {
             $('#division_id').val(division_id).trigger('change');
         }
         if (expense_id) {
             $('#expense_id').val(expense_id).trigger('change');
         }
         if (status) {
             $('#status').val(status);
             $('#status').selectpicker('refresh');
         }
         if (start_date) {
             $('#start_date').val(start_date).trigger('change');
         }
         if (end_date) {
             $('#end_date').val(end_date).trigger('change');
         }
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



     $('body').on('click', '.delete', function () {
         var id = $(this).attr("value");
         var token = $("meta[name='csrf-token']").attr("content");
         if(!confirm("Are You sure want to delete ?")) {
            return false;
         }
         $.ajax({
             url: "{{ url('expenses') }}"+'/'+id,
             type: 'DELETE',
             data: {_token: token,id: id},
             success: function (data) {
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
