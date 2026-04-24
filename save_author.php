<?php
include "dbb.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']); // Clean input

    // Validate empty name
    if (empty($name)) {
        echo "<script>alert('Author name cannot be empty!'); window.history.back();</script>";
        exit();
    }

    // 🔍 Check if author already exists (case-insensitive)
    $check_sql = "SELECT id FROM author WHERE LOWER(author_name) = LOWER(?)";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ❌ Author already exists
        echo "<script>alert('This author name already exists!'); window.history.back();</script>";
        exit();
    }

    // ✅ Insert new author
    $insert_sql = "INSERT INTO author (author_name) VALUES (?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("s", $name);

    if ($insert_stmt->execute()) {
    echo "<script>
        alert('✅ Author added successfully!');
        window.location.href = 'authordata.php';
    </script>";
    exit();
}

    } else {
        echo "Error: " . $insert_stmt->error;
    }

?>
