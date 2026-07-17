<x-app-layout>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <h3 class="card-title pb-3">{!! trans('panel.order.title_singular') !!} Detail</h3>
              </div>
              <!-- /.col -->
            </div>

            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-3">
                  <h4>
                    <small class="float-left">{!! trans('panel.order.id') !!} # {!! $orders['id'] !!}</small>
                  </h4>
                </div>
                <div class="col-3">
                  <h4>
                    <!-- <img src="" class="brand-image" width="70px" alt="Logo"> <span> </span> -->
                    <small class="float-left">{!! trans('panel.order.orderno') !!}: {!! $orders['orderno'] !!}</small>
                  </h4>
                </div>
                <div class="col-3">
                  <h4>
                    <!-- <img src="" class="brand-image" width="70px" alt="Logo"> <span> </span> -->
                    <small class="float-left">Date: {!! date("d-M-Y", strtotime($orders['order_date'])) !!}</small>
                  </h4>
                </div>
                <div class="col-3">
                  <h4>
                    <!-- <img src="" class="brand-image" width="70px" alt="Logo"> <span> </span> -->
                    <small class="float-left">Created By: {!! $orders['createdbyname']['name'] !!}</small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <hr>
              <!-- info row -->
              @php
                $seller = $orders->sellers;
                $buyer = $orders->buyers;

                $sellerName = $seller?->shop_name ?? $seller?->trade_name ?? $seller?->legal_name ?? $seller?->owner_name ?? '';
                $sellerAddress = $seller?->address_line ?? $seller?->billing_address ?? '';
                $sellerArea = $seller?->belt_area_market_name ?? '';
                $sellerCity = $seller?->city?->city_name ?? $seller?->billing_city ?? '';
                $sellerPincode = $seller?->pincode?->pincode ?? $seller?->billing_pincode ?? '';
                $sellerPhone = $seller?->mobile_number ?? $seller?->mobile ?? '';
                $sellerContact = $seller?->owner_name ?? $seller?->contact_person ?? '';

                $buyerName = $buyer?->shop_name ?? $buyer?->trade_name ?? $buyer?->legal_name ?? $buyer?->owner_name ?? '';
                $buyerAddress = $buyer?->address_line ?? $buyer?->billing_address ?? '';
                $buyerArea = $buyer?->belt_area_market_name ?? '';
                $buyerCity = $buyer?->city?->city_name ?? $buyer?->billing_city ?? '';
                $buyerPincode = $buyer?->pincode?->pincode ?? $buyer?->billing_pincode ?? '';
                $buyerPhone = $buyer?->mobile_number ?? $buyer?->mobile ?? '';
                $buyerContact = $buyer?->owner_name ?? $buyer?->contact_person ?? '';
              @endphp
              <div class="row invoice-info">
                <div class="col-sm-6 invoice-col">
                  From
                  <address>
                    <strong>{{ $sellerName }}</strong><br>
                    {{ $sellerAddress }}<br>
                    {{ collect([$sellerArea, $sellerCity, $sellerPincode])->filter()->implode(', ') }}<br>
                    Phone: {{ $sellerPhone }}<br>
                    @if($sellerContact) Contact: {{ $sellerContact }}<br> @endif
                    @if($seller?->email) Email: {{ $seller->email }} @endif
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-6 invoice-col">
                  To
                  <address>
                    <strong>{{ $buyerName }}</strong><br>
                    {{ $buyerAddress }}<br>
                    {{ collect([$buyerArea, $buyerCity, $buyerPincode])->filter()->implode(', ') }}<br>
                    Phone: {{ $buyerPhone }}<br>
                    @if($buyerContact) Contact: {{ $buyerContact }} @endif
                  </address>
                </div>
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>{!! trans('panel.global.products') !!}</th>
                        <th>{!! trans('panel.global.quantity') !!}</th>
                        <!-- <th>{!! trans('panel.global.product_detail') !!}</th> -->
                        <th>{!! trans('panel.global.list_price') !!}</th>
                        <th>Tax</th>
                        <th>Trade Discount%</th>
                        <th>{!! trans('panel.global.amount') !!}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($orders->exists && isset($orderdetails))
                      @foreach($orderdetails as $rows )
                      <tr>
                        <td>
                          {!! isset($rows['products']['product_name']) ? $rows['products']['product_name'] : '' !!}
                          {!! isset($rows['products']['product_code']) ? $rows['products']['product_code'] : '' !!} <br>
                          Detail Title :
                          <span class="prd_title">
                            {!! isset($rows['productdetails']['detail_title']) ? $rows['productdetails']['detail_title'] : '' !!}
                          </span>
                        </td>

                        <td>{!! $rows['quantity'] !!}</td>
                        <!--  <td>
                              GST Percent : <span class="gst_percent">{!! isset($rows['productdetails']['gst']) ? $rows['productdetails']['gst'] : '' !!}</span> <br>
                            GST Amount : <span class="gstamount">{!! $rows['tax_amount'] !!}</span> <br>
                            </td> -->

                        <td>{!! $rows['price'] !!}</td>
                        <td>{!! $rows['gst'] !!}</td>
                        <td>{!! $rows['discount'] !!}</td>
                        <td>{!! $rows['line_total'] !!}</td>
                      </tr>
                      @endforeach
                      @endif
                    </tbody>
                  </table>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                  <p class="lead"></p>
                  <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    {!! $orders['description'] !!}
                  </p>
                </div>
                <!-- /.col -->
                <div class="col-6">
                  <p class="lead"></p>


                  <?php

                  $scheme_dicount_sum = 0;
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

                  if (!empty($orderdetails)) {

                    foreach ($orderdetails as $keys => $rowss) {

                      $scheme_dicount_sum += $rowss['schme_amount'];
                      $ebd_dicount_sum += $rowss['ebd_amount'];
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

                  <div class="table-responsive">
                    <table class="table">
                      <tbody>
                        <!-- @if($orders->product_cat_id == '2')
                        <tr>
                          <th style="width:50%">DOD Discount :</th>
                          <td>{!! $orders->dod_discount !!}%</td>
                          <td>-</td>
                        </tr>
                        <tr>
                          <th style="width:50%">Special Distribution Discount :</th>
                          <td>{!! $orders->special_distribution_discount !!}%</td>
                          <td>-</td>
                        </tr>
                        <tr>
                          <th style="width:50%">Distribution Margin Discount :</th>
                          <td>{!! $orders->distribution_margin_discount !!}%</td>
                          <td>-</td>
                        </tr>
                        <tr>
                          <th style="width:50%">Cash Discount% :</th>
                          <td>{!! $orders->cash_discount !!} %</td>
                          <td>-</td>
                        </tr>

                        <tr>
                          <th style="width:50%">Total Discount% :</th>
                          <td>{!! $orders->total_fan_discount !!}%</td>
                          <td>{!! $orders->total_fan_discount_amount !!}</td>
                        </tr>
                        @else
                        <tr>
                          <th style="width:50%">Scheme Discount :</th>
                          <td>-</td>
                          <td>{!! $scheme_dicount_sum !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">EBD Discount :</th>
                          <td>{!! $orders->ebd_discount !!}</td>
                          <td>{!! $orders->ebd_amount !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">MOU Discount% :</th>
                          <td>{!! $orders->distributor_discount !!}</td>
                          <td>{!! $orders->distributor_amount !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">Special Discount% :</th>
                          <td>{!! $orders->special_discount !!}</td>
                          <td>{!! $orders->special_amount !!}</td>
                        </tr>

                        <tr>
                          <th style="width:50%">Frieght Discount% :</th>
                          <td>{!! $orders->frieght_discount !!}</td>
                          <td>{!! $orders->frieght_amount !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">Cluster Discount% :</th>
                          <td>{!! $orders->cluster_discount!!}</td>
                          <td>{!! $orders->cluster_amount !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">Deal Discount% :</th>
                          <td>{!! $orders->deal_discount !!}</td>
                          <td>{!! $orders->deal_amount !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">Cash Discount% :</th>
                          <td>{!! $orders->cash_discount !!}</td>
                          <td>{!! $orders->cash_amount !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">Total Discount% :</th>
                          <td>{!! $ttdis !!}</td>
                          <td>{!! $totalLP-$orders['sub_total'] !!}</td>
                        </tr>
                        @endif -->
                        <!-- <tr>
                          <th style="width:50%">Subtotal :</th>
                          <td>-</td>
                          <td>{!! $orders['sub_total'] !!}</td>
                        </tr>

                        <tr>
                          <th style="width:50%">5%Tax:</th>
                          <td>-</td>
                          <td>{!! $orders->gst5_amt !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">12%Tax:</th>
                          <td>-</td>
                          <td>{!! $orders->gst12_amt !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">18%Tax:</th>
                          <td>-</td>
                          <td>{!! $orders->gst18_amt !!}</td>
                        </tr>
                        <tr>
                          <th style="width:50%">28%Tax:</th>
                          <td>-</td>
                          <td>{!! $orders->gst28_amt !!}</td>
                        </tr>


                        <tr>
                          <th>Tax</th>
                          <td>-</td>
                          <td>{!! $orders['total_gst'] !!}</td>
                        </tr> -->
                        <tr>
                          <th>Total:</th>
                          <td>-</td>
                          <td>{!! $orders['grand_total'] !!}</td>
                        </tr>
                        <tr>
                          <th>Remark:</th>
                          <td colspan="2">{!! $orders['order_remark'] !!}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- this row will not appear when printing -->
              <div class="row no-print">
                <div class="col-12">
                  @if($orders->dispatch_status !== 'Dispatched' && $orders['status_id'] != 4)
                  <a href="{!! url('order-dispatched/'.encrypt($orders->id)) !!}" class="btn btn-success float-right">Fully Dispatched</a>
                  @endif

                  @if($orders->dispatch_status !== 'Dispatched' && $orders['status_id'] != 4)
                  <a href="{!! url('order-partially-dispatched/'.encrypt($orders->id)) !!}" class="btn btn-theme float-right">Partially Dispatched</a>
                  @endif

                  @if($orders->dispatch_status !== 'Dispatched' && $orders['status_id'] != 4)
                  <button type="button" data-orderid="{!! encrypt($orders->id) !!}" id="cancleButton" class="btn btn-danger float-right">Cancel</button>
                  @endif
                  @if($orders->dispatched_quantity > 0)
                  @if(auth()->user()->can('pendding_orders'))
                  <button type="button" data-orderid="{!! encrypt($orders->id) !!}" id="penddingButton" class="btn btn-warning float-right">Pending</button>
                  @endif
                  @endif
                  @if($orders['status_id'] == 4)
                  <button type="button" disabled class="btn btn-danger float-right">Canceled</button>
                  @endif
                  @if($orders->dispatch_status === 'Dispatched' && $orders['status_id'] != 4)
                  <button type="button" disabled class="btn btn-success float-right">Dispatched</button>
                  @endif
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
  </section>
  <script>
    $("#cancleButton").on("click", function() {
      Swal.fire({
        title: "Are you sure?",
        text: "Enter remark:",
        icon: "warning",
        input: 'text',
        returnInputValueOnDeny: true,
        showCancelButton: true,
        confirmButtonText: 'Yes Cancle',
        cancelButtonText: 'No',
        inputValidator: (value) => {
          if (!value) {
            return "You need to write something!";
          }
        }
      }).then((result) => {
        if (result.value) {
          var token = $("meta[name='csrf-token']").attr("content");
          var base_url = $('.baseurl').data('baseurl');
          var orderId = $(this).data("orderid");
          $.ajax({
            url: base_url + '/order-cancle/' + orderId,
            dataType: "json",
            type: "POST",
            data: {
              _token: token,
              remark: result.value
            },
            success: function(res) {
              Swal.fire({
                title: res.status,
                text: res.message,
              });
              if (res.status == 'success') {
                window.location.href = base_url + '/orders';
              }
            }
          });
        }
      });

    })
    $("#penddingButton").on("click", function() {
      Swal.fire({
        title: "Are you sure?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
      }).then((result) => {
        if (result.value) {
          var token = $("meta[name='csrf-token']").attr("content");
          var base_url = $('.baseurl').data('baseurl');
          var orderId = $(this).data("orderid");
          $.ajax({
            url: base_url + '/order-pendding/' + orderId,
            dataType: "json",
            type: "POST",
            data: {
              _token: token,
              // remark: result.value
            },
            success: function(res) {
              Swal.fire({
                title: res.status,
                text: res.message,
              });
              if (res.status == 'success') {
                window.location.href = base_url + '/orders';
              }
            }
          });
        }
      });

    })
  </script>
  <!-- /.content -->
</x-app-layout>
