<x-app-layout>
  <style>
    /* =======================================
   CUSTOM TAB STYLES — FieldKonnect Theme
   Works with Material Dashboard + Bootstrap 4
   ======================================= */

    /* Tabs container */
    .nav-tabs {
      border-bottom: 2px solid #e0e0e0;
      margin-bottom: 15px;
    }

    /* Default tab style */
    .nav-tabs .nav-link {
      color: #555 !important;
      font-weight: 500;
      border: none !important;
      border-radius: 0 !important;
      background: #f5f5f5;
      padding: 10px 20px;
      transition: all 0.3s ease;
      box-shadow: none;
    }

    /* Hover effect for tabs */
    .nav-tabs .nav-link:hover,
    .nav-tabs .nav-item .nav-link,
    .nav-tabs .nav-item .nav-link:hover {
      background: #e9ecef;
      color: #0256c4 !important;
      /* FieldKonnect Blue */
    }

    /* Active tab */
    .nav-tabs .nav-link.active {
      color: #fff !important;
      background: linear-gradient(60deg, #0256c4, #007bff) !important;
      border: none !important;
      border-radius: 5px 5px 0 0 !important;
      box-shadow: 0 -2px 10px rgba(2, 86, 196, 0.25);
    }

    /* Inactive tabs */
    .nav-tabs .nav-link:not(.active) {
      background-color: #f1f1f1;
      color: #6c757d !important;
    }

    /* Tab content box */
    .tab-content {
      background: #fff;
      border: 1px solid #e0e0e0;
      border-radius: 0 5px 5px 5px;
      padding: 20px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    /* Optional: Font Awesome icon alignment if used inside tab */
    .nav-tabs .nav-link i {
      margin-right: 6px;
      font-size: 14px;
    }

    /* Responsive tweak */
    @media (max-width: 767.98px) {
      .nav-tabs .nav-link {
        padding: 8px 15px;
        font-size: 14px;
      }
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Active Customer Process
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  @if(auth()->user()->can('active_process_create'))
                  <a href="{{ route('active_customer_process.create') }}" class="btn btn-just-icon btn-theme" title="Assign Process"><i class="material-icons">add_circle</i></a>
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
          <ul class="nav nav-tabs" id="processTabs" role="tablist">
            <li class="nav-item mr-2">
              <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab">
                <i class="fa fa-check-circle"></i> Active
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="closed-tab" data-toggle="tab" href="#closed" role="tab">
                <i class="fa fa-times-circle"></i> Closed
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade show active" id="active" role="tabpanel">
              <div class="table-responsive">
                <table id="getActiveProcess" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                  <thead class=" text-primary">
                    <th>{!! trans('panel.global.no') !!}</th>
                    <th>{!! trans('panel.global.action') !!}</th>
                    <th> Customer Name</th>
                    <th> Customer Number</th>
                    <th>Customer Created Date</th>
                    <th> Process</th>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="tab-pane fade" id="closed" role="tabpanel">
              <div class="table-responsive">
                <table id="getClosedProcess" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                  <thead class=" text-primary">
                    <th>{!! trans('panel.global.no') !!}</th>
                    <th>{!! trans('panel.global.action') !!}</th>
                    <th> Customer Name</th>
                    <th> Customer Number</th>
                    <th>Customer Created Date</th>
                    <th> Process</th>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
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
        var table2 = $('#getClosedProcess').DataTable({
          processing: true,
          serverSide: true,
          "order": [
            [0, 'desc']
          ],
          ajax: {
            url: "{{ route('active_customer_process.index') }}",
            data: function(d) {
              d.user_id = $('#executive_id').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val(),
              d.status = 'closed'
            }
          },
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
              data: 'customer.name',
              name: 'customer.name',
              "defaultContent": ''
            },
            {
              data: 'customer.mobile',
              name: 'customer.mobile',
              "defaultContent": ''
            },
            {
              data: 'customer.creation_date',
              name: 'customer.creation_date',
              "defaultContent": ''
            },
            {
              data: 'process.process_name',
              name: 'process.process_name',
              "defaultContent": ''
            },
          ]
        });

        $('body').on('click', '.delete', function() {
          var id = $(this).attr("value");
          var token = $("meta[name='csrf-token']").attr("content");
          if (!confirm("Are You sure want to delete ?")) {
            return false;
          }
          $.ajax({
            url: "{{ url('customer-custom-fields') }}" + '/' + id,
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
              table2.draw();
            },
          });
        });

      });
      $(function() {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        var table = $('#getActiveProcess').DataTable({
          processing: true,
          serverSide: true,
          "order": [
            [0, 'desc']
          ],
          ajax: {
            url: "{{ route('active_customer_process.index') }}",
            data: function(d) {
              d.user_id = $('#executive_id').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val(),
              d.status = 'active'
            }
          },
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
              data: 'customer.name',
              name: 'customer.name',
              "defaultContent": ''
            },
            {
              data: 'customer.mobile',
              name: 'customer.mobile',
              "defaultContent": ''
            },
            {
              data: 'customer.creation_date',
              name: 'customer.creation_date',
              "defaultContent": ''
            },
            {
              data: 'process.process_name',
              name: 'process.process_name',
              "defaultContent": ''
            },
          ]
        });

        $('body').on('click', '.delete', function() {
          var id = $(this).attr("value");
          var token = $("meta[name='csrf-token']").attr("content");
          if (!confirm("Are You sure want to delete ?")) {
            return false;
          }
          $.ajax({
            url: "{{ url('customer-custom-fields') }}" + '/' + id,
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