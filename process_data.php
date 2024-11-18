<?php
$conn = new PDO('mysql:host=localhost;dbname=BeCa', 'root', '');
date_default_timezone_set('Asia/Ho_Chi_Minh');
// Kết nối đến cơ sở dữ liệu
// include 'connect.php'; // Đảm bảo rằng bạn đã có file kết nối đúng tên

// Lấy dữ liệu JSON từ yêu cầu POST
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu đã nhận
if (!empty($data['feedingAmount'])) {
    $thoigian = date("Y/m/d H:i:s");

    // Xác định lượng thức ăn dựa trên dữ liệu nhận được
    if ($data['feedingAmount'] == 1) {
        $amount = "small";
    } elseif ($data['feedingAmount'] == 2) {
        $amount = "medium";
    } else {
        $amount = "large";
    }

    // Thực hiện truy vấn INSERT
    $sql = "INSERT INTO thoigian (Time_choan, Amount) VALUES (:thoigian, :amount)";
    $stmt = $conn->prepare($sql);
    $query = $stmt->execute([
        ':thoigian' => $thoigian,
        ':amount' => $amount
    ]);

    // Kiểm tra nếu truy vấn thành công
    if ($query) {
        echo json_encode(["status" => "success", "message" => "Data inserted successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to insert data."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid data received."]);
}
?>