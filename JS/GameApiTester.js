CreateBoard();
ExecuteVasaAuthChecker();

function PutCheckersOnBoard(board) {
    for (let rowCounter = 0; rowCounter < 8; rowCounter++){
        for (let columnCounter = 0; columnCounter < 8; columnCounter++){

            const cell = board[rowCounter][columnCounter]

            if (cell !== null){
                $(`.row_${rowCounter} .column_${columnCounter}`)
                    .html(`<div class=${cell}><div></div></div>`)
            }
        }
    }
}

function GetGameInfo() {
    if ($("#GameKey").text() === ""){
        const login = "Vasa"
        const authKey = $("#VasaAuthKey").val()

        $.get(`api/game/gameInfo?Login=${login}&Key=${authKey}`)
            .done(function (data) {
                $("#GameKey").text(data["GameKey"])
                $("#GameStatus").text(data["GameStatus"])
                $("#WhitePlayerLogin").text(data["WhitePlayerLogin"])
                $("#BlackPlayerLogin").text(data["BlackPlayerLogin"])
                const board = JSON.parse(data["Board"])
                PutCheckersOnBoard(board)
            })
            .fail(function (request) {
                const statusCode = request.status;
                switch (statusCode){
                    case 403:
                        $("#GameStatus").text("Игры не существует")
                        break
                }
            })
    }
}

function ExecuteVasaAuthChecker() {
    setInterval(function () {
        const login = "Vasa"
        const authKey = $("#VasaAuthKey").val()

        if (typeof authKey !== "string" || authKey === ""){
            $("#VasaAuthStatus").text("Не авторизован")
        } else {
            $.get(`api/profile/getAuthorizeKeyStatus?Login=${login}&Key=${authKey}`)
                .done(function (data) {
                    $("#VasaAuthStatus").text(`Ключ протухнет через ${data["Timeout"]}`)
                    GetGameInfo()
                })
                .fail(function (request) {
                    const statusCode = request.status;
                    switch (statusCode){
                        case 401:
                            $("#VasaAuthStatus").text("Неверный ключ авторизации")
                            break
                        case 406:
                            $("#VasaAuthStatus").text("Логин не зарегистрирован")
                            break
                    }
                })
        }
    }, 1000)
}

function CreateBoard() {
    let row = ""
    let board = ""

    for (let counter = 0; counter < 8; counter++){
        row += `<div class="column_${counter}"></div>`
    }

    for (let counter = 0; counter < 8; counter++){
        board += `<div class="row_${counter}">${row}</div>`
    }

    $("#Board").html(board)

}