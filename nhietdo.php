<?php
$conn = new PDO('mysql:host=localhost;dbname=BeCa', 'root', '');

    $sql = "SELECT * from nhietdo where id = (SELECT max(id) from nhietdo)";
    $stmt = $conn->prepare($sql);
    $query = $stmt->execute();
    $result = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $result[]= $row;
      }


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chức năng điều khiển lò sưởi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            max-width: 500px;
            margin: 20px auto;
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
        }
        h3 {
            text-align: center;
            color: #007bff;
            font-size: 20px;
        }
        .btn-container {
            display: flex;
            flex-wrap: wrap; /* Điều chỉnh các nút để xuống dòng khi không đủ không gian */
            justify-content: center;
            gap: 10px;
            margin: 15px 0;
        }
        .btn-container input {
            font-size: 16px;
            padding: 8px;
            border: 1px solid #007bff;
            border-radius: 5px;
            background-color: #e7f1ff;
            flex: 1; Cho phép nút chiếm không gian còn lại
            width: 320px; /* Giới hạn chiều rộng */
            margin: 0 auto;
        }
        .btn-container button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            /* margin: 0 10px; */
            font-size: 14px;
            cursor: pointer;
            flex: 1; 
            transition: background-color 0.3s;
            width: 100%;
            max-width: 80px; 
        }
        .btn-container button:hover {
            background-color: #0056b3;
        }
        .current-temperature  {
            font-size: 16px;
            text-align: center;
            margin: 15px 0;
            padding: 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            background-color: #e7f1ff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tbody tr:hover {
            background-color: #d1ecf1;
        }
        a {
            display: block;
            text-align: center;
            /* margin-top: 20px; */
            color: #007bff;
            text-decoration: none;
            font-size: 16px;
        }
        a:hover {
            text-decoration: underline;
        }
        @media screen and (max-width: 480px) {
            h2 {
                font-size: 20px;
            }
            h3 {
                font-size: 18px;
            }
            .btn-container input {
                font-size: 14px;
                max-width: 60vw;
                padding: 10px;
            }.btn-container button{
                font-size: 12px;
                max-width: 190px;    
            }
            .btn-container  {
                display: flex;
            flex-direction: column;
            align-items: center;
            }
            .current-temperature {
                font-size: 14px;
                max-width: 60vw;
                margin: 15px auto;
            }
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
            display: none;
            /* Ẩn menu khi không cần */
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
            display: block;
            /* Hiển thị menu khi click */
        }

        /* Media query cho màn hình nhỏ */
        @media (max-width: 768px) {
            .menu-btn {
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
            <div class="menu-container">
                <button class="menu-btn" onclick="toggleMenu(event)" style="padding: 3px">Menu</button>
                <div class="dropdown-content" id="dropdownMenu">
                    <a href="feeding.php">Chức năng cho ăn</a>
                    <a href="led.php">Điều khiển đèn LED</a>
                    <a href="nhietdo.php">Điều chỉnh nhiệt độ</a>
                </div>
            </div>
        </div>
        <h2>Chức năng điều khiển lò sưởi</h2>
        <div style="width: 100%; text-align: center;">
            <p id="status" style="margin: 0 auto;">Status: Disconnected</p>
        </div>
        <div class="current-temperature" id="current-temperature">Nhiệt độ hiện tại: -- °C</div>
        <h3>Cài đặt nhiệt độ sưởi</h3>
        <div class="btn-container">
            <input type="num" id="temp" name="temp" value="<?php foreach ($result as $items): echo $items['nhietdo']; endforeach;?>">
            <button id="auto-mode" onclick="tempAuto()">Bật sưởi</button>
            <button id="off-mode" onclick="tempOff()">Tắt sưởi</button>
        </div>
        <p id="log"></p>
    </div>

    <script>
        

        function tempAuto(){
            let data = {
                type: "tempAuto",
                value: document.getElementById('temp').value
            }
            sendTemperatureToServer(document.getElementById('temp').value)
            sendMessage(JSON.stringify(data));
        }

        function tempOff(){
            let data = {
                type: "tempAuto",
                value: '0'
            }
            sendMessage(JSON.stringify(data));
        }
        function sendTemperatureToServer(temp) {
    fetch("process_temperature.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ temperature: temp })
    })
    .then(response => response.json())
    .then(result => {
        console.log("Server response:", result);
        if (result.success) {
            console.log("Temperature saved successfully!");
            // Reload or update the page if needed
        } else {
            console.log("Failed to save temperature.");
        }
    })
    .catch(error => console.error("Error:", error));
}
    </script>
        <script>
            // Hàm mở/đóng menu
            function toggleMenu(event) {
                event.stopPropagation(); // Ngừng sự kiện bấm lan ra ngoài

                const menu = document.getElementById("dropdownMenu");
                menu.classList.toggle("show");
            }

            // Đóng menu khi bấm ra ngoài
            window.addEventListener("click", function (event) {
                const menu = document.getElementById("dropdownMenu");
                const menuBtn = document.querySelector(".menu-btn");

                // Kiểm tra nếu bấm vào ngoài nút hoặc menu, đóng menu
                if (!menu.contains(event.target) && !menuBtn.contains(event.target)) {
                    menu.classList.remove("show");
                }
            });
        </script>
</body>
</html>
