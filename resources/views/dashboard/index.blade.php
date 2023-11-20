<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="nav-wrapper position-relative end-0">
      <ul class="nav nav-pills nav-pills-warning nav-pills-icons justify-content-center" id="tabs" role="tablist">
         <li class="nav-item">
            <a class="nav-link active show" data-toggle="tab" href="#kpistab" role="tablist" onclick="getDashboardData()">
            <i class="material-icons">home</i> KPIs
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#travelsummarytab" role="tablist" onclick="travelSummaryDashboard()">
            <i class="material-icons">flight</i> Travel
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#visitsummarytab" role="tablist" onclick="visitSummaryDashboard()">
            <i class="material-icons">location_city</i> Visit
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#couponsummarytab" role="tablist" onclick="couponSummaryDashboard()">
            <i class="material-icons">payment</i> Coupons
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#ordersummarytab" id="li_ordertab" role="tablist" onclick="orderSummaryDashboard()">
            <i class="material-icons">business_center</i> Orders
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#salessummarytab" id="li_salestab" role="tablist"  onclick="saleSummaryDashboard()">
            <i class="material-icons">monetization_on</i> Sales
            </a>
         </li>
         <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#activitytab" id="li_activitytab" role="tablist" onclick="activityDashboard()">
            <i class="material-icons">verified_user</i> Activity
            </a>
         </li>
      </ul>
    </div>
    <div class="row">
   <div class="col col1">
      <label class="bmd-label-floating">User</label>
      <div class="form-group has-default bmd-form-group">
        <select class="form-control select2" name="user_id" id="user_id" data-style="select-with-transition" title="Select User">
           <option value="">Select User</option>
          @if(@isset($users ))
          @foreach($users as $user)
           <option value="{!! $user['id'] !!}" {{ old( 'user_id') == $user->id ? 'selected' : '' }}>{!! $user['name'] !!}</option>
          @endforeach
          @endif
        </select>
      </div>
    </div>
    <div class="col col2">
      <label class="bmd-label-floating">From Date</label>
      <div class="form-group has-default bmd-form-group">
        <input type="text" class="form-control datepicker" id="fromdate" name="fromdate" autocomplete="off" readonly>
      </div>
    </div>
    <div class="col col3">
      <label class="bmd-label-floating">To Date</label>
       <div class="form-group has-default bmd-form-group">
          <input type="text" class="form-control datepicker" id="todate" name="todate" autocomplete="off" readonly>
       </div>
    </div>
