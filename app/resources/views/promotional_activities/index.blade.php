<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Promotional Activity
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can('market_intelligence_report_download'))
<!-- <form method="GET" action="{{ route('promotional_activities.export') }}"> -->
    <div class="w-100 d-flex align-items-center">

    <span class="mr-2">PAC</span>

    <form method="GET"
          action="{{ route('promotional_activities.export') }}"
          class="mr-4 mb-0">
        <button type="submit" class="btn btn-theme ">
            <i class="material-icons">cloud_download</i>
        </button>
    </form>

    <span class="mr-2">Attendee</span>

    <form method="GET"
          action="{{ route('activity-attendees.exportAttendees') }}"
          class="mb-0">
        <button type="submit" class="btn btn-theme ">
            <i class="material-icons">cloud_download</i>
        </button>
    </form>

</div>
<!-- </form> -->

                <!-- <div class="next-btn">
                  <a href="{{ route('activity-attendees.exportAttendees') }}"
                    class="btn btn-success"
                    title="Download Excel">
                      <i class="material-icons">cloud_download</i>
                      Download Excel
                  </a>
              </div> -->
                @endif
                <div class="next-btn">
                  
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
    <th>No</th>
    <th>Activity Date</th>
    <th>Target Market</th>
    <th>Product Category</th>
    <th>Activity Type</th>
    <th>Customer Type</th>
    <th>Total Participants</th>
    <th>Retailer</th>
    <th>Distributor</th>
    <th>Approved By</th>
    <th>Created By</th>
    <!--<th>Action</th>-->
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
       
        ajax: {
            url: "{{ route('promotional_activity.index') }}"
        },
        columns: [
{
    data: 'DT_RowIndex',
    name: 'DT_RowIndex',
    orderable: false,
    searchable: false
},
{
    data: 'activity_date',
    name: 'activity_date'
},
{
    data: 'target_market',
    name: 'target_market'
},
{
    data: 'product_category',
    name: 'product_category'
},
{
    data: 'activity_type',
    name: 'activity_type'
},
{
    data: 'customer_type',
    name: 'customer_type'
},
{
    data: 'total_participants',
    name: 'total_participants'
},
{
    data: 'retailer.shop_name',
    name: 'retailer.company_name',
    defaultContent: '-'
},
{
    data: 'distributor.trade_name',
    name: 'distributor.name',
    defaultContent: '-'
},
{
    data: 'approved_by.name',
    name: 'approved_by.name',
    defaultContent: '-'
},
{
    data: 'created_by.name',
    name: 'created_by.name',
    defaultContent: '-'
},
// {
//     data: 'action',
//     name: 'action',
//     orderable: false,
//     searchable: false
// }
]
      });

      $('#division_id').change(function() {
        table.draw();
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
          url: "{{ url('promotional_activity') }}" + '/' + id,
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