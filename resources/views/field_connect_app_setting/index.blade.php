<x-app-layout>
   <style>
      .row.image_preview.p-4.m-4 {
         border: 1px solid lightgrey;
         border-radius: 10px;
      }

      .img-div {
         position: relative;
      }

      span.delete-img {
         position: absolute;
         top: 0px;
         right: 12px;
         background: #f73232;
         color: #fff;
         border-radius: 50%;
         width: 16px;
         height: 16px;
         text-align: center;
         font-size: 14px;
         line-height: 17px;
         font-weight: 900;
         cursor: pointer;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h4 class="card-title ">
                         FieldKonnect App Setting
                        @if(auth()->user()->can(['district_access']))
                        <!-- <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('schemes') }}">
                                 <i class="material-icons">next_plan</i> {!! trans('panel.scheme.title') !!}
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                        </ul> -->
                        @endif
                     </h4>
                  </div>
               </div>
            </div>
            @if (session('success'))
            <div class="alert alert-success mt-3">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               {{ session('success') }}
            </div>
            @endif
            <div class="alert mt-3" style="display: none;">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               <span class="message"></span>
            </div>
            @if (session('error'))
            <div class="alert alert-danger mt-3">
               <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <i class="material-icons">close</i>
               </button>
               {{ session('error') }}
            </div>
            @endif
          
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
               {!! Form::model($field_konnect_app_setting,[
               'route' => 'field-konnect-app-setting.store',
               'method' => 'POST',
               'id' => 'storeLoyaltyAppSetting',
               'files'=>true
               ]) !!}
              
           
              
               <div class="row">
                 
                  <div class="col-md-6">
                     <div class="form-group">
                        <div class="row">
                           <div class="col-md-3">
                              <label class="form-control">App Version </label>
                           </div>
                           <div class="col-md-9">
                              <input type="number" name="app_version" placeholder="1.01" step="0.01" class="form-control" id="app_version" value="{{old('app_version', $field_konnect_app_setting['app_version'])}}" required>
                              @if ($errors->has('app_version'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('app_version') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
              
            

               <div class="card-footer pull-right">
                  {{ Form::submit('Submit', array('class' => 'btn btn-theme')) }}
               </div>
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/validation_loyalty.js') }}"></script>
   <script>
   //    $('body').on('click', '.delete-img', function() {
   //       var deleteButton = $(this);
   //       var id = $(this).data("id");
   //       var token = $("meta[name='csrf-token']").attr("content");
   //       if (!confirm("Are You sure want to delete ?")) {
   //          return false;
   //       }
   //       $.ajax({
   //          url: "{{ url('loyalty-app-setting') }}" + '/' + id,
   //          type: 'DELETE',
   //          data: {
   //             _token: token,
   //             id: id
   //          },
   //          success: function(data) {
   //             $('.message').empty();
   //             $('.alert').show();
   //             if (data.status == 'success') {
   //                deleteButton.parent('div').addClass('d-none');
   //                $('.alert').addClass("alert-success");
   //             } else {
   //                $('.alert').addClass("alert-danger");
   //             }
   //             $('.message').append(data.message);
   //          },
   //       });
   //    });
   // </script>
</x-app-layout>