</div>
<div class="tab-content tab-space tab-subcategories">
   <div class="tab-pane active show" id="kpistab">
      <div class="row">
         <div class="col-md-12 text-center">
            <h4 class="section-heading mb-3 h4 mt-0">KPIs</h4>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card text-center">
                     <div class="card-body">
                        <h4 class="card-title visittargetcount">0</h4>
                        <p class="card-text">Visit Target</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card text-center">
                     <div class="card-body">
                        <h4 class="card-title visitedcount">0</h4>
                        <p class="card-text text-center">Visited</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center beatadherancecount">0</h4>
                        <p class="card-text text-center">Adherance %</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center beatproductivitycount">0</h4>
                        <p class="card-text text-center">Productivity %</p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
                <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center totaldealerscount">0</h4>
                        <p class="card-text text-center">Total Dealers</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center totalStockistcount">0</h4>
                        <p class="card-text text-center">Total Stockist</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center totalFleetOwnercount">0</h4>
                        <p class="card-text text-center">Total Fleet Owner</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center totalMechanicscount">0</h4>
                        <p class="card-text text-center">Total Mechanic</p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
                <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center activedealerscount">0</h4>
                        <p class="card-text text-center">Active Dealers</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center activeStockistcount">0</h4>
                        <p class="card-text text-center">Active Stockist</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center activeFleetOwnercount">0</h4>
                        <p class="card-text text-center">Active Fleet Owner</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center activeMechanicscount">0</h4>
                        <p class="card-text text-center">Active Mechanic</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">{!! date('F') !!} Beat Adherence</h4>
                        <div id="BeatAdherenceBar" class="ct-chart"></div>
                     </div>
                     <div class="card-footer">
                        <div class="row">
                           
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Visit Target
                              <i class="fa fa-circle text-danger pr-6"></i> Visited Counters
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">{!! date('F') !!} Beat Productivity</h4>
                        <div id="BeatProductivityBar" class="ct-chart"></div>
                     </div>
                     <div class="card-footer">
                        <div class="row">
                           
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Visited Counters
                              <i class="fa fa-circle text-danger pr-6"></i> Productive Counters
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
                <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">{!! date('Y') !!} Adherence</h4>
                        <div id="YearBeatAdherenceBar" class="ct-chart"></div>
                     </div>
                     <div class="card-footer">
                        <div class="row">
                           
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Visit Target
                              <i class="fa fa-circle text-danger pr-6"></i> Visited Counters
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">{!! date('Y') !!} Productivity</h4>
                        <div id="YearBeatProductivityBar" class="ct-chart"></div>
                     </div>
                     <div class="card-footer">
                        <div class="row">
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Visited Counters
                              <i class="fa fa-circle text-danger pr-6"></i> Productive Counters
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="travelsummarytab">
    <div class="row">
         <div class="col-md-12 text-center">
            <h4 class="section-heading mb-3 h4 mt-0">Travel Summary</h4>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card text-center">
                     <div class="card-body">
                        <h4 class="card-title daystouredcount">0</h4>
                        <p class="card-text">Days Toured</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card text-center">
                     <div class="card-body">
                        <h4 class="card-title citiescoveredcount">0</h4>
                        <p class="card-text text-center">Cities Covered</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center daysCentralMarketcount">0</h4>
                        <p class="card-text text-center">Days Central Market</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center daysSuburbancount">0</h4>
                        <p class="card-text text-center">Days Suburban</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center daysOfficeWorkCount">0</h4>
                        <p class="card-text text-center">Days Office Work</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-9">
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Travel Summary in {!! date('F') !!}</h4>
                        <div id="MonthlyCitiesTours" class="ct-chart"></div>
                     </div>
                     <!-- <div class="card-footer">
                        <div class="row">
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Toured
                              <i class="fa fa-circle text-danger pr-6"></i> Central Market
                              <i class="fa fa-circle text-warning pr-6"></i> Suburban
                              <i class="fa fa-circle text-primary pr-6"></i> Office Work
                           </div>
                        </div>
                     </div> -->
                  </div>
               </div>
             </div>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Travel Summary in {!! date('Y') !!}</h4>
                        <div id="YearCitiesTours" class="ct-chart ct-perfect-fourth"></div>
                     </div>
                     <div class="card-footer">
                        <div class="row">
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Toured
                              <i class="fa fa-circle text-danger pr-6"></i> Central Market
                              <i class="fa fa-circle text-warning pr-6"></i> Suburban
                              <!-- <i class="fa fa-circle text-primary pr-6"></i> Office Work -->
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="visitsummarytab">
      <div class="row text-center">
         <div class="col-md-12 text-center">
            <h4 class="section-heading mb-3 h4 mt-0">Visit Summary</h4>
         </div>
      </div>
       <div class="row">
           <div class="col-sm">
              <div class="card text-center">
                 <div class="card-body">
                    <h4 class="card-title newSTUsregisteredcount">0</h4>
                    <p class="card-text">New STUs Registered</p>
                 </div>
              </div>
           </div>
           <div class="col-sm">
              <div class="card">
                 <div class="card-body">
                    <h4 class="card-title text-center newFleetOwnercount">0</h4>
                    <p class="card-text text-center">New Fleet Owner Registered</p>
                 </div>
              </div>
           </div>
            <div class="col-sm">
              <div class="card">
                 <div class="card-body">
                    <h4 class="card-title text-center newMechanicregisteredcount">0</h4>
                    <p class="card-text text-center">New Mechanincs Registered</p>
                 </div>
              </div>
           </div>
            <div class="col-sm">
                <div class="card">
                   <div class="card-body">
                      <h4 class="card-title text-center newDealerRegisteredcount">0</h4>
                      <p class="card-text text-center">New Dealers Registered</p>
                   </div>
                </div>
             </div>
          </div>
          <div class="row">
            <div class="col-sm">
              <div class="card text-center">
                 <div class="card-body">
                    <h4 class="card-title visitedstuscount">0</h4>
                    <p class="card-text text-center">STUs Visited</p>
                 </div>
              </div>
           </div>
           <div class="col-sm">
              <div class="card">
                 <div class="card-body">
                    <h4 class="card-title text-center visitedFleetOwnercount">0</h4>
                    <p class="card-text text-center">Fleet Owner Visited</p>
                 </div>
              </div>
           </div>

           <div class="col-sm">
              <div class="card">
                 <div class="card-body">
                    <h4 class="card-title text-center visitedMechanicCount">0</h4>
                    <p class="card-text text-center">Mechanincs Visited</p>
                 </div>
              </div>
           </div>
             <div class="col-sm">
                <div class="card">
                   <div class="card-body">
                      <h4 class="card-title text-center visitedDealerCount">0</h4>
                      <p class="card-text text-center">Dealers Visited</p>
                   </div>
                </div>
             </div>
          </div>
          <div class="row">
             <div class="col-sm">
                <div class="card">
                   <div class="card-body">
                      <h4 class="card-title">Customer Registered in {!! date('F') !!}</h4>
                      <div id="MonthNewCustomerRegisteredBar" class="ct-chart"></div>
                   </div>
                   <div class="card-footer">
                        <div class="row">
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Dealers Registered
                              <i class="fa fa-circle text-danger pr-6"></i> STUs Registered
                              <i class="fa fa-circle text-warning pr-6"></i> Mechanincs Registered
                              <i class="fa fa-circle text-primary pr-6"></i> Fleet Owner Registered
                           </div>
                        </div>
                     </div>
                </div>
             </div>
          </div>
          <div class="row">
             <div class="col-sm">
                <div class="card">
                   <div class="card-body">
                      <h4 class="card-title">Customer Visited in {!! date('F') !!}</h4>
                      <div id="MonthCustomerVisitedBar" class="ct-chart"></div>
                   </div>
                   <div class="card-footer">
                        <div class="row">
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Dealers Visited 
                              <i class="fa fa-circle text-danger pr-6"></i> STUs Visited
                              <i class="fa fa-circle text-warning pr-6"></i> Mechanincs Visited 
                              <i class="fa fa-circle text-primary pr-6"></i> Fleet Owner Visited
                           </div>
                        </div>
                     </div>
                </div>
             </div>
          </div>
          <div class="row">
             <div class="col-sm">
                <div class="card">
                   <div class="card-body">
                      <h4 class="card-title">Customer Registered in {!! date('Y') !!}</h4>
                      <div id="YearNewCustomerRegisteredBar" class="ct-chart"></div>
                   </div>
                   <div class="card-footer">
                        <div class="row">
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Dealers Registered
                              <i class="fa fa-circle text-danger pr-6"></i> STUs Registered
                              <i class="fa fa-circle text-warning pr-6"></i> Mechanincs Registered
                              <i class="fa fa-circle text-primary pr-6"></i> Fleet Owner Registered
                           </div>
                        </div>
                     </div>
                </div>
             </div>
          </div>
          <div class="row">
             <div class="col-sm">
                <div class="card">
                   <div class="card-body">
                      <h4 class="card-title">Customer Visited in {!! date('Y') !!}</h4>
                      <div id="YearCustomerVisitedBar" class="ct-chart"></div>
                   </div>
                   <div class="card-footer">
                        <div class="row">
                           <div class="col-md-12 pr-6">
                              <i class="fa fa-circle text-info pr-6"></i> Dealers Visited 
                              <i class="fa fa-circle text-danger pr-6"></i> STUs Visited
                              <i class="fa fa-circle text-warning pr-6"></i> Mechanincs Visited 
                              <i class="fa fa-circle text-primary pr-6"></i> Fleet Owner Visited
                           </div>
                        </div>
                     </div>
                </div>
             </div>
          </div>
   </div>
   <div class="tab-pane" id="couponsummarytab">
      <div class="row">
         <div class="col-md-12 text-center">
            <h4 class="section-heading mb-3 h4 mt-0">Coupons Summary</h4>
         </div>
      </div>
      <div class="row">
         <div class="col-sm">
            <div class="card text-center">
               <div class="card-body">
                  <h4 class="card-title couponsCollectedValue">0</h4>
                  <p class="card-text">Coupons collected under value scheme</p>
               </div>
            </div>
         </div>
         <div class="col-sm">
            <div class="card text-center">
               <div class="card-body">
                  <h4 class="card-title mrpCollectedValue">0</h4>
                  <p class="card-text text-center">Total Value under value scheme</p>
               </div>
            </div>
         </div>
         <div class="col-sm">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title text-center couponsCollectedPoints">0</h4>
                  <p class="card-text text-center">Coupon collected under MRP scheme</p>
               </div>
            </div>
         </div>
         <div class="col-sm">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title text-center mrpCollectedPoints">0</h4>
                  <p class="card-text text-center">Total Value under MRP Scheme</p>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">Coupon Vs Mrp Scheme in {!! date('F') !!}</h4>
                  <div id="ValueVsMrpSchemePai" class="ct-chart"></div>
               </div>
               <div class="card-footer">
                  <div class="row">
                     <div class="col-md-12 pr-6">
                        <i class="fa fa-circle text-info pr-6"></i> Gift Coupon
                        <i class="fa fa-circle text-danger pr-6"></i> MRP Lable
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">Monthly Coupon Value vs MRP Scheme in {!! date('F') !!}</h4>
                  <div id="ValueVsMrpSchemeQtyPai" class="ct-chart"></div>
               </div>
               <div class="card-footer">
                  <div class="row">
                     <div class="col-md-12 pr-6">
                        <i class="fa fa-circle text-info pr-6"></i> Gift Coupon
                        <i class="fa fa-circle text-danger pr-6"></i> MRP Lable
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="row">
         <div class="col-sm">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">Coupon Vs Mrp Scheme in {!! date('Y') !!}</h4>
                  <div id="YearValueVsMrpSchemePai" class="ct-chart"></div>
               </div>
               <div class="card-footer">
                  <div class="row">
                     <div class="col-md-12 pr-6">
                        <i class="fa fa-circle text-info pr-6"></i> Gift Coupon
                        <i class="fa fa-circle text-danger pr-6"></i> MRP Lable
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-sm">
            <div class="card">
               <div class="card-body">
                  <h4 class="card-title">Monthly Coupon Value vs MRP Scheme in {!! date('Y') !!}</h4>
                  <div id="YearValueVsMrpSchemeQtyPai" class="ct-chart"></div>
               </div>
               <div class="card-footer">
                  <div class="row">
                     <div class="col-md-12 pr-6">
                        <i class="fa fa-circle text-info pr-6"></i> Gift Coupon
                        <i class="fa fa-circle text-danger pr-6"></i> MRP Lable
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="ordersummarytab">
    <div class="row">
         <div class="col-md-12 text-center">
            <h4 class="section-heading mb-3 h4 mt-0">Order Summary</h4>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card text-center">
                     <div class="card-body">
                        <h4 class="card-title orderCollectedCount">0</h4>
                        <p class="card-text">Order Collected</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card text-center">
                     <div class="card-body">
                        <h4 class="card-title orderCollectedSum">0</h4>
                        <p class="card-text text-center">Total Orders</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center GGPLorderCollectedCount">0</h4>
                        <p class="card-text text-center">Total orders GGPL</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center GGPLorderCollectedSum">0</h4>
                        <p class="card-text text-center">Total order value GGPL</p>
                     </div>
                  </div>
               </div>
             </div>
             <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center GPDorderCollectedCount">0</h4>
                        <p class="card-text text-center">Total orders GPD</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center GPDorderCollectedSum">0</h4>
                        <p class="card-text text-center">Total order value GPD</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center GDGLorderCollectedCount">0</h4>
                        <p class="card-text text-center">Total orders GDGL</p>
                     </div>
                  </div>
               </div>
                <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center GDGLorderCollectedSum">0</h4>
                        <p class="card-text text-center">Total order value GDGL</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Top 10 Product</h4>
                        <div id="Top10ProductBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Target Vs Achievement</h4>
                        <div id="TargetVsAchievementBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">State Wise Target Vs Achievement</h4>
                        <div id="StateWiseTargetVsAchievementBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Top 10 Sales Representatives</h4>
                        <div id="Top10OrderRepresentativesBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
             <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Orders by Zone </h4>
                        <div id="OrdersbyZoneBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="salessummarytab">
    <div class="row">
         <div class="col-md-12 text-center">
            <h4 class="section-heading mb-3 h4 mt-0">Sales Summary</h4>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card text-center">
                     <div class="card-body">
                        <h4 class="card-title salestargetamount">0</h4>
                        <p class="card-text">Sales Target</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card text-center">
                     <div class="card-body">
                        <h4 class="card-title salesachivmentamount">0</h4>
                        <p class="card-text text-center">Achivement </p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center salesachivmentpercent">0</h4>
                        <p class="card-text text-center">Achivement %</p>
                     </div>
                  </div>
               </div>
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title text-center salesvalues">0</h4>
                        <p class="card-text text-center">Sales in Value</p>
                     </div>
                  </div>
               </div>
             </div>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Sales Target Vs Achievement</h4>
                        <div id="SalesTargetAchievementBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">State Wise Target Vs Achievement</h4>
                        <div id="StateWiseSalesTargetVsAchievementBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Top 10 Sales Representatives</h4>
                        <div id="Top10SalesRepresentativesBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm">
                  <div class="card">
                     <div class="card-body">
                        <h4 class="card-title">Sales (Amount in RS ) by Zone </h4>
                        <div id="SalesbyZoneBar" class="ct-chart"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="activitytab">
      <div class="row">
         <div class="col-md-12 text-center">
            <h4 class="section-heading mb-3 h4 mt-0">Activity</h4>
         </div>
         <div class="col-md-12">
            <ul class="timeline timeline-simple usertodayActivity">
           
            </ul>
         </div>
      </div>
   </div>
