<?php
include 'dbb.php'; // database connection
include 'topheader.php';
?>

<div class="container-fluid py-3">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header text-white rounded-top-4 d-flex justify-content-between align-items-center" style="background-color: #045E70;">
                    <h4 class="mb-0"><i class="bi bi-arrow-counterclockwise me-2"></i> Sales Return</h4>
                    <button class="btn btn-sm btn-light" id="printBtn"><i class="bi bi-printer"></i> Print</button>
                </div>
                <div class="card-body p-4">

                    <!-- Search Form -->
                    <form id="salesReturnForm" class="row g-3 mb-4">

                        <div class="col-md-4 position-relative">
    <label class="form-label fw-semibold"><i class="bi bi-person"></i> Client</label>
    <!-- Input for typing client name -->
    <input type="text" class="form-control shadow-sm search-client" placeholder="Enter client name" autocomplete="off">
    
    <!-- Hidden input to store selected client ID -->
    <input type="hidden" class="client-id" name="client_id">
    
    <!-- Dropdown for suggestions -->
    <div class="list-group clientResults position-absolute w-100 shadow" 
         style="z-index:1000; display:none; top:100%; left:0;"></div>
</div>


                        <!-- Date Range -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold"><i class="bi bi-calendar"></i> From</label>
                            <input type="text" id="fromDate" class="form-control date-picker" placeholder="dd-mm-yyyy" maxlength="10" autocomplete="off">
                            <input type="hidden" id="fromDate_mysql" name="from_date">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold"><i class="bi bi-calendar-event"></i> To</label>
                            <input type="text" id="toDate" class="form-control date-picker" placeholder="dd-mm-yyyy" maxlength="10" autocomplete="off">
                            <input type="hidden" id="toDate_mysql" name="to_date">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn w-100 shadow-sm" style="background-color: #045E70; color: white;">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>

                    </form>

                    <!-- Table -->
                    <div class="table-responsive shadow-sm rounded">
                        <table class="table table-hover table-striped align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Invoice#</th>
                                    <th>Title</th>
                                    <th>Quantity</th>
                                    <th>Date</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="salesReturnData">
                                <tr>
                                    <td colspan="6" class="text-muted py-3">Select client and date range to view sales return</td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end">Total</td>
                                    <td id="totalPrice">0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery + AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

    // Client autocomplete
    $(".search-client").on("input", function() {
        var query = $(this).val().trim();
        $(".client-id").val(''); // reset selected client ID
        if (!query) return $(".clientResults").hide();

        $.get("fetch_client_suggestions_ajax.php", { q: query }, function(data) {
            $(".clientResults").html(data).toggle(!!data.trim());
        });
    });

    // Select client from suggestions
    $(".clientResults").on("click", ".suggestion-item", function() {
        $(".search-client").val($(this).data("name"));
        $(".client-id").val($(this).data("id"));
        $(".clientResults").hide();
    });

    // Hide suggestions if clicked outside
    $(document).on("click", function(e) {
        if (!$(e.target).closest(".search-client, .clientResults").length) {
            $(".clientResults").hide();
        }
    });

    // Handle form submission
    $("#salesReturnForm").on("submit", function(e) {
        e.preventDefault();
        var client = $(".client-id").val(); // blank = all clients
        var from = $("#fromDate").val();
        var to = $("#toDate").val();

        if (!from || !to) {
            alert("Please select both From and To dates.");
            return;
        }

        $.get("sales_return_fetch.php", { client: client, from: from, to: to }, function(data) {
            $("#salesReturnData").html(data);

            // Recalculate total
            var total = 0;
            $(".price-val").each(function() {
                total += parseFloat($(this).text()) || 0;
            });
            $("#totalPrice").text(total.toFixed(2));
        });
    });

    // Print functionality
    $("#printBtn").on("click", function() {
        var table = $("table").clone();
        var w = window.open('', '', 'width=900,height=700');
        w.document.write('<html><head><title>Sales Return</title>');
        w.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">');
        w.document.write('</head><body>');
        var clientName = $(".search-client").val() || "All Clients";
        w.document.write('<h3 class="text-center mb-3">Sales Return - ' + clientName + '</h3>');
        w.document.write(table.prop('outerHTML'));
        w.document.write('</body></html>');
        w.document.close();
        w.print();
    });

});

</script>

<?php require_once 'footer.php'; ?>
