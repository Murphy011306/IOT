<?php
$conn = new PDO('mysql:host=localhost;dbname=BeCa', 'root', '');

$sql = "SELECT * from led where id = (SELECT max(id) from led)";
$stmt = $conn->prepare($sql);
$query = $stmt->execute();
$result = array();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $result[] = $row;
}

if (!empty($_POST['submit'])) {
    $color = $_POST['colorPicker'];
    $sql = "INSERT INTO led(color) VALUES('$color')";
    var_dump($sql);
    $stmt = $conn->prepare($sql);
    $query = $stmt->execute();
    if ($query) {
        header('location:led.php');
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Điều khiển đèn LED</title>
    <style>
        /* Cài đặt chung */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 500px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 20px;
        }

        label {
            font-size: 1.2em;
            padding: auto 0;

            /* margin-bottom: 10px;
            display: block; */
        }

        input[type="color"] {
            width: 75%;
            height: 40px;
        }

        .toggle-btn, input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            width: 100%;
            margin: 10px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover, input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Responsive cho điện thoại */
        @media screen and (max-width: 600px) {
            h2 {
                font-size: 1.2em;
            }

            label {
                font-size: 1em;
            }

            .toggle-btn {
                width: 100%;
                padding: 12px;
                font-size: 1em;
            }

            input[type="color"] {
                max-width: 70%;
                height: 40px;
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
            <h2>Điều khiển đèn LED</h2>
            <p id="status">Status: Disconnected</p>
            <div style="display: flex; justify-content: space-between;">
            <label for="colorPicker">Chọn màu:</label>
            <input type="color" id="colorPicker" name="colorPicker" style=""
                value="<?php foreach ($result as $items):
                    echo $items['color']; endforeach; ?>">
            </div>
            <button class="toggle-btn" onclick="BatDen()">Bật Đèn</button>
            <button class="toggle-btn" onclick="sendMessage('Ledoff')">Tắt Đèn</button>
        </div>
        <script>

            function BatDen() {
                let data = {
                    type: "Ledon",
                    value: document.getElementById('colorPicker').value
                }
                sendColorToServer(document.getElementById('colorPicker').value)
                sendMessage(JSON.stringify(data));


            }

            function sendColorToServer(color) {
                fetch("process_led.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ color: color })
                })
                    .then(response => response.json())
                    .then(result => {
                        console.log("Server response:", result);
                        if (result.success) {
                            console.log("Color saved successfully!");
                            // Reload or update the page if needed
                        } else {
                            console.log("Failed to save color.");
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