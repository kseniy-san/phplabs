<?php
// Устанавливаем значения по умолчанию
$default_cols = 5;
$default_rows = 7;
$default_color = '#90ee90';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $cols = abs((int) $_POST['cols']);
    $rows = abs((int) $_POST['rows']);
    $color = trim(strip_tags($_POST['color']));
}

$cols = ($cols) ? $cols : $default_cols;
$rows = ($rows) ? $rows : $default_rows;
$color = ($color) ? $color : $default_color;
?>
<!-- Область основного контента -->
<h3>Таблица умножения</h3>
<form action='<?=$_SERVER['REQUEST_URI']?>' method='POST'>
    <label>Количество колонок: </label>
    <br>
    <input name='cols' type='text' value='<?=isset($_POST['cols']) ? htmlspecialchars($_POST['cols']) : $default_cols?>'>
    <br>
    <label>Количество строк: </label>
    <br>
    <input name='rows' type='text' value='<?=isset($_POST['rows']) ? htmlspecialchars($_POST['rows']) : $default_rows?>'>
    <br>
    <label>Цвет: </label>
    <br>
    <input name='color' type='color' value='<?=isset($_POST['color']) ? htmlspecialchars($_POST['color']) : $default_color?>'>
    <br>
    <small>Или выберите из списка: </small>
    <select onchange="document.querySelector('input[name=color]').value = this.value">
        <option value="#90ee90">Светло-зеленый</option>
        <option value="#ff0000">Красный</option>
        <option value="#00ff00">Зеленый</option>
        <option value="#0000ff">Синий</option>
        <option value="#ffff00">Желтый</option>
        <option value="#ff00ff">Пурпурный</option>
        <option value="#00ffff">Бирюзовый</option>
    </select>
    <br>
    <br>
    <input type='submit' value='Создать'>
</form>
<br>
<!-- Таблица -->
<?php 
// Вызываем функцию getTable() с параметрами
getTable($rows, $cols, $color);
?>
<!-- Таблица -->
<!-- Область основного контента -->