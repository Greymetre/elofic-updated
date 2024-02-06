<x-app-layout>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-icon card-header-theme">
               <div class="card-icon">
                  <i class="material-icons">perm_identity</i>
               </div>
               <h4 class="card-title ">{{ trans('panel.global.create') }} {{ trans('panel.user.title_singular') }}</h4>
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
               <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" id="storeUserData">
                  @csrf
                  <input type="hidden" name="name" id="name" required>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Assign Cities</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control select2 {{ $errors->has('cities') ? 'is-invalid' : '' }}" name="cities[]" id="cities" multiple required>
                                    @foreach($cities as $id => $cities)
                                    <option value="{{ $id }}" {{ (in_array($id, old('cities', [])) || $user->cities->contains($id)) ? 'selected' : '' }}>{{ $cities }}</option>
                                    @endforeach
                                 </select>
                                 @if ($errors->has('cities'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('cities') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Reporting To</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control select2" name="reportingid" required>
                                    <option value="" disabled selected>Select Reporting</option>
                                    @if(@isset($reportings ))
                                    @foreach($reportings as $reporting)
                                    <option value="{!! $reporting['id'] !!}" {{ old( 'reportingid' , (!empty($user->reportingid)) ? ($user->reportingid) :('') ) == $reporting['id'] ? 'selected' : '' }}>{!! $reporting['name'] !!}</option>
                                    @endforeach
                                    @endif
                                 </select>
                                 @if($errors->has('reportingid'))
                                 <div class="invalid-feedback">
                                    {{ $errors->first('reportingid') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-4 col-form-label">{{ trans('panel.user.fields.first_name') }} <span class="text-danger"> *</span></label>
                           <div class="col-md-8">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="first_name" class="form-control {{ $errors->has('first_name') ? 'is-invalid' : '' }}" value="{!! old( 'first_name') !!}" maxlength="200" required onchange="getfullName();">
                                 @if ($errors->has('first_name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('first_name') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-4 col-form-label">{{ trans('panel.user.fields.last_name') }}<span class="text-danger"> *</span></label>
                           <div class="col-md-8">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="last_name" class="form-control {{ $errors->has('last_name') ? 'is-invalid' : '' }}" value="{!! old( 'last_name') !!}" maxlength="200" required>
                                 @if ($errors->has('last_name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('last_name') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="row">
                           <label class="col-md-2 col-form-label">{!! trans('panel.global.email') !!}<span class="text-danger"> *</span></label>
                           <div class="col-md-10">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="email" name="email" class="form-control" value="{!! old( 'email') !!}" maxlength="200" required>
                                 @if ($errors->has('email'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('email') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-4 col-form-label">{!! trans('panel.global.mobile') !!}<span class="text-danger"> *</span></label>
                           <div class="col-md-8">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="number" name="mobile" class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}" value="{!! old( 'mobile') !!}" maxlength="10" minlength="10" required>
                              </div>
                              @if ($errors->has('mobile'))
                              <label class="error">{{ $errors->first('mobile') }}</label>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-4 col-form-label">{{ trans('panel.user.fields.password') }}<span class="text-danger"> *</span></label>
                           <div class="col-md-8">
                              <div class="form-group has-default bmd-form-group">
                                 <input class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" type="password" name="password" id="password" value="{{ old('password') }}" minlength="6" maxlength="200" required>
                                 @if ($errors->has('password'))
                                 <label class="error">{{ $errors->first('password') }}</label>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="row">
                           <label class="col-md-2 col-form-label">{{ trans('panel.user.fields.roles') }}</label>
                           <div class="col-md-10">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control select2" name="roles[]" id="roles" multiple required>
                                    @foreach($roles as $id => $roles)
                                    <option value="{{ $id }}" {{ in_array($id, old('roles', [])) ? 'selected' : '' }}>{{ $roles }}</option>
                                    @endforeach
                                 </select>
                                 @if ($errors->has('roles'))
                                 <label class="error">{{ $errors->first('roles') }}</label>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Employee Information </h4>
                  <hr class="my-3">
                  <div class="row">
           
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{{ trans('panel.user.fields.gender') }}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="selectpicker" data-style="select-with-transition" name="gender">
                                    <option value="" disabled selected>Gender</option>
                                    <option value="Male" {{ (old('gender') == 'Male') ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ (old('gender') == 'Female') ? 'selected' : '' }}>Female</option>
                                 </select>
                              </div>
                           </div>
                           @if ($errors->has('gender'))
                           <div class="error col-lg-12">
                              <p class="text-danger">{{ $errors->first('gender') }}</p>
                           </div>
                           @endif
                        </div>
                     </div>
            <!--          <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Location</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="location" class="form-control" value="{!! old( 'location', $user['location']) !!}">
                                 @if ($errors->has('location'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('location') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div> -->
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.marital_status') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="marital_status" required>
                                    <option value="" disabled selected>Marital Status</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Separated">Separated</option>
                                    <option value="Widowed">Widowed</option>
                                    <option value="Divorced">Divorced</option>
                                 </select>
                              </div>
                              @if ($errors->has('marital_status'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('marital_status') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="date_of_birth" id="date_of_birth" class="form-control datepicker" value="{!! old( 'date_of_birth', $user['date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>

                      <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.age') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="age" id="age" readonly class="form-control" value="{!! old( 'age') !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('age'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('age') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>



                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.pan_number') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="pan_number" id="pan_number" class="form-control" value="{!! old( 'pan_number') !!}">
                                 @if ($errors->has('pan_number'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('pan_number') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.aadhar_number') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="aadhar_number" id="aadhar_number" class="form-control" value="{!! old( 'aadhar_number') !!}">
                                 @if ($errors->has('aadhar_number'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('aadhar_number') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>




                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.emergency_number') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="number" name="emergency_number" class="form-control" value="{!! old( 'emergency_number', $user['emergency_number']) !!}">
                                 @if ($errors->has('emergency_number'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('emergency_number') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="row">
                           <label class="col-md-2 col-form-label">{!! trans('panel.user.current_address') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="current_address" class="form-control" value="{!! old( 'current_address', $user['current_address']) !!}">
                                 @if ($errors->has('current_address'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('current_address') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                           <div class="col-md-1">
                              <label class="form-check-label form-group">
                                 <input class="form-check-input copyaddreess" type="checkbox" aria-required="true">Copy Address
                                 <span class="form-check-sign">
                                    <span class="check"></span>
                                 </span>
                              </label>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12">
                        <div class="row">
                           <label class="col-md-2 col-form-label">{!! trans('panel.user.permanent_address') !!}</label>
                           <div class="col-md-10">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="permanent_address" class="form-control permanentAddress" value="{!! old( 'permanent_address', $user['permanent_address']) !!}">
                                 @if ($errors->has('permanent_address'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('permanent_address') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>


                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Family Information</h4>
                  <hr class="my-3">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.father_name') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="father_name" class="form-control" value="{!! old( 'father_name', $user['father_name']) !!}">
                                 @if ($errors->has('father_name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('father_name') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.father_date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="father_date_of_birth" class="form-control datepicker" value="{!! old( 'father_date_of_birth', $user['father_date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('father_date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('father_date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.mother_name') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="mother_name" class="form-control" value="{!! old( 'mother_name', $user['mother_name']) !!}">
                                 @if ($errors->has('mother_name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('mother_name') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.mother_date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="mother_date_of_birth" class="form-control datepicker" value="{!! old( 'mother_date_of_birth', $user['mother_date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('mother_date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('mother_date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">  
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.marriage_anniversary') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="marriage_anniversary" class="form-control datepicker" value="{!! old( 'marriage_anniversary', $user['marriage_anniversary']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('marriage_anniversary'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('marriage_anniversary') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6"> 
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.spouse_name') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="spouse_name" class="form-control" value="{!! old( 'spouse_name', $user['spouse_name']) !!}">
                                 @if ($errors->has('spouse_name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('spouse_name') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">  
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.spouse_date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="spouse_date_of_birth" class="form-control datepicker" value="{!! old( 'spouse_date_of_birth', $user['spouse_date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('spouse_date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('spouse_date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6"> 
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_one') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_one" class="form-control" value="{!! old( 'children_one', $user['children_one']) !!}">
                                 @if ($errors->has('children_one'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('children_one') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">  
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_one_date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_one_date_of_birth" class="form-control datepicker" value="{!! old( 'children_one_date_of_birth', $user['children_one_date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('children_one_date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('children_one_date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6"> 
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_two') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_two" class="form-control" value="{!! old( 'children_two', $user['children_two']) !!}">
                                 @if ($errors->has('children_two'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('children_two') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">  
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_two_date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_two_date_of_birth" class="form-control datepicker" value="{!! old( 'children_two_date_of_birth', $user['children_two_date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('children_two_date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('children_two_date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">   
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_three') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_three" class="form-control" value="{!! old( 'children_three', $user['children_three']) !!}">
                                 @if ($errors->has('children_three'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('children_three') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     
                     <div class="col-md-6">
                        <div class="row">  
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_three_date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_three_date_of_birth" class="form-control datepicker" value="{!! old( 'children_three_date_of_birth', $user['children_three_date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('children_three_date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('children_three_date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>

                    <div class="col-md-6">   
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_four') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_four" class="form-control" value="{!! old( 'children_four', $user['children_four']) !!}">
                                 @if ($errors->has('children_four'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('children_four') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     
                     <div class="col-md-6">
                        <div class="row">  
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_four_date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_four_date_of_birth" class="form-control datepicker" value="{!! old( 'children_four_date_of_birth', $user['children_four_date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('children_four_date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('children_four_date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">   
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_five') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_five" class="form-control" value="{!! old( 'children_five', $user['children_five']) !!}">
                                 @if ($errors->has('children_five'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('children_five') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     
                     <div class="col-md-6">
                        <div class="row">  
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.children_five_date_of_birth') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="children_five_date_of_birth" class="form-control datepicker" value="{!! old( 'children_five_date_of_birth', $user['children_five_date_of_birth']) !!}" autocomplete="off">
                              </div>
                              @if ($errors->has('children_five_date_of_birth'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('children_five_date_of_birth') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>





                  </div>


                


            
                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Bank Information </h4>
                  <hr class="my-3">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.account_number') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="account_number" id="account_number" class="form-control" value="{!! old( 'account_number', $user['account_number']) !!}">
                                 @if ($errors->has('account_number'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('account_number') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.bank_name') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="bank_name" class="form-control" value="{!! old( 'bank_name', $user['bank_name']) !!}">
                                 @if ($errors->has('bank_name'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('bank_name') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.ifsc_code') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="ifsc_code" class="form-control" value="{!! old( 'ifsc_code', $user['ifsc_code']) !!}">
                                 @if ($errors->has('ifsc_code'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('ifsc_code') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>


                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">HR Information </h4>
                  <hr class="my-3">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.salary') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="salary" class="form-control" value="{!! old( 'salary', $user['salary']) !!}">
                                 @if ($errors->has('salary'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('salary') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                  
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.ctc_annual') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="ctc_annual" class="form-control" value="{!! old( 'ctc_annual') !!}">
                                 @if ($errors->has('ctc_annual'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('ctc_annual') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.gross_salary_monthly') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="gross_salary_monthly" class="form-control" value="{!! old( 'gross_salary_monthly') !!}">
                                 @if ($errors->has('gross_salary_monthly'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('gross_salary_monthly') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                    <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.last_year_increments') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="last_year_increments" class="form-control" value="{!! old( 'last_year_increments') !!}">
                                 @if ($errors->has('last_year_increments'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('last_year_increments') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                   
                      
                      <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.last_year_increment_percent') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="last_year_increment_percent" class="form-control" value="{!! old( 'last_year_increment_percent') !!}">
                                 @if ($errors->has('last_year_increment_percent'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('last_year_increment_percent') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.last_year_increment_value') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="last_year_increment_value" class="form-control" value="{!! old( 'last_year_increment_value') !!}">
                                 @if ($errors->has('last_year_increment_value'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('last_year_increment_value') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.last_promotion') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="last_promotion" class="form-control" value="{!! old( 'last_promotion', $user['last_promotion']) !!}">
                                 @if ($errors->has('last_promotion'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('last_promotion') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.pf_number') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="pf_number" class="form-control" value="{!! old( 'pf_number', $user['pf_number']) !!}">
                                 @if ($errors->has('pf_number'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('pf_number') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.un_number') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="un_number" class="form-control" value="{!! old( 'un_number', $user['un_number']) !!}">
                                 @if ($errors->has('un_number'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('un_number') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.esi_number') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="esi_number" class="form-control" value="{!! old( 'esi_number', $user['esi_number']) !!}">
                                 @if ($errors->has('esi_number'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('esi_number') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.probation_period') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="probation_period" class="form-control datepicker" value="{!! old( 'probation_period', $user['probation_period']) !!}">
                                 @if ($errors->has('probation_period'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('probation_period') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.date_of_confirmation') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="date_of_confirmation" class="form-control datepicker" value="{!! old( 'date_of_confirmation', $user['date_of_confirmation']) !!}" autocomplete="off">
                                 @if ($errors->has('date_of_confirmation'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('date_of_confirmation') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.notice_period') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="notice_period" class="form-control" value="{!! old( 'notice_period', $user['notice_period']) !!}">
                                 @if ($errors->has('notice_period'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('notice_period') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.date_of_leaving') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="date_of_leaving" class="form-control datepicker" value="{!! old( 'date_of_leaving', $user['date_of_leaving']) !!}">
                                 @if ($errors->has('date_of_leaving'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('date_of_leaving') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                  </div>   



                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Joining Information </h4>
                  <hr class="my-3">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.date_of_joining') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="date_of_joining" class="form-control datepicker" value="{!! old( 'date_of_joining', $user['date_of_joining']) !!}" autocomplete="off">
                                 @if ($errors->has('date_of_joining'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('date_of_joining') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
      
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Head Quarter</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="location" class="form-control" value="{!! old( 'location', $user['location']) !!}">
                                 @if ($errors->has('location'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('location') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.employee_codes') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="employee_codes" id="employee_codes" class="form-control" value="{!! old( 'employee_codes', $user['employee_codes']) !!}" required>
                                 @if ($errors->has('employee_codes'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('employee_codes') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.biometric_code') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="biometric_code" id="biometric_code" class="form-control" value="{!! old( 'biometric_code', $user['biometric_code']) !!}">
                                 @if ($errors->has('biometric_code'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('biometric_code') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.designation') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="designation_id">
                                    <option value="" disabled selected>Select {!! trans('panel.user.designation') !!}</option>
                                    @foreach($designations as $designation)
                                    <option value="{{$designation->id}}"> {{$designation->designation_name}}</option>
                                    @endforeach
                                 </select>
                                 @if($errors->has('designation_id'))
                                 <div class="invalid-feedback">
                                    {{ $errors->first('designation_id') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.branch_name') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <div class="form-group has-default bmd-form-group">
                                    <select class="form-control" name="branch_id" required>
                                       <option value="" disabled selected>Select Branch</option>
                                       @foreach($branches as $branche)
                                       <option value="{{$branche->id}}">{{$branche->branch_name}}</option>
                                       @endforeach
                                    </select>
                                 </div>
                                 @if ($errors->has('branch_id'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('branch_id') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>



                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.division') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="division_id">
                                    <option value="" disabled selected>Select {!! trans('panel.user.division') !!}</option>
                                    @foreach($divisions as $division)
                                    <option value="{{$division->id}}">{{$division->division_name}}</option>
                                    @endforeach
                                 </select>
                                 @if($errors->has('division_id'))
                                 <div class="invalid-feedback">
                                    {{ $errors->first('division_id') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>


                              
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.department') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control" name="department_id">
                                    <option value="" disabled selected>Select {!! trans('panel.user.department') !!}</option>
                                    @foreach($departments as $department)
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                    @endforeach
                                 </select>
                                 @if($errors->has('department_id'))
                                 <div class="invalid-feedback">
                                    {{ $errors->first('department_id') }}
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

   



                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.order_mail') !!}<span class="text-danger"> *</span></label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="order_mails" id="order_mails" class="form-control" value="{!! old( 'order_mails', $user['order_mails']) !!}">
                                 @if ($errors->has('order_mails'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('order_mails') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">Order Mails Type<span class="text-danger"> *</span></label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <select class="form-control select2" multiple name="order_mails_type[]" style="width: 100%;" required id="type">
                                    <option value="">Select {!! trans('panel.customers.fields.customertype') !!}</option>
                                    @if(@isset($customertype ))
                                    @foreach($customertype as $type)
                                    <option value="{!! $type['id'] !!}" {{ old( 'order_mails_type' , (!empty($user['order_mails_type']))?($user['order_mails_type']):('') ) == $type['id'] ? 'selected' : '' }}>{!! $type['customertype_name'] !!}</option>
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                              @if ($errors->has('customertype'))
                              <div class="error col-lg-12">
                                 <p class="text-danger">{{ $errors->first('customertype') }}</p>
                              </div>
                              @endif
                           </div>
                        </div>
                     </div>

                  </div>
                  <hr class="my-3">
                  <h4 class="section-heading mb-3  h4 mt-0 text-center text-info">Educational Information </h4>
                  <hr class="my-3">
                  <div class="row">
                     <div class="table-responsive">
                        <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                           <thead>
                              <tr class="item-row">
                                 <th>Degree</th>
                                 <th>University</th>
                                 <th>Percentage</th>
                                 <th>Grade</th>
                                 <th>Upload File</th>
                              </tr>
                           </thead>
                           <tbody>
                              <tr class="item-row">
                                 <td>
                                    <input type="text" name="detail[1][degree_name]" placeholder="High School">
                                 </td>
                                 <td>
                                    <input type="text" name="hsc_board_name">
                                 </td>
                                 <td>
                                    <input type="text" name="hsc_percentage">
                                 </td>
                                 <td>
                                    <input type="text" name="hsc_grade">
                                 </td>
                                 <td>
                                    <img src="{!! asset('assets/img/placeholder.jpg') !!}" class="imagepreview1" width="70px;">
                                    <span class="btn btn-just-icon btn-round btn-file" style="position: relative; top: -20px;">
                                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                       <input type="file" name="hsc_image" class="getimage1">
                                    </span>
                                 </td>
                              </tr>
                              <tr class="item-row">
                                 <td>
                                    <input type="text" name="detail[2][degree_name]" placeholder="Higher Secondry">
                                 </td>
                                 <td>
                                    <input type="text" name="hssc_board_name">
                                 </td>
                                 <td>
                                    <input type="text" name="hssc_percentage">
                                 </td>
                                 <td>
                                    <input type="text" name="hssc_grade">
                                 </td>
                                 <td>
                                    <img src="{!! asset('assets/img/placeholder.jpg') !!}" class="imagepreview2" width="70px;">
                                    <span class="btn btn-just-icon btn-round btn-file" style="position: relative; top: -20px;">
                                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                       <input type="file" name="hssc_image" class="getimage2">
                                    </span>
                                 </td>
                              </tr>
                              <tr class="item-row">
                                 <td>
                                    <input type="text" name="ug_course" placeholder="Graduction Course">
                                 </td>
                                 <td>
                                    <input type="text" name="ug_university">
                                 </td>
                                 <td>
                                    <input type="text" name="ug_percentage">
                                 </td>
                                 <td>
                                    <input type="text" name="ug_grade">
                                 </td>
                                 <td>
                                    <img src="{!! asset('assets/img/placeholder.jpg') !!}" class="imagepreview3" width="70px;">
                                    <span class="btn btn-just-icon btn-round btn-file" style="position: relative; top: -20px;">
                                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                       <input type="file" name="ug_image" class="getimage3">
                                    </span>
                                 </td>
                              </tr>
                              <tr class="item-row">
                                 <td>
                                    <input type="text" name="pg_course" placeholder="Post Graduction Course">
                                 </td>
                                 <td>
                                    <input type="text" name="pg_university">
                                 </td>
                                 <td>
                                    <input type="text" name="pg_percentage">
                                 </td>
                                 <td>
                                    <input type="text" name="pg_grade">
                                 </td>
                                 <td>
                                    <img src="{!! asset('assets/img/placeholder.jpg') !!}" class="imagepreview4" width="70px;">
                                    <span class="btn btn-just-icon btn-round btn-file" style="position: relative; top: -20px;">
                                       <span class="fileinput-new"><i class="fa fa-pencil"></i></span>
                                       <input type="file" name="pg_image" class="getimage4">
                                    </span>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>


                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.other_education') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="other_education" id="other_education" class="form-control" value="{!! old( 'other_education') !!}">
                                 @if ($errors->has('other_education'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('other_education') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
        
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.current_company_tenture') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="current_company_tenture" id="current_company_tenture" class="form-control" value="{!! old( 'current_company_tenture') !!}">
                                 @if ($errors->has('current_company_tenture'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('current_company_tenture') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                      <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.previous_exp') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="previous_exp" id="previous_exp" class="form-control" value="{!! old( 'previous_exp') !!}">
                                 @if ($errors->has('previous_exp'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('previous_exp') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <label class="col-md-3 col-form-label">{!! trans('panel.user.total_exp') !!}</label>
                           <div class="col-md-9">
                              <div class="form-group has-default bmd-form-group">
                                 <input type="text" name="total_exp" id="total_exp" class="form-control" value="{!! old( 'total_exp') !!}">
                                 @if ($errors->has('total_exp'))
                                 <div class="error col-lg-12">
                                    <p class="text-danger">{{ $errors->first('total_exp') }}</p>
                                 </div>
                                 @endif
                              </div>
                           </div>
                        </div>
                     </div>



                  </div>
                  <div class="row pull-right">
                     {{ Form::submit('Submit', array('class' => 'btn btn-info submituser')) }}
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
   <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
   <script src="{{ url('/').'/'.asset('assets/js/validation_users.js') }}"></script>
   <script type="text/javascript">
      $(document).ready(function() {
         $('.submituser').click(function() {

         });
      });
      $('.copyaddreess').click(function() {
         var checked = $(this).is(':checked');
         if (checked == true) {
            var currentAddress = $("input[name=current_address]").val();
            $('.permanentAddress').val(currentAddress);
         } else {
            $('.permanentAddress').val('');
         }
      });

      function getfullName() {
         var first_name = $("input[name=first_name]").val();
         var last_name = $("input[name=last_name]").val();
         $('#name').val(first_name + ' ' + last_name);
      }
      $(document).on('change', '#date_of_birth', function(){
         var DOB = $(this).val();
         dob = new Date(DOB);
         var today = new Date();
         var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
         $('#age').val(age);
      })
   </script>
</x-app-layout>