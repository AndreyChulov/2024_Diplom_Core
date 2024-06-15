CreateBoard();
RegisterAuthCheckerExecutor("Vasa", "VasaAuthKey", "VasaAuthStatus", 10000)
RegisterAuthCheckerExecutor("Vasa1", "Vasa1AuthKey", "Vasa1AuthStatus", 10000)
ExecuteAuthTimeoutUpdater("VasaAuthStatus", 1000)
ExecuteAuthTimeoutUpdater("Vasa1AuthStatus", 1000)
RegisterGetGameInfo("GetGameInfo")
RegisterUpdateBoard("GetGameBoard")
RegisterSurrender("VasaSurrender", "Vasa", "VasaAuthKey")
RegisterSurrender("Vasa1Surrender", "Vasa1", "Vasa1AuthKey")

function UpdateBoard() {
    const login = "Vasa"
    const authKey = $("#VasaAuthKey").val()
    const gameKey = $("#GameKey").text()

    $.get("api/game/gameBoard", {
        GameKey: gameKey
    })
        .done(function (data) {
            const board = JSON.parse(data["GameBoard"])

            $(".column").off("click").empty()

            PutCheckersOnBoard(board)
            SetOnCheckerClickEvent()
        })
}

function SetTurnClickEvent() {
    $(`.movie`).parent().on("click", function (event) {
        const target = event.target
        const movieElement = target.firstChild ?? target
        const movie = movieElement.id
        const fromAddress = movie.substring(0, 2)
        const isBlack = $(`#${fromAddress} div`).attr("class").includes("B")
        const login = isBlack ? "Vasa1" : "Vasa"
        const authKey = isBlack ? $("#Vasa1AuthKey").val() : $("#VasaAuthKey").val()
        const gameKey = $("#GameKey").text()

        $.post("api/game/move", {
            Login: login,
            Key: authKey,
            GameKey: gameKey,
            Move: movie
        })
            .done(function (data) {
                UpdateBoard()
                UpdateGameStatus(login, authKey, gameKey)
            })
    })
}

function UpdateGameStatus(login, authKey, gameKey) {
    $.get("api/game/gameStatus", {
        GameKey: gameKey
    })
        .done(function (data) {
            $("#GameStatus").text(data["GameStatus"])
        })
}

function SetOnCheckerClickEvent() {
    $(`.checker`).on("click", function (event) {
        const id = event.target.parentElement.parentElement.id
        let login = "Vasa"
        let authKey = $("#VasaAuthKey").val()
        const gameKey = $("#GameKey").text()

        if(event.target.parentElement.classList.contains("B") === true ||
            event.target.parentElement.classList.contains("BR") === true){
            login = "Vasa1"
            authKey = $("#Vasa1AuthKey").val()
        }

        $.get("api/game/checkerAcceptableMovies",
            {
                Login: login,
                Key: authKey,
                GameKey: gameKey,
                Checker: id
            })
            .done(function (data) {
                const movies = JSON.parse(data["Movies"])

                $(".movie").remove()

                for (/**@var {string} movie**/const movie of movies) {
                    const target = movie.substring(3)
                    $(`#${target}`).html(`<div class='movie' id=${movie}></div>`)
                }

                SetTurnClickEvent()
            })
    })
}

function PutCheckersOnBoard(board) {
    for (let rowCounter = 0; rowCounter < 8; rowCounter++){
        for (let columnCounter = 0; columnCounter < 8; columnCounter++){

            const cell = board[rowCounter][columnCounter]


            if (cell !== null){
                $(`.row_${rowCounter} .column_${columnCounter}`)
                    .html(`<div class="${cell} checker"><div></div></div>`)
            }
        }
    }
}

function RegisterUpdateBoard(buttonId) {
    $(`#${buttonId}`).on("click", function (event) {
        const login = "Vasa"
        const authKey = $("#VasaAuthKey").val()
        const gameKey = $("#GameKey").text()

        $.get("/api/game/gameBoard", {
            GameKey: gameKey
        })
            .done(function (data) {
                const board = JSON.parse(data["GameBoard"])
                UpdateBoard(board)
                SetOnCheckerClickEvent()
            })
            .fail(function (request) {
                const statusCode = request.status;
                switch (statusCode){
                    case 403:
                        $("#GameStatus").text("Игры не существует")
                        break
                }
            })
    })
}

function RegisterGetGameInfo(buttonId) {
    $(`#${buttonId}`).on("click", function (event) {
        $("#GameKey").text("")
        GetGameInfo()
    })
}

function RegisterSurrender(buttonId, login, authKeyId) {
    $(`#${buttonId}`).on("click", function (event) {
        Surrender(login, authKeyId)
    })
}

