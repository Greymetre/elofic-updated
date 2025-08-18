<x-app-layout>
  <style>

.summary-box span{
  color: #111827;
  font-weight: 600;
  font-size: 14px;
}

.summary-box select.form-control.form-control-sm {
    border: 1px solid #D1D5DB;
    background: #fff;
    height: 30px !important;
    color: #111827;
    font-weight: 400;
    padding: 0px 10px;
}

.summary-box input#adjustment {
    border: 1px solid #D1D5DB;
    background: #fff;
    height: 30px;
    padding: 0px 13px;
    font-size: 14px !important;
    color: #111827 !important;
    font-weight: 600;
}

.summary-box label.summary-label {
    color: #374151 !important;
    font-weight: 400!important
    font-size: 15px !important;
}

.summary-box input#discount {
    border: 1px solid #D1D5DB;
    background: #fff;
    height: 30px;
    padding: 0px 13px;
    font-size: 14px !important;
    color: #111827 !important;
    font-weight: 600;
}

    .summary-box {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
      font-family: Arial, sans-serif;
      max-width: 600px;
      margin: 20px auto;
    }
    .summary-label {
      font-weight: 600;
      font-size: 14px;
    }
    .summary-box .form-group {
      /*margin-bottom: 1rem;*/
    }
    .summary-box .col-3,
    .summary-box .col-6 {
      display: flex;
      align-items: center;
    }
    .summary-box .col-3.text-right {
      justify-content: flex-end;
    }
    .summary-box .custom-control {
      margin-right: 1rem;
    }
    .summary-box .total-label,
    .summary-box .total-value {
       font-weight: 700;
      color: #111827;
    }

    .summary-box .border-top {
      border-top: 1px solid #dee2e6 !important;
    }

body .invocie_main .customerNotes {
    border-radius: 12px !important;
    padding: 12px !important;
    border: 1px solid #D1D5DB;
    height: 144px !important;
}

.upload-box {
      border: 2px dashed; #D1D5DB;
      border-radius: 16px;
      padding: 21px 20px;
      text-align: center;
      color: #6c757d;
      cursor: pointer;
      transition: background-color 0.3s;
      background-color: #fff;
      user-select: none;
    }
    .upload-box:hover {
      background-color: #f8f9fa;
    }
    .upload-icon {
      font-size: 36px;
      margin-bottom: 10px;
      color: #6c757d;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
    .upload-label {
      color: #007bff;
      font-weight: 500;
      display: block;
      margin-bottom: 5px;
    }
    .upload-note {
      font-size: 12px;
      color: #868e96;
      display: block;
    }
    input[type="file"] {
      display: none;
    }

    /*3 box*/

    body .invocie_main .table > tbody > tr > td {
        padding: 15px 10px !important;
        width: 4%!important;
    }

   body .invocie_main .table thead tr th{
      padding: 12px 10px !important;
    }

    body .invocie_main .table thead tr th {
      font-weight: 600;
      font-size: 12px;
      text-transform: uppercase;
      color: #6B7280!important;

    }
   .invocie_main .table > tbody > tr >td.action-cell {
      color: red!important;
      cursor: pointer!important;
      font-weight: bold!important;
    }
    .invocie_main .add-row-btn {
      cursor: pointer;
      font-size: 14px;
      font-weight: 600;
      color: #007bff;
      background: none;
      border: none;
      padding: 0;
      outline: 0px!important;
    }

    .invocie_main button.add-row-btn:focus {
        outline: 0px;
    }

    .invocie_main .add-row-btn:hover {
      text-decoration: underline;
    }

    .invocie_main input[type="text"], .invocie_main input[type="number"], .invocie_main select {
      border: none;
      background: transparent;
      width: 100%;
      padding: 0;
      margin: 0;
      color: #6B7280!important;
    }

    .invocie_main input[type="text"]:focus, .invocie_main input[type="number"]:focus, .invocie_main select:focus {
      outline: none;
      border-bottom: 1px solid #007bff;
      background: white;
      color: #6B7280!important;
    }


    /*edit box*/

    .invocie_main label.bmd-label-static {
        font-weight: 600;
        font-size: 14px;
        color: #3779B7;
    }

    .invocie_main label.bmd-label-static.othercolor {
      color: #374151!important;
  }

  label.othercolor{
    color: #374151!important;
  }

    .custom-select-box {
      border: 1px solid #D1D5DB !important;
      border-radius: 12px !important;
      font-size: 16px !important;
      color: #111827 !important;
      padding: 8px 12px; /* spacing */
      height: 45px;      /* avoid fixed Bootstrap height */
    }

     .custom-input-box {
      border: 1px solid #D1D5DB !important;
      border-radius: 12px !important;
      font-size: 16px !important;
      color: #111827 !important;
      padding: 8px 12px;
      height: 45px; /* allow natural height */
      text-indent: 10px;
    }

    select#exampleSelect {
    background-image: url(https://demo.fieldkonnect.io/public/assets/img/barrow.png);
    background-size: 2%;
    background-position: 98% 50%;
    background-repeat: no-repeat;
}


    .address-box {
      color: #111827!important;
  }

  p.subh {
    color: #111827!important;
}

  .address-box p {
    font-size: 14px !important;
    font-weight: 400;
}

span.address-title {
    color: #374151;
    font-weight: 700;
}

    .address-box {
      padding: 20px;
      background: #fff;
      position: relative;
    }

    .address-title {
      font-weight: bold;
      text-transform: uppercase;
      font-size: 14px;
    }

    .edit-icon {
      position: absolute;
      top: 22px;
      color: #888;
      cursor: pointer;
      left: 159px;
    }

    .edit-icon:hover {
      color: #007bff;
    }


    /*end*/

    .invocie_main h5{
      font-size: 20px;
      line-height: 1.4em;
      margin-bottom: 15px;
      color: #000;
      font-weight: bold;
      color: #3C4858;
      font-family: 'Poppins', sans-serif;
    }

    button.btn.btn-primary {
    background: unset !important;
    background-color: #35A3D8 !important;
    width: 40px !important;
    height: 40px !important;
    border-radius: 50px !important;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 10px 6px;
}

select#customerName {
    border: 0px;
}

