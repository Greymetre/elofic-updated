<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Leads
            <span class="">
              <div class="">


                <button type="button" data-toggle="modal" data-target="#addLeadModel" class="btn btn-primary btn-sm btn-icon-split float-right">
                    <span class="icon text-white-50">
                      <i class="material-icons">add_circle</i>
                    </span>
                    <span class="text">Add Leads</span>
                </button>
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
          @if(session()->has('message_success'))
          <div class="alert alert-success">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span>
              {!!session()->get('message_success') !!}
            </span>
          </div>
          @endif
          <!--  -->
          <div class="well">
                {!! Form::open(['method' => 'POST', 'class' => 'form-inline', 'id' => 'frmFilter']) !!}
               
                <div class="form-group mr-sm-2 mb-2 col-md-3">    
                    {!! Form::text('datetime', old('datetime'), ['class' => 'form-control','placeholder'=> __('MM/DD/YYYY - MM/DD/YYYY'), 'autocomplete' => 'off', 'style' => 'width : 100%;']) !!}             
                </div>   

                <button type="submit" class="btn btn-responsive btn-primary mr-sm-2 mb-2">{{ __('Filter') }}</button>
                <a href="javascript:;" onclick="resetFilter();" class="btn btn-responsive btn-danger mb-2">{{ __('Reset') }}</a>
                {!! Form::close() !!}
            </div>
          <!--  -->
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getLeads" class="table table-striped- table-bschemeed table-hover table-checkable  no-wrap">
              <thead class=" text-primary">
                <tr>
                  <th><input type="checkbox" id="checkAll"></th>
                  <th>Company Name</th>
                  <th>Contact</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Lead Status</th>
                  <th>Created Date</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--  -->
  <div class="modal fade" id="addLeadModel" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">New Lead</h4>
        </div>
        
          <form method="POST" 
          action="{{ route('leads.store') }}" class="form-horizontal" id="frmLeadsCreate" enctype="multipart/form-data">
            @csrf
          <div class="modal-body">
          <div class="row">
            <div class="col-md-6 pr-1 pl-1">
              <div class="col-md-12 form-group">
                  <label for="company_name">Company Name <span style="color:red">*</span></label>
                  <input type="text"  name="company_name" id="company_name" value="{{ old('company_name','') }}"  class="form-control" placeholder="Company Name">
                  @if($errors->has('company_name'))
                  <p class="help-block">
                      <strong>{{ $errors->first('company_name') }}</strong>
                  </p>
                  @endif
              </div>
            </div>
            <div class="col-md-6 pr-1 pl-1">
              <div class="col-md-12 form-group">
                  <label for="contact_name">Contact Name <span style="color:red">*</span></label>
                  <input type="text"  name="contact_name" id="contact_name" value="{{ old('contact_name','') }}"  class="form-control" placeholder="Contact Name">
                  @if($errors->has('contact_name'))
                  <p class="help-block">
                      <strong>{{ $errors->first('contact_name') }}</strong>
                  </p>
                  @endif
              </div>     
            </div>
            <div class="col-md-12" id="lead_exist_data">
            </div>
          </div>
        
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger mr-2" data-dismiss="modal">Canel</button>
          <button type="submit" class="btn btn-default">Create Lead</button>
        </div>
      </div>
      </form>
    </div>
  </div>
  <!--  -->
  <link href="{{ url('/').'/'.asset('vendor/bootstrap-daterange/daterangepicker.css') }}" rel="stylesheet">
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <!-- Load jQuery and moment.js -->
  <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

  <script src="{{ url('/').'/'.asset('vendor/bootstrap-daterange/daterangepicker.min.js') }}"></script>

<script>


jQuery(document).ready(function(){
    getLeads();

    jQuery('#frmFilter').submit(function(){
        getLeads();
        return false;
    });

    jQuery('#frmLeadsCreate').validate({
        rules: {
            company_name: {
                required: true
            },
            contact_name: {
                required: true
            },             
        }

    });


    $(document).on('change', '#checkAll', function () {
      console.log('checkAll',this.checked);
        $('.lead-checkbox').prop('checked', this.checked);
    });


    jQuery('#frmFilter [name="datetime"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    }).val("{{ old('datetime') }}"); 
    jQuery('#frmFilter [name="datetime"]').on('apply.daterangepicker', function(ev, picker) {
        jQuery(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
    }).on('cancel.daterangepicker', function(ev, picker) {
        jQuery(this).val('');
    });


});

 

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$('#company_name, #contact_name').on('keyup', function () {
    var company_name = $('#company_name').val();
    var contact_name = $('#contact_name').val();
  $.post("{{route('leads.searchExistsLead')}}", {company_name:company_name,contact_name:contact_name}, function(response){
      $('#lead_exist_data').html(response);
  });
});
 


function getLeads(){
    jQuery('#getLeads').dataTable().fnDestroy();
    jQuery('#getLeads tbody').empty();
    var datetime = jQuery('#frmFilter [name=datetime]').val();
    jQuery('#getLeads').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: '{{ route('leads.getLeads') }}',
            method: 'POST',
            data: {
                datetime: datetime,
            }
        },
        columns: [
            {data: 'checkbox', name: 'checkbox',orderable:false,searchable:false},
            {data: 'company_name', name: 'company_name'},
            {data: 'contacts', name: 'contacts',orderable:false,searchable:false},
            {data: 'phone', name: 'phone',orderable:false,searchable:false},
            {data: 'email', name: 'email',orderable:false,searchable:false},
            {data: 'status', name: 'status'},
            {data: 'created_at', name: 'created_at'},
        ],
        order: [[1, 'desc']]
    });
}

function resetFilter(){
    jQuery('#frmFilter :input:not(:button, [type="hidden"])').val('');
    getLeads();
}
</script>

</x-app-layout>