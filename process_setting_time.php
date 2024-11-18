<?php
// Kết nối đến cơ sở dữ liệu
$conn = new PDO('mysql:host=localhost;dbname=BeCa', 'root', '');
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Lấy dữ liệu JSON từ yêu cầu `fetch`
$data = json_decode(file_get_contents("php://input"), true);

// Kiểm tra dữ liệu
if (!empty($data['start']) && !empty($data['interval']) && !empty($data['amount_save'])) {
    $start = $data['start'];
    $interval = $data['interval'];
    $amount_save = $data['amount_save'];

    // Thực hiện chèn dữ liệu vào bảng `setting_time`
    $sql1 = "INSERT INTO setting_time (time_start, interval_time, amount_save) VALUES (:start, :interval, :amount_save)";
    $stmt1 = $conn->prepare($sql1);
    $query1 = $stmt1->execute([
        ':start' => $start,
        ':interval' => $interval,
        ':amount_save' => $amount_save
    ]);

    // Kiểm tra nếu chèn thành công và trả về phản hồi JSON
    if ($query1) {
        echo json_encode(["success" => true, "message" => "Data saved successfully"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to save data"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
}
?>