</div>
  
<script type="text/javascript">
   $( document ).ready(function() {
    getDashboardData();   
   })
  $('#fromdate').change(function(){
     var activetabs = $("#tabs .active").attr("href");
      switch (activetabs) {
        case '#kpistab':
          getDashboardData();
          break;
        case '#travelsummarytab':
          travelSummaryDashboard()
          break;
        case '#visitsummarytab':
           visitSummaryDashboard()
          break;
        case '#couponsummarytab':
          couponSummaryDashboard()
          break;
        case '#ordersummarytab':
          orderSummaryDashboard()
          break;
        case '#salessummarytab':
          saleSummaryDashboard()
          break;
        case '#activitytab':
          activityDashboard()
         default:
         getDashboardData();
      }
  });
  $('#todate').change(function(){
    var activetabs = $("#tabs .active").attr("href");
      switch (activetabs) {
        case '#kpistab':
          getDashboardData();
          break;
        case '#travelsummarytab':
          travelSummaryDashboard()
          break;
        case '#visitsummarytab':
           visitSummaryDashboard()
          break;
        case '#couponsummarytab':
          couponSummaryDashboard()
          break;
        case '#ordersummarytab':
          orderSummaryDashboard()
          break;
        case '#salessummarytab':
          saleSummaryDashboard()
          break;
        case '#activitytab':
          activityDashboard()
         default:
         getDashboardData();
      }
  });

  $('#user_id').change(function(){
      var activetabs = $("#tabs .active").attr("href");
      switch (activetabs) {
        case '#kpistab':
          getDashboardData();
          break;
        case '#travelsummarytab':
          travelSummaryDashboard()
          break;
        case '#visitsummarytab':
           visitSummaryDashboard()
          break;
        case '#couponsummarytab':
          couponSummaryDashboard()
          break;
        case '#ordersummarytab':
          orderSummaryDashboard()
          break;
        case '#salessummarytab':
          saleSummaryDashboard()
          break;
        case '#activitytab':
          activityDashboard()
         default:
         getDashboardData();
      }
  });

  function travelSummaryDashboard()
  {
      var fromdate = $("input[name=fromdate]").val();
      var todate = $("input[name=todate]").val();
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('travelSummaryData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", fromdate:fromdate,todate:todate,user_id:user_id },
         success: function(res){
            $(".daystouredcount").empty();
            $('.citiescoveredcount').empty();
            $(".daysOfficeWorkCount").empty()
            $(".daysCentralMarketcount").empty();
            $(".daysSuburbancount").empty();
            $(".daystouredcount").append(res.daystouredcount);
            $(".citiescoveredcount").append(res.citiescoveredcount);
            $(".daysCentralMarketcount").append(res.daysCentralMarketcount);
            $(".daysOfficeWorkCount").append(res.daysOfficeWorkCount);
            $(".daysSuburbancount").append(res.daysSuburbancount);
            monthlyTourBar(res)
            YearCitiesTours(res.yeartours)
        }
    })
  }

  function visitSummaryDashboard()
  {
      var fromdate = $("input[name=fromdate]").val();
      var todate = $("input[name=todate]").val();
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('visitSummaryData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", fromdate:fromdate,todate:todate,user_id:user_id },
         success: function(res){
            $(".newSTUsregisteredcount").empty();
            $(".newFleetOwnercount").empty();
            $(".newMechanicregisteredcount").empty();
            $(".newDealerRegisteredcount").empty();
            $(".visitedstuscount").empty();
            $(".visitedFleetOwnercount").empty();
            $(".visitedMechanicCount").empty();
            $(".visitedDealerCount").empty();
            $(".newSTUsregisteredcount").append(res.newSTUsregisteredcount);
            $(".newFleetOwnercount").append(res.newFleetOwnercount);
            $(".newMechanicregisteredcount").append(res.newMechanicregisteredcount);
            $(".newDealerRegisteredcount").append(res.newDealerRegisteredcount);
            $(".visitedstuscount").append(res.visitedstuscount);
            $(".visitedFleetOwnercount").append(res.visitedFleetOwnercount);
            $(".visitedMechanicCount").append(res.visitedMechanicCount);
            $(".visitedDealerCount").append(res.visitedDealerCount);
            monthCustomerData(res.month_created_data)
            yearCustomerData(res.year_created_data)
            monthlyBeatAdheranceBar(res.monthbeatAdherence)
            yearlyBeatAdheranceBar(res.yearbeatAdherence)
         }
      })
  }

  function couponSummaryDashboard()
  {
   var fromdate = $("input[name=fromdate]").val();
      var todate = $("input[name=todate]").val();
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('couponSummaryData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", fromdate:fromdate,todate:todate,user_id:user_id },
         success: function(res){
            $(".couponsCollectedValue").empty();
            $(".mrpCollectedValue").empty();
            $(".couponsCollectedPoints").empty();
            $(".mrpCollectedPoints").empty();
            $(".couponsCollectedValue").append(res.couponsCollectedValue);
            $(".mrpCollectedValue").append(res.mrpCollectedValue);
            $(".couponsCollectedPoints").append(res.couponsCollectedPoints);
            $(".mrpCollectedPoints").append(res.mrpCollectedPoints);
            couponsSummaryPaiChart(res.coupon_summary_chart)
        }
    })
      
  }

  function orderSummaryDashboard()
  {
   var fromdate = $("input[name=fromdate]").val();
      var todate = $("input[name=todate]").val();
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('orderSummaryData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", fromdate:fromdate,todate:todate,user_id:user_id },
         success: function(res){
            $(".orderCollectedCount").empty();
            $(".orderCollectedSum").empty();
            $(".GGPLorderCollectedCount").empty();
            $(".GGPLorderCollectedSum").empty();
            $(".GPDorderCollectedCount").empty();
            $(".GPDorderCollectedSum").empty();
            $(".GDGLorderCollectedCount").empty();
            $(".GDGLorderCollectedSum").empty();
            $(".orderCollectedCount").append(res.orderCollectedCount);
            $(".orderCollectedSum").append(res.orderCollectedSum.toFixed(1));
            $(".GGPLorderCollectedCount").append(res.GGPLorderCollectedCount);
            $(".GGPLorderCollectedSum").append(res.GGPLorderCollectedSum.toFixed(1));
            $(".GPDorderCollectedCount").append(res.GPDorderCollectedCount);
            $(".GPDorderCollectedSum").append(res.GPDorderCollectedSum.toFixed(1));
            $(".GDGLorderCollectedCount").append(res.GDGLorderCollectedCount);
            $(".GDGLorderCollectedSum").append(res.GDGLorderCollectedSum.toFixed(1));
            getTop10Products(res.top_products)
            YearTargetVsAchievement(res.yeartargetachievement)
            getTop10Representatives(res.top_representatives)
            getOrderByZone(res.orders_by_zone)
            getOrderByState(res.orders_by_state)
        }
    })
      
  }

  function saleSummaryDashboard()
  {
      var fromdate = $("input[name=fromdate]").val();
      var todate = $("input[name=todate]").val();
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('salesSummaryData') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", fromdate:fromdate,todate:todate,user_id:user_id },
         success: function(res){
         getTop10SalesRepresentatives(res.top_sales_representatives)
        }
    })
      
  }

  function activityDashboard()
  {
   var fromdate = $("input[name=fromdate]").val();
      var todate = $("input[name=todate]").val();
      var user_id = $("select[name=user_id]").val();
      $.ajax({
         url: "{{ url('activityDashboardCount') }}",
         dataType: "json",
         type: "POST",
         data:{ _token: "{{csrf_token()}}", fromdate:fromdate,todate:todate,user_id:user_id },
         success: function(res){
            $.each(res.activities, function(index,item) {  
               $(".usertodayActivity").append('<li class="timeline-inverted">'+
                  '<div class="timeline-badge danger">'+
                     '<i class="material-icons">card_travel</i>'+
                  '</div>'+
                  '<div class="timeline-panel">'+
                     '<div class="timeline-heading">'+
                        '<span class="badge badge-pill badge-danger">'+item.users.name+'</span>'+
                     '</div>'+
                     '<div class="timeline-body">'+
                        '<p>'+item.description+'</p>'+
                     '</div>'+
                     '<h6>'+
                        '<i class="ti-time"></i> '+item.time+
                     '</h6>'+
                  '</div>'+
               '</li>');
            });
         }
      }) 
  }

  function couponsSummaryPaiChart(data)
  {
   var labels = ['Mrp', 'Coupon'];
   var monthpointdata = [data.month_mrp_point, data.month_coupon_point] ;
   var monthquantitydata = [data.month_coupon_quantity, data.month_mrp_quantity] ;
   var yearpointdata = [data.year_mrp_point, data.year_coupon_point] ;
   var yearquantitydata = [data.year_coupon_quantity, data.year_mrp_quantity] ;
      var options = {
        labelInterpolationFnc: function(value) {
          return value[0]
        }
      };
   
      var responsiveOptions = [
        ['screen and (min-width: 640px)', {
          chartPadding: 10,
          labelOffset: 10,
          labelDirection: 'explode',
          labelInterpolationFnc: function(value) {
            return value;
          }
        }],
        ['screen and (min-width: 1024px)', {
          labelOffset: 0,
          chartPadding: 0
        }]
      ];
      new Chartist.Pie('#ValueVsMrpSchemePai', {
        labels: labels,
        series: monthpointdata
      }, options, responsiveOptions);
      new Chartist.Pie('#ValueVsMrpSchemeQtyPai', {
        labels: labels,
        series: monthquantitydata
      }, options, responsiveOptions);

      new Chartist.Pie('#YearValueVsMrpSchemePai', {
        labels: labels,
        series: yearpointdata
      }, options, responsiveOptions);
      new Chartist.Pie('#YearValueVsMrpSchemeQtyPai', {
        labels: labels,
        series: yearquantitydata
      }, options, responsiveOptions);
  }

  function thisMonthlyBeatAdheranceBar(data)
  {
   
      var labels = [];
      var targets = [];
      var visited = [];
      var productive = [];
      data.forEach(function(item) {
         labels.push(item.date);
         targets.push(item.visit_target)
         visited.push(item.counter_visited)
         productive.push(item.productive_counter)
      });
      new Chartist.Bar('#BeatProductivityBar', {
         labels: labels,
         series: [
            visited,
            productive
         ]
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         });

      new Chartist.Bar('#BeatAdherenceBar', {
         labels: labels,
         series: [
           targets,
           visited
         ]
       }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         });
  }
  function thisYearBeatAdheranceBar(data)
  {
   
      var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      var targets = [];
      var visited = [];
      var productive = [];
      data.forEach(function(item) {
          targets.push(item.visit_target)
          visited.push(item.counter_visited)
          productive.push(item.productive_counter)
      });

      new Chartist.Bar('#YearBeatProductivityBar', {
         labels: labels,
         series: [
            visited,
            productive
         ]
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         });

      new Chartist.Bar('#YearBeatAdherenceBar', {
         labels: labels,
         series: [
           targets,
           visited
         ]
       }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         });
  }

  function monthlyBeatAdheranceBar(data)
  {
   
      var labels = [];
      var targets = [];
      var visited = [];
      var productive = [];
      var dealer_visited = [];
      var stus_visited = [];
      var mechanic_visited = [];
      var fleet_owner_visited = [];
      data.forEach(function(item) {
         labels.push(item.date);
         targets.push(item.visit_target)
         visited.push(item.counter_visited)
         productive.push(item.productive_counter)
         dealer_visited.push(item.dealer_visited)
         stus_visited.push(item.stus_visited)
         mechanic_visited.push(item.mechanic_visited)
         fleet_owner_visited.push(item.fleet_owner_visited)
      });
      new Chartist.Bar('#BeatProductivityBar', {
         labels: labels,
         series: [
            visited,
            productive
         ]
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         });

      new Chartist.Bar('#BeatAdherenceBar', {
         labels: labels,
         series: [
           targets,
           visited
         ]
       }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         });

      new Chartist.Bar('#MonthCustomerVisitedBar', {
           labels: labels,
           series: [
             dealer_visited,
             stus_visited,
             mechanic_visited,
             fleet_owner_visited
           ]
         }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         }); 
  }

  function yearlyBeatAdheranceBar(data)
  {
   
      var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      var targets = [];
      var visited = [];
      var productive = [];
      var dealer_visited = [];
      var stus_visited = [];
      var mechanic_visited = [];
      var fleet_owner_visited = [];
      data.forEach(function(item) {
          targets.push(item.visit_target)
          visited.push(item.counter_visited)
          productive.push(item.productive_counter)
          dealer_visited.push(item.dealer_visited)
         stus_visited.push(item.stus_visited)
         mechanic_visited.push(item.mechanic_visited)
         fleet_owner_visited.push(item.fleet_owner_visited)
      });

      new Chartist.Bar('#YearBeatProductivityBar', {
         labels: labels,
         series: [
            visited,
            productive
         ]
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         });

      new Chartist.Bar('#YearBeatAdherenceBar', {
         labels: labels,
         series: [
           targets,
           visited
         ]
       }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         });

      new Chartist.Bar('#YearCustomerVisitedBar', {
           labels: labels,
           series: [
             dealer_visited,
             stus_visited,
             mechanic_visited,
             fleet_owner_visited
           ]
         }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         }); 
  }
  function monthlyTourBar(data)
  {
      new Chartist.Bar('#MonthlyCitiesTours', {
         labels: ['Toured', 'Central Market', 'Suburban', 'Office Work'],
         series: [
            [data.daystouredcount, data.daysCentralMarketcount, data.daysSuburbancount, data.daysOfficeWorkCount],
         ]
         }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         }).on('draw', function(data) {
           if(data.type === 'bar') {
             data.element.attr({
               style: 'stroke-width: 30px'
             });
           }
      });
  }
  function YearCitiesTours(data)
  {
   
       var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      var toured = [];
      var centralMarket = [];
      var suburban = [];
      var officeWork = [];
        data.forEach(function(item) {
          toured.push(item.daystouredcount)
          centralMarket.push(item.daysCentralMarketcount)
          suburban.push(item.daysSuburbancount)
          officeWork.push(item.daysOfficeWorkCount)
        });  
      new Chartist.Bar('#YearCitiesTours', {
           labels: labels,
           series: [
             toured,
             centralMarket,
             suburban,
             // officeWork
           ]
         }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           }
         });    
  }

  function monthCustomerData(data)
  {
      var labels = [];
      var dealer = [];
      var stus = [];
      var mechanic = [];
      var fleet_owner = [];
      data.forEach(function(item) {
          labels.push(item.label);
          dealer.push(item.dealer_registered)
          stus.push(item.stus_registered)
          mechanic.push(item.mechanic_registered)
          fleet_owner.push(item.fleet_owner_registered)
      });  
      new Chartist.Bar('#MonthNewCustomerRegisteredBar', {
           labels: labels,
           series: [
             dealer,
             stus,
             mechanic,
             fleet_owner
           ]
         }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           }
         }); 
  }

  function yearCustomerData(data)
  {
      var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      var dealer = [];
      var stus = [];
      var mechanic = [];
      var fleet_owner = [];
      data.forEach(function(item) {
          labels.push(item.label);
          dealer.push(item.dealer_registered)
          stus.push(item.stus_registered)
          mechanic.push(item.mechanic_registered)
          fleet_owner.push(item.fleet_owner_registered)
      });  
      new Chartist.Bar('#YearNewCustomerRegisteredBar', {
           labels: labels,
           series: [
             dealer,
             stus,
             mechanic,
             fleet_owner
           ]
         }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           resize: true
         }); 
  }

  function getTop10Products(data){
      var labels = [];
      var series = [];
      data.forEach(function(item) {
          labels.push(item.products.product_no);
          series.push(item.total_price)
      }); 

   new Chartist.Bar('#Top10ProductBar', {
        labels: labels,
        series: series
      }, {
        distributeSeries: true,
        resize: true
    });
  }

  function YearTargetVsAchievement(data)
  {
   
       var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      var achievement = [];
      var salesachievement = [];
      var target = [];
        data.forEach(function(item) {
          achievement.push(item.achievement)
          salesachievement.push(item.salesachievement)
          target.push(item.target)
        });  
      new Chartist.Bar('#TargetVsAchievementBar', {
           labels: labels,
           series: [
             target,
             achievement
           ]
         }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelInterpolationFnc: function(value) {
               return (value / 1000) + 'k';
             },
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           }
         });    

      new Chartist.Bar('#SalesTargetAchievementBar', {
           labels: labels,
           series: [
             target,
             salesachievement
           ]
         }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelInterpolationFnc: function(value) {
               return (value / 1000) + 'k';
             },
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           }
         });  
  }

  function getTop10Representatives(data){
      var labels = [];
      var series = [];
      data.forEach(function(item) {
          labels.push(item.createdbyname.name);
          series.push(item.total_amount)
      }); 

   new Chartist.Bar('#Top10OrderRepresentativesBar', {
        labels: labels,
        series: series
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelInterpolationFnc: function(value) {
               return (value / 1000) + 'k';
             },
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           distributeSeries: true,
            resize: true
         });
  }

  function getTop10SalesRepresentatives(data){
      var labels = [];
      var series = [];
      data.forEach(function(item) {
          labels.push(item.createdbyname.name);
          series.push(item.total_amount)
      }); 

   new Chartist.Bar('#Top10SalesRepresentativesBar', {
        labels: labels,
        series: series
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelInterpolationFnc: function(value) {
               return (value / 1000) + 'k';
             },
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           distributeSeries: true,
            resize: true
         });
  }

  function getOrderByZone(data)
  {
      var labels = [];
      var orderamount = [];
      var salesamount = [];

      var sortedOrderData = data.sort((a, b) => (a.order_amount > b.order_amount ? -1 : 1))
      sortedOrderData.forEach(function(item) {
         if(item.order_amount >= 100)
         {
            labels.push(item.zone_name);
            orderamount.push(item.order_amount)
         }
      }); 

      var sortedSaleData = data.sort((a, b) => (a.sales_amount > b.sales_amount ? -1 : 1))
      sortedSaleData.forEach(function(item2) {
         if(item2.sales_amount >= 1000)
         {
            labels.push(item2.zone_name);
            salesamount.push(item2.sales_amount)
         }
      }); 
   new Chartist.Bar('#OrdersbyZoneBar', {
        labels: labels,
        series: orderamount
      }, {
            axisX: {
               scaleMinSpace: 15,
               offset: 20
            },
            axisY: {
               offset: 30,
               labelInterpolationFnc: function(value) {
                  return (value / 1000) + 'k';
               },
               labelOffset: {
                  x: 0,
                  y: 10
               },
            },
            distributeSeries: true
         });

   new Chartist.Bar('#SalesbyZoneBar', {
        labels: labels,
        series: salesamount
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelInterpolationFnc: function(value) {
               return (value / 1000) + 'k';
             },
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           distributeSeries: true,
            resize: true
         });
  }

  function getOrderByState(data)
  {
      var labels = [];
      var orderamount = [];
      var salesamount = [];
     var sortedOrderData = data.sort((a, b) => (a.order_amount > b.order_amount ? -1 : 1))
      sortedOrderData.forEach(function(item) {
         if(item.order_amount >= 10000)
         {
            labels.push(item.state_name.substring(0,3));
            orderamount.push(item.order_amount)
         }
      }); 

      var sortedSaleData = data.sort((a, b) => (a.sales_amount > b.sales_amount ? -1 : 1))
      sortedSaleData.forEach(function(item2) {
            if(item2.sales_amount >= 10000)
            {
               labels.push(item2.state_name.substring(0,3));
               salesamount.push(item2.sales_amount)
            }
      }); 
   new Chartist.Bar('#StateWiseTargetVsAchievementBar', {
        labels: labels,
        series: orderamount
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelInterpolationFnc: function(value) {
               return (value / 1000) + 'k';
             },
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           distributeSeries: true,
            resize: true
         });

   new Chartist.Bar('#StateWiseSalesTargetVsAchievementBar', {
        labels: labels,
        series: salesamount
      }, {
           seriesBarDistance: 10,
           axisX: {
             offset: 20
           },
           axisY: {
             offset: 30,
             scaleMinSpace: 15,
             labelInterpolationFnc: function(value) {
               return (value / 1000) + 'k';
             },
             labelOffset: {
                     x: 0,
                     y: 10
                  },
           },
           distributeSeries: true,
            resize: true
         });
  }

  function getDashboardData(){
    var fromdate = $("input[name=fromdate]").val();
    var todate = $("input[name=todate]").val();
    var user_id = $("select[name=user_id]").val();
    $.ajax({
      url: "{{ url('dashboardData') }}",
        dataType: "json",
        type: "POST",
        data:{ _token: "{{csrf_token()}}", fromdate:fromdate,todate:todate,user_id:user_id },
        success: function(res){
            $(".visittargetcount").empty();
            $(".visitedcount").empty();
            $(".beatadherancecount").empty();
            $(".beatproductivitycount").empty();
            $(".activedealerscount").empty();
            $(".activeStockistcount").empty();
            $(".activeFleetOwnercount").empty();
            $(".activeMechanicscount").empty();
            $(".totaldealerscount").empty();
            $(".totalStockistcount").empty();
            $(".totalFleetOwnercount").empty();
            $(".totalMechanicscount").empty();
            $(".visittargetcount").append(res.visittarget);
            $(".visitedcount").append(res.visitedcounter);
            $(".beatadherancecount").append(res.beatadherance);
            $(".beatproductivitycount").append(res.beatproductivity);
            $(".activedealerscount").append(res.activedealerscount);
            $(".activeStockistcount").append(res.activeStockistcount);
            $(".activeFleetOwnercount").append(res.activeFleetOwnercount);
            $(".activeMechanicscount").append(res.activeMechanicscount);
            $(".totaldealerscount").append(res.totaldealerscount);
            $(".totalStockistcount").append(res.totalStockistcount);
            $(".totalFleetOwnercount").append(res.totalFleetOwnercount);
            $(".totalMechanicscount").append(res.totalMechanicscount);
            thisMonthlyBeatAdheranceBar(res.monthbeatAdherence)
            thisYearBeatAdheranceBar(res.yearbeatAdherence)
        }
    })
  }
</script>
</x-app-layout>
