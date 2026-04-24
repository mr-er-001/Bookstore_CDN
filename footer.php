</main>

<!-- ======= Footer ======= -->
<footer id="footer" class="footer mt-auto py-3 bg-light text-center">
  <div class="copyright">
    &copy; Copyright <strong><span>Ijaz Book Store</span></strong>. All Rights Reserved
  </div>
</footer>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center">
  <i class="bi bi-arrow-up-short"></i>
</a>

<!-- Vendor JS -->
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/chart.js/chart.min.js"></script>
<script src="assets/vendor/echarts/echarts.min.js"></script>
<script src="assets/vendor/quill/quill.min.js"></script>
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="assets/vendor/tinymce/tinymce.min.js"></script>
<script src="assets/vendor/php-email-form/validate.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap Datepicker CSS and JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {
    console.log("Date picker initializing...");

    // Initialize datepicker for all .date-picker elements
    $('.date-picker').datepicker({
        format: 'dd-mm-yyyy',  // <-- Changed from dd/mm/yyyy
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto'
    }).on('changeDate', function(e) {
        console.log("Date selected:", e.date);

        var $hiddenField = $(this).siblings('.date-mysql');

        // Convert to MySQL format (yyyy-mm-dd)
        var date = e.date;
        var mysqlDate = date.getFullYear() + '-' + 
                       ('0' + (date.getMonth() + 1)).slice(-2) + '-' + 
                       ('0' + date.getDate()).slice(-2);

        $hiddenField.val(mysqlDate);
        console.log("MySQL format:", mysqlDate);
    });

    // Manual typing format handler
    $(document).on('input', '.date-picker', function(e) {
        var value = e.target.value.replace(/\D/g, ''); // Remove non-digits

        if (value.length >= 2) value = value.slice(0,2) + '-' + value.slice(2);
        if (value.length >= 5) value = value.slice(0,5) + '-' + value.slice(5,9);

        e.target.value = value;
    });

    // Form submit validation
    $('form').on('submit', function(e) {
        var isValid = true;

        $(this).find('.date-picker').each(function() {
            var dateValue = $(this).val().trim();
            var $hiddenField = $(this).siblings('.date-mysql');
            var datePattern = /^(\d{2})-(\d{2})-(\d{4})$/; // <-- Changed separator

            // Validate format
            if (dateValue && !datePattern.test(dateValue)) {
                alert('Please enter date in dd-mm-yyyy format');
                isValid = false;
                return false;
            }

            // Convert to MySQL format if not already done
            if (dateValue && $hiddenField.val() === '') {
                var parts = dateValue.match(datePattern);
                if (parts) {
                    var mysqlDate = parts[3] + '-' + parts[2] + '-' + parts[1];
                    $hiddenField.val(mysqlDate);
                }
            }
        });

        if (!isValid) e.preventDefault();
    });

    console.log("Date picker initialized!");
});
</script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->

<script>


