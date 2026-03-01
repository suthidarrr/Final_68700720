<?php

include 'condb.php';

$action = $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    // เพิ่ม / แก้ไข / ลบ
    switch($action) {

        case 'add':
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];

            // อัพโหลดไฟล์รูป
            $filename = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $targetDir = "uploads/";
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $filename = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $filename;
                move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
            }

            $sql = "INSERT INTO employees (first_name, last_name, address, phone, image)
                    VALUES (:first_name, :last_name, :address, :phone, :image)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':image', $filename);

            if ($stmt->execute()) {
                echo json_encode(["message" => "เพิ่มข้อมูลสำเร็จสำเร็จ"]);
            } else {
                echo json_encode(["error" => "เพิ่มข้อมูลล้มเหลว"]);
            }
            break;

        case 'update':
            $emp_id = $_POST['emp_id'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $address = $_POST['address'];
            $phone = $_POST['phone'];

            // อัพโหลดไฟล์รูป
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $targetDir = "uploads/";
                $filename = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $filename;
                move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
                $imageSQL = ", image = :image";
            } else {
                $imageSQL = "";
            }

            $sql = "UPDATE employees SET 
                        first_name = :first_name,
                        last_name = :last_name,
                        address = :address,
                        phone = :phone
                        $imageSQL
                    WHERE emp_id = :emp_id";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':emp_id', $emp_id);
            if (isset($filename)) $stmt->bindParam(':image', $filename);

            if ($stmt->execute()) {
                echo json_encode(["message" => "แก้ไขสินค้าสำเร็จ"]);
            } else {
                echo json_encode(["error" => "แก้ไขสินค้าล้มเหลว"]);
            }
            break;

        case 'delete':
            $emp_id = $_POST['emp_id'];
            $stmt = $conn->prepare("DELETE FROM employees WHERE emp_id = :emp_id");
            $stmt->bindParam(':emp_id', $emp_id);
            if ($stmt->execute()) {
                echo json_encode(["message" => "ลบสินค้าสำเร็จ"]);
            } else {
                echo json_encode(["error" => "ลบสินค้าล้มเหลว"]);
            }
            break;

        default:
            echo json_encode(["error" => "Action ไม่ถูกต้อง"]);
            break;
    }

} else {
    // GET: ดึงข้อมูลสินค้า
    $stmt = $conn->prepare("SELECT * FROM employees ORDER BY emp_id DESC");
    if ($stmt->execute()) {
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $employees]);
    } else {
        echo json_encode(["success" => false, "data" => []]);
    }
}
?>
