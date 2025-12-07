<?php
// Конфигурация подключения к БД
$db_host = 'localhost:3307';
$db_user = 'root';
$db_pass = '45_P67';
$db_name = 'Travel_agency';

// Функция подключения к БД
function db_connect() {
    global $db_host, $db_user, $db_pass, $db_name;
    
    // Разделяем хост и порт
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

// Функция выполнения SQL-запроса
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
    
    // Определяем тип запроса
    $query_type = strtoupper(explode(' ', $sql)[0]);
    
    // Выполняем запрос
    $query_result = $conn->query($sql);
    
    if ($query_result === false) {
        $error = $conn->error;
        $conn->close();
        return ['error' => "Ошибка SQL: $error"];
    }
    
    // Обрабатываем результат
    if (in_array($query_type, ['SELECT', 'SHOW', 'DESCRIBE', 'EXPLAIN'])) {
        // Запрос с возвратом данных
        $data = [];
        $columns = [];
        
        if ($query_result->num_rows > 0) {
            // Получаем названия колонок
            while ($field = $query_result->fetch_field()) {
                $columns[] = $field->name;
            }
            
            // Получаем данные
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
        // Запрос без возврата данных
        $affected_rows = $conn->affected_rows;
        $conn->close();
        
        return [
            'type' => 'update',
            'message' => "Выполнено. Затронуто строк: $affected_rows",
            'rowcount' => $affected_rows
        ];
    }
}

// Проверка соединения
function check_connection() {
    $result = db_connect();
    
    if ($result[0]) {
        $conn = $result[1];
        
        // Получаем информацию о БД
        $db_info = $conn->query("SELECT DATABASE() as db")->fetch_assoc();
        $conn->close();
        
        return ['success' => true, 'database' => $db_info['db']];
    } else {
        return ['success' => false, 'error' => $result[1]];
    }
}

// Обработка AJAX запроса для проверки соединения
if (isset($_GET['action']) && $_GET['action'] == 'check_connection') {
    header('Content-Type: application/json');
    echo json_encode(check_connection(), JSON_UNESCAPED_UNICODE);
    exit;
}

// Обработка POST запроса (выполнение SQL)
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
    <title>SQL Editor - Тур Агентство</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: #f0f2f5; 
            padding: 20px; 
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
            padding: 20px; 
        }
        h1 { 
            color: #333; 
            margin-bottom: 20px; 
            padding-bottom: 10px; 
            border-bottom: 2px solid #eaeaea; 
        }
        .status { 
            background: #f8f9fa; 
            padding: 10px 15px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .status-dot { 
            width: 10px; 
            height: 10px; 
            border-radius: 50%; 
            background: #dc3545; 
        }
        .status-dot.connected { background: #28a745; }
        .form-group { margin-bottom: 20px; }
        label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: bold; 
            color: #555; 
        }
        textarea { 
            width: 100%; 
            min-height: 100px; 
            padding: 10px; 
            border: 1px solid #ddd; 
            border-radius: 4px; 
            font-family: 'Courier New', monospace; 
            font-size: 14px; 
            resize: vertical; 
        }
        button { 
            background: #007bff; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 4px; 
            cursor: pointer; 
            font-size: 16px; 
        }
        button:hover { background: #0056b3; }
        .clear-btn { 
            background: #6c757d; 
            margin-left: 10px; 
        }
        .clear-btn:hover { background: #545b62; }
        .message { 
            padding: 15px; 
            margin: 20px 0; 
            border-radius: 4px; 
        }
        .error { 
            background: #f8d7da; 
            color: #721c24; 
            border: 1px solid #f5c6cb; 
        }
        .success { 
            background: #d4edda; 
            color: #155724; 
            border: 1px solid #c3e6cb; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
            font-size: 14px; 
        }
        th { 
            background: #f8f9fa; 
            padding: 12px; 
            text-align: left; 
            border-bottom: 2px solid #dee2e6; 
            font-weight: bold; 
        }
        td { 
            padding: 10px 12px; 
            border-bottom: 1px solid #dee2e6; 
        }
        tr:hover { background: #f5f5f5; }
        .rowcount { 
            margin: 10px 0; 
            color: #666; 
            font-size: 14px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SQL Редактор - База данных "Тур Агентство"</h1>
        
        <div class="status">
            <div class="status-dot" id="status-dot"></div>
            <span id="status-text">Проверка подключения...</span>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="sql_query">SQL Запрос:</label>
                <textarea id="sql_query" name="sql_query" placeholder="Введите SQL запрос..."><?php echo htmlspecialchars($query); ?></textarea>
            </div>
            
            <div>
                <button type="submit">Выполнить</button>
                <button type="button" class="clear-btn" onclick="document.getElementById('sql_query').value = ''">Очистить</button>
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
        // Проверка подключения при загрузке страницы
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
                    statusText.textContent = `✓ Подключено к базе: ${data.database}`;
                } else {
                    statusDot.className = 'status-dot';
                    statusText.textContent = `✗ Ошибка: ${data.error}`;
                }
            } catch (error) {
                statusDot.className = 'status-dot';
                statusText.textContent = '✗ Ошибка сети';
            }
        }
    </script>
</body>
</html>