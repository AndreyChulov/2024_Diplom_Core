$("#InstallButton").on("click", InstallButtonClicked)
UpdateStatuses()

function InstallButtonClicked() {
    $.post("/API/Server/Install").done(function (data, status) {
        console.log(data, status)
        UpdateStatuses()
    })
}

function UpdateStatuses() {
    $.get("/API/Server/InstallStatus").done(function (data, status) {
        console.log(data, status)
        $("#databaseInstalled").html(data["IsDatabaseExists"] === true ? "Установлено" : "Отсутствует")
        $("#SqlLite3Loaded").html(data["IsSqlLite3ClassLoaded"] === true ? "Загружен" : "Отсутствует")
    })
}