<?php
$NewName = $_POST["NewName"];
$NewAccount = $_POST["NewAccount"];
$NewPassword = $_POST["NewPassword"];

$host = "localhost";
$user = "root";
$password = "az22021660@";
$dsn = "mysql:host=$host;dbname=login;charset=utf8";
$Conn = new PDO($dsn, $user, $password);
$Conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 檢查帳號是否已存在
$SQL = "SELECT * FROM data WHERE account = :account";
$Stmt = $Conn->prepare($SQL);
$Stmt->execute([':account' => $NewAccount]);
$Count = $Stmt->rowCount();

if ($Count == 0) {
    // 建議使用 password_hash 儲存密碼
    $HashedPassword = password_hash($NewPassword, PASSWORD_DEFAULT);

    $InsertSQL = "INSERT INTO data(name, account, password) VALUES (:name, :account, :password)";
    $InsertStmt = $Conn->prepare($InsertSQL);
    $InsertStmt->execute([
        ':name' => $NewName,
        ':account' => $NewAccount,
        ':password' => $HashedPassword
    ]);
    header("Location: Search.php");
    exit();
} else {
    echo "<script>
            alert('新增失敗，帳號已存在 !!');
            location.href='Search.php';
          </script>";
    exit();
}