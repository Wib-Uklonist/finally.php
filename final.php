<?php

const PASS_SCORE = 60;
const COURSE_NAME = "PHP с нуля";

$students = [
    ["name" => "Иван Петров",    "group" => "A", "scores" => [85, 92, 78, 90],  "age" => "19", "paid" => true],
    ["name" => "Мария Лебедева", "group" => "B", "scores" => [55, 48, 62, 51],  "age" => "20", "paid" => true],
    ["name" => "Олег Смирнов",   "group" => "A", "scores" => [70, 65, 80, 72],  "age" => "21", "paid" => false],
    ["name" => "Анна Козлова",   "group" => "B", "scores" => [98, 95, 100, 97], "age" => "19", "paid" => true],
    ["name" => "Пётр Волков",    "group" => "A", "scores" => [40, 35, 58, 45],  "age" => "22", "paid" => false],
    ["name" => "Дарья Морозова", "group" => "C", "scores" => [88, 76, 91, 84],  "age" => "20", "paid" => true],
    ["name" => "Сергей Орлов",   "group" => "C", "scores" => [],                "age" => "18", "paid" => true],
];

// ==================== Часть 1. Типы и приведение ====================
echo "=== Часть 1 ===\n";

var_dump($students[0]["age"]); // string(2) "19"

// Переходим по ссылке (&), чтобы менять сам массив
foreach ($students as &$student) {
    $student["age"] = (int)$student["age"];
}
unset($student); // обязательно убираем ссылку после цикла

var_dump($students[0]["age"]); // теперь int(19)

var_dump("19" == 19);  // true: == сравнивает только значения, строка превращается в число
var_dump("19" === 19); // false: === сравнивает ещё и типы, а string и int - разные типы

// ==================== Часть 2. Средний балл ====================
echo "\n=== Часть 2 ===\n";

foreach ($students as &$student) {
    $sum = 0;
    foreach ($student["scores"] as $score) {
        $sum = $sum + $score;
    }

    if (count($student["scores"]) > 0) {
        $student["avg"] = round($sum / count($student["scores"]), 1);
        echo $student["name"] . ": " . $student["avg"] . "\n";
    } else {
        $student["avg"] = null; // оценок нет, делить нельзя
        echo $student["name"] . ": нет оценок\n";
    }
}
unset($student);

// ==================== Часть 3. Оценка и статус ====================
echo "\n=== Часть 3 ===\n";

foreach ($students as &$student) {
    // Буквенная оценка
    $student["grade"] = match (true) {
        $student["avg"] >= 90 => "A",
        $student["avg"] >= 75 => "B",
        $student["avg"] >= 60 => "C",
        default => "F",
    };

    // Статус
    $student["status"] = $student["avg"] >= PASS_SCORE ? "сдал" : "не сдал";

    // Название группы
    switch ($student["group"]) {
        case "A":
            $student["group_name"] = "Утренняя";
            break;
        case "B":
            $student["group_name"] = "Дневная";
            break;
        case "C":
            $student["group_name"] = "Вечерняя";
            break;
        default:
            $student["group_name"] = "Неизвестная";
    }
}
unset($student);

echo "Имя | Группа | Средний балл | Оценка | Статус\n";
foreach ($students as $student) {
    $avgText = $student["avg"] === null ? "нет оценок" : $student["avg"];
    echo $student["name"] . " | " . $student["group_name"] . " | " . $avgText . " | " . $student["grade"] . " | " . $student["status"] . "\n";
}

// ==================== Часть 4. Циклы и управление потоком ====================
echo "\n=== Часть 4 ===\n";

// for: номер студента рядом с именем
for ($i = 0; $i < count($students); $i++) {
    echo ($i + 1) . ". " . $students[$i]["name"] . "\n";
}

// foreach + continue: только оплатившие
echo "\nОплатили курс:\n";
foreach ($students as $student) {
    if ($student["paid"] !== true) {
        continue;
    }
    echo $student["name"] . "\n";
}

// while + break: первый, кто не сдал
echo "\nПервый несдавший: ";
$i = 0;
while ($i < count($students)) {
    if ($students[$i]["avg"] < PASS_SCORE) {
        echo $students[$i]["name"] . "\n";
        break;
    }
    $i++;
}

