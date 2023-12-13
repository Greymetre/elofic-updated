<x-app-layout>
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title ">Tour List
              <span class="pull-right">
                <div class="btn-group">


                <form method="GET" action="{{ URL::to('tours-download') }}">
                  <div class="d-flex flex-row">

                  <div class="p-2">
                      <select class="form-control" name="executive_id" id="executive_id" data-style="select-with-transition" title="Select User">
                     <option value="">Select User</option>
                    @if(@isset($users ))
                    @foreach($users as $user)
                     <option value="{!! $user['id'] !!}" {{ old( 'executive_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
                    @endforeach
                    @endif
                   </select>
                  </div>

      

                <div class="p-2"><input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Start Date" autocomplete="off" readonly></div>
                    <div class="p-2"><input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="End Date" autocomplete="off" readonly></div>
                    <div class="p-2"><button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.tours.title') !!}"><i class="material-icons">cloud_download</i></button></div>
                  </div>
              </form>




                  <form action="{{ URL::to('tours-upload') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
                  {{ csrf_field() }}
                  <div class="input-group">
                      <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                        <span class="btn btn-just-icon btn-theme btn-file">
                          <span class="fileinput-new"><i class="material-icons">attach_file</i></span>
                          <span class="fileinput-exists">Change</span>
                          <input type="hidden">
                          <input type="file" name="import_file" required accept=".xls,.xlsx" />
                        </span>
                      </div>
                    <div class="input-group-append">
                      <button class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.upload') !!} {!! trans('panel.tour.title') !!}">
                        <i class="material-icons">cloud_upload</i>
                        <div class="ripple-container"></div>
                      </button>
                    </div>
                  </div>
                  </form>
                  <!-- <a href="{{ URL::to('tours-download') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.download') !!} {!! trans('panel.tour.title') !!}"><i class="material-icons">cloud_download</i></a> -->
                  @if(auth()->user()->can(['tour_upload']))

                  @endif
                  @if(auth()->user()->can(['tour_download']))
             
                  @endif
                  @if(auth()->user()->can(['tour_template']))
                  <a href="{{ URL::to('tours-template') }}" class="btn btn-just-icon btn-theme" title="{!!  trans('panel.global.template') !!} {!! trans('panel.tour.title_singular') !!}"><i class="material-icons">text_snippet</i></a>
                  @endif
                  @if(auth()->user()->can(['tour_create']))
                   <a data-toggle="modal" data-target="#createCategory" class="btn btn-just-icon btn-theme create" title="{!!  trans('panel.global.add') !!} {!! trans('panel.tour.title_singular') !!}"><i class="material-icons">add_circle</i></a>
                  @endif
                  <a href="{{ route('tours.create') }}" class="btn btn-just-icon btn-theme"><i class="material-icons">add_circle</i></a>
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
          <table id="gettour" class="table table-striped- table-bordered table-hover table-checkable responsive no-wrap">
            <thead class=" text-primary">
              <th>{!! trans('panel.global.no') !!}</th>
              <th>{!! trans('panel.global.action') !!}</th>
              <th>User Name</th>
              <th>Date</th>
              <th>Town</th>
              <th>Objectives</th>
              <th>Type</th>
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
<div class="modal fade bd-example-modal-lg" id="createCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content card">
      <div class="card-header card-header-icon card-header-theme">
        <div class="card-icon">
          <i class="material-icons">perm_identity</i>
        </div>
        <h4 class="card-title"><span class="modal-title">Edit</span> Tour
          <span class="pull-right" >
            <a href="javascript:void(0)" class="btn btn-just-icon btn-danger" data-dismiss="modal"><i class="material-icons">clear</i></a>
          </span>
        </h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{ route('tours.toursInfoUpdate') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">Date<span class="text-danger"> *</span></label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="date" id="date" class="form-control datepicker" autocomplete="off" required/>
                      @if ($errors->has('date'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('date') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
               <div class="col-md-6">
                <div class="row">
                  <label class="col-md-3 col-form-label">User<span class="text-danger"> *</span></label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <select class="form-control rowchange select2" name="userid" id="userid" required style="width: 100%;">
                         @if(@isset($users))
                         <option value="">Select User</option>
                         @foreach($users as $user )
                         <option value="{{ $user['id'] }}">{{ $user['name'] }}</option>
                         @endforeach
                         @endif
                      </select>
                      @if ($errors->has('userid'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('userid') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <label class="col-md-2 col-form-label">Town<span class="text-danger"> *</span></label>
                  <div class="col-md-10">
                    <div class="form-group has-default bmd-form-group">
                      <input type="text" name="town" id="town" class="form-control" required/>
                      @if ($errors->has('town'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('town') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="row">
                  <label class="col-md-3 col-form-label">Objectives</label>
                  <div class="col-md-9">
                    <div class="form-group has-default bmd-form-group">
                      <textarea class="form-control" name="objectives" id="objectives" rows="5"></textarea>
                      @if ($errors->has('objectives'))
                        <div class="error col-lg-12"><p class="text-danger">{{ $errors->first('objectives') }}</p></div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
          </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
          <input type="hidden" name="id" id="tour_id" />
          <button class="btn btn-info save"> Submit</button>
        </form>
        </div>
      </div>
    </div>
  </div>
<script src="{{ asset('public/assets/js/jquery.custom.js') }}"></script>
<script src="{{ asset('public/assets/js/validation_products.js') }}"></script>
<script type="text/javascript">
  $(function () {
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    var table = $('#gettour').DataTable({
        processing: true,
        serverSide: true,
        "order": [ [0, 'desc'] ],
        ajax: "{{ route('tours.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            {data: 'action', name: 'action',"defaultContent": '',className: 'td-actions text-center', orderable: false, searchable: false},
             {data: 'userinfo.name', name: 'userinfo.name',"defaultContent": '', orderable: false, searchable: false},
            {data: 'date', name: 'date',"defaultContent": ''},
            {data: 'town', name: 'town',"defaultContent": ''},
            {data: 'objectives', name: 'objectives',"defaultContent": ''},
            {data: 'type', name: 'type',"defaultContent": ''},
        ]
    });
         
    $(document).on('click', '.edit', function(){
      var base_url =$('.baseurl').data('baseurl');
      var id = $(this).attr('id');
      $.ajax({
        url: base_url + '/tours/'+id,
       dataType:"json",
       success:function(data)
       {
        $('#date').val(data.date);
        $('#userid').val(data.userid).change();
        $('#town').val(data.town);
        $('#objectives').val(data.objectives);
        $('#tour_id').val(data.id);
        var title = '{!! trans('panel.global.edit') !!}' ;
        $('.modal-title').text(title);
        $('#action_button').val('Edit');
        $('#createCategory').modal('show');
       }
      })
     });
    
    $('body').on('click', '.tourActive', function () {
        var id = $(this).attr("id");
        var active = $(this).attr("value");
        var status = '';
        if(active == 'Y')
        {
          status = 'Incative ?';
        }
        else
        {
           status = 'Ative ?';
        }
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want "+status)) {
           return false;
        }
        $.ajax({
            url: "{{ url('tours-active') }}",
            type: 'POST',
            data: {_token: token,id: id,active:active},
            success: function (data) {
              $('.message').empty();
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
                $('.alert').addClass("alert-danger");
              }
              $('.message').append(data.message);
              table.draw();
            },
        });
    });
    $('.create').click(function () {
        $('#tour_id').val('');
        $('#createCategoryForm').trigger("reset");
        $("#tour_image").attr({ "src": '{!! asset('public/assets/img/placeholder.jpg') !!}' });
        $('.modal-title').text('{!! trans('panel.global.add') !!}');
    });
    
    $('body').on('click', '.delete', function () {
        var id = $(this).attr("value");
        var token = $("meta[name='csrf-token']").attr("content");
        if(!confirm("Are You sure want to delete ?")) {
           return false;
        }
        $.ajax({
            url: "{{ url('tours') }}"+'/'+id,
            type: 'DELETE',
            data: {_token: token,id: id},
            success: function (data) {
              $('.alert').show();
              if(data.status == 'success')
              {
                $('.alert').addClass("alert-success");
              }
              else
              {
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
