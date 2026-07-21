<x-app-layout>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-icon card-header-theme">
                <div class="card-icon">
                    <i class="material-icons">storefront</i>
                </div>
                <h4 class="card-title">
                    {{ $typeTitle ?? 'Retailers List' }}
                    <span class="float-right">
                                                <input type="text" id="global_search" class="form-control d-inline-block" style="width: 250px;" placeholder="Search anything...">

                        <button class="btn btn-info" type="button" data-toggle="collapse" data-target="#advanceFilter">
                            <i class="material-icons">tune</i> Filter
                        </button>
                        @if(auth()->user()->can(['customer_create']))
                        <a href="{{ route('retailers.create') }}" class="btn btn-theme">
                            <i class="material-icons">add_circle</i> Add New Retailer
                        </a>
                        @endif
                    </span>
                </h4>
            </div>

            <!-- Filters -->
             <div class="collapse" id="advanceFilter">
                @if(auth()->user()->can(['customer_download']))
    <form method="GET" action="{{ $downloadRoute }}" id="downloadForm" title="Download Excel">
        <div class="d-flex flex-wrap flex-row">
            @if(!isCustomerUser())
          <!-- Owner Name -->
<div class="col-md-3">
    <label>Owner Name</label>
    {!! Form::select('owner_name', ['' => ''], null, [
    'class' => 'form-control',
    'id' => 'owner_name'
]) !!}

</div>

<!-- Shop Name -->
<div class="col-md-3">
    <label>Shop Name</label>
{!! Form::select('shop_name', ['' => ''], null, [
    'class' => 'form-control',
    'id' => 'shop_name'
]) !!}
</div>

<!-- Mobile Number -->
<div class="col-md-3">
    <label>Mobile Number</label>
{!! Form::select('mobile', ['' => ''], null, [
    'class' => 'form-control',
    'id' => 'mobile'
]) !!}
</div>

        <!-- Beat -->
        <div class="col-md-3">
            <label>Beat</label>
            {!! Form::select('beat_id', ['' => 'All Beats'] + \App\Models\Beat::where('active', 'Y')->orderBy('beat_name')->pluck('beat_name', 'id')->toArray(), null, 
                ['class' => 'form-control select2', 'id' => 'beat_id', ]) !!}
        </div>

        <!-- State -->
        <div class="col-md-3 mt-3">
            <label>State</label>
            {!! Form::select('state_id', ['' => 'All States'] + $states->pluck('state_name', 'id')->toArray(), null, 
                ['class' => 'form-control select2', 'id' => 'state_id', ]) !!}
        </div>

        <!-- City (Dynamic - initially empty except All) -->
        <div class="col-md-3 mt-3">
            <label>City</label>
            {!! Form::select('city_id', ['' => 'All Cities'], null, 
                ['class' => 'form-control select2', 'id' => 'city_id', ]) !!}
        </div>

        <!-- Opportunity Status -->
        <div class="col-md-3 mt-3">
            <label>Opportunity Status</label>
            {!! Form::select('opportunity_status', 
                ['' => 'All', 'HOT' => 'HOT', 'WARM' => 'WARM', 'COLD' => 'COLD', 'LOST' => 'LOST', 'EXISTING' => 'EXISTING'],
                null, 
                ['class' => 'form-control select2', 'id' => 'opportunity_status']) !!}
        </div>

        <!-- Awareness Status -->
        <div class="col-md-3 mt-3">
            <label id="awareness_label">
                {{ in_array($type ?? '', ['RETAILER', 'WORKSHOP']) ? 'Nistha' : 'Saathi' }} Awareness Status
            </label>
            {!! Form::select('awareness_status', 
                ['' => 'All', 'Done' => 'Done', 'Not Done' => 'Not Done'], 
                null, 
                ['class' => 'form-control select2', 'id' => 'awareness_status']) !!}
        </div>
<div class="col-md-3 mt-3">
    <label>Start Date</label>
    <input type="text" class="form-control datepicker" id="start_date"
        name="start_date" placeholder="Start Date" autocomplete="off">
</div>