$(document).ready(function() {

    // Search clients as you type
    $(document).on('keyup', '.search-client', function() {
        let input = $(this);
        let query = input.val();
        let resultsBox = input.siblings('.clientResults');

        if(query.length > 0){ 
            $.post('fetch_client.php', {search: query}, function(data){ 
                resultsBox.html(data).show(); 
            }); 
        } else {
            resultsBox.hide();
        }
    });

    // Click on a suggestion
    $(document).on('click', '.client-item', function(e){
        e.preventDefault();
        let item = $(this);
        $('.search-client').val(item.text());
        $('.client-id').val(item.data('id'));
        $('.clientResults').hide();
    });

    // Optional: hide results when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.search-client, .clientResults').length) {
            $('.clientResults').hide();
        }
    });

});
</script>
<script>
$(document).ready(function() {
    // Helper function for keyboard navigation
    function enableDropdownKeyboard(inputSelector, resultsSelector) {
        let currentIndex = -1;

        $(document).on('keydown', inputSelector, function(e) {
            let input = $(this);
            let resultsBox = input.siblings(resultsSelector);
            let items = resultsBox.find('.list-group-item');

            if (!items.length) return;

            if (e.key === "ArrowDown") {
                e.preventDefault();
                currentIndex++;
                if (currentIndex >= items.length) currentIndex = 0;
                items.removeClass('active');
                $(items[currentIndex]).addClass('active');

                // Scroll the dropdown to keep the active item visible
                let item = $(items[currentIndex]);
                let itemTop = item.position().top;
                let itemHeight = item.outerHeight();
                let scrollTop = resultsBox.scrollTop();
                let boxHeight = resultsBox.height();

                if (itemTop + itemHeight > boxHeight) {
                    resultsBox.scrollTop(scrollTop + itemTop + itemHeight - boxHeight);
                } else if (itemTop < 0) {
                    resultsBox.scrollTop(scrollTop + itemTop);
                }

            } else if (e.key === "ArrowUp") {
                e.preventDefault();
                currentIndex--;
                if (currentIndex < 0) currentIndex = items.length - 1;
                items.removeClass('active');
                $(items[currentIndex]).addClass('active');

                // Scroll
                let item = $(items[currentIndex]);
                let itemTop = item.position().top;
                let itemHeight = item.outerHeight();
                let scrollTop = resultsBox.scrollTop();
                let boxHeight = resultsBox.height();

                if (itemTop + itemHeight > boxHeight) {
                    resultsBox.scrollTop(scrollTop + itemTop + itemHeight - boxHeight);
                } else if (itemTop < 0) {
                    resultsBox.scrollTop(scrollTop + itemTop);
                }

            } else if (e.key === "Enter") {
                e.preventDefault();
                if (currentIndex >= 0 && currentIndex < items.length) {
                    $(items[currentIndex]).click();
                    currentIndex = -1;
                    resultsBox.hide();
                }
            }
        });

        // Prevent blur hiding the highlight immediately
        $(document).on('blur', inputSelector, function() {
            let input = $(this);
            let resultsBox = input.siblings(resultsSelector);
            setTimeout(() => {
                if (!resultsBox.is(':hover')) {
                    currentIndex = -1;
                    resultsBox.hide(); // optional: hide dropdown on blur if needed
                }
            }, 200);
        });

        // Prevent blur when clicking on dropdown items
        $(document).on('mousedown', resultsSelector + ' .list-group-item', function(e) {
            e.preventDefault(); // keep input focused
        });
    }

    // Enable for books and clients
    enableDropdownKeyboard('.search-book', '.bookResults');
    enableDropdownKeyboard('.search-client', '.clientResults');
});



$(document).ready(function() {

  // 🟦 Open modal and fill data
  $(document).on("click", ".edit-btn", function() {
    const id = $(this).data("id");
    const name = $(this).data("name");

    $("#editAuthorForm")[0].reset(); // reset form
    $("#author_id").val(id);
    $("#author_name").val(name);

    const modalEl = document.getElementById('editAuthorModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) modal = new bootstrap.Modal(modalEl);
    modal.show();
  });
  });



  // 🟩 Handle form submit (AJAX)
//   $("#editAuthorForm").submit(function(e) {
//     e.preventDefault();

//     $.ajax({
//       url: "update_author.php",
//       type: "POST",
//       data: $(this).serialize(),
//       success: function(response) {
//         alert(response);

//         const id = $("#author_id").val();
//         const newName = $("#author_name").val();
//         $("#author_name_" + id).text(newName);

//         // Close modal
//         const modalEl = document.getElementById('editAuthorModal');
//         const modal = bootstrap.Modal.getInstance(modalEl);
//         modal.hide();
//       },
//       error: function() {
//         alert("Something went wrong while updating.");
//       }
//     });
//   });

//   // 🧹 Reset modal form on close
//   $('#editAuthorModal').on('hidden.bs.modal', function () {
//     $(this).find('form')[0].reset();
//   });


</script>




<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>
/* Apply #045E70 color theme */

/* General text, labels, info, dropdown, search */
.dataTables_wrapper,
.dataTables_wrapper label,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_paginate,
.dataTables_wrapper .dataTables_paginate a,
.dataTables_wrapper select,
.dataTables_wrapper input,
.datatable th,
.datatable td {
  color: #045E70 !important;
}
.datatable th {
  color: white  !important;
}

/* Search input + dropdown border */
.dataTables_wrapper select,
.dataTables_wrapper input {
  border: 1px solid #045E70 !important;
  border-radius: 6px  !important;
}

/* Pagination active button */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
  background: #045E70 !important;
  color: #fff !important;
  border: none !important;
  border-radius: 6px !important;
}

