

<?php
include 'condb.php';

$stmt = $conn->prepare("SELECT * FROM employees");
$stmt->execute();
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($employees);
?>

