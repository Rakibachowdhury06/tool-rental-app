
document.addEventListener("DOMContentLoaded", () => {
  console.log("order-validation.js is running");

  const form = document.getElementById("orderForm");
  if (!form) return;

  form.addEventListener("submit", function (e) {
    let isValid = true;

    const customer = document.getElementById("customerID");
    const tool = document.getElementById("inventoryID");
    const quantity = document.getElementById("quantity");
    const startDate = document.getElementById("startDate");
    const endDate = document.getElementById("endDate");

    // Clear previous errors
    ["customerError", "toolError", "quantityError", "startDateError", "endDateError"].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.textContent = "";
    });

    [customer, tool, quantity, startDate, endDate].forEach(input => {
      input.classList.remove("invalid", "valid");
    });

    // Validate customer
    if (customer.value === "") {
      document.getElementById("customerError").textContent = "Please select a customer.";
      customer.classList.add("invalid");
      isValid = false;
    } else {
      customer.classList.add("valid");
    }

    // Validate tool
    if (tool.value === "") {
      document.getElementById("toolError").textContent = "Please select a tool.";
      tool.classList.add("invalid");
      isValid = false;
    } else {
      tool.classList.add("valid");
    }

    // Validate quantity
    if (!quantity.value || parseInt(quantity.value) < 1) {
      document.getElementById("quantityError").textContent = "Enter a valid quantity (1 or more).";
      quantity.classList.add("invalid");
      isValid = false;
    } else {
      quantity.classList.add("valid");
    }

    // Validate start date
    if (!startDate.value) {
      document.getElementById("startDateError").textContent = "Start date is required.";
      startDate.classList.add("invalid");
      isValid = false;
    } else {
      startDate.classList.add("valid");
    }

    // Validate end date
    if (!endDate.value) {
      document.getElementById("endDateError").textContent = "End date is required.";
      endDate.classList.add("invalid");
      isValid = false;
    } else {
      endDate.classList.add("valid");
    }

    // Compare dates if both are filled
    if (startDate.value && endDate.value) {
      const start = new Date(startDate.value);
      const end = new Date(endDate.value);

      if (start >= end) {
        document.getElementById("endDateError").textContent = "End date must be after start date.";
        endDate.classList.add("invalid");
        isValid = false;
      }
    }

    if (!isValid) {
      e.preventDefault();
      console.log("Rent order form validation failed.");
    }
  });
});