function Surrender(login, authKeyId) {
    const authKey = $(`#${authKeyId}`).val()
    const gameKey = $("#GameKey").text()

    $.post("api/game/surrender", {
        Login: login,
        Key: authKey,
        GameKey: gameKey
    })
        .done(function (data) {
            UpdateGameStatus(login, authKey, gameKey)
        })
}

function GetGameInfo() {
    if ($("#GameKey").text() === ""){
        const login = "Vasa"
        const authKey = $("#VasaAuthKey").val()

        $.get("api/game/gameInfo", {
            Login: login,
            Key: authKey
        })
            .done(function (data) {
                $("#GameKey").text(data["GameKey"])
                $("#GameStatus").text(data["GameStatus"])
                $("#WhitePlayerLogin").text(data["WhitePlayerLogin"])
                $("#BlackPlayerLogin").text(data["BlackPlayerLogin"])
                const board = JSON.parse(data["Board"])
                UpdateBoard(board)
                SetOnCheckerClickEvent()
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

function ExecuteAuthTimeoutUpdater(authStatusId, timeout) {
    setInterval(function () {
        const prefix = "Ключ протухнет через"
        const text = $(`#${authStatusId}`).text()
        const isPrefixFound = text.includes(prefix)

        if (isPrefixFound === true){
            const timeoutText = text.substring(text.length-8)
            const timeoutTextSplit = timeoutText.split(":")
            const oldTimeoutSeconds =
                Math.trunc(parseInt(timeoutTextSplit[0], 10)*60*60) +
                Math.trunc(parseInt(timeoutTextSplit[1], 10)*60) +
                Math.trunc(parseInt(timeoutTextSplit[2], 10));
            const newTimeoutFullSeconds = oldTimeoutSeconds - 1;
            const newTimeoutHours = Math.trunc(newTimeoutFullSeconds/60/60);
            const newTimeoutMinutes = Math.trunc((newTimeoutFullSeconds/60) - newTimeoutHours*60);
            const newTimeoutSeconds = Math.trunc(newTimeoutFullSeconds - newTimeoutHours*60*60 - newTimeoutMinutes*60);
            const newTimeoutText =
                (newTimeoutHours < 10 ? "0" + newTimeoutHours.toString() : newTimeoutHours.toString()) + ":" +
                (newTimeoutMinutes < 10 ? "0" + newTimeoutMinutes.toString() : newTimeoutMinutes.toString()) + ":" +
                (newTimeoutSeconds < 10 ? "0" + newTimeoutSeconds.toString() : newTimeoutSeconds.toString())

            $(`#${authStatusId}`).text(`${prefix} ${newTimeoutText}`)
        }

    }, timeout)
}

function AuthChecker(login, authKeyId, authStatusId) {
    const authKey = $(`#${authKeyId}`).val()

    if (typeof authKey !== "string" || authKey === ""){
        $(`#${authStatusId}`).text("Не авторизован")
    } else {
        $.get("api/profile/getAuthorizeKeyStatus", {
            Login: login,
            Key: authKey
        })
            .done(function (data) {
                $(`#${authStatusId}`).text(`Ключ протухнет через ${data["Timeout"]}`)
                GetGameInfo()
            })
            .fail(function (request) {
                const statusCode = request.status;
                switch (statusCode){
                    case 401:
                        $(`#${authStatusId}`).text("Неверный ключ авторизации")
                        break
                    case 406:
                        $(`#${authStatusId}`).text("Логин не зарегистрирован")
                        break
                }
            })
    }
}

function RegisterAuthCheckerExecutor(login, authKeyId, authStatusId, timeout) {
    $(`#${authKeyId}`).on("change", {timer:0},function (event) {
        AuthChecker(login, authKeyId, authStatusId)
        StopAuthChecker(event.data.timer)
        event.data.timer = ExecuteAuthChecker(login, authKeyId, authStatusId, timeout)
    })
}

function StopAuthChecker(interval) {
    if (interval === 0){
        return
    }

    clearInterval(interval)
}

function ExecuteAuthChecker(login, authKeyId, authStatusId, timeout) {
    return setInterval(function () {
        AuthChecker(login, authKeyId, authStatusId)
    }, timeout)
}

function CreateBoard() {
    let board = "<div><span></span>"
    const rows = ["A", "B", "C", "D", "E", "F", "G", "H"]
    const columns = ["1", "2", "3", "4", "5", "6", "7", "8"];

    for (let counter = 0; counter < 8; counter++) {
        board += `<span>${columns[counter]}</span>`
    }

    board += "</div>"

    for (let rowCounter = 0; rowCounter < 8; rowCounter++){
        let row = `<span>${rows[rowCounter]}</span>`

        for (let columnCounter = 0; columnCounter < 8; columnCounter++){
            row += `<div class="column_${columnCounter} column" id="${rows[rowCounter]}${columns[columnCounter]}"></div>`
        }

        board += `<div class="row_${rowCounter}">${row}</div>`
    }

    $("#Board").html(board)

}