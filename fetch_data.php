<?php
// Kết nối cơ sở dữ liệu
$conn = new PDO('mysql:host=localhost;dbname=BeCa', 'root', '');
date_default_timezone_set('Asia/Ho_Chi_Minh');

$sql = "SELECT * FROM thoigian ORDER BY Time_choan DESC"; // Lấy dữ liệu mới nhất trước
$stmt = $conn->prepare($sql);
$stmt->execute();

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Trả về dữ liệu dưới dạng JSON
echo json_encode($result);
?>