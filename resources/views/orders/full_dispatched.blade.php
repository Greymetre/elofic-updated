<x-app-layout>
<div class="row">
   <div class="col-md-12">
      <div class="card">
         <div class="card-header card-header-icon card-header-theme">
           <div class="card-icon">
             <i class="material-icons">perm_identity</i>
           </div>
           <h4 class="card-title"> Order Fully Dispatched </h4>
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
         <div class="row">
             <div class="col-12">
               <h4>
                 <!-- <img src="{!! asset('assets/img/logo.png') !!}" class="brand-image" width="70px" alt="Logo"> <span> {!! config('app.name') !!}</span> -->

                  <img src="{!! url('/').'/'.asset('assets/img/bediya.jpg') !!}" width="70">
                  <img src="{!! url('/').'/'.asset('assets/img/silver.png') !!}" width="70">
                 <small class="float-right">Date: {!! date("d-M-Y", strtotime($orders['order_date'])) !!}</small>
               </h4>
             </div>
         </div>
         <hr>
         <div class="row invoice-info">
            <div class="col-sm-6 invoice-col">
               From
               <address>
                 <strong>{!! isset($orders['sellers']['name']) ? $orders['sellers']['name'] :'' !!} </strong><br>
                {!! $orders['sellers']['customeraddress']['address1'] !!} {!! $orders['sellers']['customeraddress']['address2'] !!}<br>
                 {!! $orders['sellers']['customeraddress']['cityname']['city_name'] !!} {!! $orders['sellers']['customeraddress']['pincodename']['pincode'] !!}<br>
                 Phone: {!! $orders['sellers']['mobile'] !!}<br>
                 Email: {!! $orders['sellers']['email'] !!}
               </address>
            </div>
            <div class="col-sm-6 invoice-col">
               <strong>To </strong>
               <address>
                 <strong>{!! $orders['buyers']['name'] !!}</strong><br>
                {!! $orders['buyers']['customeraddress']['address1'] !!} ,{!! $orders['buyers']['customeraddress']['address2'] !!}<br>{!! isset($orders['buyers']['customeraddress']['cityname']['city_name']) ? $orders['buyers']['customeraddress']['cityname']['city_name'] :'' !!} {!! isset($orders['buyers']['customeraddress']['pincodename']['pincode']) ? $orders['buyers']['customeraddress']['pincodename']['pincode'] :'' !!}<br>
                 Phone: {!! $orders['buyers']['mobile'] !!}<br>
                 Email: {!! $orders['buyers']['email'] !!}
               </address>
            </div>
         </div>
         <hr>
         <div class="row invoice-info">
             <div class="col-sm-6 invoice-col">
                <div class="row">
                   <div class="col-md-4">Order ID</div>
                   <div class="col-md-8">{!! $orders['id'] !!}</div>
                </div>
                <div class="row">
                   <div class="col-md-4">Order No</div>
                   <div class="col-md-8">{!! $orders['orderno'] !!}</div>
                </div>
             </div>
             <div class="col-sm-6 invoice-col">
                <div class="row">
                   <div class="col-md-4">Order Date</div>
                   <div class="col-md-8">{!! $orders['order_date'] !!}</div>
                </div>
                <div class="row">
                   <div class="col-md-4">Order Status</div>
                   <div class="col-md-8">{!! isset($orders['status']['status_name']) ? $orders['status']['status_name'] :'' !!}</div>
                </div>
             </div>
          </div>
         <hr>
            {!! Form::open(['url' => 'submit-fullydispatched' , 'method' => 'POST']) !!}
