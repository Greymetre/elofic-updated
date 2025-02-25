<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">Claim Generation
            <span class="pull-right">
              <div class="btn-group">
                <!-- @if(auth()->user()->can(['product_access'])) -->
                <a href="{{ url('claim-generation') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.product.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
                <!-- @endif -->
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
          {!! Form::model($claimGeneration,[
          'route' => $claimGeneration->exists ? ['claim-generation.update', encrypt($claimGeneration->id) ] : 'claim-generation.store',
          'method' => $claimGeneration->exists ? 'PUT' : 'POST',
          'id' => 'createClaimForm',
          'files'=>true
          ]) !!}

          <div class="row">
            <div class="col-12" >
                <h4 class="mb-3" style="color: #7c7c7c;font-weight: bold !important;">{{isset($claimGeneration->service_center_details) ?  $claimGeneration->service_center_details->name : ''}}</h4>
                <hr>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">ASC's Bill No<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control" name="asc_bill_no" id="asc_bill_no">
                  @if ($errors->has('asc_bill_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('asc_bill_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">ASC's Bill Date<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control datepicker" name="asc_bill_date" id="asc_bill_date" readonly>
                  @if ($errors->has('asc_bill_date'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('asc_bill_date') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
             <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">ASC's Bill Amount<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control" name="asc_bill_amount" id="asc_bill_amount">
                  @if ($errors->has('asc_bill_amount'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('asc_bill_amount') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">ASC's Bill Date<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <input type="text" class="form-control datepicker" name="asc_bill_date" id="asc_bill_date" readonly>
                  @if ($errors->has('asc_bill_date'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('asc_bill_date') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
          <div class="pull-right col-md-12">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
          </div>
        </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  </div>
<script>
  $(document).ready(function (){
       $('#createsopForm').validate({
        rules:{
          branch_id:
          {
            required:true,
          },
          product_id : {
            required : true,
          },
          plan_next_month : {
            required : true,
            number : true
          }
        },
        errorPlacement: function(error, element) {
            error.addClass('text-danger'); // Add Bootstrap error styling
            error.insertAfter(element.closest('.form-group')); // Insert after the select field
        },
        highlight: function(element) {
            $(element).addClass('is-invalid'); // Highlight error
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid'); // Remove error highlight
        }
      });

      $("#asc_bill_date").datepicker({
        dateFormat: 'yy-mm-dd',
        maxDate: 0, // Disable future dates
      });
  })
</script>
</x-app-layout>