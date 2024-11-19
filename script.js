// const ipAddress = "ws://192.168.137.220:81"; // địa chỉ IP của máy Lộc
const ipAddress = "ws://192.168.7.14:8080"; // Thay <ESP8266_IP> bằng địa chỉ IP của ESP8266
let websocket;
var dataOutput = "";

function initWebSocket() {
    websocket = new WebSocket(ipAddress);

    websocket.onopen = () => {
        websocket.send("HELLO_FROM_WEB_CLIENT");
        document.getElementById("status").innerText = "Status: Connected";
        console.log("Connected to WebSocket Server");
    };

    websocket.onclose = () => {
        document.getElementById("status").innerText = "Status: Disconnected";
        console.log("Disconnected from WebSocket Server");
        setTimeout(initWebSocket, 10000); // Cố gắng kết nối lại sau 10 giây
    };

    websocket.onmessage = (event) => {
        console.log("Message from server: ", event.data);
        receiveData(event.data);
    };
}

function receiveData(jsonData){
    let data = JSON.parse(jsonData);
    if(data.type == "tempNow"){
    document.getElementById("current-temperature").innerText = "Nhiệt độ hiện tại: " + parseFloat(data.value).toFixed(1) + " °C";
    }else if(data.type == "Feed"){
        sendToServer(data.value, updateTable)
    }
}

function sendToServer(data, callback) {
    fetch("process_data.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ feedingAmount: data })
    })
    .then(response => response.json())
    .then(result => {
        console.log("Server response:", result);
        if (result.status === "success") {
            callback(); // Gọi hàm callback để cập nhật bảng
        }
    })
    .catch(error => console.error("Error:", error));
}

function updateTable() {
    fetch("fetch_data.php")
    .then(response => response.json())
    .then(data => {
        const tbody = document.querySelector(".body");
        tbody.innerHTML = ""; // Xóa các hàng hiện tại trong bảng

        data.forEach(item => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td>${item.Time_choan}</td>
                <td>${item.Amount}</td>
            `;
            tbody.appendChild(row);
        });
    })
    .catch(error => console.error("Error fetching table data:", error));
}

function sendMessage(message) {
    // console.log("Sent: " + message);
    if (websocket.readyState === WebSocket.OPEN) {
        websocket.send(message);
        console.log("Sent: " + message);
    } else {
        console.log("WebSocket is not open. Unable to send message.");
    }
}
window.onload = initWebSocket;