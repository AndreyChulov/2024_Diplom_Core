$("#InstallButton").on("click", InstallButtonClicked)
UpdateStatuses()

function InstallButtonClicked() {
    $.ajax("/API/Install.php").done(function (data, status) {
        console.log(data, status)
        UpdateStatuses()
    })
}

function UpdateStatuses() {
    $.ajax("/API/InstallStatus.php").done(function (data, status) {
        console.log(data, status)
        let info = JSON.parse(data)
        $("#databaseInstalled").html(info["DB"] === true ? "Установлено" : "Отсутствует")
        $("#SqlLite3Loaded").html(info["SQLite3"] === true ? "Загружен" : "Отсутствует")
    })
}