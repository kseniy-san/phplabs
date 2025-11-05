<?php
declare(strict_types=1);

function initializeDateTimeVariables(): array
{
    $now = time();
    
    $currentYear = (int)date('Y');
    $birthday = mktime(0, 0, 0, 21, 02, $currentYear);
    
    if ($birthday < $now) {
        $birthday = mktime(0, 0, 0, 21, 02, date('Y') + 1);
    }
    
    $currentDate = getdate();
    $hour = $currentDate['hours'];
    
    return [
        'now' => $now,
        'birthday' => $birthday,
        'hour' => $hour
    ];
}

function getWelcomeMessage(int $hour): string
{
    if ($hour >= 6 && $hour < 12) {
        return 'Доброе утро';
    } elseif ($hour >= 12 && $hour < 18) {
        return 'Добрый день';
    } elseif ($hour >= 18 && $hour < 23) {
        return 'Добрый вечер';
    } else {
        return 'Доброй ночи';
    }
}

function formatRussianDate(int $timestamp): string
{
    $months = [
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
    ];
    
    $daysOfWeek = [
        'воскресенье', 'понедельник', 'вторник', 'среда',
        'четверг', 'пятница', 'суббота'
    ];
    
    $date = getdate($timestamp);
    $day = $date['mday'];
    $month = $months[$date['mon']];
    $year = $date['year'];
    $dayOfWeek = $daysOfWeek[$date['wday']];
    $time = date('H:i:s', $timestamp);
    
    return "Сегодня $day $month $year года, $dayOfWeek $time";
}

function getTimeUntilBirthday(int $now, int $birthday): array
{
    $secondsLeft = $birthday - $now;
    
    $days = floor($secondsLeft / (60 * 60 * 24));
    $hours = floor(($secondsLeft % (60 * 60 * 24)) / (60 * 60));
    $minutes = floor(($secondsLeft % (60 * 60)) / 60);
    $seconds = $secondsLeft % 60;
    
    return [
        'days' => $days,
        'hours' => $hours,
        'minutes' => $minutes,
        'seconds' => $seconds
    ];
}

// Инициализация переменных
$data = initializeDateTimeVariables();
$now = $data['now'];
$birthday = $data['birthday'];
$hour = $data['hour'];

// Получение приветствия
$welcome = getWelcomeMessage($hour);

// Форматирование даты
$formattedDate = formatRussianDate($now);

// Вычисление времени до дня рождения
$timeUntilBirthday = getTimeUntilBirthday($now, $birthday);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Использование функций даты и времени</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .welcome {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .date-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .birthday-info {
            background-color: #e8f5e8;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Использование функций даты и времени</h1>
    
    <?php
   
    ?>
    
    <div class="welcome"><?= $welcome ?></div>
    
    <div class="date-info">
        <strong>Текущая дата и время:</strong><br>
        <?= $formattedDate ?>
    </div>
    
    <div class="birthday-info">
        <strong>До моего дня рождения осталось:</strong><br>
        <?= $timeUntilBirthday['days'] ?> дней, 
        <?= $timeUntilBirthday['hours'] ?> часов, 
        <?= $timeUntilBirthday['minutes'] ?> минут, 
        <?= $timeUntilBirthday['seconds'] ?> секунд
    </div>
</body>
</html>