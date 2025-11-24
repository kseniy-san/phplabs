<?php
declare(strict_types=1);

function getTable($rows = 5, $cols = 5, $color = 'lightblue') {
    drawTable($rows, $cols, $color);
}

/**
 * Генерирует HTML таблицу умножения
 * 
 * @param int $cols Количество столбцов (по умолчанию 5)
 * @param int $rows Количество строк (по умолчанию 5)
 * @param string $color Цвет фона заголовков (по умолчанию 'yellow')
 * @return int Количество вызовов функции
 */
function drawTable(int $cols = 5, int $rows = 5, string $color = 'yellow'): int
{
    static $count = 0;
    $count++;
    
    $html = "<h3>Таблица {$rows}×{$cols} (цвет: $color)</h3>";
    $html .= '<table>';
    
    // Генерируем таблицу умножения
    for ($i = 1; $i <= $rows; $i++) {
        $html .= '<tr>';
        for ($j = 1; $j <= $cols; $j++) {
            // Первая строка и первый столбец - заголовки
            if ($i === 1 || $j === 1) {
                $html .= "<th style='background-color: $color;'>" . ($i * $j) . "</th>";
            } else {
                $html .= "<td>" . ($i * $j) . "</td>";
            }
        }
        $html .= '</tr>';
    }
    
    $html .= '</table>';
    echo $html;
    
    return $count;
}

/**
 * Генерирует HTML меню из массива
 * 
 * @param array $menu Массив пунктов меню
 * @param bool $vertical Флаг вертикального отображения (true - вертикально, false - горизонтально)
 * @return string HTML код меню
 */
function getMenu(array $menu, bool $vertical = true): string
{
    $cssClass = $vertical ? 'menu vertical' : 'menu horizontal';
    $html = "<ul class=\"$cssClass\">";
    
    foreach ($menu as $menuItem) {
        $html .= "<li><a href='{$menuItem['href']}'>{$menuItem['link']}</a></li>";
    }
    
    $html .= '</ul>';
    return $html;
}
?>