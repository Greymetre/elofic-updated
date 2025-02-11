<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title">Rating Report
            <span class="">
              <div class="btn-group header-frm-btn">
                @if(auth()->user()->can(['asm_rating_download']))
                <form method="GET" action="{{ URL::to('asm_rating_report_download') }}">
                  <div class="d-flex flex-wrap flex-row">
                    <div class="p-2" style="width: 200px;">
                      <select name="role_id[]" multiple id="role_id" class="form-control select2" required>
                        <option value="" disabled>Role</option>
                        @foreach($roles as $role)
                        <option value="{{$role->id}}">{{$role->name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="user_id" id="user_id" class="form-control select2">
                        <option value="" disabled selected>User</option>
                        <!-- @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                        @endforeach -->
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="designation_id" id="designation_id" class="form-control select2">
                        <option value="" disabled selected>Designations</option>
                        @foreach($designations as $designation)
                        <option value="{{$designation->id}}">{{$designation->designation_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="division_id" id="division_id" class="form-control select2" required>
                        <option value="" disabled selected>Divisions</option>
                        @foreach($divisions as $division)
                        <option value="{{$division->id}}">{{$division->division_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="branch_id" id="branch_id" class="form-control select2">
                        <option value="" disabled selected>Branch</option>
                        @foreach($branchs as $branch)
                        <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="financial_year" id="financial_year" class="form-control select2" required>
                        <option value="" disabled selected>Select Financial Year</option>
                        @foreach($FinancialYears as $FinancialYear)
                        <option value="{{$FinancialYear}}">{{$FinancialYear}}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="p-2" style="width: 200px;">
                      <select name="month[]" multiple id="month" class="selectpicker" title="Select Month" placeholder="Select Month">
                        <option value="" disabled hidden>Select Month</option>
                        <option value="Apr">April</option>
                        <option value="May">May</option>
                        <option value="Jun">June</option>
                        <option value="Jul">July</option>
                        <option value="Aug">August</option>
                        <option value="Sep">September</option>
                        <option value="Oct">October</option>
                        <option value="Nov">November</option>
                        <option value="Dec">December</option>
                        <option value="Jan">January</option>
                        <option value="Feb">February</option>
                        <option value="Mar">March</option>
                      </select>
                    </div>
                    {{--<div class="p-2">
                      <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly>
                    </div>
                    <div class="p-2">
                      <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly>
                    </div>--}}
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" value="simple" name="download" type="submit" title="Rating Download">
                        <i class="material-icons">cloud_download</i>
                      </button>
                    </div>
                    @if(auth()->user()->can(['asm_rating_detailed_download']))
                    <div class="p-2">
                      <button class="btn btn-just-icon btn-theme" value="detailed" name="download" type="submit" title="Rating Details Download(PMS)">
                        <i class="material-icons">cloud_download</i>
                      </button>
                    </div>
                    @endif
                  </div>
                </form>
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
          @if (session('info'))
          <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          @endif

          <div class="table-responsive">
            <table id="getfosrating" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>No</th>
                <th>Employees Code</th>
                <th>FOS Name</th>
                <th>Action</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="pmsModal" tabindex="-1" aria-labelledby="pmsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pmsModalLabel">PMS Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="pmsForm">
            @csrf
            <table class="table table-bordered">
              <tbody>
                <tr>
                  <th>Name</th>
                  <td><input type="text" class="form-control" name="name" value="Gajendra Singh Rajput" readonly></td>
                  <th>Branch</th>
                  <td><input type="text" class="form-control" name="branch" value="Indore" readonly></td>
                  <th>Designation</th>
                  <td><input type="text" class="form-control" name="designation" value="Manager" readonly></td>
                </tr>
                <tr>
                  <th>Final Rating</th>
                  <td><input type="number" class="form-control" name="final_rating" value="80" readonly></td>
                  <th>Company Tenure (in Months)</th>
                  <td><input type="number" class="form-control" name="tenure" value="14" readonly></td>
                  <th>Gross Salary</th>
                  <td><input type="number" class="form-control" name="gross_salary" value="50000" readonly></td>
                </tr>
                <tr>
                  <th>Last Year Gross Increment Value</th>
                  <td><input type="number" class="form-control" name="last_year_increment_value" value="5000" readonly></td>
                  <th>Last Year Increment %</th>
                  <td><input type="text" class="form-control" name="last_year_increment" value="10%" readonly></td>
                  <th>Total Target</th>
                  <td><input type="number" class="form-control" name="total_target" value="1000000"></td>
                </tr>
                <tr>
                  <th>Total Sales</th>
                  <td><input type="number" class="form-control" id="totalSales" name="total_sales" value="500000"></td>
                  <th>Percentage Achievement</th>
                  <td><input type="number" class="form-control" id="percentageAchievement" name="percentage_achievement"></td>
                  <th>Recommended CY Increment %</th>
                  <td><input type="number" class="form-control" name="recommended_increment" value="10"></td>
                </tr>
                <tr>
                  <th>Recommended Designation</th>
                  <td><input type="text" class="form-control" name="recommended_designation" value="ASM"></td>
                  <th>Remark</th>
                  <td colspan="3">
                    <textarea class="form-control" name="remarks" placeholder="Enter remarks"></textarea>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="text-center">
              <button type="submit" class="btn btn-success btn-lg">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      var token = $("meta[name='csrf-token']").attr("content");
      oTable = $('#getfosrating').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [
          [0, 'desc']
        ],
        "ajax": {
          'type': 'POST',
          'url': "{{ url('reports/asm_rating') }}",
          'data': function(d) {
            d._token = token,
              d.user_id = $('#user_id').val(),
              d.designation_id = $('#designation_id').val(),
              d.division_id = $('#division_id').val(),
              d.branch_id = $('#branch_id').val(),
              d.start_date = $('#start_date').val(),
              d.end_date = $('#end_date').val()
            d.month = $('#month').val()
          }
        },
        "columns": [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'employee_codes',
            name: 'employee_codes',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'name',
            name: 'name',
            "defaultContent": '',
            orderable: false
          },
          {
            data: 'action',
            name: 'action',
            "defaultContent": '',
            orderable: false,
            searchable: false
          }
        ]
      });
      $('#start_date').change(function() {
        oTable.draw();
      }).trigger('change');
      $('#end_date').change(function() {
        oTable.draw();
      }).trigger('change');
      $('#user_id').change(function() {
        oTable.draw();
      });
      $('#designation_id').change(function() {
        oTable.draw();
      });
      $('#division_id').change(function() {
        oTable.draw();
      });
      $('#branch_id').change(function() {
        oTable.draw();
      });
      $('#month').change(function() {
        oTable.draw();
      });
    });

    $("#role_id").on("change", function() {
      var roles = $(this).val();
      $.ajax({
        url: "{{ url('getUserList') }}",
        dataType: "json",
        type: "POST",
        data: {
          _token: "{{csrf_token()}}",
          roles: roles
        },
        success: function(res) {
          var html = '<option value="">Select User</option>';
          $.each(res, function(k, v) {
            html += '<option value="' + v.id + '"> (' + v.employee_codes + ') ' + v.name + '</option>';
          });
          $("#user_id").html(html);
        }
      });
    }).trigger("chnage");

    $(document).on("click", ".edit_remark", function() {
      var id = $(this).data("id");
      console.log(id);
      $.ajax({
        url: "{{ url('getPMS') }}/",
        type: 'GET',
        dataType: 'json',
        data: {
          _token: "{{csrf_token()}}",
          id: id
        },
        success: function(res) {
          $("#pmsForm").find("input[name='name']").val(res.name);
          $("#pmsForm").find("input[name='branch']").val(res.branch);
          $("#pmsForm").find("input[name='designation']").val(res.designation);
          $("#pmsForm").find("input[name='final_rating']").val(res.final_rating);
          $("#pmsForm").find("input[name='tenure']").val(res.tenure);
          $("#pmsForm").find("input[name='gross_salary']").val(res.gross_salary);
          $("#pmsForm").find("input[name='last_year_increment_value']").val(res.last_year_increment_value);
          $("#pmsForm").find("input[name='last_year_increment']").val(res.last_year_increment);
          $("#pmsForm").find("input[name='total_target']").val(res.total_target);
          $("#pmsForm").find("input[name='total_sales']").val(res.total_sales);
          $("#pmsForm").find("input[name='percentage_achievement']").val(res.percentage_achievement);
          $("#pmsForm").find("input[name='recommended_increment']").val(res.recommended_increment);
          $("#pmsForm").find("input[name='recommended_designation']").val(res.recommended_designation);
          $("#pmsForm").find("textarea[name='remarks']").val(res.remarks);
          $("#pmsForm").find("input[name='id']").val(res.id);
        }
      })
      $("#pmsModal").modal("show");
    });
  </script>
</x-app-layout>