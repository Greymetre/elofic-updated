<x-app-layout>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header card-header-icon card-header-theme">
          <div class="card-icon">
            <i class="material-icons">perm_identity</i>
          </div>
          <h4 class="card-title ">{{ trans('panel.global.create') }} Planned S&OP
            <span class="pull-right">
              <div class="btn-group">
                <!-- @if(auth()->user()->can(['product_access'])) -->
                <a href="{{ url('planned-sop') }}" class="btn btn-just-icon btn-theme" title="{!! trans('panel.product.title_singular') !!}{!! trans('panel.global.list') !!}"><i class="material-icons">next_plan</i></a>
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
          {!! Form::model($plannedsop,[
          'route' => $plannedsop->exists ? ['planned-sop.update', encrypt($planned-sop->id) ] : 'planned-sop.store',
          'method' => $plannedsop->exists ? 'PUT' : 'POST',
          'id' => 'createsopForm',
          'files'=>true
          ]) !!}

          <div class="row">
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">S&OP Month<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control datepicker" id="start_month" 
                       name="planning_month" placeholder="S&OP Month" 
                       autocomplete="off" readonly required
                       value="{{ old('planning_month', now()->addMonth()->format('F Y')) }}">
                  @if ($errors->has('planning_month'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('planning_month') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Branch Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="branch_id" id="branch_id" required>
                         <option value=''>Select Branch</option>
                         @foreach($branches as $branch)
                              <option value="{{ $branch->id }}" >{{ $branch->branch_name}}</option>
                         @endforeach
                  </select>
                  @if ($errors->has('product_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Division<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="product_division" id="product_division" onchange="getProductlist()" required>
                         <option value=''>Select Division</option>
                         @foreach($divisions as $division)
                              <option value="{{ $division->id }}" >{{ $division->category_name}}</option>
                         @endforeach
                  </select>
                  @if ($errors->has('product_division'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_division') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto; width: 100%;">
                    <table class="table kvcodes-dynamic-rows-example" id="tab_logic" style="min-width: 1800px;">
                        <thead>
                            <tr class="text-white">
                                <th class="text-center" style="width: 50px !important;">#</th>
                                <th class="text-center" style="width: 500px !important;">{!! trans('panel.global.products') !!}</th>
                                <th class="text-center" style="width: 500px !important;">Product Group</th>
                                <th class="text-center" style="width: 500px !important;">Product Code</th>
                                <th class="text-center" style="width: 500px !important;">Product Description</th>
                                <th class="text-center" style="width: 500px !important;">Opening stock as on 1st (Qty.)</th>
                                <th class="text-center" style="width: 500px !important;">S&OP Plan for Next running month (M+1) (Qty.)</th>
                                <th class="text-center" style="width: 500px !important;">Budget for the month (Qty.)</th>
                                <th class="text-center" style="width: 500px !important;">LM Sale (Qty.)</th>
                                <th class="text-center" style="width: 500px !important;">L3M Avg Sale (Qty.)</th>
                                <th class="text-center" style="width: 500px !important;">LY same month sale (Qty.)</th>
                                <th class="text-center" style="width: 500px !important;">SKU Unit Price</th>
                                <th class="text-center" style="width: 500px !important;">S&OP Val_L (Unit Price *Qty.)</th>
                                <th class="text-center" style="width: 500px !important;">TOP 20 SKU for the Branch (*)</th>
                                <th class="text-center" style="width: 250px !important;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sopTableBody">
                               <tr id='addr0' value="1">
                                <td>1</td>
                                <td>
                                    <div class="input_section">
                                        <div class="form-group has-default bmd-form-group">
                                            <select class="form-select select2 product" name="product_id[]" required>
                                                <option value=''>Select Product</option>
                                            </select>
                                            @if ($errors->has('product_id'))
                                            <div class="error">
                                                <p class="text-danger">{{ $errors->first('product_id') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control product_group_name" name="product_group_name[]"  readonly >
                                        @if ($errors->has('product_group_name'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('product_group_name') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control product_code" name="product_code[]"  readonly>
                                        @if ($errors->has('product_code'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('product_code') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control product_description" name="product_description[]"  readonly>
                                        @if ($errors->has('product_description'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('product_description') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control opening_stock" name="opening_stock[]"  readonly>
                                        @if ($errors->has('opening_stock'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('opening_stock') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control plan_next_month" name="plan_next_month[]" required>
                                        @if ($errors->has('plan_next_month'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('plan_next_month') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control budget_for_month" name="budget_for_month[]"  readonly>
                                        @if ($errors->has('budget_for_month'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('budget_for_month') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control last_month_sale" name="last_month_sale[]"  readonly>
                                        @if ($errors->has('last_month_sale'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('last_month_sale') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control last_three_month_avg" name="last_three_month_avg[]"  readonly>
                                        @if ($errors->has('last_three_month_avg'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('last_three_month_avg') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control last_year_month_sale" name="last_year_month_sale[]"  readonly>
                                        @if ($errors->has('last_year_month_sale'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('last_year_month_sale') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control sku_unit_price" name="sku_unit_price[]"  readonly>
                                        @if ($errors->has('sku_unit_price'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('sku_unit_price') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control s_op_val" name="s_op_val[]"  readonly>
                                        @if ($errors->has('s_op_val'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('s_op_val') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="input_section">
                                      <div class="form-group has-default bmd-form-group">
                                      <input type="text" class="form-control top_sku" name="top_sku[]"  readonly>
                                        @if ($errors->has('top_sku'))
                                        <div class="error">
                                          <p class="text-danger">{{ $errors->first('top_sku') }}</p>
                                        </div>
                                        @endif
                                      </div>
                                    </div>
                                </td>
                                <td class="td-actions text-center">
                                    <!-- <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-minus"></i></button> -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row clearfix">
            <div class="col-md-12">
               <table>
                  <tbody>
                     <tr>
                        <td class="td-actions text-center">
                           <a href="#" title="" class="btn btn-success btn-xs add-rows" onclick="getProductlist()"> <i class="fa fa-plus"></i> </a>
                        </td>
                     </tr>
                  </tbody>
               </table>

            </div>
         </div>


        <!-- <div class="row">
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Branch Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="branch_id" id="branch_id" >
                         <option value=''>Select Branch</option>
                         @foreach($branches as $branch)
                              <option value="{{ $branch->id }}" >{{ $branch->branch_name}}</option>
                         @endforeach
                  </select>
                  @if ($errors->has('product_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                  <select class="form-select select2" name="product_id" id="product_id" >
                         <option value=''>Select Product</option>
                         @foreach($products as $product)
                              <option value="{{ $product->id }}" >{{ $product->product_name}}</option>
                         @endforeach
                  </select>
                  @if ($errors->has('product_no'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_no') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Group Name<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_group_name" id="product_group_name" readonly>
                  @if ($errors->has('product_group_name'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_group_name') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Division<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_division" id="product_division" readonly>
                  @if ($errors->has('product_division'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_division') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Code<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_code" id="product_code" readonly>
                  @if ($errors->has('product_code'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_code') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Product Description<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="product_description" id="product_description" readonly>
                  @if ($errors->has('product_description'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('product_description') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Opening stock as on 1st (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="opening_stock" id="opening_stock" readonly>
                  @if ($errors->has('opening_stock'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('opening_stock') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">S&OP Plan for Next running month (M+1) (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="plan_next_month" id="plan_next_month">
                  @if ($errors->has('plan_next_month'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('plan_next_month') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">Budget for the month (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="budget_for_month" id="budget_for_month" readonly>
                  @if ($errors->has('budget_for_month'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('budget_for_month') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">LM Sale (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="last_month_sale" id="last_month_sale" readonly>
                  @if ($errors->has('last_month_sale'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('last_month_sale') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">L3M Avg Sale (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="last_three_month_avg" id="last_three_month_avg" readonly>
                  @if ($errors->has('last_three_month_avg'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('last_three_month_avg') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">LY same month sale (Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="last_year_month_sale" id="last_year_month_sale" readonly>
                  @if ($errors->has('last_year_month_sale'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('last_year_month_sale') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
             <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">SKU Unit Price<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="sku_unit_price" id="sku_unit_price" readonly>
                  @if ($errors->has('sku_unit_price'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('sku_unit_price') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">S&OP Val_L (Unit Price *Qty.)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="number" class="form-control" name="s_op_val" id="s_op_val" readonly>
                  @if ($errors->has('s_op_val'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('s_op_val') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="input_section">
                <label class="col-form-label">TOP 20 SKU for the Branch (*)<span class="text-danger"> *</span></label>
                <div class="form-group has-default bmd-form-group">
                <input type="text" class="form-control" name="top_sku" id="top_sku" readonly>
                  @if ($errors->has('top_sku'))
                  <div class="error">
                    <p class="text-danger">{{ $errors->first('top_sku') }}</p>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div> 
          <div class="pull-right col-md-12">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
          </div>
        </div> -->
         <div class="pull-right col-md-12">
            {{ Form::submit('Submit', array('class' => 'btn btn-theme pull-right')) }}
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
  </div>
<script>
   $(document).ready(function() {
      var $table = $('table.kvcodes-dynamic-rows-example'),
          counter = parseInt($('#tab_logic tr:last').attr('value')) || 0;

      $('a.add-rows').click(function(event) {
          event.preventDefault();
          counter++;

          var newRow = `
              <tr id='addr${counter}' value="${counter}">
                  <td class="row-count">${counter}</td>
                  <td>
                      <div class="input_section">
                          <div class="form-group has-default bmd-form-group">
                              <select class="form-select select2 product" name="product_id[]" required>
                                  <option value=''>Select Product</option>
                              </select>
                          </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group">
                             <input type="text" class="form-control product_group_name" name="product_group_name[]" readonly>
                          </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control product_code" name="product_code[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control product_description" name="product_description[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control opening_stock" name="opening_stock[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control plan_next_month" name="plan_next_month[]" required> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control budget_for_month" name="budget_for_month[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control last_month_sale" name="last_month_sale[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control last_three_month_avg" name="last_three_month_avg[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control last_year_month_sale" name="last_year_month_sale[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control sku_unit_price" name="sku_unit_price[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control s_op_val" name="s_op_val[]" readonly> </div>
                      </div>
                  </td>
                  <td><div class="input_section">
                          <div class="form-group has-default bmd-form-group"><input type="text" class="form-control top_sku" name="top_sku[]" readonly> </div>
                      </div>
                  </td>
                  <td class="td-actions text-center">
                      <button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-minus"></i></button>
                  </td>
              </tr>`;

          $('#sopTableBody').append(newRow);

          // Initialize Select2 for new row
          $('.select2').select2({
              minimumResultsForSearch: 10
          });

          updateRowNumbers();
      });

      $(document).on('click', '.remove-row', function() {
          $(this).closest('tr').remove();
          counter--;

          updateRowNumbers();
      });

      function updateRowNumbers() {
          $('#sopTableBody tr').each(function(index) {
              $(this).attr('value', index + 1);
              $(this).find('.row-count').text(index + 1);
          });
          counter = $('#sopTableBody tr').length;
      }
  });
  $(document).ready(function (){
    getProductlist();
    $("#start_month").datepicker({
        dateFormat: "MM yy", // Show only month and year
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        closeText: "Select", // Custom text for closing
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 1), // Allow till next month
        onClose: function(dateText, inst) {
            var month = parseInt($("#ui-datepicker-div .ui-datepicker-month option:selected").val());
            var year = parseInt($("#ui-datepicker-div .ui-datepicker-year option:selected").val());

            var selectedDate = new Date(year, month, 1);
            $(this).val($.datepicker.formatDate('MM yy', selectedDate));
        }
    }).focus(function () {
        $(".ui-datepicker-calendar").hide(); // Hide date picker
    });

    $(document).on("change", ".product", function() {
        var $row = $(this).closest("tr"); // Get the current row
        var product_id = $(this).val();
        
        if (product_id != null && product_id != '') {
            $.ajax({
                url: "{{ url('getProductInfo') }}",
                dataType: "json",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: product_id
                },
                success: function(res) {
                    $row.find(".product_description").val(res.product_description);
                    $row.find(".product_division").val(res.categories.category_name);
                    $row.find(".product_code").val(res.product_code);
                    $row.find(".product_group_name").val(res.subcategories.subcategory_name);
                    $row.find(".budget_for_month").val(res.budget_for_month);
                    $row.find(".top_sku").val(res.top_sku);
                    $row.find(".sku_unit_price").val(res.price).trigger('change');
                    
                    // Set read-only fields
                    $row.find(".product_group_name, .product_description, .sku_unit_price, .product_division, .product_code, .budget_for_month, .top_sku").prop('readonly', true);
                }
            });
        } else {
            // Clear only the fields in this row
            $row.find(".product_description, .product_division, .product_code, .product_group_name, .budget_for_month, .top_sku, .sku_unit_price").val('').trigger('change');
            $row.find(".product_group_name, .product_description, .sku_unit_price, .product_division, .product_code, .budget_for_month, .top_sku").prop('readonly', true);
        }
    });
   
    $(document).on("change", ".product", function() {
        var $row = $(this).closest("tr"); // Get the row of the changed select box
        var product_id = $(this).val();

        if (product_id != null && product_id != '') {
            $.ajax({
                url: "{{ url('getProductInfo') }}",
                dataType: "json",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    product_id: product_id
                },
                success: function(res) {
                    $row.find(".product_description").val(res.product_description);
                    $row.find(".product_division").val(res.categories.category_name);
                    $row.find(".product_code").val(res.product_code);
                    $row.find(".product_group_name").val(res.subcategories.subcategory_name);
                    $row.find(".budget_for_month").val(res.budget_for_month);
                    $row.find(".top_sku").val(res.top_sku);
                    $row.find(".sku_unit_price").val(res.price).trigger('change');
                    
                    // Set read-only fields
                    $row.find(".product_group_name, .product_description, .sku_unit_price, .product_division, .product_code, .budget_for_month, .top_sku").prop('readonly', true);

                    // Fetch and update sales data only for this row
                    getSaledata($row);
                }
            });
        } else {
            // Clear only the fields in this row
            $row.find(".product_description, .product_division, .product_code, .product_group_name, .budget_for_month, .top_sku, .sku_unit_price").val('').trigger('change');
            $row.find(".product_group_name, .product_description, .sku_unit_price, .product_division, .product_code, .budget_for_month, .top_sku").prop('readonly', true);
        }
    });

    $(document).on('change', '.sku_unit_price, .plan_next_month', function () {
        var $row = $(this).closest('tr'); // Adjust selector if needed

        var sku_unit_price = $row.find('.sku_unit_price').val();
        var plan_next_month = $row.find('.plan_next_month').val();

        if (sku_unit_price !== '' && plan_next_month !== '') {
            var total = parseFloat(sku_unit_price) * parseFloat(plan_next_month);
            $row.find('.s_op_val').val(total);
        } else {
            $row.find('.s_op_val').val('');
        }
    });
  });

  function getProductlist() {
     var category = $('#product_division').val();
     $.ajax({
        url: "{{ url('getProductData') }}",
        dataType: "json",
        type: "POST",
        data: {
           _token: "{{csrf_token()}}",
           category: category
        },
        success: function(res) {
           var table = document.getElementById(tab_logic),
              rIndex;
           if (res) {
              $('#tab_logic tr:last').find(".product").empty();
              $('#tab_logic tr:last').find(".product").append('<option value="">Select Product</option>');
              $.each(res, function(key, value) {

                 if (value.product_code) {
                    var productcode = value.product_code
                 } else {
                    var productcode = '';
                 }

                 $('#tab_logic tr:last').find('.product').append('<option value="' + value.id + '">' + value.product_name + productcode + '</option>');

              });
           } else {
              row.find(".product").empty();
           }
        }
     });
  }

  function getSaledata($row) {
      var product_id = $row.find(".product").val(); // Get product ID from the row
      var date = $("#start_month").val(); // Get product ID from the row

      if (product_id != null && product_id != '') {
          $.ajax({
              url: "{{ url('getSaledata') }}",
              dataType: "json",
              type: "POST",
              data: {
                  _token: "{{ csrf_token() }}",
                  product_id: product_id,
                  date      : date
              },
              success: function(res) {
                  $row.find(".last_month_sale").val(res.last_month_sale).prop('readonly', true);
                  $row.find(".last_three_month_avg").val(res.last_three_month_avg).prop('readonly', true);
                  $row.find(".last_year_month_sale").val(res.last_year_same_month).prop('readonly', true);
              }
          });
      } else {
          $row.find(".last_month_sale, .last_three_month_avg, .last_year_month_sale").val('').prop('readonly', true);
      }
  }
</script>
</x-app-layout>