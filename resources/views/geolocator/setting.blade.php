<x-app-layout>
  <style>
    .swal2-container.swal2-center.swal2-fade.swal2-shown {
      z-index: 9999;
    }

    #invoice_logo {
      cursor: pointer;
    }

    #invoice_esign {
      cursor: pointer;
    }
  </style>
  <section class="invoice_main">
    @if (count($errors) > 0)
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    @endif

    <div class="container-fluid">
      <div class="card p-4">
        <form action="{{ route('geo_locator_setting.store') }}" method="post" enctype="multipart/form-data">
          @csrf
          <h5><i class="fa fa-file-text-o"></i> Geo Locator Settings</h5>

          <div class="form-row">

            <div class="form-group col-md-6">
              <label for="customer_filter">Customer Filter</label>
              <select name="customer_filter[]" id="customer_filter" multiple class="form-control select2">
                <option value="">Select Customer Filter</option>

                @php
                $selectedCustomerFilters = $setting->customer_filter ?? [];
                @endphp

                <option value="Status" {{ in_array("Status", $selectedCustomerFilters) ? 'selected' : '' }}>Status</option>
                <option value="Customer Type" {{ in_array("Customer Type", $selectedCustomerFilters) ? 'selected' : '' }}>Customer Type</option>
                <option value="Created By" {{ in_array("Created By", $selectedCustomerFilters) ? 'selected' : '' }}>Created By</option>
                <option value="Firm Name" {{ in_array("Firm Name", $selectedCustomerFilters) ? 'selected' : '' }}>Firm Name</option>
                <option value="Parent Customer" {{ in_array("Parent Customer", $selectedCustomerFilters) ? 'selected' : '' }}>Parent Customer</option>
                <option value="First Name" {{ in_array("First Name", $selectedCustomerFilters) ? 'selected' : '' }}>First Name</option>
                <option value="Last Name" {{ in_array("Last Name", $selectedCustomerFilters) ? 'selected' : '' }}>Last Name</option>
                <option value="Mobile" {{ in_array("Mobile", $selectedCustomerFilters) ? 'selected' : '' }}>Mobile</option>
                <option value="Contact Number 2" {{ in_array("Contact Number 2", $selectedCustomerFilters) ? 'selected' : '' }}>Contact Number 2</option>
                <option value="Email" {{ in_array("Email", $selectedCustomerFilters) ? 'selected' : '' }}>Email</option>
                <option value="Address" {{ in_array("Address", $selectedCustomerFilters) ? 'selected' : '' }}>Address</option>
                <option value="Pincode" {{ in_array("Pincode", $selectedCustomerFilters) ? 'selected' : '' }}>Pincode</option>
                <option value="Market Place" {{ in_array("Market Place", $selectedCustomerFilters) ? 'selected' : '' }}>Market Place</option>
                <option value="City" {{ in_array("City", $selectedCustomerFilters) ? 'selected' : '' }}>City</option>
                <option value="District" {{ in_array("District", $selectedCustomerFilters) ? 'selected' : '' }}>District</option>
                <option value="State" {{ in_array("State", $selectedCustomerFilters) ? 'selected' : '' }}>State</option>
                <option value="Grade" {{ in_array("Grade", $selectedCustomerFilters) ? 'selected' : '' }}>Grade</option>
                <option value="Visit Status" {{ in_array("Visit Status", $selectedCustomerFilters) ? 'selected' : '' }}>Visit Status</option>
                <option value="GSTIN No" {{ in_array("GSTIN No", $selectedCustomerFilters) ? 'selected' : '' }}>GSTIN No</option>
                <option value="Aadhar No" {{ in_array("Aadhar No", $selectedCustomerFilters) ? 'selected' : '' }}>Aadhar No</option>
                <option value="PAN No" {{ in_array("PAN No", $selectedCustomerFilters) ? 'selected' : '' }}>PAN No</option>
                <option value="Other No" {{ in_array("Other No", $selectedCustomerFilters) ? 'selected' : '' }}>Other No</option>
                <option value="Shop Image" {{ in_array("Shop Image", $selectedCustomerFilters) ? 'selected' : '' }}>Shop Image</option>
                <option value="Employee Code" {{ in_array("Employee Code", $selectedCustomerFilters) ? 'selected' : '' }}>Employee Code</option>
                <option value="Employee Name" {{ in_array("Employee Name", $selectedCustomerFilters) ? 'selected' : '' }}>Employee Name</option>
                <option value="Designation" {{ in_array("Designation", $selectedCustomerFilters) ? 'selected' : '' }}>Designation</option>
                <option value="Branch Name" {{ in_array("Branch Name", $selectedCustomerFilters) ? 'selected' : '' }}>Branch Name</option>
              </select>
            </div>


            <div class="form-group col-md-6">
              <label for="lead_filter">Lead Filter</label>
              <select name="lead_filter[]" id="lead_filter" multiple class="form-control select2">
                <option value="">Select Lead Filter</option>

                @php
                $selectedLeadFilters = $setting->lead_filter ?? [];
                @endphp

                <option value="Lead Generation Date" {{ in_array("Lead Generation Date", $selectedLeadFilters) ? 'selected' : '' }}>Lead Generation Date</option>
                <option value="Firm Name" {{ in_array("Firm Name", $selectedLeadFilters) ? 'selected' : '' }}>Firm Name</option>
                <option value="Customer Name" {{ in_array("Customer Name", $selectedLeadFilters) ? 'selected' : '' }}>Customer Name</option>
                <option value="Customer Number" {{ in_array("Customer Number", $selectedLeadFilters) ? 'selected' : '' }}>Customer Number</option>
                <option value="Email" {{ in_array("Email", $selectedLeadFilters) ? 'selected' : '' }}>Email</option>
                <option value="Lead Source" {{ in_array("Lead Source", $selectedLeadFilters) ? 'selected' : '' }}>Lead Source</option>
                <option value="On Location" {{ in_array("On Location", $selectedLeadFilters) ? 'selected' : '' }}>On Location</option>
                <option value="Pincode" {{ in_array("Pincode", $selectedLeadFilters) ? 'selected' : '' }}>Pincode</option>
                <option value="City" {{ in_array("City", $selectedLeadFilters) ? 'selected' : '' }}>City</option>
                <option value="District" {{ in_array("District", $selectedLeadFilters) ? 'selected' : '' }}>District</option>
                <option value="State" {{ in_array("State", $selectedLeadFilters) ? 'selected' : '' }}>State</option>
                <option value="Lead Type" {{ in_array("Lead Type", $selectedLeadFilters) ? 'selected' : '' }}>Lead Type</option>
                <option value="Address" {{ in_array("Address", $selectedLeadFilters) ? 'selected' : '' }}>Address</option>
                <option value="Assignee" {{ in_array("Assignee", $selectedLeadFilters) ? 'selected' : '' }}>Assignee</option>
                <option value="Lead Status" {{ in_array("Lead Status", $selectedLeadFilters) ? 'selected' : '' }}>Lead Status</option>
                <option value="Close Duration" {{ in_array("Close Duration", $selectedLeadFilters) ? 'selected' : '' }}>Close Duration</option>
                <option value="Created By" {{ in_array("Created By", $selectedLeadFilters) ? 'selected' : '' }}>Created By</option>
              </select>
            </div>


          </div>

          <div class="mt-3">
            <button type="submit" class="btn btn-primary">Save Settings</button>
          </div>
        </form>
      </div>
    </div>
  </section>
  <script src="{{ url('/').'/'.asset('assets/js/jquery.custom.js') }}"></script>
</x-app-layout>