<?php
include 'dbb.php'; // database connection

$client = $_POST['client'] ?? '';
$from   = $_POST['from_date'] ?? '';
$to     = $_POST['to_date'] ?? '';

// ✅ Base query (adjusted for correct table)
$query = "
    SELECT sri.*, 
           c.company_name AS client_name,
           b.title AS book_title
    FROM sale_return_invoice sri
    JOIN client c ON sri.client_id = c.id
    JOIN books b ON sri.book_id = b.id
    WHERE 1
";

$params = [];
$types = "";

// ✅ Filter by client
if (!empty($client)) {
    $query .= " AND sri.client_id = ?";
    $params[] = $client;
    $types .= "i";
}

// ✅ Filter by date range
if (!empty($from)) {
    $query .= " AND sri.return_date >= ?";
    $params[] = $from;
    $types .= "s";
}

if (!empty($to)) {
    $query .= " AND sri.return_date <= ?";
    $params[] = $to;
    $types .= "s";
}

$query .= " ORDER BY sri.return_date DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['invoice_no']) . "</td>";
        echo "<td>" . htmlspecialchars($row['book_title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td>" . htmlspecialchars(date('d-m-Y', strtotime($row['return_date']))) . "</td>";
        echo "<td class='price-val'>" . number_format($row['net_price'], 2) . "</td>";
        echo "<td>
                <button class='btn btn-sm btn-danger delete-btn' data-id='{$row['id']}'>
                    <i class='bi bi-trash'></i>
                </button>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center text-muted'>
            <i class='bi bi-info-circle'></i> No Data Found
          </td></tr>";
}

$stmt->close();
$conn->close();
?>
