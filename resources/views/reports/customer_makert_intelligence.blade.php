<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Market Intelligence
            <span class="">
              <div class="btn-group header-frm-btn">
                <form method="GET" action="{{ URL::to('customers-download') }}">
                  <div class="d-flex flex-wrap flex-row">

                    <div class="p-2" style="width:200px;">
                      <select class="selectpicker" name="division_id" id="division_id" data-style="select-with-transition" title="Select Division">
                        @if(@isset($divisions ))
                        @foreach($divisions as $division)
                        <option value="{!! $division['id'] !!}" {{ old( 'division_id') == $division->id || 10 == $division->id ? 'selected' : '' }}>{!! $division['division_name'] !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                  </div>
                </form>
                <div class="next-btn">
                  <!--   @if(auth()->user()->can('market_intelligence_create'))
                  <a href="{{ route('market_intelligences.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Field"><i class="material-icons">add_circle</i></a>

                  @endif -->
                  @if(auth()->user()->can('market_intelligence_report_download'))

                  <a href="{{ route('market_intelligences.download') }}" class="btn btn-just-icon btn-theme" title="Download Market Intelligence Report"><i class="material-icons">download</i></a>
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
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getPumpSR" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class="text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>State</th>
                @foreach($keys as $key)
                <th>{{ $key->field_name }}</th>
                @endforeach
                <!-- <th>{!! trans('panel.global.created_at') !!}</th>
                  <th>{!! trans('panel.global.action') !!}</th> -->
              </thead>

              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getPumpSR').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: "{{ route('reports.marketIntelligence') }}",
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'created_at',
            name: 'created_at',
            orderable: false,
          },
          {
            data: 'createdbyname.name',
            name: 'createdbyname.name',
            orderable: false,
          },
          {
            data: 'createdbyname.employee_codes',
            name: 'createdbyname.employee_codes',
            orderable: false,
          },
          {
            data: 'state.state_name',
            name: 'state.state_name',
            orderable: false,
          },
          {
            data: 'division_id',
            name: 'division_id',
            orderable: false,
          },
          {
            data: 'category_id',
            name: 'category_id',
            "defaultContent": ''
          },
          {
            data: 'brand_id',
            name: 'brand_id',
            "defaultContent": ''
          },
          {
            data: 'product_name',
            name: 'product_name',
            "defaultContent": ''
          },
          {
            data: 'cooling_arrangement_id',
            name: 'cooling_arrangement_id',
            "defaultContent": ''
          },
          {
            data: 'type_of_construction_id',
            name: 'type_of_construction_id',
            "defaultContent": ''
          }
        ]
      });


      $('body').on('click', '.is_active', function() {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if (active == 'Y') {
          status = 'Incative ?';
        } else {
          status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want " + status)) {
          return false;
        }
        $.ajax({
          url: "{{ url('fields-active') }}",
          type: 'POST',
          data: {
            _token: token,
            id: id,
            active: active
          },
          success: function(data) {
            $('.message').empty();
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            table.draw();
          },
        });
      });


      $('body').on('click', '.delete', function() {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if (!confirm("Are You sure want to delete ?")) {
          return false;
        }
        $.ajax({
          url: "{{ url('market_intelligences') }}" + '/' + id,
          type: 'DELETE',
          data: {
            _token: token,
            id: id
          },
          success: function(data) {
            $('.alert').show();
            if (data.status == 'success') {
              $('.alert').addClass("alert-success");
            } else {
              $('.alert').addClass("alert-danger");
            }
            $('.message').append(data.message);
            table.draw();
          },
        });
      });

    });
  </script>
</x-app-layout>