/* Pagination hover */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: #045E70 !important;
  color: #fff !important;
  border: none !important;
  border-radius: 6px !important;
}
</style>

<script>
$(document).ready(function() {
  $('.datatable').DataTable({
    paging: true,       // pagination
    searching: true,    // search bar
    info: true,         // "showing X of Y entries"
    lengthChange: true  // entries dropdown
  });
});


$(document).ready(function() {
    var table = $('.datatable').DataTable();
    table.page.len(100).draw(); // ✅ Sets 100 entries without reinitializing
});


</script>

<script>
$(document).ready(function() {
    console.log("Date picker initializing...");

    // Initialize datepicker for all .date-picker elements
    $('.date-picker').datepicker({
        format: 'dd-mm-yyyy',  // <-- Changed from dd/mm/yyyy
        autoclose: true,
        todayHighlight: true,
        orientation: 'bottom auto'
    }).on('changeDate', function(e) {
        console.log("Date selected:", e.date);

        var $hiddenField = $(this).siblings('.date-mysql');

        // Convert to MySQL format (yyyy-mm-dd)
        var date = e.date;
        var mysqlDate = date.getFullYear() + '-' + 
                       ('0' + (date.getMonth() + 1)).slice(-2) + '-' + 
                       ('0' + date.getDate()).slice(-2);

        $hiddenField.val(mysqlDate);
        console.log("MySQL format:", mysqlDate);
    });


    
    // Manual typing format handler
    $(document).on('input', '.date-picker', function(e) {
        var value = e.target.value.replace(/\D/g, ''); // Remove non-digits

        if (value.length >= 2) value = value.slice(0,2) + '-' + value.slice(2);
        if (value.length >= 5) value = value.slice(0,5) + '-' + value.slice(5,9);

        e.target.value = value;
    });

    // Form submit validation
    $('form').on('submit', function(e) {
        var isValid = true;

        $(this).find('.date-picker').each(function() {
            var dateValue = $(this).val().trim();
            var $hiddenField = $(this).siblings('.date-mysql');
            var datePattern = /^(\d{2})-(\d{2})-(\d{4})$/; // <-- Changed separator

            // Validate format
            if (dateValue && !datePattern.test(dateValue)) {
                alert('Please enter date in dd-mm-yyyy format');
                isValid = false;
                return false;
            }

            // Convert to MySQL format if not already done
            if (dateValue && $hiddenField.val() === '') {
                var parts = dateValue.match(datePattern);
                if (parts) {
                    var mysqlDate = parts[3] + '-' + parts[2] + '-' + parts[1];
                    $hiddenField.val(mysqlDate);
                }
            }
        });

        if (!isValid) e.preventDefault();
    });

    console.log("Date picker initialized!");
});
</script>


<!-- STEP 4: In your PHP, use the hidden field value -->

<!-- ======= Custom JS for dynamic rows, calculations, search ======= -->
<script>

// Vendor Search
$(document).on("keyup", ".search-vendor", function() {
    let input = $(this);
    let query = input.val();
    let resultsBox = input.siblings(".vendorResults");
    if (query.length > 0) {
        $.post("fetch_vendor.php", { search: query }, function(data) {
            resultsBox.html(data).show();
        });
    } else resultsBox.hide();
});

$(document).on("click", ".vendor-item", function(e) {
    e.preventDefault();
    $(".search-vendor").val($(this).text());
    $(".vendor-id").val($(this).data("id"));
    $(".vendorResults").hide();
});

// Book Search
$(document).on("keyup", ".search-book", function() {
    let input = $(this);
    let query = input.val().trim();
    let resultsBox = input.siblings(".bookResults");
    if (query.length > 0) {
        $.post("fetch_books.php", { search: query }, function(data) {
            resultsBox.html(data).show();
        });
    } else resultsBox.hide();
});