// do-while: обратный отсчёт
echo "\nОбратный отсчёт:\n";
$n = count($students);
do {
    echo $n . "\n";
    $n--;
} while ($n >= 1);

// ==================== Часть 5. Функции массивов ====================
echo "\n=== Часть 5 ===\n";

// array_filter: только сдавшие
$passed = array_values(array_filter($students, fn($s) => $s["avg"] >= PASS_SCORE));
echo "Сдали: " . count($passed) . "\n";

// array_map: строки вида "Иван Петров (A)"
$labels = array_map(fn($s) => $s["name"] . " (" . $s["group"] . ")", $students);
foreach ($labels as $label) {
    echo $label . "\n";
}

// array_reduce: общий средний балл (только те, у кого есть оценки)
$withScores = array_filter($students, fn($s) => $s["avg"] !== null);
$totalAvg = array_reduce($withScores, fn($carry, $s) => $carry + $s["avg"], 0);
$totalAvg = round($totalAvg / count($withScores), 1);
echo "Общий средний балл по курсу: " . $totalAvg . "\n";

// usort: сортировка по убыванию среднего балла, топ-3
$sorted = $students;
usort($sorted, fn($a, $b) => $b["avg"] <=> $a["avg"]);
echo "\nТоп-3:\n";
for ($i = 0; $i < 3; $i++) {
    echo ($i + 1) . ". " . $sorted[$i]["name"] . " - " . $sorted[$i]["avg"] . "\n";
}

// array_filter + array_reduce: сдали, но не оплатили
$passedNotPaid = array_filter($students, fn($s) => $s["avg"] >= PASS_SCORE && $s["paid"] === false);
$passedNotPaidCount = array_reduce($passedNotPaid, fn($carry, $s) => $carry + 1, 0);
echo "\nСдали, но не оплатили: " . $passedNotPaidCount . "\n";

// ==================== Часть 6. Группировка и аналитика ====================
echo "\n=== Часть 6 ===\n";

$byGroup = [];
foreach ($students as $student) {
    $byGroup[$student["group_name"]][] = $student;
}

$bestGroup = "";
$bestGroupAvg = -1;

foreach ($byGroup as $groupName => $members) {
    $sum = 0;
    $count = 0;
    foreach ($members as $member) {
        if ($member["avg"] !== null) { // студентов без оценок не считаем
            $sum = $sum + $member["avg"];
            $count++;
        }
    }
    $groupAvg = round($sum / $count, 1);

    echo $groupName . ": студентов " . count($members) . ", средний балл " . $groupAvg . "\n";

    if ($groupAvg > $bestGroupAvg) {
        $bestGroupAvg = $groupAvg;
        $bestGroup = $groupName;
    }
}
echo "Лучшая группа: " . $bestGroup . " (" . $bestGroupAvg . ")\n";

if (isset($byGroup["D"])) {
    echo "Группа D есть\n";
} else {
    echo "Группы D нет\n";
}

// ==================== Часть 7. Итоговый отчёт ====================
echo "\n=== Итоговый отчёт: " . COURSE_NAME . " ===\n";

$total = count($students);
$passedCount = count($passed);
$failedCount = $total - $passedCount;
$percent = round($passedCount / $total * 100, 1);

echo "Всего студентов: " . $total . "\n";
echo "Сдали: " . $passedCount . ", не сдали: " . $failedCount . "\n";
echo "Процент сдавших: " . $percent . "%\n";
echo "Лучший студент: " . $sorted[0]["name"] . " (" . $sorted[0]["avg"] . ")\n";

$debtors = 0;
$needHelp = [];
foreach ($students as $student) {
    if ($student["paid"] === false) {
        $debtors++;
    }
    if ($student["avg"] < PASS_SCORE && $student["paid"] === true) {
        $needHelp[] = $student["name"];
    }
}
echo "Должников по оплате: " . $debtors . "\n";
echo "Нужно подтянуть оценки: " . implode(", ", $needHelp) . "\n";

$verdict = match (true) {
    $percent >= 70 => "Курс успешен",
    $percent >= 40 => "Курс требует доработки",
    default => "Курс провален",
};
echo "Вердикт: " . $verdict . "\n";
