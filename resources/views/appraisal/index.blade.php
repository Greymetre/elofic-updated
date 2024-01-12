<x-app-layout>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header card-header-icon card-header-theme">
                    <div class="card-icon">
                        <i class="material-icons">perm_identity</i>
                    </div>
                    <h4 class="card-title">{!! trans('panel.appraisal.title_singular') !!} {!! trans('panel.global.list') !!}
                        <span class="pull-right">
                            <div class="btn-group">
                                <div class="p-2" style="width: 250px;">
                                    <select class="selectpicker" multiple name="branch_id" id="branch_id" data-style="select-with-transition" title="Select Branch">
                                        <option value="">Select Branch</option>
                                        @if(@isset($branches ))
                                        @foreach($branches as $branche)
                                        <option value="{!! $branche['id'] !!}" {{ old( 'branch_id') == $branche['id'] ? 'selected' : '' }}>{!! $branche['name'] !!}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
    
                                <div class="p-2" style="width: 250px;">
                                    <select class="selectpicker" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                                        <option value="">Select User</option>
                                        @if(@isset($users ))
                                        @foreach($users as $user)
                                        <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user['id'] ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                <a style="border-right: 1px solid;" href="{{ URL::to('attendance-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.appraisal.title') !!}"><i class="material-icons">cloud_download</i></a>
                                <a href="{{url('appraisal/create')}}" class="btn btn-just-icon btn-theme create" title="Add Appraisal">
                                    <i class="material-icons">add_circle</i>
                                </a>
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
                    <div class="table-responsive">
                        <table id="getattendance" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
                            <thead class=" text-primary">
                                <th>{!! trans('panel.global.no') !!}</th>
                                <th>{!! trans('panel.branch.title_singular') !!}</th>
                                <th>{!! trans('panel.user.department') !!}</th>
                                <th>{!! trans('panel.user.employee_code') !!}</th>
                                <th>{!! trans('panel.global.name') !!}</th>
                                <th>{!! trans('panel.designation.title_singular') !!}</th>
                                <th>{!! trans('panel.user.date_of_joining') !!}</th>
                                <th>{!! trans('panel.appraisal.ctc') !!}</th>
                                <th>{!! trans('panel.appraisal.last_increments') !!}</th>
                                <th>{!! trans('panel.appraisal.last_promotion') !!}</th>
                                <th>{!! trans('panel.appraisal.sales_weightage') !!}</th>
                                <th>{!! trans('panel.appraisal.target') !!}</th>
                                <th>{!! trans('panel.appraisal.achievement') !!}</th>
                                <th>{!! trans('panel.appraisal.rating') !!}</th>
                                <th>{!! trans('panel.appraisal.rating_by') !!}</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            oTable = $('#getattendance').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [
                    [0, 'desc']
                ],
                //"dom": 'Bfrtip',
                "ajax": "{{ route('appraisal.index') }}",
                "columns": [
                    // {data: 'DT_RowIndex',name: 'DT_RowIndex',orderable: false,searchable: false},
                    {data: 'branch',name: 'branch',orderable: false,searchable: false},
                    {data: 'department',name: 'department',orderable: false,searchable: false},
                    {data: 'employee_code',name: 'employee_code',orderable: false,searchable: false},
                    {data: 'name',name: 'name',orderable: false,searchable: false},
                    {data: 'designation',name: 'designation',orderable: false,searchable: false},
                    {data: 'date_of_joining',name: 'date_of_joining',orderable: false,searchable: false},
                    {data: 'ctc',name: 'ctc',orderable: false,searchable: false},
                    {data: 'last_increments',name: 'last_increments',orderable: false,searchable: false},
                    {data: 'last_promotion',name: 'last_promotion',orderable: false,searchable: false},
                    {data: 'sales_weightage',name: 'sales_weightage',orderable: false,searchable: false},
                    {data: 'target',name: 'sale_target',orderable: false,searchable: false},
                    {data: 'achievement',name: 'sale_achievement',orderable: false,searchable: false},
                    {data: 'rating',name: 'rating',orderable: false,searchable: false},
                    {data: 'rating_by',name: 'rating_by',orderable: false,searchable: false},
                ]
            });
        });

        $("#branch_id").on('change', function() {
            var search_branches = $(this).val();
            $.ajax({
                url: "{{ url('reports/attendancereport') }}",
                data: {
                    "search_branches": search_branches
                },
                success: function(res) {
                    if (res.status == true) {
                        var select = $('#executive_id');
                        select.empty();
                        select.append('<option>Select User</option>');
                        $.each(res.users, function(k, v) {
                            select.append('<option value="' + v.id + '" >' + v.name + '</option>');
                        });
                        select.selectpicker('refresh');
                    }
                }
            });

        })
    </script>
</x-app-layout>