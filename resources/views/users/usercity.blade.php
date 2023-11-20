<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">User City List
              <span class="pull-right">
            <div class="btn-group">
              <form action="{{ URL::to('admin/usercity-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
              {{ csrf_field() }}
                <div class="input-group">
                  <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                    <span class="btn btn-just-icon btn-theme btn-file">
                      <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                      <span class="fileinput-exists">Change</span>
                      <input type="hidden">
                      <input type="file" name="import_file" required accept=".xls,.xlsx" />
                    </span>
                  </div>
                <div class="input-group-append">
                  <button class="btn btn-just-icon btn-theme" title="User City Upload">
                    <i class="material-icons">cloud_upload</i>
                    <div class="ripple-container"></div>
                  </button>
                </div>
              </div>
            </form>
            <a href="{{ URL::to('admin/usercity-download') }}" class="btn btn-just-icon btn-theme" title="User City Download"><i class="material-icons">cloud_download</i></a>
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
        <div class="alert " style="display: none;">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <i class="material-icons">close</i>
          </button>
          <span class="message"></span>
        </div>
        <div class="table-responsive">
          <table id="getaward" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>User Name</th>
              <th>Reporting Name</th>
              <th>City Name</th>
              <th>Grade</th>
              <th>District Name</th>
              <th>State Name</th>
            </thead>
            <tbody>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset('public/assets/js/jquery.hrms.js') }}"></script>
<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#getaward').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('users.usercity') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
            {data: 'userinfo.name', name: 'userinfo.name',"defaultContent": ''},
            {data: 'reportinginfo.name', name: 'reportinginfo.name',"defaultContent": ''},
            {data: 'cityname.city_name', name: 'cityname.city_name',"defaultContent": ''},
            {data: 'cityname.grade', name: 'cityname.grade',"defaultContent": ''},
            {data: 'cityname.districtname.district_name', name: 'cityname.districtname.district_name',"defaultContent": ''},
            {data: 'cityname.districtname.statename.state_name', name: 'cityname.districtname.statename.state_name',"defaultContent": ''},
        ]
    }); 
});
</script>
</x-app-layout>