$(document).on("click", ".book-item", function(e) {
    e.preventDefault();
    let parent = $(this).closest(".col-md-4");
    parent.find(".search-book").val($(this).text());
    parent.find(".book_id").val($(this).data("id"));
    parent.find(".bookResults").hide();
    let row = parent.closest(".productRow");
    if ($(this).data("price")) row.find(".price").val($(this).data("price"));
});

$(document).ready(function(){

    // Keep first row as template (but don't remove existing rows — drafts may have multiple)
    let rowTemplate = $('.productRow').first().clone();
    rowTemplate.find('input').not('[type=radio]').val('');
    let rowCount = $('.productRow').length;

    // Add new row
    window.addProductRow = function() {
        rowCount++;
        let newRow = rowTemplate.clone();
        newRow.find('input').val('');
        newRow.find('.discountType').each(function(){
            $(this).attr('name','discount_type_' + rowCount);
            $(this).prop('checked', $(this).val() === 'percent');
        });
        $('#productRows').append(newRow);
        calculateGrandTotal();
    };

    // Remove row
    $(document).on('click','.removeRow',function(){
        if($('.productRow').length>1){
            $(this).closest('.productRow').remove();
            calculateGrandTotal();
        }
    });

    // Calculate one row
// 🔹 Calculate one row
// function calculateRow(row){
//     let price    = parseFloat($(row).find('.price').val()) || 0;
//     let qty      = parseFloat($(row).find('.quantity').val()) || 0;
//     let discount = parseFloat($(row).find('.discount').val()) || 0;
//     let total    = price * qty;

//     // ✅ Show Gross Total (no discount) in row
//     $(row).find('.total').val(total.toFixed(2));

//     // ✅ Apply discount for net
//     let type = $(row).find('.discountType:checked').val() || 'percent';
//     let net  = (type === 'percent') ? total - (total * discount / 100) : total - discount;
//     if(net < 0) net = 0;

//     // ✅ Hidden field for net calculation (used for grand total)
//     if ($(row).find('.net_price').length === 0) {
//         $(row).append('<input type="hidden" class="net_price" name="net_price[]">');
//     }
//     $(row).find('.net_price').val(net.toFixed(2));

//     // Recalculate grand net
//     calculateGrandTotal();
// }

// 🔹 Calculate grand net price (bottom box)
function calculateGrandTotal(){
    let netTotal = 0;
    $('.productRow').each(function(){
        netTotal += parseFloat($(this).find('.net_price').val()) || 0;
    });
    $('#net_price').val(netTotal.toFixed(2));
}

// 🔹 Trigger calculation when user changes values
$(document).on('input change', '.price, .quantity, .discount, .discountType', function(){
    calculateRow($(this).closest('.productRow'));
});


    // Client search
    $(document).on('keyup','.search-client', function(){
        let input = $(this); 
        let query = input.val();
        let resultsBox = input.siblings('.clientResults');
        if(query.length>0){ 
            $.post('fetch_client.php',{search:query}, function(data){ resultsBox.html(data).show(); }); 
        } else { resultsBox.hide(); }
    });

    // Book search
    $(document).on('keyup','.search-book', function(){
        let input = $(this); 
        let query = input.val();
        let resultsBox = input.siblings('.bookResults');
        if(query.length>0){ 
            $.post('fetch_books.php',{search:query}, function(data){ resultsBox.html(data).show(); }); 
        } else { resultsBox.hide(); }
    });

    // Pick client
    $(document).on('click','.client-item', function(e){
        e.preventDefault();
        let item=$(this);
        $('.search-client').val(item.text());
        $('.client-id').val(item.data('id'));
        $('.clientResults').hide();
    });

    // Pick book
    $(document).on('click','.book-item', function(e){
        e.preventDefault();
        let item=$(this), parent=item.closest('.col-md-4');
        parent.find('.search-book').val(item.text());
        parent.find('.book-id').val(item.data('id'));
        parent.find('.bookResults').hide();

        let row = parent.closest('.productRow');
        row.find('.price').val(item.data('price'));
        row.find('.current_qty').val(item.data('stock'));
        calculateRow(row);
    });

    // First row calc
    calculateRow($('.productRow').first());
});
    
