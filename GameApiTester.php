<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Game API tester</title>
    <link rel="stylesheet" href="CSS/main.css">
    <link rel="stylesheet" href="CSS/GameApiTester.css">
    <script src="JS/jquery-3.7.1.js"></script>
    <script defer src="JS/GameApiTester.js"></script>
</head>
<body>
<hr>
<h1>Тестер API игры</h1>
<hr>
<div class="gameInfo">
    <div>
        <div>
            <div>Логин:</div>
            <div>Vasa</div>
        </div>
        <div>
            <div><label for="VasaAuthKey">Ключ авторизации:</label></div>
            <div><input type="text" id="VasaAuthKey"></div>
        </div>
        <div>
            <div>Статус авторизации:</div>
            <div id="VasaAuthStatus"></div>
        </div>
    </div>
    <div>
        <div>
            <div>Ключ игры:</div>
            <div id="GameKey"></div>
        </div>
        <div>
            <div>Статус игры:</div>
            <div id="GameStatus"></div>
        </div>
        <div>
            <div>Логин белых:</div>
            <div id="WhitePlayerLogin"></div>
        </div>
        <div>
            <div>Логин черных:</div>
            <div id="BlackPlayerLogin"></div>
        </div>
    </div>
    <div>
        <div>
            <div>Логин:</div>
            <div>Vasa1</div>
        </div>
        <div>
            <div><label for="Vasa1AuthKey">Ключ авторизации:</label></div>
            <div><input type="text" id="Vasa1AuthKey"></div>
        </div>
    </div>
</div>
<div id="Board"></div>
</body>
</html>
