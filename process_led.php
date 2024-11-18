<?php
// Kết nối đến cơ sở dữ liệu
$conn = new PDO('mysql:host=localhost;dbname=BeCa', 'root', '');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Lấy dữ liệu JSON từ yêu cầu `fetch`
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra nếu có dữ liệu `color`
if (!empty($data['color'])) {
    $color = $data['color'];

    // Thực hiện chèn dữ liệu vào bảng `led`
    $sql = "INSERT INTO led (color) VALUES (:color)";
    $stmt = $conn->prepare($sql);
    $query = $stmt->execute([':color' => $color]);

    // Kiểm tra nếu chèn thành công và trả về phản hồi JSON
    if ($query) {
        echo json_encode(["success" => true, "message" => "Color saved successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to save color"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
?>