<div class="col-md-3 mt-3">
    <label>End Date</label>
    <input type="text" class="form-control datepicker" id="end_date"
        name="end_date" placeholder="End Date" autocomplete="off">
</div>

<!-- Export Page Limit -->
<div class="col-md-2 mt-3">
    <label>Export Limit</label>
    <select name="export_limit" id="export_limit" class="form-control select2">
        <option value="100">100</option>
        <option value="500">500</option>
        <option value="1000">1000</option>
        <option value="5000">5000</option>
    </select>
</div>

<!-- Export Page Number -->
<div class="col-md-2 mt-3">
    <label>Page No</label>
    <!-- <select name="export_page" id="export_page" class="form-control select2">
        @for($i = 1; $i <= 100; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select> -->
    <select name="export_page" id="export_page" class="form-control select2">
    </select>
    <input type="hidden" id="total_records" value="{{ $totalRecords }}">
</div>
            <!-- <div class="p-2" style="width:150px;"><input type="text" class="form-control datepicker" id="start_date"
                    name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
            <div class="p-2" style="width:150px;"><input type="text" class="form-control datepicker" id="end_date"
                    name="end_date" placeholder="End Date" autocomplete="off" readonly></div> -->
            <div class="p-2">
                                            <button 
                                                type="button"
                                                id="downloadBtn"
                                                class="btn btn-just-icon btn-theme"
                                                title="{!!  trans('panel.global.download') !!} {!! trans('panel.customers.title') !!}">
                                                
                                                <i class="material-icons">cloud_download</i>
                                            </button>
                                        </div>
                         @endif
        </div>
    </form>
    @endif
    <div class="next-btn">
        @if(auth()->user()->can(['customer_upload']))
        <form action="{{ route(strtolower($type).'s.import') }}" method="POST"
                            enctype="multipart/form-data">

                            {{ csrf_field() }}

                            <div class="input-group">
                                <div class="fileinput fileinput-new text-center">
                                    <span class="btn btn-just-icon btn-theme btn-file">
                                        <span class="fileinput-new">
                                            <i class="material-icons">attach_file</i>
                                        </span>

                                        <input type="file" name="import_file" required accept=".xls,.xlsx" />
                                    </span>
                                </div>

                                <div class="input-group-append">
                                    <button class="btn btn-just-icon btn-theme">
                                        <i class="material-icons">cloud_upload</i>
                                    </button>
                                </div>
                            </div>
                        </form>
        @endif
        @if(auth()->user()->can(['customer_template']))
        <a href="{{ $templateRoute }}" class="btn btn-just-icon btn-theme"
            title="{!!  trans('panel.global.template') !!} {!! trans('panel.customers.title_singular') !!}"><i
                class="material-icons">text_snippet</i></a>
        @endif
        <!-- @if(auth()->user()->can(['customer_create']))
        <a href="{{ route('customers.create') }}" class="btn btn-just-icon btn-theme"
            title="{!!  trans('panel.global.add') !!} {!! trans('panel.customers.title_singular') !!}"><i
                class="material-icons">add_circle</i></a>
        @endif -->
    </div>
</div>
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
            @endif
            @if(session('importErrors'))
            <div class="alert alert-danger">

                <strong>{{ count(session('importErrors')) }} Errors Found</strong>

                <ul>
                    @foreach(session('importErrors') as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
            @endif  
            <!-- Table -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="retailersTable" class="table table-striped table-bordered table-hover w-100">
                        <thead class="text-primary">
                            <tr>
                                <th width="100">Action</th>
                                <th>Owner Name</th>
                                <th>Shop Name</th>
                                <th>Mobile</th>
                                <th>Assigned Employee Name</th>
                                <th>Beat</th>
                                <th>State</th>
                                <th>District</th>
                                <th>Opportunity</th>
                                <th>Nistha Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- DataTables will fill this -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
$(document).ready(function () {
    
    let table = $('#retailersTable').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ordering: true,
        drawCallback: function(settings) {

                let api = this.api();

                let totalFilteredRecords = api.page.info().recordsDisplay;

                $('#total_records').val(totalFilteredRecords);

                updatePageDropdown();
            },
        order: [[10, 'desc']], // Latest first
        ajax: {
            url: "{{ route(strtolower($type).'s.index') }}",
            type: "GET",
            data: function (d) {
                d.owner_name = $('#owner_name').val();
                d.shop_name = $('#shop_name').val();
                d.mobile     = $('#mobile').val();
                d.status     = $('#status').val();
                d.beat_id           = $('#beat_id').val() || '';
                d.state_id          = $('#state_id').val() || '';
                d.city_id           = $('#city_id').val() || '';
                d.opportunity_status = $('#opportunity_status').val() || '';
                d.awareness_status  = $('#awareness_status').val() || '';
                d.global_search     = $('#global_search').val() || '';
                d.start_date = $('#start_date').val() || '';
                d.end_date   = $('#end_date').val() || '';
            }
        },
        columns: [
            { data: 'action',                  name: 'action', orderable: false, searchable: false },
            { data: 'owner_name',              name: 'owner_name' },
            { data: 'shop_name',               name: 'shop_name' },
            { data: 'mobile_number',           name: 'mobile_number' },
            { data: 'assigned_employee_name',  name: 'assigned_employee_name', orderable: false, searchable: false, defaultContent: '' },
           { data: 'beat_id', name: 'beats.beat_name',defaultContent: '-' },
                {
                    data: 'state_id',
                    name: 'state.state_name',
                    defaultContent: '-'
                },
                {
                    data: 'city_id',
                    name: 'cities.city_name',
                    defaultContent: '-'
                },
            { data: 'opportunity_status',      name: 'opportunity_status', orderable: false },
            { data: 'nistha_awareness_status', name: 'nistha_awareness_status', orderable: false },
            { data: 'created_at',              name: 'created_at' }
            
        
        ],

        dom: 't<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
        language: {
            processing: "Loading retailers...",
            zeroRecords: "No retailers found",
            info: "Showing _START_ to _END_ of _TOTAL_ retailers",
            infoEmpty: "No records available",
            paginate: {
                previous: "Previous",
                next: "Next"
            }
        }
    });

    $('#downloadBtn').on('click', function () {

            let params = {

                owner_name: $('#owner_name').val() || '',
                shop_name: $('#shop_name').val() || '',
                mobile: $('#mobile').val() || '',
                beat_id: $('#beat_id').val() || '',
                state_id: $('#state_id').val() || '',
                city_id: $('#city_id').val() || '',
                opportunity_status: $('#opportunity_status').val() || '',
                awareness_status: $('#awareness_status').val() || '',
                global_search: $('#global_search').val() || '',
                start_date: $('#start_date').val() || '',
                end_date: $('#end_date').val() || '',
                export_limit: $('#export_limit').val() || '100',
                export_page: $('#export_page').val() || '1'
            };

            let query = $.param(params);

            window.location.href = "{{ $downloadRoute }}?" + query;
        }); 

    $('#state_id').on('change', function() {
    let stateId = $(this).val();

    // City dropdown reset karo
    $('#city_id').empty().append('<option value="">All Cities</option>').val('').trigger('change');

    if (stateId) {
        $.ajax({
            url: '{{ route("secondary-customers.get-cities") }}', // Route name update karo apne hisab se
            type: 'GET',
            data: { state_id: stateId },
            success: function(data) {
                $.each(data, function(key, city) {
                    $('#city_id').append('<option value="' + city.id + '">' + city.city_name + '</option>');
                });
                $('#city_id').trigger('change'); // select2 refresh
            },
            error: function() {
                alert('Failed to load cities');
            }
        });
    }

    // State change hone par table reload
    table.draw();
});

// City change par bhi table reload
$('#city_id').on('change', function() {
    table.draw();
});
$('#start_date, #end_date').on('change', function () {
    table.draw();
});

    // Normal change events (text inputs aur normal selects)
    $(document).on('change', '#owner_name, #shop_name, #mobile, #beat_id, #state_id, #city_id, #opportunity_status, #awareness_status', function() {
        table.draw();
    });

    // Select2 ke special events (jab clear ya select kare)
    $(document).on('select2:select select2:clear', '#owner_name, #shop_name, #mobile, #beat_id, #state_id, #city_id, #opportunity_status, #awareness_status', function() {
        table.draw();
    });


$('#advanceFilter').on('shown.bs.collapse', function () {

$('#owner_name').select2({
    placeholder: 'Select Owner',
    allowClear: true,
    width: '100%',
    ajax: {
        url: "{{ url('/secondary-customers/dropdown') }}",
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                search: params.term || '',
                type: 'owner',
                customer_type: "{{ $type }}"
            };
        },
        processResults: function(data) {

            data.results.unshift({
                id: '',
                text: 'Select Owner'
            });

            return data;
        }
    }
});

$('#shop_name').select2({
    placeholder: 'Select Shop Name',
    allowClear: true,
    width: '100%',
    ajax: {
        url: "{{ url('/secondary-customers/dropdown') }}",
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                search: params.term || '',
                type: 'shop',
                customer_type: "{{ $type }}"
            };
        },
        processResults: function(data) {

            data.results.unshift({
                id: '',
                text: 'Select Shop Name'
            });

            return data;
        }
    }
});
    
