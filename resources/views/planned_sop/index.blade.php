<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Planned S&OP List
            <span class="">
              <div class="btn-group header-frm-btn">
                <div class="next-btn">
                  <!-- @if(auth()->user()->can(['product_create'])) -->
                  <a href="{{ route('planned-sop.create') }}" class="btn btn-just-icon btn-theme" title="Add Planned SOP"><i class="material-icons">add_circle</i></a>
                  <!-- @endif -->
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
            <table id="getplannedsop" class="table table-striped- table-bordered table-hover table-checkable no-wrap">
              <thead class=" text-primary">
                <th>{!! trans('panel.global.no') !!}</th>
                <th>Branch Name</th>
                <th>Group Name</th>
                <th>Item Name</th>
                <th>Product Desc.</th>
                <th>Opening stock as on 1st (Qty)</th>
                <th>S&OP Plan for Next running month (M+1) (Qty.)</th>
                <th>Budget for the month (Qty.)</th>
                <th>LM Sale (Qty.)</th>
                <th>L3M Avg Sale (Qty.)</th>
                <th>LY same month sale (Qty.)</th>
                <th>SKU Unit Price</th>
                <th>S&OP Val_L (Unit Price *Qty.)</th>
                <th>TOP 20 SKU for the Branch (*)</th>
                <th>{!! trans('panel.global.created_at') !!}</th>
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
      var token = $("meta[name='csrf-token']").attr("content");
      var table = $('#getplannedsop').DataTable({
        processing: true,
        serverSide: true,
        "order": [
          [0, 'desc']
        ],
        ajax: {
          type:'POST',
          url: "{{ route('plannedSopList') }}",
          data: function (d) {
                d._token = token,
                d.category_id = $('#category_id').val()
                d.active = $('#active').val()
            }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'get_branch.branch_name',
            name: 'get_branch.branch_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'get_product.subcategories.subcategory_name',
            name: 'get_product.subcategories.subcategory_name',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'get_product.categories.category_name',
            name: 'get_product.categories.category_name',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'get_product.description',
            name: 'get_product.description',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'opening_stock',
            name: 'opening_stock',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'plan_next_month',
            name: 'plan_next_month',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'budget_for_month',
            name: 'budget_for_month',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'last_month_sale',
            name: 'last_month_sale',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'last_three_month_avg',
            name: 'last_three_month_avg',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'last_year_month_sale',
            name: 'last_year_month_sale',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'sku_unit_price',
            name: 'sku_unit_price',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 's_op_val',
            name: 's_op_val',
            orderable: false,
            "defaultContent": ''
          },
           {
            data: 'top_sku',
            name: 'top_sku',
            orderable: false,
            "defaultContent": ''
          },
          {
            data: 'created_by',
            name: 'created_by',
            orderable: false,
            "defaultContent": ''
          }
        ]
      });
    });
  </script>
</x-app-layout>