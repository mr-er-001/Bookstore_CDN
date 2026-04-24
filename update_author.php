<?php
include 'dbb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $author_name = trim($_POST['author_name']);

    if ($id > 0 && $author_name !== '') {
        $sql = "UPDATE author SET author_name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $author_name, $id);
        if ($stmt->execute()) {
            echo "Author updated successfully!";
        } else {
            echo "Error updating author.";
        }
    } else {
        echo "Invalid input.";
    }
}
?>