$('#mobile').select2({
    placeholder: 'Select Mobile',
    allowClear: true,
    width: '100%',
    ajax: {
        url: "{{ url('/secondary-customers/dropdown') }}",
        dataType: 'json',
        delay: 250,
        data: function(params) {
            return {
                search: params.term || '',
                type: 'mobile',
                customer_type: "{{ $type }}"
            };
        },
        processResults: function(data) {

            data.results.unshift({
                id: '',
                text: 'Select Mobile'
            });

            return data;
        }
    }
});
});
    // ====== STATE CHANGE → CITY LOAD + TABLE FILTER ======
   

    // Global search with debounce
    let searchTimeout;
    $('#global_search').on('keyup', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            table.draw();
        }, 600);
    });

         $(document).on('click', '.deleteCustomer', function(e) {
        e.preventDefault();

        const deleteUrl = $(this).data('url');
        const row = $(this).closest('tr');

        if (confirm('Are you sure you want to delete this record? This action cannot be undone.')) {
            
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('DELETE SUCCESS:', response);
                    toastr.success('Record deleted successfully!');
                    
                    // Reload table - This is what you asked for
                    table.draw(false); // false = keep current page
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        toastr.error('You do not have permission to delete this record.');
                    } else {
                        toastr.error('Failed to delete record. Please try again.');
                    }
                }
            });
        }
    });

    // ====================== ACTIVE / INACTIVE TOGGLE ======================
    $(document).on('change', '.distributor-status-toggle', function() {
        const checkbox = $(this);
        const id = checkbox.data('id');
        const newStatus = checkbox.is(':checked') ? 'Y' : 'N';

        $.ajax({
            url: "{{ route('secondary-customers.toggle-active') }}",
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: id,
                active: newStatus
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('Status updated successfully');
                    table.draw(false);   // Reload table after toggle
                } else {
                    toastr.error('Failed to update status');
                    checkbox.prop('checked', !checkbox.is(':checked')); // revert
                }
            },
            error: function() {
                toastr.error('Something went wrong while updating status');
                checkbox.prop('checked', !checkbox.is(':checked')); // revert on error
            }
        });
    });

    function updatePageDropdown() {

    let totalRecords = parseInt($('#total_records').val()) || 0;
    let limit = parseInt($('#export_limit').val()) || 100;

    let totalPages = Math.ceil(totalRecords / limit);

    let currentPage = $('#export_page').val();

    $('#export_page').empty();

    for (let i = 1; i <= totalPages; i++) {
        $('#export_page').append(
            `<option value="${i}">${i}</option>`
        );
    }

    // restore selected page if exists
    if (currentPage && currentPage <= totalPages) {
        $('#export_page').val(currentPage);
    }

    $('#export_page').trigger('change.select2');
}

$(document).ready(function () {

    updatePageDropdown();

    $('#export_limit').on('change', function () {
        updatePageDropdown();
    });

});

    
});
</script>
</x-app-layout>
