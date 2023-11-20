<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-tabs card-header-warning">
            <div class="nav-tabs-navigation">
               <div class="nav-tabs-wrapper">
                  <h4 class="card-title ">
                     {!! trans('panel.scheme.title_singular') !!}
                     @if(auth()->user()->can(['district_access']))
                     <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                        <li class="nav-item">
                           <a class="nav-link" href="{{ url('schemes') }}">
                              <i class="material-icons">next_plan</i> {!! trans('panel.scheme.title') !!}
                              <div class="ripple-container"></div>
                           </a>
                        </li>
                     </ul>
                     @endif
                  </h4>
               </div>
            </div>
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
            {!! Form::model($schemes,[
            'route' => $schemes->exists ? ['schemes.update', encrypt($schemes->id) ] : 'schemes.store',
            'method' => $schemes->exists ? 'PUT' : 'POST',
            'id' => 'storeSchemeData',
            'files'=>true
            ]) !!}
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="bmd-label-floating">{!! trans('panel.scheme.fields.scheme_name') !!} </label>
                     <input type="text" name="scheme_name" class="form-control" value="{!! old( 'scheme_name', $schemes['scheme_name']) !!}" >
                     @if ($errors->has('scheme_name'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('scheme_name') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="bmd-label-floating">{!! trans('panel.scheme.fields.point_value') !!} </label>
                     <input type="text" name="point_value" class="form-control" value="{!! old( 'point_value', $schemes['point_value']) !!}" >
                     @if ($errors->has('point_value'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('point_value') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label class="bmd-label-floating">{!! trans('panel.scheme.fields.start_date') !!} </label>
                     <input type="text" name="start_date" class="form-control datepicker" value="{!! old( 'start_date', $schemes['start_date']) !!}" autocomplete="off" readonly>
                     @if ($errors->has('start_date'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('start_date') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <label class="bmd-label-floating">{!! trans('panel.scheme.fields.end_date') !!} </label>
                     <input type="text" name="end_date" class="form-control datepicker" value="{!! old( 'end_date', $schemes['end_date']) !!}" autocomplete="off" readonly>
                     @if ($errors->has('end_date'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('end_date') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                     <select name="scheme_type" class="form-control" id="schemetype" >
                        <option value="">{!! trans('panel.scheme.fields.scheme_type') !!}</option>
                        <option value="invoiceValue" {!! ($schemes->scheme_type == 'invoiceValue' ) ? "selected" : ''!!}>Invoice Amount</option>
                        <option value="productValue" {!! ($schemes->scheme_type == 'productValue' ) ? "selected" : ''!!}>Product Amount</option>
                        <option value="productQty" {!! ($schemes->scheme_type == 'productQty' ) ? "selected" : ''!!}>Product Quantity</option>
                        <option value="couponCode" {!! ($schemes->scheme_type == 'couponCode' ) ? "selected" : ''!!}>Coupons</option>
                        <option value="redemption" {!! ($schemes->scheme_type == 'redemption' ) ? "selected" : ''!!}>Redemption</option>
                     </select>
                     @if ($errors->has('scheme_type'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('scheme_type') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="bmd-label-floating">{!! trans('panel.scheme.fields.scheme_description') !!} </label>
                     <textarea class="form-control" rows="4" name="scheme_description">{!! old( 'scheme_description', $schemes['scheme_description']) !!}</textarea>
                     @if ($errors->has('scheme_description'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('scheme_description') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
               <div class="col-md-4">
                  <label class="bmd-label-floating">{!! trans('panel.scheme.fields.scheme_image') !!}</label>
                  <div class="input-group">
                     <div class="custom-file">
                        <input type="file" class="custom-file-input" name="image" accept="image/*">
                        <label class="custom-file-label">Choose file</label>
                     </div>
                     @if ($errors->has('scheme_image'))
                     <div class="error col-lg-12">
                        <p class="text-danger">{{ $errors->first('scheme_image') }}</p>
                     </div>
                     @endif
                  </div>
               </div>
               <div class="col-md-2">
                  <div class="form-group">
                     <img id="avtar_preview" src="@if(@isset($schemes['scheme_image'])){{ asset($schemes['scheme_image']) }} @else http://placehold.it/70x70 @endif " width="80" alt="your image" />
                  </div>
               </div>
            </div>
            <div class="row redemption">
               <div class="col-md-6">
                  <div class="row">
                     <label class="col-sm-3 col-form-label">{!! trans('panel.scheme.fields.points_start_date') !!}</label>
                     <div class="col-sm-9">
                        <div class="form-group bmd-form-group is-filled">
                           <input type="text" name="points_start_date" class="form-control datepicker" value="{!! old( 'points_start_date', $schemes['points_start_date']) !!}" autocomplete="off" readonly>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="row">
                     <label class="col-sm-3 col-form-label">{!! trans('panel.scheme.fields.points_end_date') !!}</label>
                     <div class="col-sm-9">
                        <div class="form-group bmd-form-group is-filled">
                           <input type="text" name="points_end_date" class="form-control datepicker" value="{!! old( 'points_end_date', $schemes['points_end_date']) !!}" autocomplete="off" readonly>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row redemption">
               <div class="col-md-6">
                  <div class="row">
                     <label class="col-sm-3 col-form-label">{!! trans('panel.scheme.fields.block_points') !!}</label>
                     <div class="col-sm-9">
                        <div class="form-group bmd-form-group is-filled">
                           <input class="form-control" name="block_points" type="text">
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="row">
                     <label class="col-sm-3 col-form-label">{!! trans('panel.scheme.fields.block_percents') !!}</label>
                     <div class="col-sm-9">
                        <div class="form-group bmd-form-group is-filled">
                           <input class="form-control" name="block_percents" type="text">
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row clearfix earnscheme">
               <div class="col-md-12">
                  <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                     <thead>
                        <tr>
                           <th class="text-center"> # </th>
                           <th class="text-center category"> {!! trans('panel.scheme.fields.category_id') !!} </th>
                           <th class="text-center product"> {!! trans('panel.scheme.fields.product_id') !!} </th>
                           <th class="text-center"> {!! trans('panel.scheme.fields.minimum') !!}</th>
                           <th class="text-center"> {!! trans('panel.scheme.fields.maximum') !!}</th>
                           <th class="text-center"> {!! trans('panel.scheme.fields.points') !!} </th>
                           <th class="text-center"> </th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr id='addr1'></tr>
                        @if( $schemes->exists && isset($schemes['schemedetails']) )
                        @foreach($schemes['schemedetails'] as $row)
                        <tr>
                           <td></td>
                           @if( isset($row['categories']['category_name']))
                           <td>
                              <select name="category_id[]" class="form-control category rowchange"/>
                                 <option value="{{ $row['category_id'] }}" >{{ $row['categories']['category_name'] }}</option>
                              </select>
                           </td>
                           @endif
                           @if( isset($row['products']['product_name']))
                           <td>
                              <select name="product_id[]" class="form-control product rowchange"/>
                                 <option value="{{ $row['product_id'] }}" >{{ $row['products']['product_name'] }}</option>
                              </select>
                           </td>
                           @endif
                           <td>
                              <input type="hidden" name="detail_id[]" value="{{ $row['id'] }}"  />
                              <input type="text" name="minimum[]" class="form-control minimum rowchange" value="{{ $row['minimum'] }}"  />
                           </td>
                           <td>
                              <input type="text" name="maximum[]" class="form-control maximum rowchange" value="{{ $row['maximum'] }}"/>
                           </td>
                           <td>
                              <input type="text" name="points[]" class="form-control points rowchange" value="{{ $row['points'] }}"/>
                           </td>
                           <td class="td-actions text-center">
                            <a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a>
                          </td>
                        </tr>
                        @endforeach
                        @endif
                     </tbody>
                  </table>
               </div>
               <div class="row clearfix">
                  <div class="col-md-12">
                    <table class="table">
                      <tbody>
                        <tr>
                          <td class="td-actions">
                             <a href="#" title="" class="btn btn-success btn-just-icon btn-sm add-rows" onclick="getcategorylist()"> <i class="fa fa-plus"></i> </a>
                           </td>
                        </tr>
                      </tbody>
                    </table>
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
   $(document).ready(function(){
     getcategorylist();
     columnDisplay();
     var $table = $('table.kvcodes-dynamic-rows-example'),
         counter = 0;
     $('a.add-rows').click(function(event){
         event.preventDefault();
         var schemetype = $("#schemetype option:selected").val();
         counter++;
         if (schemetype === 'invoiceValue')
         {
             var newRow = 
             '<tr> <td>'+counter+'</td>'+
                 '<td><input type="number" name="minimum[]' + counter + '"class="form-control minimum rowchange"/></td>' +
                 '<td><input type="text" name="maximum[]' + counter + '"class="form-control maximum rowchange" /></td>' +
                '<td><input type="text" name="points[]' + counter + '"class="form-control points rowchange" /></td>' +
                '<td><a href="#" class="remove-rows btn btn-danger btn-xs"> <i class="fa fa-minus"></i></a></td> </tr>';
         }
         else
         {
           var newRow = 
             '<tr> <td>'+counter+'</td>'+
                 '<td class="category"><select name="category_id[]' + counter + '" class="form-control category rowchange" onchange="getproductlist(this)" </select></td>' +
                 '<td class="product"><select name="product_id[]' + counter + '" class="form-control product rowchange" onchange="getproductinfo(this)"/> </select></td>' +
                 '<td><input type="number" name="minimum[]' + counter + '"class="form-control minimum rowchange"/></td>' +
                 '<td><input type="text" name="maximum[]' + counter + '"class="form-control maximum rowchange" /></td>' +
                '<td><input type="text" name="points[]' + counter + '"class="form-control points rowchange" /></td>' +
                '<td class="td-actions text-center"><a class="remove-rows btn btn-danger btn-just-icon btn-sm"><i class="fa fa-minus"></i></a></td> </tr>';
         }
         
         $table.append(newRow);
     });
   
      $table.on('click', '.remove-rows', function() {
         $(this).closest('tr').remove();
     });
   });
   
   function columnDisplay()
   {
   var schemetype = $("#schemetype option:selected").val();
   if (schemetype === 'invoiceValue')
   {
     $('.category').hide();
     $('.product').hide();
     $('.redemption').hide();
     $('.earnscheme').show();
   }
   else if(schemetype === 'redemption')
   {
      $('.redemption').show();
      $('.earnscheme').hide();
   }
   else
   {
     $('.category').show();
     $('.product').show();
     $('.earnscheme').show();
      $('.redemption').hide();
   }
   }
   
   $(function(){
   $("#schemetype").on('change', function(){
    columnDisplay();
   })
   
   });
   
   function getcategorylist()
   {
   
   }
   
</script>
</x-app-layout>