.innerborder {
    width: 100%;
    height: 50px !important;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 5px 5px !important;
    border: 1px solid #D1D5DB;
    border-radius: 10px;
    background: #fff;
}

.table{
  overflow: unset!important;
}

body .table > tbody > tr > td{
  padding: 10px 20px!important;
}

body .table thead {
    background-color: #fff !important;
    box-shadow: 0px 4px 4px 0px #DBDBDB40!important;
    border: 1px solid #E5E7EB!important;
}

body .table tbody tr{
  box-shadow: unset!important;
}

body .table thead tr th{
    font-size: 14px!important;
    font-weight: 600 !important;
    color: #262A2A!important;
    font-family: 'Poppins', sans-serif !important;
    color: #6B7280 !important;
}


.custom-select:focus{
  box-shadow: unset!important;
}

.custom-select{
  background: #fff!important;
}

.table{
  border: 1px solid #E5E7EB!important;
}

.table .thead-light th{
  background: #F9FAFB!important;
}

    .form-check, label {
        font-size: 14px;
        font-weight: 600;
        color: #3779B7;
    }

    .custom-select {
     /* appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      background-image: none !important;*/
    }
  </style>
  <section class="invocie_main">
    <div class="container-fluid">
      <div class="card p-4">
        <h5><i class="fa fa-file-text-o"></i> New Invoice</h5>
      <div class="col-md-6">
      <div class="form-group">
        <label for="customerName">Customer Name <span class="text-danger">*</span></label>
        <div class="input-group">
           <div class="innerborder">
          <!-- Dropdown -->
          <select class="custom-select" id="customerName">
            <option selected>Sumeru, Inc</option>
            <option>ABC Pvt Ltd</option>
            <option>XYZ Corporation</option>
            <option>Global Traders</option>
            <option>NextGen Solutions</option>
          </select>
          <!-- Search Button -->
          <div class="input-group-append">
            <button class="btn btn-primary" type="button">
              <i class="fa fa-search"></i>
            </button>
          </div>
        </div>
        </div>
      </div>
    </div>
    <div class="row">
    <!-- Billing Address -->
    <div class="col-md-6 mb-3">
      <div class="address-box">
        <span class="address-title">Billing Address</span>
        <i class="fa fa-pencil edit-icon"></i>
        <p class="mb-0 font-weight-bold subh">Shivaan</p>
        <p class="mb-0">24, MG Road, Suite 101</p>
        <p class="mb-0">Bengaluru, Karnataka</p>
        <p class="mb-0">560001</p>
        <p class="mb-0">India</p>
        <p class="mb-0">Phone: +91 98765 43210</p>
        <p class="mb-0">GST Treatment: Registered Business</p>
        <p class="mb-0">GSTIN: 27AATCA6479H1ZD</p>
      </div>
    </div>

    <!-- Shipping Address -->
    <div class="col-md-6 mb-3">
      <div class="address-box">
        <span class="address-title subh">Shipping Address</span>
        <i class="fa fa-pencil edit-icon"></i>
        <p class="mb-0 font-weight-bold">Shivaan</p>
        <p class="mb-0">24, MG Road, Suite 101</p>
        <p class="mb-0">Bengaluru, Karnataka</p>
        <p class="mb-0">560001</p>
        <p class="mb-0">India</p>
        <p class="mb-0">Phone: +91 98765 43210</p>
      </div>
    </div>
  </div>
  
    <div class="row">
    <!-- Column with form-group -->
    <div class="col-md-6">
      <div class="form-group mb-3">
        <label for="exampleSelect">Choose Option</label>
        <select class="form-control custom-select-box" id="exampleSelect">
          <option>Option 1</option>
          <option>Option 2</option>
          <option>Option 3</option>
          <option>Option 4</option>
        </select>
      </div>
    </div>
  </div>
  <div class="row">
    <!-- Column with form-group -->
    <div class="col-md-4">
      <div class="form-group mb-3">
        <label for="exampleInput">Invoice#*</label>
        <input type="text" class="form-control custom-input-box" id="exampleInput" >
      </div>
    </div>
    <div class="col-md-4">
      <div class="form-group mb-3">
        <label for="exampleInput" class="othercolor">Order Number</label>
        <input type="text" class="form-control custom-input-box" id="exampleInput" >
      </div>
    </div>
  </div>


   <div class="row">
    <!-- Column with form-group -->
    <div class="col-md-3">
      <div class="form-group mb-3">
        <label for="exampleInput">Invoice Date*</label>
        <input type="date" class="form-control custom-input-box" id="exampleInput" >
      </div>
    </div>
    <div class="col-md-3">
      <div class="form-group mb-3">
        <label for="exampleInput" class="othercolor">Salesperson</label>
        <select class="form-control custom-select-box" id="exampleSelect">
          <option>Option 1</option>
          <option>Option 2</option>
          <option>Option 3</option>
          <option>Option 4</option>
        </select>
      </div>
    </div>
     <div class="col-md-3">
      <div class="form-group mb-3">
        <label for="exampleInput" class="othercolor">Terms</label>
        <select class="form-control custom-select-box" id="exampleSelect">
          <option>Option 1</option>
          <option>Option 2</option>
          <option>Option 3</option>
          <option>Option 4</option>
        </select>
      </div>
    </div>
     <div class="col-md-3">
      <div class="form-group mb-3">
        <label for="exampleInput" class="othercolor">Due Date</label>
        <input type="date" class="form-control custom-input-box" id="exampleInput" >
      </div>
    </div>
  </div>

   <div class="row">
      <div class="col-md-12">
        <h6>Item Table</h6>
        <div class="table-responsive">
         <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th>Item Details</th>
                <th>HSN/SAC Code</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Tax</th>
                <th>Amount</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="item-table-body">
              <tr>
                <td> <select>
                    <option selected>IGST18(18%)</option>
                    <option>CGST9(9%)</option>
                    <option>SGST9(9%)</option>
                    <option>None</option>
                  </select></td>
                <td><input type="text" value="ABC0123456" /></td>
                <td><input type="number" min="0" step="0.01" value="1.00" /></td>
                <td><input type="number" min="0" step="0.01" value="0.00" /></td>
                <td>
                  <select>
                    <option selected>IGST18(18%)</option>
                    <option>CGST9(9%)</option>
                    <option>SGST9(9%)</option>
                    <option>None</option>
                  </select>
                </td>
                <td><input type="text" value="2,96,56000.00" readonly /></td>
                <td class="action-cell" onclick="removeRow(this)">×</td>
              </tr>
            </tbody>
          </table>
           <button class="add-row-btn" onclick="addRow()">+ Add New Row</button>
        </div>  
      </div>
    </div>

    <div class="row">
      <div class="col-md-4">
         <div class="form-group">
            <label for="customerNotes" class="othercolor">Customer Notes</label>
            <textarea 
              class="form-control customerNotes " 
              id="customerNotes" 
              rows="4" 
              placeholder="Bank Details :- Bank Name - Axis Bank Ltd"
            ></textarea>
        </div>
      </div>
      <div class="col-md-4">
         <label class="othercolor mb-0" for="fileUpload">Attach File(s) to Invoice</label>
            <div class="upload-box" id="uploadBox">
              <svg xmlns="http://www.w3.org/2000/svg" class="upload-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="36" height="36">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5m0 0l5 5m-5-5v12" />
              </svg>
              <span class="upload-label">Upload File</span>
              <span class="upload-note">You can upload a maximum of 10 files, 10MB each</span>
            </div>
          <input type="file" id="fileUpload" multiple />
      </div>
      <div class="col-md-4">
        <div class="summary-box">

           <!-- Sub Total at the top -->
          <div class="form-group d-flex justify-content-between align-items-center mb-2">
              <label class="summary-label mb-0">Sub Total</label>
              <span>0.00</span>
          </div>

          <!-- Discount row -->
          <div class="form-group d-flex align-items-center">
            <div class="col-3 px-0">
              <label class="summary-label mb-0" for="discount">Discount</label>
            </div>
            <div class="col-6 px-1">
              <input type="number" class="form-control form-control-sm" id="discount" value="0" min="0" />
            </div>
            <div class="col-3 px-0 text-right">
              <span>0.00</span>
            </div>
          </div>

          <!-- Tax (TDS/TCS) row -->
          <div class="form-group d-flex align-items-center">
            <div class="col-3 px-0 d-flex">
               <label class="summary-label mb-0" for="TDS">TDS</label>
            </div>
            <div class="col-6 px-1">
              <select class="form-control form-control-sm">
                <option>Select a Tax</option>
                <option>Tax 1</option>
                <option>Tax 2</option>
              </select>
            </div>
            <div class="col-3 px-0 text-right">
              <span>0.00</span>
            </div>
          </div>

          <!-- Adjustment row -->
          <div class="form-group d-flex align-items-center">
              <div class="col-3 px-0">
                <label class="summary-label mb-0" for="adjustment">Adjustment</label>
              </div>
              <div class="col-6 px-1">
                <input
                  type="text"
                  class="form-control form-control-sm"
                  id="adjustment"
                  placeholder="Text"
                />
              </div>
              <div class="col-3 px-0 text-right">
                <span>0.00</span>
              </div>
          </div>

          <!-- Total at the bottom -->
          <div class="form-group d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
            <div class="total-label">Total ( ₹ )</div>
            <div class="total-value">0.00</div>
          </div>
        </div>
      </div>
    </div>
    </div>

