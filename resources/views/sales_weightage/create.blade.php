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
                            <h3 class="card-title">Sale Weightage Create</h3>
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
                                {!! Form::model($sales_weightage,[
                                'route' => $sales_weightage->exists ? ['sales_weightage.update', $sales_weightage->id] : 'sales_weightage.store',
                                'method' => $sales_weightage->exists ? 'PUT' : 'POST',
                                'id' => 'createCompany',
                                'files'=>true
                                ]) !!}
                                    <div class="p-2 form-group">
                                        <label for="name">Sale Weightage Name</label>
                                        <input value="{{$sales_weightage?$sales_weightage->name:''}}" type="text" name="name" id="name" class="form-control">
                                    </div>

                                    <div class="p-2">
                                    <label for="weightage">Weightage</label>
                                        <input type="text" name="weightage" id="weightage" value="{{$sales_weightage?$sales_weightage->weightage:''}}"class="form-control">
                                    </div>
                                    @if($sales_weightage->name)
                                    {{ Form::submit('Update', array('class' => 'btn btn-theme pull-right')) }}
                                    @else
                                    {{ Form::submit('Save', array('class' => 'btn btn-theme pull-right')) }}
                                    @endif
                                    {{ Form::close() }}
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

        $("#executive_id").on("change", function(){
            var executive_id = $(this).val();
            var f_year = $("#f_year").val();
            var appraisal_type = $("#appraisal_type").val();
            var appraisal_session = $("#appraisal_session").val();

            if(executive_id != '' && f_year != '' && appraisal_type != ''){
                if(appraisal_type == 'quarterly' || appraisal_type == 'half_yearly'){
                    if(appraisal_session != ''){
                        $.ajax({
                            url: "{{ url('getappraisal') }}",
                            data: {
                                "executive_id": executive_id,
                                "f_year": f_year,
                                "appraisal_type": appraisal_type,
                                "appraisal_session": appraisal_session
                            },
                            success: function(res) {
                                if(res.length > 0){
                                    $.each(res, function(i, item) {
                                        var tdElement = $('td:contains(' + item.sales_weightage.weightage + '%)');
                                        var trElement = tdElement.closest('tr');

                                        $(trElement).find('td').each (function() {
                                            var targetInputtarget = $(this).find('input[name="target[]"]');
                                            var targetInputachivment = $(this).find('input[name="achivment[]"]');
                                            var targetInputacual = $(this).find('input[name="acual[]"]');
                                            var targetInputrating = $(this).find('input[name="rating[]"]');

                                            if (targetInputtarget.length > 0) {
                                                targetInputtarget.val(item.target);
                                            }
                                            if (targetInputachivment.length > 0) {
                                                targetInputachivment.val(item.achivment);
                                            }
                                            if (targetInputacual.length > 0) {
                                                targetInputacual.val(item.acual);
                                            }
                                            if (targetInputrating.length > 0) {
                                                targetInputrating.val(item.rating);
                                            }
                                        });
                                        // console.log(trElement);
                                    });
                                 }else{
                                    $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                                 }
                            }
                        });
                    }else{
                        $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                    }
                }else{
                    $.ajax({
                            url: "{{ url('getappraisal') }}",
                            data: {
                                "executive_id": executive_id,
                                "f_year": f_year,
                                "appraisal_type": appraisal_type,
                            },
                            success: function(res) {
                                 if(res.length > 0){
                                    $.each(res, function(i, item) {
                                        var tdElement = $('td:contains(' + item.sales_weightage.weightage + '%)');
                                        var trElement = tdElement.closest('tr');

                                        $(trElement).find('td').each (function() {
                                            var targetInputtarget = $(this).find('input[name="target[]"]');
                                            var targetInputachivment = $(this).find('input[name="achivment[]"]');
                                            var targetInputacual = $(this).find('input[name="acual[]"]');
                                            var targetInputrating = $(this).find('input[name="rating[]"]');

                                            if (targetInputtarget.length > 0) {
                                                targetInputtarget.val(item.target);
                                            }
                                            if (targetInputachivment.length > 0) {
                                                targetInputachivment.val(item.achivment);
                                            }
                                            if (targetInputacual.length > 0) {
                                                targetInputacual.val(item.acual);
                                            }
                                            if (targetInputrating.length > 0) {
                                                targetInputrating.val(item.rating);
                                            }
                                        });
                                        // console.log(trElement);
                                    });
                                 }else{
                                    $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                                 }
                            }
                        });
                }
            }else{
                $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
            }
        })

        $(document).ready(function(){
            // $('#session_div').hide();
        })

        $("#appraisal_type").on('change', function(){
            var appraisal_type = $(this).val();
            var executive_id = $("#executive_id").val();
            var f_year = $("#f_year").val();
            var appraisal_session = $("#appraisal_session").val();

            if(executive_id != '' && f_year != '' && appraisal_type != ''){
                if(appraisal_type == 'quarterly' || appraisal_type == 'half_yearly'){
                    if(appraisal_session != ''){
                        $.ajax({
                            url: "{{ url('getappraisal') }}",
                            data: {
                                "executive_id": executive_id,
                                "f_year": f_year,
                                "appraisal_type": appraisal_type,
                                "appraisal_session": appraisal_session
                            },
                            success: function(res) {
                                if(res.length > 0){
                                    $.each(res, function(i, item) {
                                        var tdElement = $('td:contains(' + item.sales_weightage.weightage + '%)');
                                        var trElement = tdElement.closest('tr');

                                        $(trElement).find('td').each (function() {
                                            var targetInputtarget = $(this).find('input[name="target[]"]');
                                            var targetInputachivment = $(this).find('input[name="achivment[]"]');
                                            var targetInputacual = $(this).find('input[name="acual[]"]');
                                            var targetInputrating = $(this).find('input[name="rating[]"]');

                                            if (targetInputtarget.length > 0) {
                                                targetInputtarget.val(item.target);
                                            }
                                            if (targetInputachivment.length > 0) {
                                                targetInputachivment.val(item.achivment);
                                            }
                                            if (targetInputacual.length > 0) {
                                                targetInputacual.val(item.acual);
                                            }
                                            if (targetInputrating.length > 0) {
                                                targetInputrating.val(item.rating);
                                            }
                                        });
                                        // console.log(trElement);
                                    });
                                 }else{
                                    $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                                 }
                            }
                        });
                    }else{
                        $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                    }
                }else{
                    $.ajax({
                            url: "{{ url('getappraisal') }}",
                            data: {
                                "executive_id": executive_id,
                                "f_year": f_year,
                                "appraisal_type": appraisal_type,
                            },
                            success: function(res) {
                                 if(res.length > 0){
                                    $.each(res, function(i, item) {
                                        var tdElement = $('td:contains(' + item.sales_weightage.weightage + '%)');
                                        var trElement = tdElement.closest('tr');

                                        $(trElement).find('td').each (function() {
                                            var targetInputtarget = $(this).find('input[name="target[]"]');
                                            var targetInputachivment = $(this).find('input[name="achivment[]"]');
                                            var targetInputacual = $(this).find('input[name="acual[]"]');
                                            var targetInputrating = $(this).find('input[name="rating[]"]');

                                            if (targetInputtarget.length > 0) {
                                                targetInputtarget.val(item.target);
                                            }
                                            if (targetInputachivment.length > 0) {
                                                targetInputachivment.val(item.achivment);
                                            }
                                            if (targetInputacual.length > 0) {
                                                targetInputacual.val(item.acual);
                                            }
                                            if (targetInputrating.length > 0) {
                                                targetInputrating.val(item.rating);
                                            }
                                        });
                                        // console.log(trElement);
                                    });
                                 }else{
                                    $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
                                 }
                            }
                        });
                }
            }else{
                $('input[name="target[]"], input[name="achivment[]"], input[name="acual[]"], input[name="rating[]"]').val('');
            }
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