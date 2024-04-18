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
                                <input type="text" name="orderdetail[{{ $index }}][cash_dis]" class="cash_dis" hidden>
                                <input type="text" name="orderdetail[{{ $index }}][cash_amounts]" class="cash_amounts" hidden>

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

                     <?php

                     $ebd_dicount_sum = 0;
                     $scheme_dicount_sum = 0;
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


                     if (!empty($orders['orderdetails'])) {

                        foreach ($orders['orderdetails'] as $keys => $rowss) {

                           $ebd_dicount_sum += $rowss['ebd_amounts'];
                           $scheme_dicount_sum += $rowss['scheme_amount'];
                           $clustor_dicount_sum += $rowss['cluster_amount'];
                           $deal_dicount_sum += $rowss['deal_amount'];
                           $distributor_dicount_sum += $rowss['distributor_amount'];
                           $frieght_dicount_sum += $rowss['frieght_amount'];



                           $cluster_discount = $rowss['cluster_discount'];
                           $deal_discount = $rowss['deal_discount'];
                           $distributor_discount = $rowss['distributor_discount'];
                           $frieght_discount = $rowss['frieght_discount'];

                           if ($rowss['gst'] == 5) {

                              //$gst_amount_5+= $rowss['gst_amount'];
                              $gst_amount_5 += $rowss['tax_amount'];
                           } elseif ($rowss['gst'] == 12) {

                              //$gst_amount_12+= $rowss['gst_amount'];
                              $gst_amount_12 += $rowss['tax_amount'];
                           } elseif ($rowss['gst'] == 18) {

                              //$gst_amount_18+= $rowss['gst_amount'];
                              $gst_amount_18 += $rowss['tax_amount'];
                           } else {

                              //$gst_amount_28+= $rowss['gst_amount'];
                              $gst_amount_28 += $rowss['tax_amount'];
                           }
                        }
                     }

                     ?>


                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">Scheme Discount</label>
                        </div>
                        <div class="col-sm-8">
                           <!-- <input type="number" name='scheme_discount' id="scheme_discount" class="form-control scheme_discount" value="{!! old( 'scheme_discount', $orders['scheme_discount']) !!}" readonly/> -->

                           <input type="number" readonly name='scheme_discount' id="scheme_discount" class="form-control scheme_discount" value="{!! old( 'scheme_discount', $scheme_dicount_sum) !!}" />
                           @if($errors->has('scheme_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('scheme_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">EBD Discount%</label>
                        </div>
                        <div class="col-sm-4">
                           <select disabled name='ebd_discount' class="form-control ebd_discount">
                              <option value="">Select EBD Discount</option>
                              <option value="1" {{($orders['ebd_discount'] == '1')?'selected':''}}>1%</option>
                              <option value="2" {{($orders['ebd_discount'] == '2')?'selected':''}}>2%</option>
                              <option value="3" {{($orders['ebd_discount'] == '3')?'selected':''}}>3%</option>
                           </select>
                        </div>
                        <div class="col-sm-4">
                           <input type="text" name="extra_ebd_discount" class="extra_ebd_discount form-control" value="{{$orders['ebd_amount']}}" readonly>
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">MOU Discount%</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <div class="input-group-prepend">
                              </div>
                         
                              <input type="number" readonly name='distributor_discount' class="form-control distributor_discount" value="{!! old( 'distributor_discount', $orders['distributor_discount']) !!}" />

                              <input type="number" step="00.01" name="distributor_discount_amount" class="form-control distributor_discount_amount" readonly value="{!! old( 'distributor_discount_amount', $orders['distributor_amount']) !!}">
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
                           <label class="bmd-label">Special Discount%</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <div class="input-group-prepend">
                              </div>
                              <input readonly type="number" name='special_discount' class="form-control special_discount" value="{!! old( 'special_discount', $orders['special_discount']) !!}" />

                              <input type="text" name="special_discount_amount" class="form-control special_discount_amount" value="{!! old( 'special_discount_amount', $orders['special_amount']) !!}" readonly>
                           </div>
                           @if($errors->has('special_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('special_discount') }}
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

                              <div class="col-sm-4">
                                 <select disabled name='frieght_discount' class="form-control frieght_discount">
                                    <option value="">Select Frieght Discount</option>
                                    <option value="1" {!! old( 'frieght_discount' , $orders['frieght_discount'])=='1' ?'selected':'' !!}>1%</option>
                                 </select>
                              </div>
                              <div class="col-sm-4">
                                 <input readonly type="text" name="frieght_discount_amount" class="form-control frieght_discount_amount" readonly value="{!! old( 'frieght_discount_amount', $orders['frieght_amount']) !!}">
                              </div>
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
                           <label class="bmd-label">Cluster Discount%</label>
                        </div>
                        <div class="col-sm-4">
                           <select disabled name='cluster_discount' class="form-control cluster_discount">
                              <option value="">Select Cluster Discount</option>
                              <option value="1" <?php if ($orders['cluster_discount'] == 1) {
                                                   echo "selected";
                                                } ?>>1%</option>
                              <option value="2" <?php if ($orders['cluster_discount'] == 2) {
                                                   echo "selected";
                                                } ?>>2%</option>
                              <option value="3" <?php if ($orders['cluster_discount'] == 3) {
                                                   echo "selected";
                                                } ?>>3%</option>
                           </select>
                        </div>
                        <div class="col-sm-4">
                           <!-- <input type="text" name="extra_cluster_discount" class="extra_cluster_discount" readonly>   -->
                           <input type="text" name="extra_cluster_discount" class="extra_cluster_discount form-control" readonly value="{!! old( 'extra_cluster_discount', $orders['cluster_amount']) !!}">
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
                              <!-- <input type="number" name='extra_discount' class="form-control deal_discnt" value="{!! old( 'extra_discount', $orders['extra_discount']) !!}"/> -->

                              <!-- <input type="text" name="extra_discount_amount" class="form-control extra_discount_amount" readonly> -->

                              <input readonly type="number" name='extra_discount' class="form-control deal_discnt" value="{!! old( 'extra_discount', $deal_discount) !!}" />

                              <input type="text" name="extra_discount_amount" class="form-control extra_discount_amount" readonly value="{!! old( 'extra_discount_amount', $orders['deal_amount']) !!}">
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
                           <label class="bmd-label">Cash Discount%</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <div class="input-group-prepend">
                              </div>
                              
                              <input type="number" name='cash_discount' class="form-control cash_discount" value="{!! old( 'cash_discount', $deal_discount) !!}" />

                              <input type="text" name="cash_amount" class="form-control cash_amount" readonly value="{!! old( 'cash_amount', $orders['cash_amount']) !!}">
                           </div>
                           @if($errors->has('cash_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('cash_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>


                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">{!! trans('panel.order.sub_total') !!}</label>
                        </div>
                        <div class="col-sm-8">
                           <input type="number" name='sub_total' class="form-control" id="subtotal" readonly value="{!! old( 'sub_total', $orders['sub_total']) !!}" />
                           @if($errors->has('sub_total'))
                           <div class="invalid-feedback">
                              {{ $errors->first('sub_total') }}
                           </div>
                           @endif
                        </div>
                     </div>



                     <!-- for tax start -->
                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">5%Tax</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <input type="text" name='5_gst' class="form-control 5_gst" value="{!! old( '5_gst', $orders['gst5_amt']) !!}" disabled />
                           </div>
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">12%Tax</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <input type="number" name='12_gst' class="form-control 12_gst" readonly value="{!! old( '12_gst', $orders['gst12_amt']) !!}" disabled />
                           </div>
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">18%Tax</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <input type="number" name='18_gst' class="form-control 18_gst" readonly value="{!! old( '18_gst', $orders['gst18_amt']) !!}" disabled />
                           </div>
                        </div>
                     </div>

                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">28%Tax</label>
                        </div>
                        <div class="col-sm-8">
                           <div class="input-group">
                              <input type="number" name='28_gst' class="form-control 28_gst" readonly value="{!! old( '28_gst', $orders['gst28_amt']) !!}" disabled />
                           </div>
                        </div>
                     </div>

                     <!-- for tax end -->



                     <!-- <div class="form-group row"> 
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
                     </div> -->


                     <div class="form-group row" hidden>
                        <div class="col-sm-4">
                           <label class="bmd-label">{!! trans('panel.order.total_discount') !!}</label>
                        </div>
                        <div class="col-sm-8">
                           <input type="number" name='total_discount' id="totaldiscount" class="form-control" value="{!! old( 'total_discount', $orders['total_discount']) !!}" readonly />
                           @if($errors->has('total_discount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('total_discount') }}
                           </div>
                           @endif
                        </div>
                     </div>
                     <!-- <div class="form-group row">
                       <div class="col-sm-4">
                        <label class="bmd-label">Transportation</label>
                      </div>
                        <div class="col-sm-8">
                           <input type="number" name='transportation_amount' id="transportation_amount" class="form-control" value="{!! old( 'transportation_amount', $orders['transportation_amount']) !!}"/>
                           @if($errors->has('transportation_amount'))
                           <div class="invalid-feedback">
                              {{ $errors->first('transportation_amount') }}
                           </div>
                           @endif
                        </div>
                     </div> -->
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


                     <div class="form-group row">
                        <div class="col-sm-4">
                           <label class="bmd-label">Remark</label>
                        </div>
                        <div class="col-sm-8">
                           <input type="text" name='order_remark' id="order_remark" class="form-control" value="{!! old( 'order_remark',$orders['order_remark']) !!}" />
                           @if($errors->has('order_remark'))
                           <div class="invalid-feedback">
                              {{ $errors->first('order_remark') }}
                           </div>
                           @endif
                        </div>
                     </div>

                     <input type="hidden" name="first_total" id="first_total" value="{{$orders['sub_total']}}">
                     <input type="hidden" name="first_grandtotal" id="first_grandtotal" value="{{$orders['grand_total']}}">

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
<script>
   $(".cash_discount").on('keyup', function(){
      var firsttotal = $("#first_total").val();
      var firstgrandtotal = $("#first_grandtotal").val();
      var cash_discount = $(this).val();
      var dis_is = (firsttotal*cash_discount)/100;
      $("#subtotal").val((firsttotal-dis_is).toFixed(2));
      $("#grandtotal").val((firstgrandtotal-dis_is).toFixed(2));
      $(".cash_amount").val((dis_is).toFixed(2));

   })

</script>

</x-app-layout>