</div>

  </div>
    
  
  
  </section>
   <script>
    // Get references to elements
    const uploadBox = document.getElementById('uploadBox');
    const fileInput = document.getElementById('fileUpload');

    // When upload box is clicked, trigger the hidden file input click
    uploadBox.addEventListener('click', () => {
      fileInput.click();
    });

    // Optional: handle selected files
    fileInput.addEventListener('change', () => {
      if (fileInput.files.length > 0) {
        alert(`${fileInput.files.length} file(s) selected.`);
      }
    });
  </script>
    <script>
   function addRow() {
      const tbody = document.getElementById('item-table-body');
      const newRow = document.createElement('tr');

      newRow.innerHTML = `
        <td><input type="text" placeholder="Type or click to select" /></td>
        <td><input type="text" /></td>
        <td><input type="number" min="0" step="0.01" value="1.00" /></td>
        <td><input type="number" min="0" step="0.01" value="0.00" /></td>
        <td>
          <select>
            <option selected>IGST18(18%)</option>
            <option>CGST9(9%)</option>
            <option>SGST9(9%)</option>
            <option>None</option>
          </select>
        </td>
        <td><input type="text" readonly /></td>
        <td class="action-cell" onclick="removeRow(this)">×</td>
      `;

      tbody.appendChild(newRow);
    }

    function removeRow(el) {
      const row = el.closest('tr');
      row.remove();
    }
  </script>
</x-app-layout>