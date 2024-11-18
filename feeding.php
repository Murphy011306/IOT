<?php
$conn = new PDO('mysql:host=localhost;dbname=BeCa', 'root', '');
date_default_timezone_set('Asia/Ho_Chi_Minh');



if (isset($_POST['start_time']) && isset($_POST['end_time'])) {
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];

    // Câu lệnh SQL tìm kiếm trong khoảng thời gian
    $sql = "SELECT * FROM thoigian WHERE Time_choan BETWEEN :start_time AND :end_time ORDER BY Time_choan DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':start_time', $startTime);
    $stmt->bindParam(':end_time', $endTime);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Lấy tất cả dữ liệu

    // Trả kết quả dưới dạng JSON
    echo json_encode($result);
    exit;
}

$sql = "SELECT * from thoigian ORDER BY Time_choan DESC";
$stmt = $conn->prepare($sql);
$query = $stmt->execute();
$result = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $result[] = $row;
}



$sql1 = "SELECT * from setting_time where id = (SELECT max(id) from setting_time)";
$stmt1 = $conn->prepare($sql1);
$query1 = $stmt1->execute();
$result1 = array();
while ($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $result1[] = $row1;
}

foreach ($result1 as $items):
    $temp = $items['amount_save'];
endforeach;




?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chức năng cho ăn</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #6d83f2, #83b1f2);
            margin: 0;
            padding: 0;
            color: #333;
            overflow: hidden;
        }

        .container {
            width: 90%;
            max-width: 800px;
            height: 100vh;
            margin: auto;
            background-color: #fff;
            padding: 23px;
            /* border-radius: 15px; */
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
        }

        h2,
        h3 {
            color: #444;
            margin-bottom: 20px;
        }

        /* p,h3{
            margin-left: 10vw;
        } */

        h2 {
            color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            margin-top: 15px;
            height: 200px;
            overflow-y: scroll;
        }

        tbody {
            display: block;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
        }

        tr {
            display: table;
            width: 100%;
            table-layout: fixed;
            /* Cố định độ rộng các cột */
        }

        th,
        td {
            padding: 12px 15px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #e1e6ea;
        }
        tbody tr:nth-child(odd) {
            background-color: #f0f2f5;
        }

        tbody tr:hover {
            background-color: #d0d7e1;
        }

        tbody::-webkit-scrollbar {
            width: 6px;
        }

        tbody::-webkit-scrollbar-thumb {
            background: #007bff;
            border-radius: 6px;
        }

        #setup, #history{
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin: 8px 0;
            font-size: 16px;
        }

        input[type="datetime-local"],
        #amount,
        #intervalTime,
        #startTime {
            width: 100%;
            max-width: 287.875px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        input[type="datetime-local"]{
            width: 200px;
            padding: 3px;
            margin: 0 0 0 10px;
        }

        .searchForm{
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        a {
            text-align: center;
            /* margin-top: 20px; */
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        #status {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 20px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            height: 3vh;
        }

        dialog {
            border: none;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 300px;
            text-align: center;
        }

        menu {
            display: flex;
            width: 100%;
            padding: 0 20px;
            flex-direction: row-reverse;
            margin-top: 20px;
        }

        button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button[value="cancel"] {
            background-color: #007bff;
            color: white;
        }
        input[value="Tìm kiếm"] {
            background-color: #007bff;
            color: white;
            width: 100%;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }


        button:hover {
            background-color: #0056b3;
            color: white;
        }

        /* dropdownmenu */
        .menu-btn {
            font-size: 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .menu-btn:hover {
            background-color: #218838;
        }

        .dropdown-content {
            display: none; /* Ẩn menu khi không cần */
            position: absolute;
            background-color: #fff;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            display: block;
            padding: 12px;
            text-decoration: none;
            color: black;
            font-size: 16px;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        .show {
            display: block; /* Hiển thị menu khi click */
        }

        /* Media query cho màn hình nhỏ */
        @media (max-width: 768px) {
            .menu-btn {
                width: 100%; /* Mở rộng nút menu trên màn hình nhỏ */
                padding: 3px;
                font-size: 16px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
    <script src="./script.js"></script>
</head>

<body>
    <div class="container">
        <div class="nav">
            <!-- <a href="index.html"><button style="padding: 3px">Quay lại</button></a> -->
            <div class="menu-container">
            <button class="menu-btn" onclick="toggleMenu(event)" style="padding: 3px">Menu</button>
            <div class="dropdown-content" id="dropdownMenu">
                <a href="feeding.php">Chức năng cho ăn</a>
                <a href="led.php">Điều khiển đèn LED</a>
                <a href="nhietdo.php">Điều chỉnh nhiệt độ</a>
            </div>
        </div>
            <button type="button" onclick="showDialog()" style="padding: 3px">Lịch sử</button>
        </div>




        <dialog id="confirmDialog">
            <h3>Lịch sử cho ăn</h3>
            <form id="searchForm">
                <div class="searchForm">
        <label for="start_time">Từ ngày:</label><input type="datetime-local" name="start_time" required></div>
        
        <div class="searchForm">
        <label for="end_time">Đến ngày:</label><input type="datetime-local" name="end_time" required></div>
        
        <input type="submit" value="Tìm kiếm">
    </form>

    <div id="message"></div>
                
        <form method="dialog" id="history">
                <table >
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Lượng thức ăn</th>
                        </tr>
                    </thead>
                    <tbody class="body" id="data-table">
                        <?php foreach ($result as $items): ?>
                            <tr>
                                <td><?php echo date("d/m/Y - H:i:s", strtotime($items['Time_choan'])); ?></td>
                                <td><?php echo $items['Amount']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <menu>
                    <button value="cancel">Quay lại</button>
                </menu>
            </form>
        </dialog>




        <h2>Chức năng cho ăn</h2>
        <p id="status">Status: Disconnected</p>

        <h3>Thiết lập thời gian cho ăn tự động</h3>
        <form method="post" id="setup">
            <label for="startTime">Thời gian bắt đầu:</label>
            <input name="start" type="time" id="startTime" step="1" required value="<?php foreach ($result1 as $items):
                echo date("H:i:s", strtotime($items['time_start']));
            endforeach; ?>">

            <label for="intervalTime">Thời gian lặp lại:</label>
            <input name="interval" type="time" id="intervalTime" step="1" required value="<?php foreach ($result1 as $items):
                echo date("H:i:s", strtotime($items['interval_time']));
            endforeach; ?>">

            <label for="amount">Lượng thức ăn (g):</label>
            <select id="amount" name="feeding-amount">
                <option value="1" <?php echo ($temp == 1) ? 'selected' : ''; ?>>small</option>
                <option value="2" <?php echo ($temp == 2) ? 'selected' : ''; ?>>medium</option>
                <option value="3" <?php echo ($temp == 3) ? 'selected' : ''; ?>>large</option>
            </select>
            <button type="button" onclick="luuThietLap()">Lưu thiết lập</button>
        </form>

        <div style="text-align: center;">
            <button onclick="Feed()">Cho ăn</button>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr("#intervalTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i:S",
            time_24hr: true,
            enableSeconds: true,
            disableMobile: true // Thêm dòng này
        });
        flatpickr("#startTime", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i:S",
            time_24hr: true,
            enableSeconds: true,
            disableMobile: true // Thêm dòng này
        });
    </script>
    <script>
        // Hàm mở/đóng menu
        function toggleMenu(event) {
            event.stopPropagation(); // Ngừng sự kiện bấm lan ra ngoài

            const menu = document.getElementById("dropdownMenu");
            menu.classList.toggle("show");
        }

        // Đóng menu khi bấm ra ngoài
        window.addEventListener("click", function(event) {
            const menu = document.getElementById("dropdownMenu");
            const menuBtn = document.querySelector(".menu-btn");
            
            // Kiểm tra nếu bấm vào ngoài nút hoặc menu, đóng menu
            if (!menu.contains(event.target) && !menuBtn.contains(event.target)) {
                menu.classList.remove("show");
            }
        });
    </script>
    <script>
        function showDialog() {
            const dialog = document.getElementById("confirmDialog");
            dialog.showModal(); // Hiển thị dialog
        }




        function luuThietLap() {
            startTime = document.getElementById('startTime').value;
            intervalTime = document.getElementById('intervalTime').value;
            amount = 'feed' + document.getElementById('amount').value;
            let data = {
                type: "feedAuto",
                value: [
                    startTime,
                    intervalTime,
                    amount
                ]
            }
            sendSettingTimeToServer(startTime, intervalTime, amount)
            sendMessage(JSON.stringify(data));
        }

        function Feed() {
            let data = {
                type: "feedManual",
                value: 'feed' + document.getElementById('amount').value
            }
            sendMessage(JSON.stringify(data));
        }

        function sendSettingTimeToServer(start, interval, amount_save) {
            fetch("process_setting_time.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    start: start,
                    interval: interval,
                    amount_save: amount_save
                })
            })
                .then(response => response.json())
                .then(result => {
                    console.log("Server response:", result);
                    if (result.success) {
                        // Reload the page or update the UI as needed
                        console.log("Data saved successfully!");
                    } else {
                        console.log("Failed to save data.");
                    }
                })
                .catch(error => console.error("Error:", error));
        }



    </script>
    <script>
        // Gửi yêu cầu AJAX khi form tìm kiếm được submit
        $('#searchForm').submit(function(e) {
            e.preventDefault(); // Ngừng hành động mặc định của form

            var startTime = $('input[name="start_time"]').val();
            var endTime = $('input[name="end_time"]').val();

            // Kiểm tra nếu cả 2 tham số thời gian đều có giá trị
            if (!startTime || !endTime) {
                alert('Vui lòng nhập cả 2 thời gian');
                return;
            }

            $.ajax({
                type: 'POST',
                url: '', // Gửi yêu cầu về chính trang này
                data: {
                    start_time: startTime,
                    end_time: endTime
                },
                success: function(response) {
                    var result = JSON.parse(response); // Chuyển JSON thành mảng PHP

                    // Kiểm tra nếu có kết quả
                    if (result.length > 0) {
                        $('#message').html(''); // Xóa thông báo lỗi nếu có
                        var tableContent = '';
                        result.forEach(function(item) {
                            tableContent += '<tr><td>' + item.Time_choan + '</td><td>' + item.Amount + '</td></tr>';
                        });
                        $('#data-table').html(tableContent); // Cập nhật lại bảng
                    } else {
                        $('#message').html('Không có kết quả tìm kiếm.'); // Thông báo không có kết quả
                    }
                },
                error: function() {
                    alert('Lỗi khi gửi yêu cầu.');
                }
            });
        });
    </script>
    
</body>

</html>