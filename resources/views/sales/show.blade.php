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
          <div class="card-body card-header card-header-icon card-header-theme">
            <div class="row">
              <div class="col-12">
                <h4 class="card-title pb-3">{!! trans('panel.sale.title_singular') !!}</h3>
              </div>
              <!-- /.col -->
            </div>

            <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
                <div class="col-12">
                  <h4>
                    <small class="float-right">Date: {!! $sales['invoice_date'] !!}</small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-5 invoice-col">
                  From
                  <address>
                    <strong>{{ data_get($fromParty, 'shop_name') ?? data_get($fromParty, 'trade_name') ?? data_get($fromParty, 'legal_name') ?? data_get($fromParty, 'name') ?? '-' }}</strong><br>
                    {{ data_get($fromParty, 'address_line') ?? data_get($fromParty, 'billing_address') ?? data_get($fromParty, 'customeraddress.address1') ?? '' }}<br>
                    {{ data_get($fromParty, 'belt_area_market_name') ?? data_get($fromParty, 'customeraddress.locality') ?? '' }}
                    {{ data_get($fromParty, 'city.city_name') ?? data_get($fromParty, 'billing_city') ?? data_get($fromParty, 'customeraddress.cityname.city_name') ?? '' }}
                    {{ data_get($fromParty, 'pincode.pincode') ?? data_get($fromParty, 'billing_pincode') ?? data_get($fromParty, 'customeraddress.pincodename.pincode') ?? '' }}<br>
                    @if(data_get($fromParty, 'mobile_number') ?? data_get($fromParty, 'mobile'))
                    Phone: {{ data_get($fromParty, 'mobile_number') ?? data_get($fromParty, 'mobile') }}<br>
                    @endif
                    @if(data_get($fromParty, 'email'))
                    Email: {{ data_get($fromParty, 'email') }}
                    @endif
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-5 invoice-col">
                  To
                  <address>
                    <strong>{{ data_get($toParty, 'shop_name') ?? data_get($toParty, 'trade_name') ?? data_get($toParty, 'legal_name') ?? data_get($toParty, 'name') ?? '-' }}</strong><br>
                    {{ data_get($toParty, 'address_line') ?? data_get($toParty, 'billing_address') ?? data_get($toParty, 'customeraddress.address1') ?? '' }}<br>
                    {{ data_get($toParty, 'belt_area_market_name') ?? data_get($toParty, 'customeraddress.locality') ?? '' }}
                    {{ data_get($toParty, 'city.city_name') ?? data_get($toParty, 'billing_city') ?? data_get($toParty, 'customeraddress.cityname.city_name') ?? '' }}
                    {{ data_get($toParty, 'pincode.pincode') ?? data_get($toParty, 'billing_pincode') ?? data_get($toParty, 'customeraddress.pincodename.pincode') ?? '' }}<br>
                    @if(data_get($toParty, 'mobile_number') ?? data_get($toParty, 'mobile'))
                    Phone: {{ data_get($toParty, 'mobile_number') ?? data_get($toParty, 'mobile') }}<br>
                    @endif
                    @if(data_get($toParty, 'email'))
                    Email: {{ data_get($toParty, 'email') }}
                    @endif
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-2 invoice-col">
                  <b>Invoice #{!! $sales['invoice_no'] !!}</b><br>
                  <br>
                  <b>Order ID:</b> {!! $sales['order_id'] !!}<br>
                  <b>Payment Due:</b> {!! $sales['orderno'] !!}<br>
                  <b>No:</b> {!! $sales['sales_no'] !!}
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>{!! trans('panel.global.products') !!}</th>
                        <th>{!! trans('panel.global.product_detail') !!}</th>
                        <th>{!! trans('panel.global.price') !!}</th>
                        <th>{!! trans('panel.global.quantity') !!}</th>
                        <th>{!! trans('panel.global.amount') !!}</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($sales->exists && isset($sales['saledetails']))
                      @foreach($sales['saledetails'] as $rows )
                      <tr>
                        <td>{!! $rows['products']['product_name'] !!}</td>
                        <td>GST Percent : <span class="gst_percent">{!! isset($rows['productdetails']['gst']) ? $rows['productdetails']['gst'] : '' !!}</span> <br>
                          GST Amount : <span class="gstamount">{!! $rows['tax_amount'] !!}</span> <br>
                        </td>
                        <td>{!! $rows['price'] !!}</td>
                        <td>{!! $rows['quantity'] !!}</td>
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
                  <p class="lead">{!! trans('panel.sale.fields.description') !!}:</p>
                  <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                    {!! $sales['description'] !!}
                  </p>
                </div>
                <!-- /.col -->
                <div class="col-6">
                  <div class="table-responsive">
                    <table class="table">
                      <tbody>
                        <tr>
                          <th style="width:50%">Subtotal:</th>
                          <td>{!! $sales['sub_total'] !!}</td>
                        </tr>
                        <tr>
                          <th>Tax</th>
                          <td>{!! $sales['total_gst'] !!}</td>
                        </tr>
                        <tr>
                          <th>Total:</th>
                          <td>{!! $sales['grand_total'] !!}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
  </section>
  <!-- /.content -->
</x-app-layout>
