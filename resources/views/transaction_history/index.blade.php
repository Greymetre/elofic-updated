<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Transaction Coupon History {!! trans('panel.global.list') !!}
            <span class="pull-right">
              <div class="btn-group align-items-center">
                @if(auth()->user()->can(['serial_number_transaction_download']))
                <form method="GET" action="{{ URL::to('services/serial_number_transaction/download') }}" class="form-horizontal">
                  <div class="d-flex flex-row align-items-center">
                    <div class="p-2" style="width:200px;">
                      <label for="branch_id">Branch</label>
                      <select class="select2" placeholder="Select Branch" multiple name="branch_id[]" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <label for="parent_customer">Parent Customer</label>
                      <select class="select2" multiple name="parent_customer[]" id="parent_customer" data-style="select-with-transition" title="Select Parent Customer">
                        <option value="">Select Parent Customer</option>
                        @if(@isset($parent_customers ))
                        @foreach($parent_customers as $parent_customer)
                        <option value="{!! $parent_customer->id !!}">{!! $parent_customer->name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:180px;"><label for="start_date">Start Date</label><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:180px;"><label for="end_date">End Date</label><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} Transaction Coupon History"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                @if(auth()->user()->can(['scheme_create']))
                <a href="{{ route('transaction_history.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} Transaction Coupon History"><i class="material-icons">add_circle</i></a>
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
              {{ session('message_success') }}
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
            <table id="getTransactionHistory" class="table table-striped- table-bschemeed table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>{!! trans('panel.global.created_at') !!}</th>
                <th>Firm Name</th>
                <th>CONTACT PERSON</th>
                <th>parent name</th>
                <th>Mobile Number</th>
                <th>COUPON Code</th>
                <th>Sub Category</th>
                <th>Prodcut Name</th>
                <th>Point</th>
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
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>

  <script type="text/javascript">
    $(function() {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      var table = $('#getTransactionHistory').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'url': "{{ route('transaction_history.index') }}",
          'data': function(d) {
            d.branch_id = $('#branch_id').val(),
              d.parent_customer = $('#parent_customer').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'created_at',
            name: 'created_at',
            "defaultContent": ''
          },
          {
            data: 'customer.name',
            name: 'customer.name',
          },
          {
            data: 'contact_person',
            name: 'contact_person',
            orderable: false,
            searchable: false
          },
          {
            data: 'parent_name',
            name: 'parent_name',
            orderable: false,
            searchable: false
          },
          {
            data: 'customer.mobile',
            name: 'customer.mobile',
            "defaultContent": ''
          },
          {
            data: 'coupen_code',
            name: 'coupen_code',
            "defaultContent": ''
          },
          {
            data: 'subcategory_name',
            name: 'subcategory_name',
            orderable: false,
            searchable: false
          },
          {
            data: 'product_name',
            name: 'product_name',
            orderable: false,
            searchable: false
          },
          {
            data: 'points',
            name: 'points',
            orderable: false,
            searchable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            className: 'td-actions text-center',
            orderable: false,
            searchable: false
          },
        ]
      });
      $('#branch_id').change(function() {
        table.draw();
      });
      $('#parent_customer').change(function() {
        table.draw();
      });
      $('#start_date').change(function() {
        table.draw();
      });
      $('#end_date').change(function() {
        table.draw();
      });
      $('body').on('click', '.activeRecord', function() {
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
          url: "{{ url('schemes-active') }}",
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
          url: "{{ url('transaction_history') }}" + '/' + id,
          type: 'DELETE',
          data: {
            _token: token,
            id: id
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

    });
  </script>
</x-app-layout>