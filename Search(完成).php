<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>SearchSystem</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            th,td{
                font-family: 微軟正黑體;
                font-size: 15pt;
                padding: 10px;
                border: 1px solid black;
            }
            table{
                border-collapse: separate; /* 這是關鍵！ */
                border-spacing: 0;
                border: 1px solid black;
                border-radius: 20px; /* 圓角設定 */
                overflow: hidden; /* 讓子元素跟隨圓角 */
                text-align: center;
            }
            .checkbox-col {
                display: none;
                width: 100px;
                text-align: center;
            }
            body{
                background-color: aqua;
            }
        </style>
    </head>
    <body>
    <h1 style="text-align: center"><a href="Search.php" style="text-decoration: none">~~ Search System ~~</a></h1>
        <div class="text-center mb-3 w-50">
            <button class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#InsertModel">新增(Insert)</button>
            <button class="btn btn-primary fw-bold" id="editBtn" onclick="toggleEditMode()">編輯 (Editor)</button>
        </div>
        <div class="modal fade" id="InsertModel" tabindex="-1" aria-labelledby="InsertModalLabel" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="InsertModalLabel">新增資料</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                    </div>
                    <div class="modal-body">
                        <form action="InsertServer.php" method="post">
                            <div class="mb-3">
                                <label for="Name" class="form-label">姓名</label>
                                <input type="text" class="form-control" id="Name" name="NewName" placeholder="請輸入姓名">
                            </div>
                            <div class="mb-3">
                                <label for="Account" class="form-label">帳號</label>
                                <input type="text" class="form-control" id="Account" name="NewAccount" placeholder="請輸入帳號">
                            </div>
                            <div class="mb-3">
                                <label for="Password" class="form-label">密碼</label>
                                <input type="password" class="form-control" id="Password" name="NewPassword" placeholder="請輸入密碼">
                            </div>
                            <button type="submit" class="btn btn-success">送出</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <div class="modal fade" id="dataModal" tabindex="-1" aria-labelledby="dataModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="modalForm" action="UpdateServer.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="dataModalLabel">更新資料</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="關閉"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="modalId">
                        <div class="mb-3">
                            <label for="modalName" class="form-label">Name(名字)</label>
                            <input type="text" class="form-control" id="modalName" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="modalAccount" class="form-label">Account(帳號)</label>
                            <input type="text" class="form-control" id="modalAccount" name="account">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                        <button type="submit" class="btn btn-primary">送出</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <center>
            <?php
                // 資料庫連線資訊
                $host = "localhost";
                $user = "root";
                $passwd = "az22021660@";
                $cdn = "mysql:host=$host;dbname=login;charset=utf8";

                // 建立 PDO 連線
                $Connect = new PDO($cdn, $user, $passwd);

                // 分頁設定
                $items_per_page = 5; // 每頁顯示 5 筆
                $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $current_page = max($current_page, 1); // 不可小於 1
                $start_index = ($current_page - 1) * $items_per_page;

                // 取得總筆數
                $total_sql = "SELECT COUNT(*) FROM data";
                $total_items = $Connect->query($total_sql)->fetchColumn();
                $total_pages = ceil($total_items / $items_per_page);

                // 撈取當前頁的資料
                $sql = "SELECT * FROM data LIMIT :start, :limit";
                $stmt = $Connect->prepare($sql);
                $stmt->bindValue(':start', $start_index, PDO::PARAM_INT);
                $stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
                $stmt->execute();
                $Data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <!-- 顯示資料表 -->
            <table style="width: 1000px">
                <thead>
                <tr>
                    <th class='checkbox-col'><input type='checkbox' id="selectAll" class='form-check-input' onclick="toggleAll(this)"></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Account</th>
                    <th>Create Time</th>
                    <th>Time</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($Data as $DataValue): ?>
                    <tr data-id="<?= $DataValue['id'] ?>"
                        data-name="<?= htmlspecialchars($DataValue['name'], ENT_QUOTES) ?>"
                        data-account="<?= htmlspecialchars($DataValue['account'], ENT_QUOTES) ?>">
                        <td class='checkbox-col'><input type='checkbox' class='form-check-input row-checkbox'></td>
                        <td>
                            <a href="#" style="text-decoration: none" title="點擊更新資料" class="open-modal" data-id="<?= $DataValue["id"] ?>"><?= $DataValue["id"] ?></a>
                        </td>
                        <td><?= $DataValue["name"] ?></td>
                        <td><?= $DataValue["account"] ?></td>
                        <td><?= $DataValue["create_time"] ?></td>
                        <td><?= $DataValue["time"] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <!-- 顯示分頁按鈕 -->
            <?php if ($total_pages > 1): ?>
                <nav style="padding: 25px">
                    <ul class="pagination justify-content-center">
                        <!-- 上一頁 -->
                        <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $current_page - 1 ?>">上一頁</a>
                        </li>

                        <!-- 頁碼 -->
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- 下一頁 -->
                        <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $current_page + 1 ?>">下一頁</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </center>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            let isEditMode = false;

            let id,name,account;

            document.querySelectorAll('.open-modal').forEach(link => {
                link.addEventListener('click', function(e){
                    e.preventDefault();
                    const tr = this.closest('tr');
                    id = tr.dataset.id;
                    name = tr.dataset.name;
                    account = tr.dataset.account;
                    // 填入表單資料
                    document.getElementById('modalId').value = id;
                    document.getElementById('modalName').value = name; // 可根據需要填預設值
                    document.getElementById('modalAccount').value = account;

                    // 顯示 Modal
                    var modal = new bootstrap.Modal(document.getElementById('dataModal'));
                    modal.show();
                });
            });

            // 可在這裡加 submit 事件，AJAX 或表單送出
            document.getElementById('modalForm').addEventListener('submit', function(e){
                e.preventDefault();
                // 取得表單資料
                const formData = new FormData(this);
                console.log('送出的資料:', Object.fromEntries(formData.entries()));
                // 使用 fetch 傳到後端 (UpdateServer.php)
                fetch('UpdateServer.php', {
                    method: 'POST',
                    body: formData   // 直接送 FormData
                })
                    .then(response => response.json())  // 假設 PHP 回傳 JSON
                .then(result => {
                    console.log('後端回應:', result);

                    if (result.status === "success") {
                        alert("✅ " + result.message);
                        location.href="Search.php";
                        bootstrap.Modal.getInstance(document.getElementById('dataModal')).hide();
                    } else if (result.status === "warning") {
                        alert("⚠ " + result.message);
                    } else if (result.status === "error") {
                        alert("❌ " + result.message);
                    } else {
                        alert("❓ 未知回應");
                    }
                })
                .catch(error => {
                    console.error('錯誤:', error);
                });
            });
            function toggleEditMode() {
                const editBtn = document.getElementById('editBtn');
                const checkboxCols = document.querySelectorAll('.checkbox-col');

                if (!isEditMode) {
                    // 進入編輯模式
                    isEditMode = true;
                    editBtn.textContent = '刪除(Delete)';
                    checkboxCols.forEach(el => el.style.display = 'table-cell');
                } else {
                    // 執行刪除動作
                    const checked = document.querySelectorAll('.row-checkbox:checked');
                    if (checked.length === 0) {
                        alert("請先選取要刪除的資料列！");
                        return;
                    }

                    if (!confirm("確定要刪除選取的資料嗎？")) return;

                    // 收集要刪除的 ID
                    const idsToDelete = Array.from(checked).map(cb => {
                        return cb.closest('tr').getAttribute('data-id');
                    });

                    // 傳送到後端 (用 fetch)
                    fetch('delete.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ ids: idsToDelete })
                    })
                    .then(response => response.json())
                    .then(result => {
                        console.log(result);
                        // 從前端刪除列
                        location.href='Search.php';

                        // 結束編輯模式
                        isEditMode = false;
                        editBtn.textContent = '編輯(Editor)';
                        document.getElementById('selectAll').checked = false;
                        checkboxCols.forEach(el => el.style.display = 'none');
                    })
                    .catch(err => {
                        console.error("刪除失敗:", err);
                        alert("刪除時發生錯誤！");
                    });
                }
            }

            function toggleAll(source) {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(cb => cb.checked = source.checked);
            }
        </script>
    </body>
</html>