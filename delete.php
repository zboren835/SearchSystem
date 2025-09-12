<?php
$raw = file_get_contents('php://input');  // ← 不要改成 URL！
$data = json_decode($raw, true);          // 解 JSON 為 PHP 陣列

if (!isset($data['ids']) || !is_array($data['ids'])) {
    http_response_code(400);
    echo json_encode(['error' => '缺少或格式錯誤的 IDs']);
    exit;
}

$ids = $data['ids'];

// ✅ 建立 PDO 連線
$pdo = new PDO("mysql:host=localhost;dbname=login;charset=utf8mb4", "root", "az22021660@", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// ✅ 安全刪除（防注入）
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$sql = "DELETE FROM data WHERE id IN ($placeholders)";
$stmt = $pdo->prepare($sql);
$stmt->execute($ids);

// ✅ 回傳結果給前端
echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);