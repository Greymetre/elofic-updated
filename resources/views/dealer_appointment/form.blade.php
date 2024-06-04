<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Dealer / Distributor Appointment (Greymeter)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://silver.fieldkonnect.io//public/assets/plugins/select2/css/select2.css">
    <style>
        .inner-border {
            padding: 5px;
            border-bottom: 1px solid gray;
        }

        .middle-border {
            padding: 1px;
            border-bottom: 6px solid #787373;
        }

        .outer-border {
            border-bottom: 2px solid #000;
        }

        .content {
            padding: 20px;
            background-color: white;
        }

        input.form-check-input {
            border: 2px solid;
            width: 20px;
            height: 20px;
        }

        .box-inputs {
            border: 1px solid;
            border-radius: 5px;
            padding: 15px;
        }

        input {
            border-bottom: 1px solid #000 !important;
        }

        .uppercase {
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="outer-border">
            <div class="middle-border">
                <div class="inner-border">
                    <div class="text-center mt-3 content">
                        <img src="{{asset('assets/img/dealer_appointment_logo.png')}}" alt="">
                        <p style="font-weight: 900;font-family: revert;" >SILVER CONSUMER ELECTRICALS (P) LTD</p>
                    </div>
                </div>
            </div>
        </div>

        <h1 class="text-center">DEALER / DISTRIBUTOR DATA SHEET</h1>
        <p>(All information furnished by you will be treated as strictly confidential)</p>

        <form action="{{route('dealer-appointment-form.store')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row mt-3">
                <div class="col-md-4 content-frm bg-light">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="branch">Branch </label>
                        </div>
                        <div class="col-md-9">
                            <select class="form-select" name="branch" id="blood_group">
                                <option value="" disabled selected>Your answer</option>
                                @if($branchs && count($branchs) > 0)
                                @foreach($branchs as $branch)
                                <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 content-frm bg-light">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label for="district">District </label>
                        </div>
                        <div class="col-md-9">
                            <select class="form-select select2" name="district" id="district">
                                <option value="" disabled selected>Your answer</option>
                                @if($districts && count($districts) > 0)
                                @foreach($districts as $district)
                                <option value="{{$district->id}}">{{$district->district_name}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 content-frm bg-light">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <label for="city">Town / City </label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-select select2" name="city" id="city">
                                <option value="" disabled selected>Select City</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-2">
                    <label for="appointment_date">Date of Appointment </label>
                </div>
                <div class="col-md-4">
                    <input type="date" name="appointment_date" id="appointment_date" class="form-control uppercase" required>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="distributor"> Distributor </label>
                        <input required class="form-check-input mr-3" type="radio" name="customertype" value="distributor" id="distributor">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="dealer"> Dealer </label>
                        <input required class="form-check-input mr-3" type="radio" name="customertype" value="dealer" id="dealer">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="shopee"> Shopee </label>
                        <input required class="form-check-input mr-3" type="radio" name="customertype" value="shopee" id="shopee">
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="PUMPMOTORS"> PUMP & MOTORS </label>
                        <input required class="form-check-input" type="radio" value="PUMP&MOTORS" name="division" id="PUMPMOTORS">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="FAN&APP"> FAN & APP </label>
                        <input required class="form-check-input" type="radio" name="division" value="FAN&APP" id="FAN&APP">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="AGRI"> AGRI </label>
                        <input required class="form-check-input" type="radio" name="division" value="AGRI" id="AGRI">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="SOLAR"> SOLAR </label>
                        <input required class="form-check-input" type="radio" name="division" value="SOLAR" id="SOLAR">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="LIGHTING"> LIGHTING </label>
                        <input required class="form-check-input" type="radio" name="division" id="LIGHTING" value="LIGHTING">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="Others"> Others </label>
                        <input required class="form-check-input" type="radio" name="division" id="Others" value="Others">
                    </div>
                </div>
            </div>

            <h5 class="mt-5">SECURITY DEPOSIT:</h5>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <label class="form-check-label" for="pumo"> PUMP & MOTORS </label>
                                <input required class="form-check-input" type="radio" name="security_deposit" id="pumo" value="10000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mr-3" type="text" oninput="this.value = this.value.toUpperCase()" name="SDPUMPMOTORS" value="10000" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <label class="form-check-label" for="F&A"> F&A </label>
                                <input required class="form-check-input" type="radio" name="security_deposit" id="F&A" value="5000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mr-3" type="text" oninput="this.value = this.value.toUpperCase()" name="SDF&A" value="5000" readonly>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group row">
                        <div class="col-md-4">
                            <div class="form-check">
                                <label class="form-check-label" for="agri"> AGRI </label>
                                <input required class="form-check-input" type="radio" name="security_deposit" id="agri" value="100000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control mr-3" type="text" oninput="this.value = this.value.toUpperCase()" name="SDPUMPMOTORS" value="100000" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">GST DETAILS:</h5>

            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="REGULAR"> REGULAR </label>
                        <input class="form-check-input mr-3" type="radio" name="gst_type" value="REGULAR" id="REGULAR">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="Composition"> COMPOSITION </label>
                        <input class="form-check-input mr-3" type="radio" name="gst_type" value="Composition" id="Composition">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="UNREGD"> UNREGD </label>
                        <input class="form-check-input mr-3" type="radio" name="gst_type" value="UNREGD" id="UNREGD">
                    </div>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <label> GST No. </label>
                        </div>
                        <div class="col-md-8">
                            <input class="form-control mr-3" type="text" oninput="this.value = this.value.toUpperCase()" name="gst_no" value="">
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">Firm:</h5>

            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="Prop"> Proprietorship </label>
                        <input class="form-check-input mr-3" type="radio" name="firm_type" value="Prop" id="Prop">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="Partnership"> Partnership Firm </label>
                        <input class="form-check-input mr-3" type="radio" name="firm_type" value="Partnership" id="Partnership">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <label class="form-check-label" for="LTD"> (P) LTD </label>
                        <input class="form-check-input mr-3" type="radio" name="firm_type" value="LTD" id="LTD">
                    </div>
                </div>
            </div>

            <h5 class="mt-5">GENERAL:</h5>

            <div class="main-details">
                <div class="row box-inputs mt-2">
                    <div class="col-md-12 mt-4">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Name of the Company/Firm </label>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="firm_name" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> CIN No in case of Company </label>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="cin_no" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Name of Related Firm in which presently dealing </label>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="related_firm_name" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Line of Business </label>
                            </div>
                            <div class="col-md-8">
                                <textarea name="line_business" class="form-control uppercase" id="line_business"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Office Address: </label>
                            </div>
                            <div class="col-md-12">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="office_address" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Pin: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="office_pincode" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Mobile No.: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="office_mobile" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Email: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="office_email" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-2">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <label> Showroom Address / GODOWN: </label>
                            </div>
                            <div class="col-md-12">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="godown_address" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Pin: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="godown_pincode" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Mobile No.: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="godown_mobile" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Email: </label>
                            </div>
                            <div class="col-md-9">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="godown_email" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">ORGANISATION:</h5>

            <div class="main-details">
                <div class="row box-inputs mt-2">
                    <div class="col-md-12 mt-4">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Status </label>
                            </div>
                            <div class="col-md-8">
                                <select name="status" id="status" class="form-control uppercase">
                                    <option value="" disabled selected>Please Select Status</option>
                                    <option value="Proprietor">Proprietor</option>
                                    <option value="Partnership">Partnership</option>
                                    <option value="Private">Private LTD</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-2">
                        <table class="table border">
                            <tbody>
                                <tr>
                                    <th>#</th>
                                    <th>NAME</th>
                                    <th>AADHAR No</th>
                                    <th>PAN NO</th>
                                </tr>
                                <tr>
                                    <th rowspan="4" style="width: 15%;">Name of the Proprietor/Partners/Direct ors (Self attested copy Of AADHAR Card and PAN No to be attached)</th>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_name_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_adhar_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_pan_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_name_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_adhar_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_pan_2" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_name_3" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_adhar_3" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_pan_3" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_name_4" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_adhar_4" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="ppd_pan_4" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Contact Person / Name</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="contact_person_name" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Mobile No./ E-Mail</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="mobile_email" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Name of your Bankers</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="bank_name" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Address of the Banker</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="bank_address" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Account Type</th>
                                    <td colspan="3">
                                        <select name="account_type" id="account_type" class="form-control uppercase">
                                            <option value="" disabled selected>Please Select Account Type</option>
                                            <option value="Current Account">Current Account</option>
                                            <option value="CC Account">CC Account</option>
                                            <option value="OD Account">OD Account</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Account No.</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="account_number" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>IFSC CODE</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="ifsc_code" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Payment terms</th>
                                    <td colspan="3">
                                        <select name="payment_term" id="payment_term" class="form-control uppercase">
                                            <option value="" disabled selected>Please Select Payment Term</option>
                                            <option value="Direct">Direct</option>
                                            <option value="against">against</option>
                                            <option value="Advance">Advance</option>
                                            <option value="PDC">PDC</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Maximum Credit period</th>
                                    <td colspan="3"><input type="text" oninput="this.value = this.value.toUpperCase()" name="credit_period" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th colspan="3">Whether two (2) Cheque (s) have been collected – MCL CHEQUES (Nationalize) <span class="text-info">*(To Be filled at HO)</span></th>
                                    <td>
                                        <select name="payment_term" disabled id="payment_term" class="form-control uppercase">
                                            <option value="" disabled selected>Please Select</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-center" colspan="4">Give the Cheque details- (PLS SEND THE TWO CHEQUE (S) TO HO)</th>
                                </tr>
                                <tr>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>S.No.</th>
                                                <th>Cheque No.</th>
                                                <th>Account Number</th>
                                                <th>Banker’s Name & Address</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_no_1" class="form-control uppercase"></td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_account_number_1" class="form-control uppercase"></td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_bank_1" class="form-control uppercase"></td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_no_2" class="form-control uppercase"></td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_account_number_2" class="form-control uppercase"></td>
                                                <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="cheque_bank_2" class="form-control uppercase"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">ACTIVITES:</h5>

            <div class="main-details">
                <div class="row box-inputs mt-2">
                    <div class="col-md-12 mt-4">
                        <p class="text-center">Please give details of your present business (mention manufacturer’s name)</p>
                    </div>
                    <div class="col-md-12 mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>PRODUCT</th>
                                    <th>Nature Of Business (Dealer/Distributor/Stockiest)</th>
                                    <th>Annual Turn Over</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_company_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_product_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_turn_over_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_company_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_product_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_business_2" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="manufacture_turn_over_2" class="form-control uppercase"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-12 mt-2">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label> Present Annual Turnover </label>
                            </div>
                            <div class="col-md-8">
                                <input class="form-control uppercase" type="text" oninput="this.value = this.value.toUpperCase()" name="present_annual_turnover" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">ANTICIPATED BUSINESS:</h5>

            <div class="main-details">
                <div class="row box-inputs mt-2">
                    <div class="col-md-12 mt-4">
                        <p class="text-center">Division for which you are interested & Anticipated Turnover for Ensuing FY (All Figures in Lacs)</p>
                    </div>
                    <div class="col-md-12 mt-4">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Products</th>
                                    <th>Anticipated Business in Ensuing Full Year</th>
                                    <th>Next Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>MOTROS</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="motor_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="motor_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>PUMP</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="pump_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="pump_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>FAN & APP</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="F&A_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="F&A_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>LIGHTING</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="lighting_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="lighting_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>AGRI</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="agri_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="agri_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <td>SOLAR – PUMP</td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="solar_anticipated_business_1" class="form-control uppercase"></td>
                                    <td><input type="text" oninput="this.value = this.value.toUpperCase()" name="solar_next_year_business_1" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td colspan="2"><input type="text" oninput="this.value = this.value.toUpperCase()" name="anticipated_business_total" class="form-control uppercase"></td>
                                </tr>
                                <tr>
                                    <th colspan="3"><b>Note: Please sign Target sheets for TOD incentives</b></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <h5 class="mt-5">Signatures of Dealer:</h5>

            <div class="main-details">
                <div class="row box-inputs mt-2">
                    <div class="col-md-12 mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <p>Dealer Name and Rubber Stamp </p>
                            </div>
                            <div class="col-md-6 text-center">
                                <p>.............................................................. <br> (With Signature) </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-details">
                <div class="row box-inputs mt-2">
                    <div class="col-md-6 mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Credit limit (Lacs)</label>
                            </div>
                            <div class="col-md-8">
                                <input readonly type="text" oninput="this.value = this.value.toUpperCase()" name="credit_limit" class="form-control uppercase">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-4">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Credit Rating (in grade)</label>
                            </div>
                            <div class="col-md-8">
                                <input readonly type="text" oninput="this.value = this.value.toUpperCase()" name="credit_rating" class="form-control uppercase">
                            </div>
                        </div>
                    </div>
                    <span class="text-info">*(To be Filled in by Branch manager)</span>
                </div>
            </div>

            <div class="row mt-5">
                <h5 style="text-decoration: underline;line-height: 5px;">Signatures and Approvals</h5>
                <h6 style="text-decoration: underline;">Important Note.</h6>
                <ol>
                    <li>Dealer/Customer has no Financial Interest in the Company apart from the making the purchases of the products of the Company.</li>
                    <li>Dealer to submit Order copy on letter head, Recent Photograph along with one Visiting card and photograph of counter</li>
                    <li>Any discount / offer / commercial terms etc. shall not be applicable unless communicated to dealer in writing, jointly, at least by the concerned Branch Head and National Head.</li>
                    <li>The Company shall not be responsible for any kind of loss or damage suffered by any person resulting out of any unethical / unwarranted acts or omissions etc. of any individual associated with the Company, whether deliberate or otherwise.</li>
                    <li>In Case of any dispute the courts at Rajkot along shall have the sole and exclusive jurisdiction.</li>
                </ol>
            </div>

            <div class="row mt-5">
                <h6 class="text-center" style="text-decoration: underline;">Declaration: Payment Instructions - Company Bank Account Only</h6>
                <p style="font-size: 12px;">I hope this letter finds you well. I am writing to formally communicate our company's payment policy regarding transactions. We kindly request that all payments to Silver Consumer Electricals Pvt Ltd be made exclusively through our designated company bank account. <br><br> In line with our commitment to ensuring transparency, security, and accountability in financial transactions, this policy to safeguard both our organization and our clients. Utilizing only our official company bank account for payments will help us better track and manage transactions, minimize errors, and prevent potential risks associated with cash transactions. <br><br> Kindly ensure that all future payments, including invoices and any other financial transactions, are processed using the provided bank account information. We kindly request your full cooperation in adhering to this payment policy to ensure a smooth and efficient business relationship.</p>
                <br>
                <p style="font-size: 15px;">Company does not entertain any type of cash transactions with any of the Company Representatives. Company is totally against CASH DEALING. If Dealer deals in cash with any Company representatives than he is personally liable for that.</p>
                <br>
                <p style="font-size: 15px;">Thank you for your understanding and cooperation in this matter. We look forward to continuing our positive business association.</p>
                <br>
                <br>
                <br>
                <h6><b>Channel Partners</b></h6>
                <br>
                <br>
                <br>
                <br>
                <h5><b>(Sign With Stamp)</b></h5>
                <br>
                <br>
                <br>
                <div class="row">
                    <div class="col-md-3 text-center"><b>(TM-ASM) </b></div>
                    <div class="col-md-3 text-center"><b>Branch Manager </b></div>
                    <div class="col-md-3 text-center"><b>Cluster/State Head </b></div>
                    <div class="col-md-3 text-center"><b>National Head (HO) </b></div>
                </div>
                <div class="row mt-5">
                    <div class="col-md-3 text-center"><b>(SIGN) </b></div>
                    <div class="col-md-3 text-center"><b>(SIGN) </b></div>
                    <div class="col-md-3 text-center"><b>(SIGN) </b></div>
                    <div class="col-md-3 text-center"><b>(SIGN) </b></div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-4">
                    <button class="btn btn-success">Submit</button>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4" style="text-align: right;">
                    <button type="button" id="printButton" class="btn btn-info">Print Form</button>
                </div>
            </div>


            <div class="row mt-5"></div>

        </form>
        <div class="row mt-3"></div>
    </div>
    <div class="baseurl" data-baseurl="{{ url('/')}}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://silver.fieldkonnect.io//public/assets/plugins/select2/js/select2.full.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#printButton').click(function() {
                    window.print();
                });
            });
            $(document).on('change', '#district', function() {
                var district_id = $(this).val();
                var base_url = $('.baseurl').data('baseurl');
                $.ajax({
                    url: base_url + '/getCity',
                    dataType: "json",
                    type: "GET",
                    data: {
                        _token: "{{csrf_token()}}",
                        district_id: district_id
                    },
                    success: function(res) {
                        var html = '<option value="">Select City</option>';
                        $.each(res, function(index, value) {
                            html += '<option value="' + value.id + '">' + value.city_name + '</option>';
                        });
                        $("#city").html(html);

                    }
                });
            })
            $('.select2').select2()
        </script>
</body>

</html>