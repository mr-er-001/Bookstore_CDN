<?php include 'topheader.php'; ?>
<div class="container-fluid py-4">
    <div class="card shadow-lg rounded-3">
        <div class="card-header text-center text-white" style="background-color: #045E70;">
            <h4 style="color: white !important;"><i class="bi bi-building me-2"></i> Vendor Report</h4>
        </div>
        <div class="card-body mt-3">

            <!-- Search Form -->
            <form id="vendorForm" class="row g-3 align-items-end">

                <!-- Vendor Input -->
                <div class="col-md-4 position-relative">
                    <label class="form-label">Vendor</label>
                    <input type="text" id="vendorInput" class="form-control" placeholder="Enter vendor name" autocomplete="off">
                    <input type="hidden" id="vendor_id" name="vendor_id">
                    <div id="vendorResults" class="list-group position-absolute w-100 shadow" style="z-index:1000; display:none;"></div>
                </div>

                <!-- Invoice Input -->
                <div class="col-md-2">
                    <label class="form-label">Invoice#</label>
                    <input type="text" id="invoiceInput" name="invoice" class="form-control" placeholder="Invoice number" autocomplete="off">
                </div>

                <!-- From Date -->
              <!-- From Date -->
<div class="col-md-2">
    <label class="form-label">From Date</label>
    <input type="text" class="form-control date-picker" id="dateFrom" 
           name="dateFrom" placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
    <input type="hidden" class="date-mysql" name="dateFrom_mysql">
</div>

<!-- To Date -->
<div class="col-md-2">
    <label class="form-label">To Date</label>
    <input type="text" class="form-control date-picker" id="dateTo" 
           name="dateTo" placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
    <input type="hidden" class="date-mysql" name="dateTo_mysql">
</div>


                <!-- Search Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>

            </form>

            <!-- Table -->
            <div class="table-responsive mt-3">
                <table class="table table-striped table-hover" id="vendorTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Invoice#</th>
                            <th>Vendor</th>
                            
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Date</th>
                            
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr><td colspan="6" class="text-center text-muted py-4">Search to view records</td></tr>
                    </tbody>
                    <!-- <tfoot class="table-success">
                        <tr>
                            <td><strong>Total</strong></td>
                            <td></td><td></td>
                            <td id="totalQty"><strong>0</strong></td>
                            <td></td>
                            <td id="totalPrice"><strong>0</strong></td>
                        </tr>
                    </tfoot> -->
                </table>
            </div>

            <!-- Print Button -->
            <div class="text-end my-3" id="printButtonContainer" style="display:none;">
                <button class="btn btn-success" id="printButton"><i class="fas fa-print"></i> Print</button>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

    // Vendor autocomplete
    $('#vendorInput').on('keyup', function(){
        let query = $(this).val();
        $('#vendor_id').val('');
        if(query.length === 0) { $('#vendorResults').hide(); return; }

        $.post('vendor_api.php', {action:'vendor_suggest', vendor: query}, function(data){
            $('#vendorResults').html(data).show();
        });
    });

    $(document).on('click', '.vendor-item', function(e){
        e.preventDefault();
        $('#vendorInput').val($(this).text());
        $('#vendor_id').val($(this).data('id'));
        $('#vendorResults').hide();
    });

    $(document).click(function(e){
        if(!$(e.target).closest('#vendorInput,#vendorResults').length){
            $('#vendorResults').hide();
        }
    });

    // Search report
    $('#vendorForm').on('submit', function(e){
        e.preventDefault();

        let invoice = $('#invoiceInput').val().trim();
        if(invoice !== ''){
            $('#vendor_id').val('');
            $('#dateFrom').val('');
            $('#dateTo').val('');
        }

        $.post('vendor_api.php', $(this).serialize() + '&action=fetch_report', function(html){
            $('#tableBody').html(html);

            // Totals
            let totalQty = 0, totalPrice = 0;
            $('#tableBody tr').each(function(){
                let qty = parseInt($(this).find('td:eq(3)').text()) || 0;
                let price = parseFloat($(this).find('td:eq(5)').text().replace('$','')) || 0;
                totalQty += qty;
                totalPrice += price;
            });
            $('#totalQty').text(totalQty);
            $('#totalPrice').text(+totalPrice.toFixed(2));

            $('#printButtonContainer').toggle($('#tableBody tr').length > 0);
        });
    });

    // Print with proper styles
    $('#printButton').click(function(){
        let tableHTML = $('#vendorTable')[0].outerHTML;

        let printWindow = window.open('', '', 'width=1000,height=800');
        printWindow.document.write(`
            <html>
            <head>
                <title>Vendor Report</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
                <style>
                    body { padding: 20px; font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { text-align: left; padding: 8px; border: 1px solid #dee2e6; }
                    th { background-color: #045E70; color: white; }
                    tfoot td { font-weight: bold; background-color: #d1e7dd; }
                    h3 { text-align: center; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <h3>Vendor Report</h3>
                ${tableHTML}
            </body>
            </html>
        `);
        printWindow.document.close();

        setTimeout(function(){
            printWindow.print();
        }, 200);
    });

});

$('#tableBody').html(html);

// Totals
let totalQty = 0, totalPrice = 0;
$('#tableBody tr').each(function(){
    let qty = parseInt($(this).find('td:eq(3)').text()) || 0;
    let price = parseFloat($(this).find('td:eq(5)').text().replace('$','')) || 0;
    totalQty += qty;
    totalPrice += price;
});
$('#totalQty').text(totalQty);
$('#totalPrice').text(+totalPrice.toFixed(2));


$('#vendorTable tbody').html(html);
$('#printButtonContainer').toggle($('#vendorTable tbody tr').length > 0);



</script>


<?php include 'footer.php'; ?>
