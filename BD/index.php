<?php

$db_host = 'localhost:3307';
$db_user = 'root';
$db_pass = '45_P67';
$db_name = 'Travel_agency';

function db_connect() {
    global $db_host, $db_user, $db_pass, $db_name;
    
    list($host, $port) = explode(':', $db_host);
    
    try {
        $conn = new mysqli($host, $db_user, $db_pass, $db_name, $port);
        
        if ($conn->connect_error) {
            return [false, "Ошибка подключения: " . $conn->connect_error];
        }
        
        $conn->set_charset("utf8mb4");
        return [true, $conn];
        
    } catch (Exception $e) {
        return [false, "Ошибка: " . $e->getMessage()];
    }
}

function execute_query($sql) {
    $result = db_connect();
    
    if (!$result[0]) {
        return ['error' => $result[1]];
    }
    
    $conn = $result[1];
    $sql = trim($sql);
    
    if (empty($sql)) {
        $conn->close();
        return ['error' => 'Пустой SQL-запрос'];
    }
    
    $query_type = strtoupper(explode(' ', $sql)[0]);
    
    $query_result = $conn->query($sql);
    
    if ($query_result === false) {
        $error = $conn->error;
        $conn->close();
        return ['error' => "Ошибка SQL: $error"];
    }
    
    if (in_array($query_type, ['SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN'])) {
        $data = [];
        $columns = [];
        
        if ($query_result->num_rows > 0) {
            while ($field = $query_result->fetch_field()) {
                $columns[] = $field->name;
            }
            
            while ($row = $query_result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        
        $conn->close();
        
        return [
            'type' => 'select',
            'data' => $data,
            'columns' => $columns,
            'rowcount' => count($data)
        ];
        
    } else {
        $affected_rows = $conn->affected_rows;
        $conn->close();
        
        return [
            'type' => 'update',
            'message' => "Выполнено. Затронуто строк: $affected_rows",
            'rowcount' => $affected_rows
        ];
    }
}

function check_connection() {
    $result = db_connect();
    
    if ($result[0]) {
        $conn = $result[1];
        
        $db_info = $conn->query("SELECT DATABASE() as db")->fetch_assoc();
        $conn->close();
        
        return ['success' => true, 'database' => $db_info['db']];
    } else {
        return ['success' => false, 'error' => $result[1]];
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'check_connection') {
    header('Content-Type: application/json');
    echo json_encode(check_connection(), JSON_UNESCAPED_UNICODE);
    exit;
}

$result = null;
$error = null;
$query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sql_query'])) {
    $query = $_POST['sql_query'];
    $result = execute_query($query);
    
    if (isset($result['error'])) {
        $error = $result['error'];
        $result = null;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>SQL Editor</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            padding: 20px; 
            background: #fff; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
        }
        h1 { 
            color: #333; 
            margin-bottom: 20px; 
        }
        .status { 
            margin-bottom: 20px; 
            padding: 10px; 
            background: #f5f5f5; 
            border-radius: 4px;
        }
        .status-dot { 
            display: inline-block;
            width: 10px; 
            height: 10px; 
            border-radius: 50%; 
            background: #ccc; 
            margin-right: 5px;
        }
        .status-dot.connected { background: #0c0; }
        .form-group { margin-bottom: 20px; }
        textarea { 
            width: 100%; 
            min-height: 100px; 
            padding: 10px; 
            border: 1px solid #ccc; 
            font-family: monospace; 
            font-size: 14px; 
            border-radius: 4px;
        }
        button { 
            background: #0066cc; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            cursor: pointer; 
            border-radius: 4px;
        }
        .clear-btn { 
            background: #999; 
            margin-left: 10px; 
        }
        .message { 
            padding: 10px; 
            margin: 20px 0; 
            border-radius: 4px;
        }
        .error { 
            background: #ffd6d6; 
            color: #900; 
        }
        .success { 
            background: #d6ffd6; 
            color: #090; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th { 
            background: #eee; 
            padding: 8px; 
            text-align: left; 
            border-bottom: 1px solid #ccc; 
        }
        td { 
            padding: 8px; 
            border-bottom: 1px solid #eee; 
        }
        .rowcount { 
            margin: 10px 0; 
            color: #666; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SQL-запросы к базе данных "Тур агентство"</h1>
        
        <div class="status">
            <span class="status-dot" id="status-dot"></span>
            <span id="status-text">Проверка подключения...</span>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="sql_query">Введите SQL-запрос:</label>
                <textarea id="sql_query" name="sql_query" placeholder="Введите SQL запрос..." rows="5"><?php echo htmlspecialchars($query); ?></textarea>
            </div>
            
            <div>
                <button type="submit">Выполнить</button>
                <button type="button" class="clear-btn" onclick="document.getElementById('sql_query').value=''">Очистить</button>
            </div>
        </form>
        
        <?php if ($error): ?>
            <div class="message error">
                <strong>Ошибка:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($result): ?>
            <?php if ($result['type'] === 'select'): ?>
                <div class="rowcount">
                    Найдено строк: <?php echo $result['rowcount']; ?>
                </div>
                
                <?php if ($result['rowcount'] > 0): ?>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <?php foreach ($result['columns'] as $column): ?>
                                        <th><?php echo htmlspecialchars($column); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($result['data'] as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $value): ?>
                                            <td><?php echo htmlspecialchars($value ?? 'NULL'); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="message">Запрос выполнен, но данные не найдены</div>
                <?php endif; ?>
                
            <?php else: ?>
                <div class="message success">
                    <?php echo $result['message']; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            checkConnection();
        });
        
        async function checkConnection() {
            const statusDot = document.getElementById('status-dot');
            const statusText = document.getElementById('status-text');
            
            try {
                const response = await fetch('?action=check_connection');
                const data = await response.json();
                
                if (data.success) {
                    statusDot.className = 'status-dot connected';
                    statusText.textContent = `Подключено к базе: ${data.database}`;
                } else {
                    statusDot.className = 'status-dot';
                    statusText.textContent = `Ошибка: ${data.error}`;
                }
            } catch (error) {
                statusDot.className = 'status-dot';
                statusText.textContent = 'Ошибка сети';
            }
        }
    </script>
</body>
</html>