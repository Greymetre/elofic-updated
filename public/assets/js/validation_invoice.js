$(document).ready(function () {
  $('#createInvoiceForm').validate({
    ignore: [], // Needed for select2
    rules: {
      customer_id: {
        required: true
      },
      'product_id[]': {
        required: true
      },
      user_id: {
        required: true
      },
      invoice_date: {
        required: true
      }
    },
    messages: {
      customer_id: {
        required: "Please select Customer",
      },
      'product_id[]': {
        required: "Please select at least one Product",
      },
      user_id: {
        required: "Please select User",
      },
      invoice_date: {
        required: "Please select Invoice Date",
      }
    },
    errorPlacement: function (error, element) {
      if (element.hasClass("select2-hidden-accessible")) {
        error.insertAfter(element.next('span.select2'));
      } else {
        error.insertAfter(element);
      }
    },
    highlight: function (element) {
      if ($(element).hasClass("select2-hidden-accessible")) {
        $(element).next(".select2").find(".select2-selection").addClass("is-invalid");
      } else {
        $(element).addClass("is-invalid");
      }
    },
    unhighlight: function (element) {
      if ($(element).hasClass("select2-hidden-accessible")) {
        $(element).next(".select2").find(".select2-selection").removeClass("is-invalid");
      } else {
        $(element).removeClass("is-invalid");
      }
    }
  });

  // For select2 real-time remove error on change
  $(".select2").on("change", function () {
    $(this).valid();
  });
});
