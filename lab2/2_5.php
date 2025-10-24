<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Результат функции swap</title>
</head>

<body>
	<h1>Результат функции swap</h1>
	<?php
	$swap = function (&$x, &$y): void {
		$dop = $x;
		$x = $y;
		$y = $dop;
	};

	$a = 5;
	$b = 8;

	/**
	 * Функция swap() меняет местами аргументы
	 * 
	 * @param mixed $a Первая переменная (передаётся по ссылке)
	 * @param mixed $b Вторая переменная (передаётся по ссылке)
	 */

	$swap($a, $b);

	echo var_dump(5 === $b) . "<br>";
	echo var_dump(8 === $a);
	?>
</body>

</html>