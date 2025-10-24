<?php
/*
	ЗАДАНИЕ 1
	- Опишите функцию getTable()
	- Задайте для функции три аргумента: $cols, $rows, $color

	ЗАДАНИЕ 2
	- Откройте файл table.php
	- Скопируйте код, который отрисовывает таблицу умножения
	- Вставьте скопированный код в тело функции getTable()
	- Измените код таким образом, чтобы таблица отрисовывалась в зависимости от входящих параметров $cols, $rows и $color
	- Добавьте в объявлние функции описание типов аргументов
	*/

declare(strict_types=1);

/**
 * Функция построения таблицы умножения
 * 
 * @param int $cols Количество столбцов 
 * @param int $rows Количество строк 
 * @param string $color Цвет фона заголовков 
 * @return int Количество вызовов функции
 */
function getTable(int $cols = 10, int $rows = 10, string $color = 'yellow'): int
{
	static $count = 0;
	$count++;

	echo "<table>";

	echo "<tr>";
	for ($j = 1; $j <= $cols; $j++) {
		echo "<th style='background-color: $color; text-align: center; font-weight: bold;'>" . $j . "</th>";
	}
	echo "</tr>";

	for ($i = 2; $i <= $rows; $i++) {
		echo "<tr>";
		echo "<th style='background-color: $color; text-align: center; font-weight: bold;'>" . $i . "</th>";

		for ($j = 2; $j <= $cols; $j++) {
			echo "<td>" . ($i * $j) . "</td>";
		}
		echo "</tr>";
	}

	echo "</table>";

	return $count;
}
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Таблица умножения</title>
	<style>
		table {
			border: 2px solid black;
			border-collapse: collapse;
		}

		th,
		td {
			padding: 10px;
			border: 1px solid black;
		}

		th {
			background-color: yellow;
		}
	</style>
</head>

<body>
	<h1>Таблица умножения</h1>
	<?php

		echo "<hr>";

	/*
	ЗАДАНИЕ 3
	- Отрисуйте таблицу умножения вызывая функцию getTable() с различными параметрами
	- Создайте локальную статическую переменную $count для подсчёта числа вызовов функции getTable()
	- Функция getTable() должна возвращать значение переменной $count
	*/
	/*
	ЗАДАНИЕ 4
	- Измените входящие параметры функции getTable() на параметры по умолчанию
	- Добавьте описание функции getTable() с помощью стандарта PHPDoc https://ru.wikipedia.org/wiki/PHPDoc
	*/
	/*
	ЗАДАНИЕ 5
	- Отрисуйте таблицу умножения вызывая функцию getTable() без параметров
	- Отрисуйте таблицу умножения вызывая функцию getTable() с одним параметром
	- Отрисуйте таблицу умножения вызывая функцию getTable() с двумя параметрами
	- Используя статическую переменную $count выведите общее число вызовов функции getTable()
	*/

	echo "<h2>Таблица без параметров:</h2>";
	$count1 = getTable();

	echo "<hr>";

	echo "<h2>Таблица с одним параметром (cols=5):</h2>";
	$count2 = getTable(5);

	echo "<hr>";

	echo "<h2>Таблица с двумя параметрами (cols=3, rows=4):</h2>";
	$count3 = getTable(3, 4);

	echo "<hr>";

	echo "<h2>Таблица с тремя параметрами (cols=6, rows=6, color='lightgreen'):</h2>";
	$count4 = getTable(6, 6, 'lightgreen');

	echo "<hr>";

	echo "<h2>Общее количество вызовов функции: " . $count4 . "</h2>";
	?>
</body>

</html>