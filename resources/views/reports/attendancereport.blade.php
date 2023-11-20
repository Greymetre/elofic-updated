<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title">Attendance Report
          <span class="pull-right">
            <div class="btn-group">
              @if(auth()->user()->can(['attendance_download']))
              <form method="GET" action="{{ URL::to('attendance-download') }}">
                  <div class="d-flex flex-row">
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Attendance">
                        <i class="material-icons">cloud_download</i>
                      </button>
                    </div>
                  </div>
              </form>
              @endif
              @if(auth()->user()->can(['attendance_create']))
              <a data-toggle="modal" data-target="#submitAttendance" class="btn btn-just-icon btn-theme create" title="Submit Attendance">
                <i class="material-icons">add_circle</i>
              </a>
               @endif
               <a href="{{ URL::to('attendance-location') }}" class="btn btn-just-icon btn-theme" title="Update Location"><i class="material-icons">add_location</i></a>
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
        <div class="table-responsive">
          <table id="getattendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>No</th>
              <th>User ID</th>
              <th>User Name</th>
              <th>Punch in Date</th>
              <th>Punch In Time</th>
              <!-- <th>Punch  In Address</th> -->
              <th>Punch Image</th>
              <th>Punch Out Date</th>
              <th>Punch Out Time</th>
              <!-- <th>Punch  Out Address</th> -->
              <th>Working Time</th>
              <!-- <th>Status</th> -->
              <th>Punch In Longitude</th>
              <th>Punch In Letitude</th>
              <th>Punch Out Longitude</th>
              <th>Punch Out Letitude</th>
              <th>Punch In summary</th>
              <th>Punch Out summary</th>
              <th>Working Type</th>
              <th>Action</th>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="submitAttendance" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title">
          <span class="modal-title">Submit </span> Attendance <span class="pull-right">
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal">
              <i class="material-icons">clear</i>
            </a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('submitAttendances') }}" enctype="multipart/form-data" id="createleadstagesForm"> @csrf 
          <div class="row">
            <div class="col-md-6">
              <div class="input-group input-group-outline my-3">
                <label class="form-label">User</label>
                <select class="form-control select2" name="user_id" id="user_id" style="width: 100%;" required>
                  <option value="">Select User</option>
                  @if(@isset($users))
                    @foreach($users as $user)
                      <option value="{!! $user['id'] !!}">{!! $user['name'] !!}</option>
                    @endforeach
                  @endif
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline my-3">
                <label class="form-label">Working Type</label>
                <select class="form-control select2" name="working_type" id="working_type" style="width: 100%;" required>
                  <option value="">Select Working Type</option>
                  <option value="Tour">Tour</option>
                  <option value="Office Work">Office Work</option>
                  <option value="Suburban">Suburban</option>
                  <option value="Central Market">Central Market</option>
                  <option value="Holiday">Holiday</option>
                  <option value="Leave">Leave</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline my-3">
                <label class="form-label">Punchin</label>
                <input type="text" name="punchin_date" id="punchin_date" class="form-control datetimepicker" value="{!! old( 'punchin_date') !!}" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-outline my-3">
                <label class="form-label">Punch Out</label>
                <input type="text" name="punchout_date" id="punchout_date" class="form-control datetimepicker" value="{!! old( 'punchout_date') !!}">
              </div>
            </div>
          </div>
          <button class="btn btn-info save"> Submit</button>
        </form>
      </div>
    </div>
  </div>
</div> 

<script type="text/javascript">
  $(document).ready(function() {
    var token = $("meta[name='csrf-token']").attr("content");
    oTable = $('#getattendance').DataTable({
      "processing": true,
      "serverSide": true,
      "order": [
        [0, 'desc']
      ],
      //"dom": 'Bfrtip',
      //"ajax": "{{ url('reports/attendancereport') }}",
      "ajax": {
          'type': 'POST',
          'url': "{{ url('reports/attendancereport') }}",
          data: {
            "_token": token,
          }
        },
      "columns": [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false
        }, {
          data: 'user_id',
          name: 'user_id',
          "defaultContent": ''
        }, {
          data: 'users.name',
          name: 'users.name',
          "defaultContent": ''
        }, {
          data: 'punchin_date',
          name: 'punchin_date',
          "defaultContent": ''
        }, {
          data: 'punchin_time',
          name: 'punchin_time',
          "defaultContent": ''
        },
        // {data: 'punchin_address', name: 'punchin_address',"defaultContent": ''},
        {
          data: 'punchin',
          name: 'punchin',
          "defaultContent": ''
        }, {
          data: 'punchout_date',
          name: 'punchout_date',
          "defaultContent": ''
        }, {
          data: 'punchout_time',
          name: 'punchout_time',
          "defaultContent": ''
        },
        // {data: 'punchout_address', name: 'punchout_address',"defaultContent": ''},
        {
          data: 'worked_time',
          name: 'worked_time',
          "defaultContent": ''
        },
        // {data: 'district_name', name: 'district_name',"defaultContent": ''},
        {
          data: 'punchin_longitude',
          name: 'punchin_longitude',
          "defaultContent": ''
        }, {
          data: 'punchin_latitude',
          name: 'punchin_latitude',
          "defaultContent": ''
        }, {
          data: 'punchout_longitude',
          name: 'punchout_longitude',
          "defaultContent": ''
        }, {
          data: 'punchout_latitude',
          name: 'punchout_latitude',
          "defaultContent": ''
        }, {
          data: 'punchin_summary',
          name: 'punchin_summary',
          "defaultContent": ''
        }, {
          data: 'punchout_summary',
          name: 'punchout_summary',
          "defaultContent": ''
        },
        {
          data: 'working_type',
          name: 'working_type',
          "defaultContent": ''
        },
        { data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
      ]
    });

    $('body').on('click', '.removePunchout', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want Punchout")) {
           return false;
        }
        $.ajax({
            url: "{{ url('removePunchout') }}",
            type: 'POST',
            data: {_token: token,id: id},
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
    
    $('body').on('click', '.deleteAttendance', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('attendances') }}"+'/'+id,
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
  });
</script> 
</x-app-layout>