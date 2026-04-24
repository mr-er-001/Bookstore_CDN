<?php
include 'dbb.php';

$q = $_GET['q'] ?? '';
$clients = [];

if (strlen($q) >= 1) {
    // Search clients from 'client' table and get 'total_amount' as balance
    $sql = "SELECT id, company_name, 
                   COALESCE(total_amount, 0) as balance
            FROM client
            WHERE company_name LIKE ?
            ORDER BY company_name
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $search_term = "%$q%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $clients[] = [
            'id' => $row['id'],
            'company_name' => $row['company_name'],
            'ref_no' => $row['id'], // Just show the ID
            'balance' => number_format($row['balance'], 2)
        ];
    }
    
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($clients);
?>