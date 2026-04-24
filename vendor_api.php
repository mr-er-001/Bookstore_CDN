<?php
include 'dbb.php';
$action = $_POST['action'] ?? '';

/* -----------------------------
   🔍 Vendor Name Autocomplete
----------------------------- */
if ($action == 'vendor_suggest') {
    $q = trim($_POST['vendor'] ?? '');
    if ($q == '') exit;

    $stmt = $conn->prepare("SELECT id, company_name FROM vendor WHERE company_name LIKE ? LIMIT 10");
    $like = "%$q%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            echo '<a href="#" class="list-group-item list-group-item-action vendor-item" data-id="' . $row['id'] . '">' . htmlspecialchars($row['company_name']) . '</a>';
        }
    } else {
        echo '<span class="list-group-item text-muted">No match</span>';
    }
    exit;
}

/* -----------------------------
   📊 Vendor Report Fetch
----------------------------- */
/* -----------------------------
   📊 Vendor Report Fetch (Grouped by Invoice)
----------------------------- */
if ($action == 'fetch_report') {
    $vendor  = $_POST['vendor_id'] ?? '';
    $invoice = trim($_POST['invoice'] ?? '');
    $from    = !empty($_POST['dateFrom']) ? date('Y-m-d', strtotime($_POST['dateFrom'])) : '';
    $to      = !empty($_POST['dateTo']) ? date('Y-m-d', strtotime($_POST['dateTo'])) : '';

    // 🟢 Case 1: Search by Invoice Number only
    if ($invoice !== '') {
        $sql = "
            SELECT 
                pi.invoice_no,
                MIN(pi.id) AS invoice_id,
                MAX(pi.invoice_date) AS invoice_date,
                pi.vendor_id,
                v.company_name AS vendor_name,
                SUM(pi.quantity * pi.price) AS total_price,
                COUNT(*) AS book_count
            FROM purchase_invoice pi
            INNER JOIN vendor v ON pi.vendor_id = v.id
            WHERE pi.invoice_no = ?
            GROUP BY pi.invoice_no, pi.vendor_id, v.company_name
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $invoice);
    }

    // 🟢 Case 2: Search by Vendor + Date Range
    elseif (!empty($vendor) && !empty($from) && !empty($to)) {
        $sql = "
            SELECT 
                pi.invoice_no,
                MIN(pi.id) AS invoice_id,
                MAX(pi.invoice_date) AS invoice_date,
                pi.vendor_id,
                v.company_name AS vendor_name,
                SUM(pi.quantity * pi.price) AS total_price,
                COUNT(*) AS book_count
            FROM purchase_invoice pi
            INNER JOIN vendor v ON pi.vendor_id = v.id
            WHERE pi.vendor_id = ? 
              AND pi.invoice_date BETWEEN ? AND ?
            GROUP BY pi.invoice_no, pi.vendor_id, v.company_name
            ORDER BY invoice_date ASC
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $vendor, $from, $to);
    }

    // ❌ Invalid input
    else {
        echo "<tr><td colspan='6' class='text-center text-muted'>Invalid input</td></tr>";
        exit;
    }

    $stmt->execute();
    $res = $stmt->get_result();

    $totalNet = 0;

    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $totalNet += $row['total_price'];
            $invoiceLink = "invoi.php?invoice_no=" . urlencode($row['invoice_no']);

            echo "<tr>
                    <td>" . htmlspecialchars($row['invoice_no']) . "</td>
                    <td><a href='{$invoiceLink}'>" . htmlspecialchars($row['vendor_name']) . "</a></td>
                    <td>" . (int)$row['book_count'] . "</td>
                    <td>" . number_format($row['total_price'], 2) . "</td>
                    <td>" . date('d-m-Y', strtotime($row['invoice_date'])) . "</td>
                  </tr>";
        }

        // ✅ Total row
        echo "<tr class='table-secondary fw-bold'>
                <td colspan='3' class='text-end'>Total Purchase Amount:</td>
                <td>" . number_format($totalNet, 2) . "</td>
                <td></td>
              </tr>";

        // ✅ Print button
        echo "<tr>
                <td colspan='6' class='text-end'>
                    <button class='btn btn-primary mt-3' onclick='printReport()'>
                        🖨️ Print Report
                    </button>
                </td>
              </tr>";

        // ✅ Print script
        echo "<script>
                function printReport() {
                    const btn = document.querySelector('button[onclick=\"printReport()\"]');
                    btn.style.display = 'none';
                    window.print();
                    btn.style.display = 'inline-block';
                }
              </script>";
    } else {
        echo "<tr><td colspan='6' class='text-center text-muted'>No invoices found</td></tr>";
    }

    exit;
}
?>
