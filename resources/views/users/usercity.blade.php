<x-app-layout>
  <style>
    .user-city-page .user-city-head { display:flex; align-items:center; justify-content:space-between; margin:0 0 18px; }
    .user-city-page .user-city-breadcrumb { color:#8490a8; font-size:12px; font-weight:600; letter-spacing:1px; margin-bottom:5px; }
    .user-city-page .user-city-title-row { display:flex; align-items:center; gap:12px; }
    .user-city-page .user-city-title { color:#303b50; font-size:28px; font-weight:700; margin:0; }
    .user-city-page .user-city-count { background:#e8f5ff; border:1px solid #3498db; border-radius:18px; color:#2583c5; font-size:13px; font-weight:600; padding:5px 12px; }
    .user-city-page .user-city-filter-trigger { align-items:center; border:1px solid #3498db; border-radius:8px; box-shadow:none; display:flex; gap:8px; padding:10px 20px; text-transform:none; }
    .user-city-page .user-city-card { margin-top:0; }
    .user-city-drawer-backdrop { background:rgba(16, 30, 54, .42); inset:0; opacity:0; pointer-events:none; position:fixed; transition:opacity .2s ease; z-index:1040; }
    .user-city-drawer-backdrop.is-open { opacity:1; pointer-events:auto; }
    .user-city-filter-drawer { background:#fff; box-shadow:-12px 0 35px rgba(29,55,91,.2); display:flex; flex-direction:column; height:100vh; max-width:560px; position:fixed; right:0; top:0; transform:translateX(105%); transition:transform .25s ease; width:92vw; z-index:1050; }
    .user-city-filter-drawer.is-open { transform:translateX(0); }
    .user-city-filter-head { align-items:center; border-bottom:1px solid #e5e9f0; display:flex; gap:15px; padding:24px; }
    .user-city-filter-icon { align-items:center; background:#e8f5ff; border-radius:10px; color:#2583c5; display:flex; height:48px; justify-content:center; width:48px; }
    .user-city-filter-head h3 { color:#303b50; font-size:22px; font-weight:700; margin:0 0 4px; }
    .user-city-filter-head p { color:#7b879d; margin:0; }
    .user-city-filter-close { background:#f5f7fa; border:0; border-radius:8px; color:#66738a; margin-left:auto; padding:9px; }
    .user-city-filter-body { flex:1; overflow:auto; padding:25px; }
    .user-city-filter-grid { display:grid; gap:20px; grid-template-columns:1fr 1fr; }
    .user-city-filter-field label { color:#637089; display:block; font-size:12px; font-weight:700; letter-spacing:1px; margin-bottom:8px; text-transform:uppercase; }
    .user-city-filter-field .form-control, .user-city-filter-field .select2-container { width:100% !important; }
    .user-city-filter-tools { border-top:1px solid #e5e9f0; display:flex; gap:12px; padding:20px 25px; }
    .user-city-filter-tools form { flex:1; margin:0; }
    .user-city-filter-tools .btn { align-items:center; border:1px solid #d7deea; box-shadow:none; display:flex; justify-content:center; margin:0; text-transform:none; width:100%; }
    .user-city-filter-tools input[type=file] { display:none; }
    .user-city-filter-foot { border-top:1px solid #e5e9f0; display:flex; gap:12px; padding:20px 25px; }
    .user-city-filter-foot .btn { box-shadow:none; margin:0; text-transform:none; }
    .user-city-filter-reset { border:1px solid #d7deea; flex:0 0 120px; }
    .user-city-filter-apply { background:#258bc8; color:#fff; flex:1; }
    body.user-city-drawer-open { overflow:hidden; }
    @media (max-width:767px) { .user-city-page .user-city-title { font-size:22px; } .user-city-filter-grid { grid-template-columns:1fr; } }
  </style>
  <div class="row user-city-page">
    <div class="col-md-12">
      <div class="user-city-head">
        <div>
          <div class="user-city-breadcrumb">USER MANAGEMENT &nbsp;&rsaquo;&nbsp; USER CITY LIST</div>
          <div class="user-city-title-row">
            <h1 class="user-city-title">User City List</h1>
            <span class="user-city-count" id="user-city-record-count">0 records</span>
          </div>
        </div>
        <button class="btn btn-theme user-city-filter-trigger" type="button" id="open-user-city-filters">
          <i class="material-icons">tune</i><span>Filters</span>
        </button>
      </div>
      <div class="user-city-drawer-backdrop" id="user-city-drawer-backdrop"></div>
      <aside class="user-city-filter-drawer" id="user-city-filter-drawer" aria-hidden="true">
        <div class="user-city-filter-head">
          <div class="user-city-filter-icon"><i class="material-icons">tune</i></div>
          <div><h3>Advanced Filters</h3><p>Filter the city assignments and export</p></div>
          <button type="button" class="user-city-filter-close" aria-label="Close filters"><i class="material-icons">close</i></button>
        </div>
        <div class="user-city-filter-body">
          <form method="GET" action="{{ URL::to('/usercity-download') }}" id="user-city-export-form">
            <div class="user-city-filter-grid">
              <div class="user-city-filter-field"><label for="filter_user">User</label><select name="user_id" id="filter_user" class="form-control select2"><option value="">All Users</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
              <div class="user-city-filter-field"><label for="filter_state">State</label><select name="state_id" id="filter_state" class="form-control select2"><option value="">All States</option>@foreach($states as $state)<option value="{{ $state->id }}">{{ $state->state_name }}</option>@endforeach</select></div>
              <div class="user-city-filter-field"><label for="tableInfo">Page Number</label><input type="number" class="form-control" name="page_number" id="tableInfo" value="1" min="1"></div>
              <div class="user-city-filter-field"><label for="page_length">Records</label><input type="number" class="form-control" name="page_length" id="page_length" value="100" min="1" max="5000" required></div>
            </div>
          </form>
        </div>
        <div class="user-city-filter-tools">
          @if(auth()->user()->can(['user_upload']))
          <form action="{{ URL::to('/usercity-upload') }}" method="post" enctype="multipart/form-data">{{ csrf_field() }}<label class="btn"><i class="material-icons">cloud_upload</i>&nbsp; Import<input type="file" name="import_file" required accept=".xls,.xlsx" onchange="this.form.submit()"></label></form>
          @endif
          @if(auth()->user()->can(['user_download']))
          <button class="btn" type="submit" form="user-city-export-form"><i class="material-icons">cloud_download</i>&nbsp; Export</button>
          @endif
        </div>
        <div class="user-city-filter-foot"><button class="btn user-city-filter-reset" type="button" id="reset-user-city-filters">Reset</button><button class="btn user-city-filter-apply" type="button" id="apply-user-city-filters">Apply Filters</button></div>
      </aside>
      <div class="card user-city-card">
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
                <th>User Designation</th>
                <th>Reporting Name</th>
                <th>Reporting Designation</th>
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
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getaward').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          url: "{{ route('users.usercity') }}",
          data: function(d) {
            d.user_id = $('#filter_user').val();
            d.state_id = $('#filter_state').val();
          }
        },
        lengthMenu: [
          [10, 25, 50, 100, 500, 1000, 5000],
          [10, 25, 50, 100, 500, 1000, 5000]
        ],
        pageLength: 100,

        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          },
          {
            data: 'userinfo.name',
            name: 'userinfo.name',
            "defaultContent": ''
          },
          {
            data: 'userinfo.getdesignation.designation_name',
            name: 'userinfo.getdesignation.designation_name',
            "defaultContent": ''
          },
          {
            data: 'reportinginfo.name',
            name: 'reportinginfo.name',
            "defaultContent": ''
          },
          {
            data: 'reportinginfo.getdesignation.designation_name',
            name: 'reportinginfo.getdesignation.designation_name',
            "defaultContent": ''
          },
          {
            data: 'cityname.city_name',
            name: 'cityname.city_name',
            "defaultContent": ''
          },
          {
            data: 'cityname.grade',
            name: 'cityname.grade',
            "defaultContent": ''
          },
          {
            data: 'cityname.districtname.district_name',
            name: 'cityname.districtname.district_name',
            "defaultContent": ''
          },
          {
            data: 'cityname.districtname.statename.state_name',
            name: 'cityname.districtname.statename.state_name',
            "defaultContent": ''
          },
        ]
      });

      function setUserCityDrawer(open) {
        $('#user-city-filter-drawer').toggleClass('is-open', open).attr('aria-hidden', open ? 'false' : 'true');
        $('#user-city-drawer-backdrop').toggleClass('is-open', open);
        $('body').toggleClass('user-city-drawer-open', open);
      }

      $('#open-user-city-filters').on('click', function() { setUserCityDrawer(true); });
      $('.user-city-filter-close, #user-city-drawer-backdrop').on('click', function() { setUserCityDrawer(false); });
      $(document).on('keydown.userCityDrawer', function(event) {
        if (event.key === 'Escape') setUserCityDrawer(false);
      });

      table.on('draw.dt', function() {
        var info = table.page.info();
        $('#user-city-record-count').text(info.recordsDisplay + ' records');
      });

      $('#apply-user-city-filters').on('click', function() {
        $('#tableInfo').val(1);
        table.page(0).draw('page');
        setUserCityDrawer(false);
      });

      $('#reset-user-city-filters').on('click', function() {
        $('#filter_user, #filter_state').val('').trigger('change');
        $('#tableInfo').val(1);
        table.page(0).draw('page');
        setUserCityDrawer(false);
      });
    });



    $(document).ready(function() {

      var table = $('#getaward').DataTable();
      var info = table.page.info();
      $('#getaward').on('page.dt', function() {
        var info = table.page.info();
        //$('#tableInfo').html(info.page+1);
        $('#tableInfo').val(info.page + 1);
      });

      table.on('length', function(e, settings, len) {
        table.ajax.reload(null, false); // user paging is not reset on reload
        $('#page_length').val(len);
      });
    });
  </script>
</x-app-layout>
