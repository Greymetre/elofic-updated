<x-app-layout>
   <style>
      .select2-results__options {
         overflow: auto;
         max-height: 200px !important;
      }

      .select2-results,
      .select2-search--dropdown,
      .select2-dropdown--above {
         min-width: 250px !important;
      }

      .select2-container {
         border-bottom: 1px solid lightgray;
      }

      button.delete-img-btn {
         position: absolute;
         top: 7px;
         right: 8px;
      }

      .row {
         align-items: end !important;
      }

      .inp-div {
         position: relative;
         display: flex;
         align-items: center;
         background: #ebe7e7;
         border-radius: 5px;
      }

      .inp-div input {
         position: absolute;
         top: 0;
         width: 100%;
         height: 100%;
         opacity: 0;
         cursor: pointer;
      }

      .inp-div i:first-child {
         font-size: 40px !important;
      }
   </style>
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header card-header-tabs card-header-warning">
               <div class="nav-tabs-navigation">
                  <div class="nav-tabs-wrapper">
                     <h3 class="card-title ">
                        Service Bill Creation
                        @if(auth()->user()->can(['district_access']))
                        <ul class="nav nav-tabs pull-right" data-tabs="tabs">
                           <li class="nav-item">
                              <a class="nav-link" href="{{ url('service_bills') }}">
                                 <i class="material-icons">next_plan</i> Service Bills
                                 <div class="ripple-container"></div>
                              </a>
                           </li>
                        </ul>
                        @endif
                     </h3>
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
               {!! Form::model($service_bill,[
               'route' => $service_bill->exists ? ['service_bills.update', $service_bill->id ] : 'service_bills.store',
               'method' => $service_bill->exists ? 'PUT' : 'POST',
               'id' => 'storeServiceBillData',
               'files'=>true
               ]) !!}
               <div class="row mt-2 mb-2">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="service_bill_no">Service Bill No.</label>
                        <input class="form-control" type="text" readonly name="service_bill_no" id="service_bill_no" value="{{$serviceBillNo}}">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="complaint_number">Complaint Number</label>
                        <select name="complaint_number" id="complaint_number" class="select2">
                           <option value="">Select Complaint Number</option>
                           @if(count($all_complaint_number) > 0)
                           @foreach($all_complaint_number as $val)
                           <option value="{{$val->complaint_number}}" {{($complaint && $complaint->complaint_number == $val->complaint_number)?'selected':''}}>{{$val->complaint_number}}</option>
                           @endforeach
                           @endif
                        </select>
                        <input type="hidden" readonly name="product_division" id="product_division" value="{{($complaint?($complaint->product_details?$complaint->product_details->categories->id:''):'')}}">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="division">Division</label>
                        <select name="division" id="division" class="select2">
                           <option value="">Select Division</option>
                           @if($divisions && count($divisions) > 0)
                           @foreach($divisions as $division)
                           <option value="{{$division->id}}">{{$division->category_name}}</option>
                           @endforeach
                           @endif
                        </select>
                     </div>
                  </div>
               </div>

               <h3 class="mt-2"><b>Complaint Details: </b></h3>
               <hr>
               <div class="row mt-2 mb-2">
                  <table class="table" id="complain_details">
                     <thead>
                        <tr>
                           <th>Complaint Number</th>
                           <th>Recived From</th>
                           <th>Date</th>
                           <th>Item</th>
                           <th>Comments</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td>{{$complaint?$complaint->complaint_number:'-'}}</td>
                           <td>{{$complaint?($complaint->createdbyname?$complaint->createdbyname->name:''):'-'}}</td>
                           <td>{{$complaint?date('d M Y' ,strtotime($complaint->complaint_date)):'-'}}</td>
                           <td>{{$complaint?($complaint->product_details?$complaint->product_details->product_name:''):''}}</td>
                           <td>{{$complaint?$complaint->description:'-'}}</td>
                        </tr>
                     </tbody>
                  </table>
               </div>

               <h3 class="mt-2"><b>Warranty Details: </b></h3>
               <hr>
               <div class="row mt-2 mb-2">
                  <table class="table table-striped responsive" id="warranty_details">
                     <thead>
                        <tr>
                           <th>Product Serial No.</th>
                           <th>Item</th>
                           <th>Warranty Start Date</th>
                           <th>Warranty Upto</th>
                           <th>Warranty Status</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td>{{$complaint?strtoupper($complaint->product_serail_number):'-'}}</td>
                           <td>{{$complaint?'['.$complaint->product_code.']':''}} {{$complaint?$complaint->product_name:'-'}}</td>
                           <td>
                              @if($complaint)
                              @if ($complaint->customer_bill_date)
                              {{ date('d-m-Y', strtotime($complaint->customer_bill_date)) }}
                              @else
                              -
                              @endif
                              @else
                              -
                              @endif
                           </td>
                           <td>
                              @if($complaint)
                              @if ($complaint->customer_bill_date)
                              @php
                              $today = Carbon\Carbon::today();
                              $date = Carbon\Carbon::parse($complaint->customer_bill_date);
                              if ($date !== false) {
                              $date->addMonths(18);
                              } else {
                              $date = null;
                              }
                              @endphp
                              @if ($date)
                              {{ $date->format('d-m-Y') }}
                              @else
                              Invalid date
                              @endif
                              @else
                              -
                              @endif
                              @else
                              -
                              @endif
                           </td>
                           <td>
                              @if($complaint)
                              @if ($complaint->customer_bill_date)
                              @if ($date)
                              @if ($date->gt($today))
                              <span class="badge badge-success">In Warranty</span>
                              @else
                              <span class="badge badge-danger">Out Of Warranty</span>
                              @endif
                              @else
                              Invalid date
                              @endif
                              @else
                              -
                              @endif
                              @else
                              -
                              @endif
                           </td>
                        </tr>
                     </tbody>

                  </table>
               </div>

               <h3 class="mt-2"><b>Complaint Category: </b></h3>
               <div class="border border-dark rounded p-4">
               <div class="row mt-2 mb-2">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="category">Category Of Complaint</label>
                        <select name="category" id="category" class="select2">
                           <option value="">Select Category</option>
                           <option value="Electrical Fault">Electrical Fault</option>
                           <option value="Mechanical Fault">Mechanical Fault</option>
                           <option value="Physical Fault">Physical Fault</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="complaint_type">Complaint Type</label>
                        <select name="complaint_type" id="complaint_type" class="select2">
                           <option value="">Select Complaint Type</option>
                           <option value="Auto ON-OFF">Auto ON-OFF</option>
                           <option value="Body Current">Body Current</option>
                           <option value="Body Damage">Body Damage</option>
                           <option value="Fan Does Not Start">Fan Does Not Start</option>
                           <option value="Fan Running Slow">Fan Running Slow</option>
                           <option value="Fan Wobbling">Fan Wobbling</option>
                           <option value="Noise Problem">Noise Problem</option>
                           <option value="Oscillation Issue">Oscillation Issue</option>
                           <option value="Poor Flow Of Air">Poor Flow Of Air</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="complaint_reason">Complaint Reason</label>
                        <select name="complaint_reason" id="complaint_reason" class="select2">
                           <option value="">Select Complaint Reason</option>
                           <option value="Stator Dead">Stator Dead</option>
                           <option value="PCB Burn">PCB Burn</option>
                           <option value="CutOff Issue">CutOff Issue</option>
                           <option value="Loose Connection">Loose Connection</option>
                           <option value="Low Voltage">Low Voltage</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="condition_fo_service">Condition Of Service</label>
                        <select name="condition_fo_service" id="condition_fo_service" class="select2">
                           <option value="">Select Condition Of Service</option>
                           <option value="Full Finish">Full Finish</option>
                           <option value="Regular Repair">Regular Repair</option>
                           <option value="Field Visit">Field Visit</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="received_product">Received Product</label>
                        <select name="received_product" id="received_product" class="select2">
                           <option value="">Select Received Product</option>
                           <option value="Pump">Pump</option>
                           <option value="Motor">Motor</option>
                           <option value="Pump Set">Pump Set</option>
                           <option value="Fan">Fan</option>
                           <option value="Heater">Heater</option>
                           <option value="Induction CookTop">Induction CookTop</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="nature_of_fault">Nature Of Fault</label>
                        <select name="nature_of_fault" id="nature_of_fault" class="select2">
                           <option value="">Select Nature Of Fault</option>
                           <option value="Transit Damage">Transit Damage</option>
                           <option value="Manufacturing Fault">Manufacturing Fault</option>
                           <option value="Customer Field Fault">Customer Field Fault</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="service_location">Service Location</label>
                        <select name="service_location" id="service_location" class="select2">
                           <option value="">Select Service Location</option>
                           <option value="Site Visit">Site Visit</option>
                           <option value="At ASC">At ASC</option>
                        </select>
                     </div>
                  </div>
               </div>
               </div>

               <h3 class="mt-2"><b>Service Type: </b></h3>
               <div class="border border-dark rounded p-4">
               <div class="row mt-2 mb-2">
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="repaired_replacement">Repaired / Replacement</label>
                        <select name="repaired_replacement" id="repaired_replacement" class="select2">
                           <option value="">Select Repaired / Replacement</option>
                           <option value="Repaired">Repaired</option>
                           <option value="Replacement">Replacement</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="form-group">
                        <label for="replacement_tag">Replacement Tag</label>
                        <select disabled name="replacement_tag" id="replacement_tag" class="select2">
                           <option value="">Select Replacement Tag</option>
                           <option value="Yes">Yes</option>
                           <option value="No">No</option>
                        </select>
                     </div>
                  </div>
                  <div class="col-md-3 d-none" id="tag-number">
                     <div class="form-group">
                        <input type="text" name="replacement_tag_number" id="replacement_tag_number" placeholder="Replacement Tag Number" class="form-control">
                     </div>
                  </div>
               </div>
               </div>

               <h3 class="mt-2"><b>Photos: </b></h3>
               <div class="border border-dark rounded p-4">
               <div class="row mt-2 mb-2">
                  <div class="col-md-2">
                     <label for="product_sr_no">Product Sr. No.</label>
                     <div class="inp-div">
                        <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                        <p class="m-0">Attach a File</p>
                        <input type="file" name="product_sr_no" id="product_sr_no" class="form-control" accept="image/*">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <label for="scr_job_card">SCR-Job Card</label>
                     <div class="inp-div">
                        <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                        <p class="m-0">Attach a File</p>
                        <input type="file" name="scr_job_card" id="scr_job_card" class="form-control" accept="image/*">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <label for="photo_3">Photo 3</label>
                     <div class="inp-div">
                        <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                        <p class="m-0">Attach a File</p>
                        <input type="file" name="photo_3" id="photo_3" class="form-control" accept="image/*">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <label for="photo_4">Photo 4</label>
                     <div class="inp-div">
                        <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                        <p class="m-0">Attach a File</p>
                        <input type="file" name="photo_4" id="photo_4" class="form-control" accept="image/*">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <label for="photo_5">Photo 5</label>
                     <div class="inp-div">
                        <i class="material-icons">upload_file</i><i class="material-icons">attach_file</i>
                        <p class="m-0">Attach a File</p>
                        <input type="file" name="photo_5" id="photo_5" class="form-control" accept="image/*">
                     </div>
                  </div>
               </div>
               </div>

               <!-- <h3 class="mt-2"><b>Service: </b></h3>
               <hr>
               <div class="row mt-2 mb-2">
                  <table class="table" id="table-service">
                     <thead>
                        <tr>
                           <td>Service</td>
                           <td>Quantity</td>
                           <td>Price</td>
                           <td>Sub Total</td>
                        </tr>
                     </thead>
                  </table>
                  <button type="button" class="btn btn-info btn-sm" id="add-service">ADD</button>
               </div>

               <h3 class="mt-2"><b>Spare Part: </b></h3>
               <hr>
               <div class="row mt-2 mb-2">
                  <table class="table" id="table-spare">
                     <thead>
                        <tr>
                           <td>Product</td>
                           <td>Unit</td>
                           <td>Quantity</td>
                           <td>Price</td>
                           <td>Sub Total</td>
                        </tr>
                     </thead>
                  </table>
                  <button type="button" class="btn btn-info btn-sm" id="add-spare">ADD</button>
               </div> -->

               <input type="submit" value="Add" class="btn btn-success float-right mt-2">
               {{ Form::close() }}
            </div>
         </div>
      </div>
   </div>
   <script>
      var counter = 0;
      var counter2 = 0;
      $(document).on('change', '#complaint_number', function() {
         var complaint_number = $(this).val();
         $.ajax({
            url: "{{ url('getComplaintsDataProduct') }}",
            dataType: "json",
            type: "POST",
            data: {
               _token: "{{csrf_token()}}",
               complaint_number: complaint_number
            },
            success: function(res) {
               if (res.status == 'success') {
                  console.log(res.data);
                  var html = '<tr><td>';
                  html += complaint_number;
                  html += '</td><td>';
                  if (res.data.complaint.createdbyname != null && res.data.complaint.createdbyname != '') {
                     html += res.data.complaint.createdbyname.name;
                  } else {
                     html += '-';
                  }
                  html += '</td><td>';

                  var date = new Date(res.data.complaint.complaint_date);
                  var options = {
                     day: '2-digit',
                     month: 'short',
                     year: 'numeric'
                  };
                  var formattedDate = date.toLocaleDateString('en-GB', options).replace(/ /g, ' ');

                  html += formattedDate;
                  html += '</td><td>';
                  if (res.data.product != '' && res.data.product != null) {
                     html += res.data.product.product_name;
                  } else {
                     html += '-';
                  }
                  html += '</td><td>';
                  if (res.data.complaint.description && res.data.complaint.description != null) {
                     html += res.data.complaint.description;
                  } else {
                     html += '-';
                  }
                  html += '</td></tr>';

                  $("#complain_details tbody").html(html);

                  var html2 = '<tr><td>';
                  html2 += res.data.complaint.product_serail_number;
                  html2 += '</td><td>';
                  if (res.data.product != '' && res.data.product != null) {
                     html2 += res.data.product.product_name;
                  } else {
                     html2 += '-';
                  }
                  html2 += '</td><td>';

                  if (res.data.complaint.customer_bill_date && res.data.complaint.customer_bill_date != null && res.data.complaint.customer_bill_date != '') {
                     var date = new Date(res.data.complaint.customer_bill_date);
                     var dateupto = moment(res.data.complaint.customer_bill_date);
                     dateupto.add(18, 'months');
                     var options = {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                     };
                     var formattedDate = date.toLocaleDateString('en-GB', options).replace(/ /g, ' ');
                     var formattedDateupto = dateupto.format('DD MMM YYYY');
                  } else {
                     var formattedDate = '-';
                     var formattedDateupto = '-';
                  }

                  html2 += formattedDate;
                  html2 += '</td><td>';
                  html2 += formattedDateupto;

                  html2 += '</td><td>';
                  var today = moment();
                  if (dateupto.isAfter(today)) {
                     html2 += '<span class="badge badge-success">In Warranty</span>';
                  } else {
                     html2 += '<span class="badge badge-danger">Out Of Warranty</span>';
                  }
                  html2 += '</td></tr>';

                  $("#warranty_details tbody").html(html2);

                  if (res.data.product != '' && res.data.product != null) {
                     $('#division').val(res.data.product.category_id);
                     $('#division').change();
                  } else {
                     $('#division').val('');
                     $('#division').change();
                  }

               }
            }
         });
      });
      $(document).ready(function() {
         var division = $('#product_division').val();
         if (division != '' && division != null) {
            $('#division').val(division);
            $('#division').change();
         }
      })
      $(document).on('change', '#repaired_replacement', function() {
         if ($(this).val() == 'Replacement') {
            $("#replacement_tag").val('Yes');
            $("#replacement_tag").change();
            $("#tag-number").removeClass('d-none');
         } else if ($(this).val() == 'Repaired') {
            $("#replacement_tag").val('No');
            $("#replacement_tag").change();
            $("#tag-number").addClass('d-none');
         } else {
            $("#replacement_tag").val('');
            $("#replacement_tag").change();
            $("#tag-number").addClass('d-none');
         }
      });
      $(document).on("click", "#add-service", function() {
         var newTR = '<tr><td><select name="service['+counter+'][service]" class="form-control select2"><option value="">Select Service</option></select></td><td><input name="service['+counter+'][quantity]" class="form-control" /></td><td><input name="service['+counter+'][price]" readonly class="form-control" /></td><td><input name="service['+counter+'][subtotal]" readonly class="form-control" /></td></tr>';
         counter++;
         $("#table-service").append(newTR);
         $('.select2').select2();
      });

      $(document).on("click", "#add-spare", function() {
         var newTR = '<tr><td><select name="spare['+counter2+'][product]" class="form-control select2"><option value="">Select Product</option></select></td><td><input name="spare['+counter2+'][unit]" class="form-control" /></td><td><input name="spare['+counter2+'][quantity]" class="form-control" /></td><td><input name="spare['+counter2+'][price]" readonly class="form-control" /></td><td><input name="spare['+counter2+'][subtotal]" readonly class="form-control" /></td></tr>';
         counter2++;
         $("#table-spare").append(newTR);
         $('.select2').select2();
      });
   </script>
</x-app-layout>