$(document).ready(function () {
    function updateSerialNumbers() {
        $("#productRows .productRow").each(function(index) {
            $(this).find(".serial").text(index + 1);
        });
    }

    $("#addRow").click(function () {
        let newRow = $("#rowTemplate").clone().removeAttr("id").show();
        newRow.find("input").val(""); 
        newRow.find(".vendor_id, .book_id").val(""); 
        $("#productRows").append(newRow);
        updateSerialNumbers();
    });

    $(document).on("click", ".removeRow", function () {
        if ($("#productRows .productRow").length > 1) {
            $(this).closest("tr").remove();
            updateSerialNumbers();
        }
    });

    updateSerialNumbers();
});

// Auto calculate row total when inputs change
// Auto calculate row total when inputs change
// function calculateNetPrice() {
//     let net = 0;
//     $(".productRow").each(function() {
//         let price = parseFloat($(this).find(".price").val()) || 0;
//         let qty = parseFloat($(this).find(".quantity").val()) || 0;
//         let discount = parseFloat($(this).find(".discount").val()) || 0;
//         let discountType = $(this).find(".discountType:checked").val();
// //        alert('kin');
//         let rowTotal = price * qty;

//         if (discountType === "percent") {
//             rowTotal = rowTotal - (rowTotal * discount / 100);
//         } else if (discountType === "cash") {
//             rowTotal = rowTotal - discount;
//         }

//         net += rowTotal;
//     });

//     $("#net_price").val(net.toFixed(2));
// }

// Update totals (without discount) on price or qty change
$(document).on("input", ".price, .quantity", function() {
    let row = $(this).closest(".productRow");
    let price = parseFloat(row.find(".price").val()) || 0;
    let qty = parseFloat(row.find(".quantity").val()) || 0;
    row.find(".total").val((price * qty).toFixed(2));
    calculateNetPrice();
});

// Recalculate net price if discount or type changes
$(document).on("input change", ".discount, .discountType", function() {
    calculateNetPrice();
});

// When adding/removing rows


// Initial calculation on page load
$(".productRow").each(function() {
    let price = parseFloat($(this).find(".price").val()) || 0;
    let qty = parseFloat($(this).find(".quantity").val()) || 0;
    $(this).find(".total").val((price * qty).toFixed(2));
});
calculateNetPrice();

// Vendor search (row specific)
$(document).on('keyup', '.searchvendor', function() {
    let input = $(this);
    let query = input.val();
    let resultsBox = input.siblings('.vendorResults');

    if (query.length > 0) {
        $.ajax({
            url: 'fetch_vendor.php',
            method: 'POST',
            data: {search: query},
            success: function(data) {
                resultsBox.html(data).fadeIn();
            }
        });
    } else {
        resultsBox.fadeOut().html('');
    }
});

$(document).on('click', '.vendor-item', function() {
    let vendorName = $(this).text();
    let vendorId = $(this).data('id');
    let parent = $(this).closest('.productRow');

    parent.find('.searchvendor').val(vendorName);
    parent.find('.vendor_id').val(vendorId);
    parent.find('.vendorResults').fadeOut();
});

// Book search (row specific)
$(document).on('keyup', '.searchbook', function() {
    let input = $(this);
    let query = input.val();
    let resultsBox = input.siblings('.bookResults');

    if (query.length > 0) {
        $.ajax({
            url: 'fetch_book.php',
            method: 'POST',
            data: {search: query},
            success: function(data) {
                resultsBox.html(data).fadeIn();
            }
        });
    } else {
        resultsBox.fadeOut().html('');
    }
});

$(document).on('click', '.book-item', function() {
    let bookName = $(this).text();
    let bookId = $(this).data('id');
    let parent = $(this).closest('.productRow');

    parent.find('.searchbook').val(bookName);
    parent.find('.book_id').val(bookId);
    parent.find('.bookResults').fadeOut();
});



  document.addEventListener("DOMContentLoaded", function () {
    let currentPath = window.location.pathname.split("/").pop();
    let navLinks = document.querySelectorAll(".navbar .nav-link, .navbar .dropdown-menu .dropdown-item");

    navLinks.forEach(link => {
      let linkPath = link.getAttribute("href");
      if (linkPath === currentPath) {
        link.classList.add("active");
      }
    });
  });
</script>



</body>
</html>