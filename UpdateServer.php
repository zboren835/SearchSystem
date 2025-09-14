<?php
header('Content-Type: application/json; charset=utf-8');

// DB 連線設定 (依實際狀況修改)
$host = "localhost";
$dbname = "login";
$user = "root";
$pass = "az22021660@";

try {
    // 建立 PDO 連線
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 取得表單資料
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $account = isset($_POST['account']) ? $_POST['account'] : null;

        if ($id && $name && $account) {
            // 先檢查 ID 是否存在
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM data WHERE id = :id");
            $checkStmt->execute([':id' => $id]);
            $exists = $checkStmt->fetchColumn();

            if (!$exists) {
                echo json_encode([
                    "status" => "error",
                    "message" => "找不到該 ID"
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // 執行 UPDATE
            $sql = "UPDATE data SET name = :name, account = :account WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':account' => $account,
                ':id' => $id
            ]);

            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    "status" => "success",
                    "message" => "更新成功",
                    "data" => [
                        "id" => $id,
                        "name" => $name,
                        "account" => $account
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "status" => "warning",
                    "message" => "資料未變更"
                ], JSON_UNESCAPED_UNICODE);
            }
        }
    } else {
        http_response_code(405); // Method Not Allowed
        echo json_encode([
            "status" => "error",
            "message" => "只接受 POST 請求"
        ], JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "status" => "error",
        "message" => "資料庫連線或操作失敗",
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}