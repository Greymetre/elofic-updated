<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">ADD NEW LEAD
            <span class="pull-right">
              <div class="btn-group">
                @if(auth()->user()->can(['customer_access']))
                <a href="{{ url('leads') }}" class="btn btn-just-icon btn-theme" title="Leads"><i class="material-icons">next_plan</i></a>
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
          <form method="POST" action="{{ route('leads.store') }}" id="frmLeadsCreate" enctype="multipart/form-data" class="w-100">
            @csrf

            <div class="modal-content lead-modal">
              {{-- Header --}}
              <!-- <div class="modal-header border-0 pb-0">
            <h5 class="modal-title font-weight-bold text-uppercase mb-0">ADD NEW LEAD</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div> -->

              {{-- Body --}}
              <div class="modal-body pt-3">
                <div class="form-row">
                  {{-- Lead Type --}}
                  <div class="form-group col-6 mb-2">
                    <select name="status" id="status" class="custom-select" required>
                      <option value="" disabled selected>Lead Type</option>
                      @foreach($status as $opt)
                      <option value="{{ $opt->id }}" {{ old('status')==$opt->id ? 'selected' : '' }}>{{ $opt->display_name }}</option>
                      @endforeach
                    </select>
                    @error('status') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>

                  {{-- Firm Name --}}
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                      class="form-control form-control-lg" placeholder="Firm Name" required>
                    @error('company_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>
                <div class="form-row">
                  {{-- Customer Name --}}
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}"
                      class="form-control form-control-lg" placeholder="Customer Name" required>
                    @error('contact_name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>

                  {{-- Mobile --}}
                  <div class="form-group col-6 mb-2">
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                      class="form-control form-control-lg" placeholder="Mobile Number" maxlength="15" required>
                    @error('phone_number') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>
                <div class="form-row">
                  {{-- Email --}}
                  <div class="form-group col-6 mb-2">
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                      class="form-control form-control-lg" placeholder="Email Id">
                    @error('email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>

                  {{-- Address --}}
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="address" id="address" value="{{ old('address') }}"
                      class="form-control form-control-lg" placeholder="Address">
                    @error('address') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>

                {{-- Pin / City --}}
                <div class="form-row">
                  <div class="form-group col-3 mb-2">
                    <select class="form-control pincode select2" name="pincode_id" id="pincode_id" onchange="getAddressData()" style="width: 100%;">
                      <option value="">Select {!! trans('panel.global.pincode') !!}</option>
                      @if(@isset($pincodes ))
                      @foreach($pincodes as $pincode)
                      <option value="{!! $pincode['id'] !!}" @if(isset($address) && $address->pincode_id==$pincode['id']) selected @endif >{!! $pincode['pincode'] !!}</option>
                      @endforeach
                      @endif
                    </select>
                    @error('pin_code') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-3 mb-2">
                    <select class="form-control select2 city" name="city_id" id="city_id" onchange="getPincodeList()" style="width: 100%;">
                      @if(isset($address) && $address->city_id)
                      <option value="{!!  $address->city_id !!}">{!! $address->cityname->city_name??'' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.city') !!}</option>
                      @endif
                    </select>
                    @error('city') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>

                  {{-- State / State --}}
                  <div class="form-group col-3 mb-2">
                    <select class="form-control select2 district" name="district_id" id="district_id" onchange="getCityList()" style="width: 100%;">
                      @if(isset($address) && $address->district_id)
                      <option value="{!!  $address->district_id !!}">{!! $address->cityname->city_name??'' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.district') !!}</option>
                      @endif
                    </select>
                    @error('state') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-3 mb-2">
                    <select class="form-control select2 state" name="state_id" id="state_id" onchange="getDistrictList()" style="width: 100%;">
                      @if(isset($address) && $address->state_id)
                      <option value="{!!  $address->state_id !!}">{!! $address->statename->state_name??'' !!}</option>
                      @else
                      <option value="">Select {!! trans('panel.global.state') !!}</option>
                      @endif
                    </select>
                    @error('state_alt') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>

                {{-- Other / Lead Source --}}
                <div class="form-row">
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="other" id="other" value="{{ old('other') }}"
                      class="form-control form-control-lg" placeholder="Other">
                    @error('other') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-6 mb-2">
                    <select name="lead_source" id="lead_source" class="custom-select" required>
                      <option value="" disabled selected>Lead Source</option>
                      @foreach($lead_sources as $src)
                      <option value="{{ $src }}" {{ old('lead_source')==$src ? 'selected' : '' }}>{{ $src }}</option>
                      @endforeach
                    </select>
                    @error('lead_source') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>

                {{-- Assigned To --}}
                <div class="form-row">
                  <div class="form-group col-6 mb-2">
                    <input type="text" name="company_url" id="company_url" value="{{ old('company_url') }}" class="form-control form-control-lg" placeholder="Website">
                    @error('company_url') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                  <div class="form-group col-6 mb-2">
                    <select name="assign_to" id="assign_to" class="custom-select" required>
                      <option value="" disabled selected>Assigned To</option>
                      @foreach($users as $user)
                      <option value="{{ $user->id }}" {{ old('assign_to')==$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                      @endforeach
                    </select>
                    @error('assign_to') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                  </div>
                </div>

                {{-- Note --}}
                <div class="form-group mb-2">
                  <textarea name="note" id="note" rows="3" class="form-control" placeholder="Note">{{ old('note') }}</textarea>
                  @error('note') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                </div>
              </div>

              {{-- Footer --}}
              <div class="modal-footer border-0">
                <button type="submit" class="btn btn-info btn-block lead-submit">SUBMIT</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>

</x-app-layout>