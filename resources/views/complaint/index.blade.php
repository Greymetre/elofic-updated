<x-app-layout>
  <style>
    #copyText {
      cursor: pointer;
      font-weight: 800;
      color: #000;
      text-shadow: 0 0 3px #fff;
    }
  </style>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{!! trans('panel.complaint.title_singular') !!} {!! trans('panel.global.list') !!}
            <span class="">
              <div class="btn-group header-frm-btn">
              @if(auth()->user()->can(['complaint_download']))
                <form method="GET" action="{{ URL::to('complaint_download') }}" class="form-horizontal">
                  <div class="d-flex flex-wrap flex-row">
                    <!-- <div class="p-2" style="width:200px;">
                      <select class="select2" name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                        <option value="">Select Branch</option>
                        @if(@isset($branches ))
                        @foreach($branches as $branch)
                        <option value="{!! $branch->id !!}">{!! $branch->branch_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="p-2" style="width:200px;">
                      <select class="select2" name="product_id" id="product_id" data-style="select-with-transition" title="Select Product">
                        <option value="">Select Product</option>
                        @if(@isset($products ))
                        @foreach($products as $product)
                        <option value="{!! $product->id !!}">{!! $product->product_name !!}</option>
                        @endforeach
                        @endif
                      </select>
                    </div>  -->
                    <div class="p-2" style="width:180px;"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2" style="width:180px;"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.complaint.title_singular') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
                </form>
                @endif
                <div class="next-btn">
                @if(auth()->user()->can(['complaint_create']))
                <a href="{{ route('complaints.create') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.add') !!} {!! trans('panel.complaint.title_singular') !!}"><i class="material-icons">add_circle</i></a>
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
          <div class="alert " style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
            <span class="message"></span>
          </div>
          <div class="table-responsive">
            <table id="getscheme" class="table table-striped- table-bschemeed table-hover table-checkable  no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <!-- <th>{!! trans('panel.global.action') !!}</th> -->
                <th>Date</th>
                <th>Complaint Number</th>
                <th>Service Center</th>
                <th>Service Center Code</th>
                <th>Seller Name</th>
                <th>Customer Name</th>
                <th>Customer Email</th>
                <th>Contact No</th>
                <th>Address</th>
                <th>Place</th>
                <th>Pincode</th>
                <th>Country</th>
                <th>State</th>
                <th>City</th>
                <th>Customer Complaint Type</th>      
                <th>Division</th> 
                <th>Product Name</th>
                <th>Product Code</th> 
                <th>Product Category</th>         
                <th>Product Serial No</th>  
                <th>HP</th> 
                <th>Stage</th>
                <th>Phase</th>
                <th>Warranty Customer Bill Date</th>
                <th>Service Paid/Free</th>
                <th>Work Done Time</th>
                <th>Action Done By ASC</th>
                <th>Service Center Remark</th>
                <!-- <th>Last Status</th> -->
                <th>Last Status Update Time</th>
                <th>Pending TAT</th>
                <th>Open TAT</th>
                <th>Cancelled TAT</th>
                <th>Work Done TAT</th>
                <th>Completed TAT</th>
                <th>Close TAT</th>
                <th>Serive Bill status</th>
                <th>Service Bill Approved Date</th>
                <th>Description</th>
                <th>Service Branch</th>
                <th>Purchased Party Name</th>
                <th>Warrenty Bill</th>
                <th>Customer Bill No </th>
                <th>Customer Bill Date</th>
                <th>Under Warrantye</th>
                <th>Service Type</th>
                <th>Company Sale Bill No.</th>
                <th>company sale bill date</th>
                <th>service centre remarks</th>
                <th>complaint register by</th>
                <th>division name</th>
                <th>work complated duration</th>
                <th>open duration</th>
                <th>closed date</th>
             <!--    <th>complaint feedback type</th>
                <th>feedback</th> -->
                <th>created by</th>
                <th>created at</th>
                <th>Complaint Status</th>
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
      var table = $('#getscheme').DataTable({
          processing: true,
          serverSide: true,
          order: [[0, 'desc']],
          ajax: {
              url: "{{ route('complaints.index') }}",
              type: "POST",  // Change GET to POST
              headers: {
                  "Accept": "application/json"  // This forces Laravel to return JSON properly
              },
              data: function (d) {
                  // This helps if you want to manually filter data
                  d._token = "{{ csrf_token() }}"; // Add CSRF token for Laravel
              }
          },
          columns: [
              {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
              {data: 'complaint_date', name: 'complaint_date', orderable: false, defaultContent: ''},
              {data: 'complaint_number', name: 'complaint_number', orderable: false, defaultContent: ''},
              {data: 'service_center_details.name', name: 'service_center_details.name', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_center_details.customer_code', name: 'service_center_details.customer_code', orderable: false, defaultContent: '', searchable: false},
              {data: 'seller', name: 'seller', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_name', name: 'customer.customer_name', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_email', name: 'customer.customer_email', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_number', name: 'customer.customer_number', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_address', name: 'customer.customer_address', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_place', name: 'customer.customer_place', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer_pindcode', name: 'customer_pindcode', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_country', name: 'customer.customer_country', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_state', name: 'customer.customer_state', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer.customer_city', name: 'customer.customer_city', orderable: false, defaultContent: '', searchable: false},
              {data: 'complaint_type_details.name', name: 'complaint_type_details.name', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.categories.category_name', name: 'product_details.categories.category_name', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.product_name', name: 'product_details.product_name', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.product_code', name: 'product_details.product_code', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.categories.category_name', name: 'product_details.categories.category_name', orderable: false, defaultContent: '', searchable: false},  
              {data: 'product_serail_number', name: 'product_serail_number', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.specification', name: 'product_details.specification', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.product_no', name: 'product_details.product_no', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.phase', name: 'product_details.phase', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer_bill_date', name: 'customer_bill_date', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_type', name: 'service_type', orderable: false, defaultContent: '', searchable: false},
              {data: 'work_done_time', name: 'work_done_time', orderable: false, defaultContent: '', searchable: false},
              {data: 'complaint_work_dones', name: 'complaint_work_dones', orderable: false, defaultContent: '', searchable: false},
              {data: 'complaint_work_remark', name: 'complaint_work_remark', orderable: false, defaultContent: '', searchable: false},
              {data: 'updated_at', name: 'updated_at', orderable: false, defaultContent: '', searchable: false},
              {data: 'pending_tat', name: 'pending_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'open_tat', name: 'open_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'canceled_tat', name: 'canceled_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'work_done_tat', name: 'work_done_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'compleated_tat', name: 'compleated_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'close_tat', name: 'close_tat', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_bill_status', name: 'service_bill_status', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_bill_date', name: 'service_bill_date', orderable: false, defaultContent: '', searchable: false},
              {data: 'description', name: 'description', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_branch', name: 'service_branch', orderable: false, defaultContent: '', searchable: false},
              {
                  data: null,
                  orderable: false,
                  render: function(data, type, full, meta) {
                      return data.customer.customer_name + ' (' + data.customer.customer_number + ')';
                  }
              },
              {data: 'warranty_bill', name: 'warranty_bill', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer_bill_no', name: 'customer_bill_no', orderable: false, defaultContent: '', searchable: false},
              {data: 'customer_bill_date', name: 'customer_bill_date', orderable: false, defaultContent: '', searchable: false},
              {data: 'under_warranty', name: 'under_warranty', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_type', name: 'service_type', orderable: false, defaultContent: '', searchable: false},
              {data: 'company_sale_bill_no', name: 'company_sale_bill_no', orderable: false, defaultContent: '', searchable: false},
              {data: 'company_sale_bill_date', name: 'company_sale_bill_date', orderable: false, defaultContent: '', searchable: false},
              {data: 'service_center_remark', name: 'service_center_remark', orderable: false, defaultContent: '', searchable: false},
              {data: 'register_by', name: 'register_by', orderable: false, defaultContent: '', searchable: false},
              {data: 'product_details.categories.category_name', name: 'product_details.categories.category_name', orderable: false, defaultContent: '', searchable: false},
              {data: 'work_complated_duration', name: 'work_complated_duration', orderable: false, defaultContent: '', searchable: false},
              {data: 'open_duration', name: 'open_duration', orderable: false, defaultContent: '', searchable: false},
              {data: 'closed_date', name: 'closed_date', orderable: false, defaultContent: '', searchable: false},
              // {data: 'complaint_feedback', name: 'complaint_feedback', orderable: false, defaultContent: '', searchable: false},
              // {data: 'feedback', name: 'feedback', orderable: false, defaultContent: '', searchable: false},
              {data: 'createdbyname.name', name: 'createdbyname.name', orderable: false, defaultContent: '', searchable: false},
              {data: 'created_at', name: 'created_at', orderable: false, defaultContent: '', searchable: false},
              {data: 'status', name: 'status', orderable: false, defaultContent: ''}
          ]
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
          url: "{{ url('schemes') }}" + '/' + id,
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

    $(document).ready(function() {
      $("#copyText").click(function() {
        var textToCopy = $("#copyText").text();
        var tempInput = $("<input>");
        $("body").append(tempInput);
        tempInput.val(textToCopy).select();
        document.execCommand("copy");
        tempInput.remove();
        const Toast = Swal.mixin({
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
          }
        });
        Toast.fire({
          icon: "success",
          title: "Complaint number copied to clipboard: " + textToCopy
        });
      });
    });
  </script>
</x-app-layout>