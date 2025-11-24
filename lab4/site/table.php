<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $cols = abs((int) $_POST['cols']);
    $rows = abs((int) $_POST['rows']);
    $color = trim(strip_tags($_POST['color']));
}
$cols = ($cols) ? $cols : 10;
$rows = ($rows) ? $rows : 10;
$color = ($color) ? $color : '#ffff00';
?>

    <!-- Область основного контента -->
    <form action=''>
      <label>Количество колонок: </label>
      <br>
      <input name='cols' type='text' value='<?php echo isset($_GET['cols']) ? $_GET['cols'] : ''; ?>'>
      <br>
      <label>Количество строк: </label>
      <br>
      <input name='rows' type='text' value='<?php echo isset($_GET['rows']) ? $_GET['rows'] : ''; ?>'>
      <br>
      <label>Цвет: </label>
      <br>
      <input name='color' type='color' value='<?php echo isset($_GET['color']) ? $_GET['color'] : '#ff0000'; ?>' list="listColors">
      <datalist id="listColors">
        <option>#ff0000</option>
        <option>#00ff00</option>
        <option>#0000ff</option>
      </datalist>
      <br>
      <br>
      <input type='submit' value='Создать'>
    </form>
    <br>
    <!-- Таблица -->
   <?php 
    $cols = isset($_GET['cols']) ? (int)$_GET['cols'] : 6;
    $rows = isset($_GET['rows']) ? (int)$_GET['rows'] : 6;
    $color = isset($_GET['color']) ? $_GET['color'] : 'lightgreen';
    
    getTable($rows, $cols, $color);
    ?>
    <!-- Таблица -->
    <!-- Область основного контента -->
     