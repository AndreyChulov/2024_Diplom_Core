<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Server installator</title>
    <link rel="stylesheet" href="CSS/main.css">
    <script src="JS/jquery-3.7.1.js"></script>
    <script defer src="JS/Install.js"></script>
</head>
<body>
    <hr>
        <h1>Инсталлятор сервера</h1>
    <hr>
    <table>
        <tr>
            <th>Компонент сервера</th>
            <th>Статус</th>
        </tr>
        <tr>
            <td>База данных</td>
            <td id="databaseInstalled"></td>
        </tr>
        <tr>
            <td>Класс SqlLite3</td>
            <td id="SqlLite3Loaded"></td>
        </tr>
        <tr>
            <td>Генерация документации swager</td>
            <td id="OpenApiLoaded"><?php echo function_exists("\OpenApi\scan") ? "Включено" : "Выключено" ?></td>
        </tr>
    </table>
    <hr>
    <button id="InstallButton">Установить</button>
</body>
</html>