<!--                <input type="hidden" name="buyer_id" value="{!! $orders['buyer_id'] !!}">
               <input type="hidden" name="seller_id" value="{!! $orders['seller_id'] !!}"> -->

               <input type="hidden" name="buyer_id" value="{!! $orders['seller_id'] !!}">
               <input type="hidden" name="seller_id" value="{!! $orders['buyer_id'] !!}">
               
               <input type="hidden" name="order_id" value="{!! $orders['id'] !!}">
               <input type="hidden" name="orderno" value="{!! $orders['orderno'] !!}">
               <div class="row invoice-info">
                  <div class="col-sm-6 invoice-col">
                     <div class="row">
                        <label class="col-md-4">Invoice Date</label>
                         <div class="col-md-9">
                           <div class="form-group has-default bmd-form-group">
                              <input type="text" name="invoice_date" class="form-control datepicker" value="{!! old( 'invoice_date') !!}" autocomplete="off" required>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-sm-6 invoice-col">
                     <div class="row">
                        <label class="col-md-4">Invoice No</label>
                         <div class="col-md-9">
                           <div class="form-group has-default bmd-form-group">
                              <input type="text" class="form-control" name="invoice_no" value="{!! old( 'invoice_no') !!}" required>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <br>

              <!--  <div class="row">
                <label class="col-md-1"></label>
                <div class="col-md-10">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="form-check-input" id="withoutgst" type="checkbox" value="1">Without GST
                      <span class="form-check-sign">
                        <span class="check"></span>
                      </span>
                    </label>
                  </div>
                </div>
              </div> -->

               <div class="row">
                  <div class="container-fluid mt-5 d-flex justify-content-center w-100">
                     <div class="table-responsive w-100">
                        <table class="table kvcodes-dynamic-rows-example" id="tab_logic">
                           <thead>
                              <tr class="card-header-warning text-white">
                                 <th class="text-center"> # </th>
                                 <th class="text-center"> {!! trans('panel.global.products') !!} </th>
                                 <!-- <th class="text-center"> {!! trans('panel.global.product_detail') !!} </th> -->
                                 <th class="text-center"> {!! trans('panel.global.quantity') !!}</th>
                                 <th class="text-center"> {!! trans('panel.global.list_price')!!} </th>
                                 <th class="text-center"> Tax</th>
                                 <th class="text-center"> Trade Discount%</th>
                                 <th class="text-center"> {!! trans('panel.global.amount') !!} </th>
                                 <!-- <th class="text-center"> </th> -->
                              </tr>
                           </thead>
                           <tbody >
                              @if($orders->exists && isset($orders['orderdetails']))
                              @foreach($orders['orderdetails'] as $index => $rows )
                                 @if($rows['quantity']-$rows['shipped_qty'] >= 1 )
                                 <tr id='addr0'>
                                    <td>{!! $index +1 !!}</td>
                                    <td>
                                       <input type="hidden" name="orderdetail[{!! $index !!}][product_id]" value="{!! $rows['product_id'] !!}">
                                       {!! $rows['products']['product_name'] !!} <br>
                                       {!! $rows['products']['product_code']??'' !!} <br>
                                    </td>
                                    <td>
                                       <input type="number" name='orderdetail[{!! $index !!}][quantity]' class="form-control quantity rowchange" step="0" min="0" max="{!! $rows['quantity']-$rows['shipped_qty'] !!}" value="{!! $rows['quantity']-$rows['shipped_qty'] !!}" readonly />
                                       <div class='error-quantity'></div>
                                    </td>

                                    <td style="display: none;">
                                       <input type="hidden" name="orderdetail[{!! $index !!}][product_detail]" value="{!! $rows['product_detail_id'] !!}">
                                       {!! isset($rows['productdetails']['detail_title']) ? $rows['productdetails']['detail_title'] :'' !!}
                                       <span class="gst_percent" style="display:none;">
                                       {!! isset($rows['productdetails']['gst']) ? $rows['productdetails']['gst'] :'' !!}</span> <br>
                                       <span class="gstamount" style="display:none;">
                                          {!! isset($rows['tax_amount']) ? $rows['tax_amount'] :'' !!}</span> <br>
                                       <span class="linediscount" style="display:none;">{!! isset($rows['discount_amount']) ? $rows['discount_amount'] :'' !!}</span>
                                       <input type="hidden" name="orderdetail[{!! $index !!}][tax_amount]" class="form-control tax_amount readonly" value="{!! isset($rows['tax_amount']) ? $rows['tax_amount'] :'' !!}" />
                                       <input type="hidden" name="orderdetail[{!! $index !!}][discount_amount]" class="form-control discountamount readonly" value="{!! isset($rows['discount_amount']) ? $rows['discount_amount'] :'' !!}"/>
                                    </td>

                                    <td>
                                       <input type="number" name="orderdetail[{!! $index !!}][mrp]" class="form-control price rowchange" step="0.01" min="0" value="{!! $rows['price'] !!}" readonly />
                                       <div class='error-price'></div>
                                    </td>

                                    <!-- <td>
                                       <input type="number" name="orderdetail[{!! $index !!}][price]" class="form-control price rowchange" step="0.01" min="0" value="{!! $rows['price'] !!}" />
                                       <div class='error-price'></div>
                                    </td> -->

                                    <td>
                                       <input type="number" name="orderdetail[{!! $index !!}][gst]" class="form-control gst_new rowchange" step="0.01" min="0" value="{!! $rows['gst'] !!}" readonly />
                                       <div class='error-price'></div>
                                    </td>


                                    <td>
                                       <input type="number" name="orderdetail[{!! $index !!}][discount]" class="form-control discount rowchange" step="0.01" min="0" value="{!! $rows['discount'] !!}" readonly />
                                       <div class='error-discount'></div>
                                    </td>


                               <td hidden>

                                <input type="number" name='orderdetail[{{ $index }}][ebd_discount]' class="form-control total_new scheme_dis" value="{!! $rows['ebd_discount'] !!}" readonly hidden />

                                 <!-- nnn -->

                                <input type="text" name="orderdetail[{{ $index }}][scheme_type]" class="scheme_type" hidden>
                                <input type="text" name="orderdetail[{{ $index }}][scheme_value_type]" class="scheme_value_type" hidden> 
                                <input type="text" name="orderdetail[{{ $index }}][minimum]" class="minimum" hidden>
                                <input type="text" name="orderdetail[{{ $index }}][maximum]" class="maximum" hidden>  

                                <input type="text" name="orderdetail[{{ $index }}][start_date]" class="start_date" hidden>    
                                <input type="text" name="orderdetail[{{ $index }}][end_date]" class="end_date" hidden>    
                                 <!-- nnn end -->

                                <input type="text" name="orderdetail[{{ $index }}][ebd_amount]" class="ebd_amount" value="{!! $rows['ebd_amount'] !!}" hidden>


                                 <input type="text" name="orderdetail[{{ $index }}][clus_amounts]" class="clus_amounts" value="{!! $rows['cluster_amount'] !!}" hidden>

                                 <input type="text" name="orderdetail[{{ $index }}][clustered_dis]" class="clustered_dis">
                                 

                                 <input type="text" name="orderdetail[{{ $index }}][deal_amounts]" class="deal_amounts" value="{!! $rows['deal_amount'] !!}" hidden>

                                 <input type="text" name="orderdetail[{{ $index }}][deal_dis]" class="deal_dis" hidden>

                                <input type="text" name="orderdetail[{{ $index }}][distributot_amounts]" class="distributot_amounts" value="{!! $rows['distributor_amount'] !!}" hidden>

                                <input type="text" name="orderdetail[{{ $index }}][distributot_dis]" class="distributot_dis" hidden>

                                <input type="text" name="orderdetail[{{ $index }}][frieght_dis]" class="frieght_dis" value="{!! $rows['frieght_discount'] !!}" hidden>

                                <input type="text" name="orderdetail[{{ $index }}][frieght_amounts]" class="frieght_amounts"  value="{!! $rows['frieght_amount'] !!}" hidden>


                               <?php

                                $gst_amount_fives = 0;
                                $gst_amount_twels = 0;
                                $gst_amount_eighteens = 0;
                                $gst_amount_twenty_eights = 0;

                                if($rows['gst'] == 5){
                                 //$gst_amount_fives+= $rows['gst_amount']; 
                                 $gst_amount_fives = $rows['tax_amount']; 
                                  
                                 ?>
                                 <input type="text" name="orderdetail[{{ $index }}][five_gst]" class="five_gst" value="{!! $gst_amount_fives !!}" hidden>

                                <?php }elseif($rows['gst'] == 12){
                                 //$gst_amount_twels+= $rows['gst_amount']; 
                                 $gst_amount_twels = $rows['tax_amount']; 
                                 ?>
                                
                                <input type="text" name="orderdetail[{{ $index }}][twelve_gst]" class="twelve_gst" value="{!! $gst_amount_twels !!}" hidden>

                                <?php }elseif($rows['gst'] == 18){
                                  //$gst_amount_eighteens+= $rows['gst_amount']; 
                                  $gst_amount_eighteens = $rows['tax_amount']; 
                                  ?>

                                <input type="text" name="orderdetail[{{ $index }}][eighteen_gst]" class="eighteen_gst" value="{!! $gst_amount_eighteens !!}" hidden>

                                <?php }else{ 
                                 //$gst_amount_twenty_eights+= $rows['gst_amount']; 
                                 $gst_amount_twenty_eights = $rows['tax_amount']; 
                                 ?>

                                <input type="text" name="orderdetail[{{ $index }}][twenti_eight_gst]" class="twenti_eight_gst" value="{!! $gst_amount_twenty_eights !!}" hidden> 
                                <?php 
                                  }

                                 ?>

                                    </td>







                                    <td>
                                       <input type="number" name='orderdetail[{!! $index !!}][line_total]' class="form-control total" value="{!! $rows['line_total'] !!}" readonly/>
                                    </td>
                                    <!-- <td class="td-actions text-center"><a class="remove btn btn-danger btn-xs"><i class="fa fa-minus"></i></a></td> -->
                                 </tr>
                                 <tr id='addr1'></tr>
                                 @endif
                              @endforeach
                           @endif
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>

             <!-- <div class="row clearfix">
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
               </div> -->

               <div class="baseurl" data-baseurl="{{ url('/')}}">
               </div>
               <br>
               <!-- /.row -->
               <div class="row">

                  <?php 

                  $ebd_dicount_sum = 0;
                  $clustor_dicount_sum = 0;
                  $deal_dicount_sum = 0;
                  $distributor_dicount_sum = 0;
                  $frieght_dicount_sum = 0;

                  $cluster_discount = 0;
                  $deal_discount = 0;
                  $distributor_discount = 0;
                  $frieght_discount = 0;

                  $gst_amount_5 = 0;
                  $gst_amount_12 = 0;
                  $gst_amount_18 = 0;
                  $gst_amount_28 = 0;

                  if(!empty($orders['orderdetails'])){

                  foreach($orders['orderdetails'] as $keys => $rowss){

                  $ebd_dicount_sum+= $rowss['ebd_amount'];
                  $clustor_dicount_sum+= $rowss['cluster_amount'];
                  $deal_dicount_sum+= $rowss['deal_amount'];
                  $distributor_dicount_sum+= $rowss['distributor_amount'];
                  $frieght_dicount_sum+= $rowss['frieght_amount'];


                  $cluster_discount = $rowss['cluster_discount'];
                  $deal_discount = $rowss['deal_discount'];
                  $distributor_discount = $rowss['distributor_discount'];
                  $frieght_discount = $rowss['frieght_discount'];

                  if($rowss['gst'] == 5){

                  //$gst_amount_5+= $rowss['gst_amount'];
                  $gst_amount_5+= $rowss['tax_amount'];

                  }elseif($rowss['gst'] == 12){

                  //$gst_amount_12+= $rowss['gst_amount'];
                  $gst_amount_12+= $rowss['tax_amount'];

                  }elseif($rowss['gst'] == 18){

                  //$gst_amount_18+= $rowss['gst_amount'];
                  $gst_amount_18+= $rowss['tax_amount'];

                  }else{

                  //$gst_amount_28+= $rowss['gst_amount'];
                  $gst_amount_28+= $rowss['tax_amount'];
                  }

                  }

                  } 

                  ?>

                  <div class="col-6">
                    
                  </div>
                  <!-- /.col -->
                  <div class="col-6">
                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">{!! trans('panel.order.sub_total') !!}</label>
                      </div>
                        <div class="col-sm-8">
                           <input type="number" name='sub_total' class="form-control" id="subtotal" readonly value="{!! old( 'sub_total', $orders['sub_total']) !!}"/>
                           @if($errors->has('sub_total'))
                           <div class="invalid-feedback">
                              {{ $errors->first('sub_total') }}
                           </div>
                           @endif
                        </div>
                     </div>

                     <!-- new -->

                     <div class="form-group row">
                       <div class="col-sm-4">
                          <label class="bmd-label">EBD Discount</label>
                      </div>
                        <div class="col-sm-8">
                            <input type="number" name='scheme_discount' id="scheme_discount" class="form-control scheme_discount" value="{!! old( 'scheme_discount', $ebd_dicount_sum) !!}" readonly/>
                           @if($errors->has('scheme_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('scheme_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>


                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">Cluster Discount%</label>
                      </div>
                        <div class="col-sm-4"> 
                         <select name='cluster_discount' class="form-control cluster_discount" disabled>
                            <option value="">Select Cluster Discount</option>
                            <option value="1" <?php if($cluster_discount == 1){ echo "selected";} ?>>1%</option>
                            <option value="2" <?php if($cluster_discount == 2){ echo "selected";} ?>>2%</option>
                            <option value="3" <?php if($cluster_discount == 3){ echo "selected";} ?>>3%</option>
                         </select> 
                        </div>
                        <div class="col-sm-4">
                          <input type="text" name="extra_cluster_discount" class="extra_cluster_discount form-control" readonly value="{!! old( 'extra_cluster_discount', $clustor_dicount_sum) !!}"> 
                        </div>

                     </div>



                     <div class="form-group row">
                       <div class="col-sm-4">
                        <!-- <label class="bmd-label">{!! trans('panel.order.extra_discount') !!}</label> -->
                        <label class="bmd-label">Deal Discount%</label>
                      </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                             <div class="input-group-prepend">
                             </div>
                            
                             <input type="number" name='extra_discount' class="form-control deal_discnt" value="{!! old( 'extra_discount', $deal_discount) !!}" readonly />

                              <input type="text" name="extra_discount_amount" class="form-control extra_discount_amount" readonly value="{!! old( 'extra_discount_amount', $deal_dicount_sum) !!}">
                           </div>
                           @if($errors->has('extra_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('extra_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>


                     <div class="form-group row">
                      <div class="col-sm-4">
                        <label class="bmd-label">Distributor Discount%</label>
                      </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                             <div class="input-group-prepend">
                             </div>
                             <input type="number" name='distributor_discount' class="form-control distributor_discount" value="{!! old( 'distributor_discount', $distributor_discount) !!}" readonly />
                              <input type="text" name="distributor_discount_amount" class="form-control distributor_discount_amount" readonly value="{!! old( 'distributor_discount_amount', $distributor_dicount_sum) !!}">
                           </div>
                           @if($errors->has('distributor_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('distributor_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>

                      <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">frieght Discount%</label>
                      </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                             <div class="input-group-prepend">
                             </div>
                            
                             <input type="number" name='frieght_discount' class="form-control frieght_discount" value="{!! old( 'frieght_discount', $frieght_discount) !!}" readonly />

                              <input type="text" name="frieght_discount_amount" class="form-control frieght_discount_amount" readonly value="{!! old( 'frieght_discount_amount', $frieght_dicount_sum) !!}">
                           </div>
                           @if($errors->has('frieght_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('frieght_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>





                     <div class="form-group row"> 
                     <div class="col-sm-4">
                        <label class="bmd-label">5%Tax</label>
                      </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                             <input type="number" name='5_gst' class="form-control 5_gst" readonly  value="{!! old( '5_gst', $gst_amount_5) !!}" />
                        </div>
                       </div> 
                     </div> 

                     <div class="form-group row"> 
                     <div class="col-sm-4">
                        <label class="bmd-label">12%Tax</label>
                      </div>
                        <div class="col-sm-8">  
                           <div class="input-group">
                             <input type="number" name='12_gst' class="form-control 12_gst" readonly value="{!! old( '12_gst', $gst_amount_12) !!}"/>
                        </div>
                       </div> 
                     </div>

                     <div class="form-group row"> 
                     <div class="col-sm-4">
                        <label class="bmd-label">18%Tax</label>
                      </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                             <input type="number" name='18_gst' class="form-control 18_gst" readonly value="{!! old( '18_gst', $gst_amount_18) !!}"/>
                        </div>
                       </div> 
                     </div> 

                     <div class="form-group row"> 
                     <div class="col-sm-4">
                        <label class="bmd-label">28%Tax</label>
                      </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                             <input type="number" name='28_gst' class="form-control 28_gst" readonly value="{!! old( '28_gst', $gst_amount_28) !!}"/>
                        </div>
                       </div> 
                    </div>

                     <!-- end new -->



                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">{!! trans('panel.order.total_gst') !!}</label>
                      </div>
                        <div class="col-sm-8">
                           <input type="number" name='total_gst' id="totalgst" class="form-control" value="{!! old( 'total_gst', $orders['total_gst']) !!}" readonly/>
                           @if($errors->has('total_gst'))
                           <div class="invalid-feedback">
                              {{ $errors->first('total_gst') }}
                           </div>
                           @endif
                        </div>
                     </div>

                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">{!! trans('panel.order.extra_discount') !!}</label>
                      </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                             <div class="input-group-prepend">
                             </div>
                             <input type="number" name='extra_discount' class="form-control" value="{!! old( 'extra_discount', $orders['extra_discount']) !!}"/>
                             <input type="text" name="extra_discount_amount" class="form-control extra_discount_amount" readonly>
                           </div>
                           @if($errors->has('extra_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('extra_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">{!! trans('panel.order.total_discount') !!}</label>
                      </div>
                        <div class="col-sm-8">
                           <input type="number" name='total_discount' id="totaldiscount" class="form-control" value="{!! old( 'total_discount', $orders['total_discount']) !!}" readonly/>
                           @if($errors->has('total_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('total_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>
                     <div class="form-group row">
                        <div class="col-sm-4">
                          <label class="bmd-label">{!! trans('panel.order.grand_total') !!}</label>
                        </div>
                        <div class="col-sm-8">
                           <input type="number" class="form-control" id="grandtotal" name="grand_total" value="{!! old( 'grand_total', $orders['grand_total']) !!}" readonly>
                           @if($errors->has('grand_total'))
                           <div class="invalid-feedback">
                              {{ $errors->first('grand_total') }}
                           </div>
                           @endif
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
<script src="{{ url('/').'/'.asset('assets/js/validation_orders.js') }}"></script>
<!-- <script src="{{ url('/').'/'.asset('assets/js/invoice_js') }}"></script> -->
<script type="text/javascript">
   $(document).ready(function(){
   
    var $table = $('table.kvcodes-dynamic-rows-example'),
          counter = 1;
      $('a.add-rows').click(function(event){
          event.preventDefault();
          counter++;
          var newRow = 
              '<tr> <td>'+counter+'</td>'+
                  '<td><select name="orderdetail[' + counter + '][product_id]" class="form-control product rowchange select2" onchange="getproductinfo(this)"/> </select></td>' +
                  '<td> <select class="form-control productdetails rowchange select2" name="orderdetail[' + counter + '][product_detail]" onchange="getproductdetailinfo(this)" ></select><span class="gst_percent" style="display:none;"></span> <br><span class="gstamount" style="display:none;"></span> <br><span class="linediscount" style="display:none;"></span> <br><input type="hidden" name="orderdetail[' + counter + '][tax_amount]" class="form-control tax_amount readonly"/><input type="hidden" name="orderdetail[' + counter + '][discount_amount]" class="form-control discountamount readonly"/></td>'+
                  '<td><input type="number" name="orderdetail[' + counter + '][price]" class="form-control price readonly"/></td>' +
                  '<td><input type="number" name="orderdetail[' + counter + '][discount]" class="form-control discount readonly"/></td>' +
                  '<td><input type="text" name="orderdetail[' + counter + '][quantity]" class="form-control quantity rowchange" /></td>' +
                  '<td><input type="text" name="orderdetail[' + counter + '][line_total]" class="form-control total rowchange" readonly /></td>' +
                  '<td class="td-actions text-center"><a href="#" class="remove-rows btn btn-danger btn-xs"> <i class="fa fa-minus"></i></a></td> </tr>';
          $table.append(newRow);
      });
   
      $table.on('click', '.remove-rows', function() {
          $(this).closest('tr').remove();
      });
   
    $('#tab_logic tbody').on('keyup change',function(){
      calc();
    });
   });


   //new
    $('.cluster_discount').on('change',function(){
      var cls_dis = $(this).val();  
    
     $('.clustered_dis').val(cls_dis);  
    }).trigger('change');


    $('.deal_discnt').on('keyup',function(){
      var deal_discnt = $(this).val();   
      $('.deal_dis').val(deal_discnt);  
    }).trigger('keyup');


    $('.distributor_discount').on('keyup',function(){
      var distributot_dis = $(this).val();   
      $('.distributot_dis').val(distributot_dis);  
    }).trigger('keyup');

   $('.frieght_discount').on('keyup',function(){
      var frieght_discount = $(this).val();   
      $('.frieght_dis').val(frieght_discount);  
    }).trigger('keyup');


   //new

   
    function sellerinfo()
    {
      var customer_id = $("select[name=seller_id]").val();
      if(customer_id){
          $.ajax({
              url: "{{ url('getCustomerData') }}",
              dataType: "json",
              type: "POST",
              data:{ _token: "{{csrf_token()}}", customer_id:customer_id},
              success: function(res){
                  if(res)
                  {
                      $(".seller_address").empty();
                      $('.seller_address').append(res.address1+'<br> '+res.address2+'<br>Phone : '+res.mobile+'<br>Email: '+res.email);
                  }
                  else
                  {
                      $(".seller_address").empty();
                  }
              }
          });
      }
      else
      {
         $(".buyer_address").empty(); 
      }
    }
    function buyerinfo()
    {
        var customer_id = $("select[name=buyer_id]").val();
        if(customer_id){
            $.ajax({
                url: "{{ url('getCustomerData') }}",
                dataType: "json",
                type: "POST",
                data:{ _token: "{{csrf_token()}}", customer_id:customer_id},
                success: function(res){
                    if(res)
                    {
                        $(".buyer_address").empty();
                        $('.buyer_address').append(res.address1+'<br> '+res.address2+'<br>Phone : '+res.mobile+'<br>Email: '+res.email);
                    }
                    else
                    {
                        $(".buyer_address").empty();
                    }
                }
            });
        }
        else
        {
           $(".buyer_address").empty(); 
        } 
    }
    function getproductinfo(e)
    {
      var base_url =$('.baseurl').data('baseurl'); 
      var row = $(e).parent().parent();
      var product_id = $(e).val(); 
   
      $.ajax({
         url: "{{ url('getProductInfo') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}",product_id:product_id},
         success: function(res)
         {
            row.find('.price').empty();
            row.find('.gst_percent').empty();
            row.find('.price').val(res.price);
            row.find('.gst_percent').append(res.gst);
            row.find('.discount').val(res.discount);
            row.find('.discount').attr("max",res.max_discount);
            if(res.productdetails){
              $.each(res.productdetails,function(key,value){ 
                row.find('.productdetails').append('<option value="'+value.id+'">'+value.detail_title+'</option>');
   
              });
            }
         }
     });
    }

   function getproductdetailinfo(e)
   {
      var base_url =$('.baseurl').data('baseurl'); 
      var row = $(e).parent().parent();
      var productdetail_id = $(e).val(); 
      $.ajax({
         url: "{{ url('getProductDetailInfo') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}",productdetail_id:productdetail_id},
         success: function(res)
         {
            row.find('.price').empty();
            row.find('.gst_percent').empty();
            row.find('.price').val(res.price);
            row.find('.gst_percent').append(res.gst);
            row.find('.discount').val(res.discount);
            row.find('.discount').attr("max",res.max_discount);
         }
     });
   }
   
    $('#tab_logic tbody').on('keyup change',function(){
      calc();
    });
   
    // function calc()
    // {
    //   $('#tab_logic tbody tr').each(function(i, element) {
    //     var html = $(this).html();
    //     if(html!='')
    //     {
    //       var quantity = $(this).find('.quantity').val();
    //       var price = $(this).find('.price').val();
    //       var discount = $(this).find('.discount').val();
    //       var total = quantity*price;
    //       var discount_amount = total * discount / 100;
    //       total = total - discount_amount;
    //        if($("#withoutgst").is(":checked")){
    //             var tax_amount = 0.00;
    //         }
    //         else
    //         {
    //            var gst = $(this).find('.gst_percent').html();
    //            var tax_amount = total * gst / 100;
    //         }
    //       $(this).find('.total').val((total).toFixed(2));
    //       $(this).find('.tax_amount').val((tax_amount).toFixed(2));
    //       $(this).find('.gstamount').empty();
    //       $(this).find('.gstamount').append(tax_amount);
    //       $(this).find('.discountamount').empty();
    //       $(this).find('.discountamount').val(discount_amount);
    //       calc_total();
    //     }
    //   });
    // }

      $('#tab_logic tbody tr').each(function(i, element) {

        var html = $(this).html();
        if(html!='')
        { 
           
            var quantity = $(this).find('.quantity').val();
            var price = $(this).find('.price').val();
            var discount = $(this).find('.discount').val();
            var total = quantity*price;
            var discount_amount = total * discount / 100;
             total = total - discount_amount;
         
            //code for scheme
            var ebd_dis = 0;
            var ebd_discount = 0;


           // ebd_discount = $(this).find('.scheme_dis').val();   
           // // var ebd_dis = total * ebd_discount / 100;
           //  ebd_dis = total * ebd_discount / 100;
           //  total = total - ebd_dis;
           //  var ebd_amount = $(this).find('.ebd_amount').val((ebd_dis).toFixed(2));  


           var scheme_value_type = $(this).find('.scheme_value_type').val();
            if(scheme_value_type == 'percentage'){
           ebd_discount = $(this).find('.scheme_dis').val();   
           ebd_dis = total * ebd_discount / 100;
            total = total - ebd_dis;
           var ebd_amount = $(this).find('.ebd_amount').val((ebd_dis).toFixed(2));        
            }

           if(scheme_value_type == 'value'){
           ebd_discount = $(this).find('.scheme_dis').val();   
           ebd_dis = ebd_discount;
            total = total - ebd_discount;
           var ebd_amount = $(this).find('.ebd_amount').val(ebd_dis);       

            } 

        
            //code scheme end

            //new code for clustor
            var clusters_discount = $(this).find('.clustered_dis').val();   
            var clus_dis = total * clusters_discount / 100;
             total = total - clus_dis;

             $(this).find('.clus_amounts').val((clus_dis).toFixed(2));   

            //end clustor 




            // start deal discount
            var deal_dis = $(this).find('.deal_dis').val();   
            var deal_dis_cal = total * deal_dis /100;
             total = total - deal_dis_cal;


             $(this).find('.deal_amounts').val((deal_dis_cal).toFixed(2));
            // end deal discount 

            // start code for distributor
            var distributots_dis = $(this).find('.distributot_dis').val();  
            var distributots_dis_cal = total * distributots_dis/100; 
             total = total - distributots_dis_cal;
             $(this).find('.distributot_amounts').val((distributots_dis_cal).toFixed(2));
            // end distributor


            var gst = $(this).find('.gst_percent').html();
            var tax_amount = total * gst / 100;

            if(gst == 5){
            var five_amount = total * gst /100;  
             $(this).find('.five_gst').val((five_amount).toFixed(2)); 
            }else if(gst == 12){

            var twelve_amount = total * gst /100;     
            $(this).find('.twelve_gst').val((twelve_amount).toFixed(2));

            }else if(gst == 18){
            var eighteen_amount = total * gst /100; 
            $(this).find('.eighteen_gst').val((eighteen_amount).toFixed(2));   

            }else{
             var twenty_eight_amount = total * gst /100; 
             $(this).find('.twenti_eight_gst').val((twenty_eight_amount).toFixed(2)); 
            }
            

         $(this).find('.total').val((total).toFixed(2));

          
         //$('#tab_logic tr:last').find('.total').val((total).toFixed(2));
         //$('#tab_logic tr:last').find(".total").empty();

           //$('#tab_logic tr:last').find('.total').val((total).toFixed(2));

          $(this).find('.tax_amount').val((tax_amount).toFixed(2));
          $(this).find('.gstamount').empty();
          $(this).find('.gstamount').append(tax_amount);
          $(this).find('.discountamount').empty();
          $(this).find('.discountamount').val(discount_amount);
          calc_total();

        }

      });   



   
    // function calc_total()
    // {
    //   total=0;
    //   subtotal=0;
    //   taxamount=0;
    //   discount=0;
    //   var extra_discount = $("input[name=extra_discount]").val();
    //   var discount_amount = 0;
    //   $('.total').each(function() {
    //     total += parseInt($(this).val());
    //   });
    //   $('.tax_amount').each(function() {
    //     taxamount += parseInt($(this).val());
    //   });
    //   $('.discountamount').each(function() {
    //     discount += parseInt($(this).val());
    //   });
    //    discount_amount = total * extra_discount / 100;
    //    subtotal = total;
    //    total -= discount_amount;
    //   $('#subtotal').val((subtotal).toFixed(2));
    //   $('#totalgst').val(taxamount.toFixed(2));
    //   $('#totaldiscount').val(discount.toFixed(2));
    //   $('.extra_discount_amount').empty();
    //   $('.extra_discount_amount').val(discount_amount.toFixed(2));
    //   $('#grandtotal').val((total+taxamount).toFixed(2));
    // }

   function calc_total()
    {   

     total_new_ebd = 0;
     total_scheme = 0;

     clus_amounts_new = 0;
     total_slustor_amount_new = 0;

     deal_amounts_new = 0;
     total_deal_amounts_new = 0;

     distributot_amounts_new = 0;
     total_distributot_amounts_new = 0;


     five_gst_new = 0;
     total_five_gst_new = 0;

     twelve_gst_new = 0;
     total_twelve_gst_new = 0;


     eighteen_gst_new = 0;
     total_eighteen_gst_new = 0;

     twenti_eight_gst_new = 0;
     total_twenti_eight_gst_new = 0;

    
      five_gst=0; 
      twelve_gst=0; 
      eighteen_gst=0;
      twenti_eight_gst=0;

      clus_amounts=0;  
      scheme_discount = 0; 
      deal_amounts = 0;
      cluster_amnt=0;  
      distributot_amounts=0;
      frieght_amounts=0;

      total=0;
      subtotal=0;
      taxamount=0;
      discount=0;
      transportation = 0;
      var extra_discount = $("input[name=extra_discount]").val();


      discount_amount_cluster = 0;


      var transportamt = $("#transportation_amount").val();
      if(transportamt)
      {
         transportation = parseInt(transportamt);
      }
      var discount_amount = 0;
      $('.total').each(function() {
        // total += parseInt($(this).val());
        total += parseFloat($(this).val());
      });

     //scheme discount start
      $('.ebd_amount').each(function() {
        // scheme_discount += parseInt($(this).val());
        scheme_discount += parseFloat($(this).val());
      }); 


       //new skm

        $('.ebd_amount_new').each(function() {
         total_new_ebd += parseFloat($(this).val());
        });


      total_scheme = total_new_ebd+scheme_discount;

       //new skm end
     
     //$('#scheme_discount').val((scheme_discount).toFixed(2));

     $('#scheme_discount').val((scheme_discount).toFixed(2));
     //scheme discount end

     //cluster discout start
       var cluster_amnt = $(".cluster_discount").val();
        discount_amount_cluster_new = total * cluster_amnt / 100;
    

      $('.clus_amounts').each(function() {
        clus_amounts += parseFloat($(this).val());
         // $('.extra_cluster_discount').val(clus_amounts.toFixed(2));
      }); 


       $('.clus_amounts_new').each(function() {
         clus_amounts_new += parseFloat($(this).val());
        });

      total_slustor_amount_new = clus_amounts+clus_amounts_new;

      $('.extra_cluster_discount').val(clus_amounts.toFixed(2));
      //cluster discout end


      //deal discount
      $('.deal_amounts').each(function() {
        deal_amounts += parseFloat($(this).val());
         // $('.extra_discount_amount').val(deal_amounts.toFixed(2));
      });

       $('.deal_amounts_new').each(function() {
         deal_amounts_new += parseFloat($(this).val());
        });

       total_deal_amounts_new = deal_amounts+deal_amounts_new; 
       $('.extra_discount_amount').val(deal_amounts.toFixed(2));


      //deal discoun


      //distributor discount
       $('.distributot_amounts').each(function() {
        distributot_amounts += parseFloat($(this).val());
         // $('.distributor_discount_amount').val(distributot_amounts.toFixed(2));
      }); 

        $('.distributot_amounts_new').each(function() {
         distributot_amounts_new += parseFloat($(this).val());
        });

      total_distributot_amounts_new = distributot_amounts+distributot_amounts_new;

      $('.distributor_discount_amount').val(distributot_amounts.toFixed(2));

      //end distributor discount


       //frieght_discount start

     $('.frieght_amounts').each(function() {
        frieght_amounts += parseFloat($(this).val());
         $('.frieght_discount_amount').val(frieght_amounts.toFixed(2));
      }); 

       //frieght_discount  end






      $('.tax_amount').each(function() {
        taxamount += parseInt($(this).val());
      });

      $('.discountamount').each(function() {
        discount += parseInt($(this).val());
      });

      
       //new calculation

         subtotal = total;

       //end ne calculation

        discount_amount = total * extra_discount / 100;

        //conditions for tax start

        $('.five_gst').each(function() {
         five_gst += parseFloat($(this).val())||0;
         // $('.5_gst').val(five_gst.toFixed(2));
         });

         $('.five_gst_new').each(function() {
         five_gst_new += parseFloat($(this).val())||0;
         //$('.5_gst').val(five_gst.toFixed(2));

         });

         total_five_gst_new = five_gst+five_gst_new; 
         $('.5_gst').val(five_gst.toFixed(2));




        $('.twelve_gst').each(function() {
        twelve_gst += parseFloat($(this).val())||0;
          //$('.12_gst').val(twelve_gst.toFixed(2));
         });

        $('.twelve_gst_new').each(function() {
        twelve_gst_new += parseFloat($(this).val())||0;
         // $('.12_gst').val(twelve_gst.toFixed(2));
         });

        total_twelve_gst_new = twelve_gst+twelve_gst_new;
        $('.12_gst').val(twelve_gst.toFixed(2));


        $('.eighteen_gst').each(function() {
        eighteen_gst += parseFloat($(this).val())||0;
          // $('.18_gst').val(eighteen_gst.toFixed(2));
         });


        $('.eighteen_gst_new').each(function() {
        eighteen_gst_new += parseFloat($(this).val())||0;
           // $('.18_gst').val(eighteen_gst.toFixed(2));
         });

       total_eighteen_gst_new = eighteen_gst+eighteen_gst_new;

        $('.18_gst').val(eighteen_gst.toFixed(2));


        $('.twenti_eight_gst').each(function() {
        twenti_eight_gst += parseFloat($(this).val())||0;
         //$('.28_gst').val(twenti_eight_gst.toFixed(2));
         });


         $('.twenti_eight_gst_new').each(function() {
        twenti_eight_gst_new += parseFloat($(this).val())||0;
         // $('.28_gst').val(twenti_eight_gst.toFixed(2));
         });

         total_twenti_eight_gst_new = twenti_eight_gst+twenti_eight_gst_new;
         $('.28_gst').val(twenti_eight_gst.toFixed(2));



    //conditions for tax  end 

      $('#subtotal').val((subtotal).toFixed(2));
      $('#totalgst').val(taxamount.toFixed(2));
      $('#totaldiscount').val(discount.toFixed(2));
      $('#grandtotal').val((total+five_gst+twelve_gst+eighteen_gst+twenti_eight_gst).toFixed(2));
     
    }   




   
    function getProductlist()
    {
      var base_url =$('.baseurl').data('baseurl'); 
        $.ajax({
          url: "{{ url('getProductData') }}",
          dataType: "json",
          type: "POST",
          data:{ _token: "{{csrf_token()}}"},
          success: function(res){
            var table = document.getElementById(tab_logic),rIndex;
            if(res){
              $('#tab_logic tr:last').find(".product").empty();
                $('#tab_logic tr:last').find(".product").append('<option value="">Select Product</option>');
              $.each(res,function(key,value){ 
                $('#tab_logic tr:last').find('.product').append('<option value="'+value.id+'">'+value.display_name+'</option>');
   
              });
            }
            else{
               row.find(".product").empty();
            }
          }
      });
    }
</script>
</x-app-layout>