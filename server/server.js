const WebSocket = require('ws');

// Tạo server WebSocket trên cổng 8080
const wss = new WebSocket.Server({ port: 8080 });

// Lưu trữ các kết nối của NodeMCU và các client
let nodeMCUSocket = null;
let webClientSocket = null;

wss.on('connection', (ws) => {
    console.log('Client mới đã kết nối.');
  
    // Xử lý tin nhắn từ client
    ws.on('message', (msg) => {
      let message = msg.toString('utf-8');
      console.log('Tin nhắn nhận được:', message);
  
      try {
        let parsedMessage = message;
        // Parse tin nhắn JSON
        if(isValidJSON(message)){
            parsedMessage = JSON.parse(message);
        }
        // Kiểm tra loại tin nhắn (type)
        if (parsedMessage.type === "tempNow") {
          // Nếu type là tempNow, gửi đến Web Client
          if (webClientSocket) {
            console.log('Gửi tới Web Client: ', message);
            webClientSocket.send(message); // Gửi tin nhắn tới Web Client
          } else {
            console.log('Không có Web Client kết nối.');
          }
        } else {
          // Nếu không phải type tempNow, gửi tới NodeMCU
          if (nodeMCUSocket) {
            console.log('Gửi lệnh tới NodeMCU:', message);
            nodeMCUSocket.send(message); // Gửi tin nhắn tới NodeMCU
          } else {
            console.log('Không có NodeMCU kết nối.');
          }
        }
  
      } catch (err) {
        console.log('Lỗi parse JSON:', err);
      }
  
      // Nếu NodeMCU kết nối, lưu socket của nó
      if (message === "HELLO_FROM_NODEMCU") {
        nodeMCUSocket = ws;
        console.log('NodeMCU đã kết nối.');
        ws.send("CONNECTED_TO_SERVER");
      }
  
      // Lưu socket của Web client khi kết nối
      if (message === "HELLO_FROM_WEB_CLIENT") {
        webClientSocket = ws;
        console.log('Web Client đã kết nối.');
        ws.send("CONNECTED_TO_SERVER");
      }
    });
  
    // Khi client ngắt kết nối
    ws.on('close', () => {
      if (ws === nodeMCUSocket) {
        nodeMCUSocket = null; // Xóa kết nối NodeMCU
        console.log('NodeMCU ngắt kết nối');
      }
      if (ws === webClientSocket) {
        webClientSocket = null; // Xóa kết nối Web Client
        console.log('Web Client ngắt kết nối');
      }
    });
  });

console.log('WebSocket Server đang chạy trên cổng 8080.');

function isValidJSON(jsonString) {
    try {
        JSON.parse(jsonString); // Thử chuyển đổi chuỗi thành đối tượng
        return true;  // Nếu không có lỗi, JSON hợp lệ
    } catch (e) {
        return false;  // Nếu có lỗi, JSON không hợp lệ
    }
}

