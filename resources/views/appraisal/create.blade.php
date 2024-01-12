<x-app-layout>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- left column -->
                <div class="col-md-12">
                    <!-- jquery validation -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Appraisal Create</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        @if($errors->any())
                        <div>
                            <ul class="alert alert-danger">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <div class="card-body ">
                            <div class="tab-content tab-space">
                                {!! Form::model($appraisal,[
                                'route' => $appraisal->exists ? ['appraisal.update', $appraisal->id] : 'appraisal.store',
                                'method' => $appraisal->exists ? 'PUT' : 'POST',
                                'id' => 'createCompany',
                                'files'=>true
                                ]) !!}
                                <div class="row">
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

                                    <div class="p-2" style="width: 250px;">
                                        <select class="selectpicker" name="f_year" id="f_year" data-style="select-with-transition" title="Select Financial Year">
                                            <option value="">Select Financial Year</option>
                                            <option value="{{Carbon\Carbon::now()->format('Y')-1}}_{{Carbon\Carbon::now()->format('y')}}">{{Carbon\Carbon::now()->format('Y')-1}}-{{Carbon\Carbon::now()->format('y')}}</option>
                                        </select>
                                    </div>

                                    <div class="p-2" style="width: 250px;">
                                        <select class="selectpicker" name="appraisal_type" id="appraisal_type" data-style="select-with-transition" title="Select Appraisal Type">
                                            <option value="">Select Appraisal Type</option>
                                            <option value="quarterly">Quarterly</option>
                                            <option value="half_yearly">Half Yearly</option>
                                            <option value="yearly">Yearly</option>
                                        </select>
                                    </div>

                                    <div class="p-2" id="session_div" style="width: 250px;">
                                        <select class="selectpicker" name="appraisal_session" id="appraisal_session" data-style="select-with-transition" title="Select Appraisal Type">
                                        </select>
                                    </div>

                                    <div class="table-responsive w-100">
                                        <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                                            <thead>
                                                <tr class="card-header-warning text-white">
                                                    <th class="text-center">Sale Weightage </th>
                                                    <th class="text-center">Weightage</th>
                                                    <th class="text-center">Target</th>
                                                    <th class="text-center">Achivment</th>
                                                    <th class="text-center">Acual</th>
                                                    <th class="text-center">Max Rating</th>
                                                    <th class="text-center">Rating</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sale_weightage as $k=>$val)
                                                <tr>
                                                    <td>
                                                        <h6>{{$val->name}}</h6>
                                                        <input type="hidden" name="sale_weightage_id[]" value="{{$val->id}}">
                                                    </td>
                                                    <td>
                                                        {{$val->weightage}}%
                                                    </td>
                                                    <td>
                                                        <input type="number" name="target[]" id="target">
                                                    </td>
                                                    <td>
                                                        <input type="number" id="achivment" name="achivment[]">
                                                    </td>
                                                    <td>
                                                        <input name="acual[]" id="acual" type="text" readonly>
                                                    </td>
                                                    <td>10</td>
                                                    <td>
                                                        @if($k > 0)
                                                            <input name="rating[]" type="number">
                                                        @else
                                                            <input readonly id="rating" name="rating[]" type="number">
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="form-group">
                                            <label for="remark">Remark</label>
                                            <input type="text" class="form-control" name="remark" id="remark" placeholder="Remark">
                                        </div>
                                    </div>
                                    {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
                                    {{ Form::close() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <script>
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

        $(document).ready(function(){
            $('#session_div').hide();
        })

        $("#appraisal_type").on('change', function(){
            var appraisal_type = $(this).val();
            var select = $('#appraisal_session');
            if(appraisal_type == 'quarterly' || appraisal_type == 'half_yearly')
            {
                select.empty();
                $('#session_div').show();
                select.append('<option value="">Select Appraisal Session</option>');
                if(appraisal_type == 'quarterly'){
                    select.append('<option value="Q1">Q1</option>');
                    select.append('<option value="Q2">Q2</option>');
                    select.append('<option value="Q3">Q3</option>');
                    select.append('<option value="Q4">Q4</option>');
                }else if(appraisal_type == 'half_yearly'){
                    select.append('<option value="first_half">First Half</option>');
                    select.append('<option value="second_half">Second Half</option>');
                }
                select.selectpicker('refresh');
            }else{
                $('#session_div').hide();
                select.selectpicker('refresh');
            }
        })

        $("#achivment").on('keyup', function(){
            var achivment = $(this).val();
            var target = $("#target").val();
            if(achivment > 0 && target > 0){
                let acual = ((achivment/target)*100).toFixed(2);
                $("#acual").val(acual+"%");
                let rating = (acual/10).toFixed(1);
                $("#rating").val(rating);
            }
        })
    </script>

</x-app-layout>