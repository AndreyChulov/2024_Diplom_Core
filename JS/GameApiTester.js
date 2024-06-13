CreateBoard();
ExecuteVasaAuthChecker();
ExecuteVasa1AuthChecker();

function SetTurnClickEvent(id) {

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

        $.get(`api/game/checkerAcceptableMovies?Login=${login}&Key=${authKey}&GameKey=${gameKey}&Checker=${id}`)
            .done(function (data) {
                const movies = JSON.parse(data["Movies"])

                $(".movie").remove()

                for (/**@var {string} movie**/const movie of movies) {
                    const target = movie.substring(3)
                    $(`#${target}`).html(`<div class='movie' id=${movie}></div>`)
                    SetTurnClickEvent(target)
                }
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

function ExecuteVasa1AuthChecker() {
    setInterval(function () {
        const login = "Vasa1"
        const authKey = $("#Vasa1AuthKey").val()

        if (typeof authKey !== "string" || authKey === ""){
            $("#Vasa1AuthStatus").text("Не авторизован")
        } else {
            $.get(`api/profile/getAuthorizeKeyStatus?Login=${login}&Key=${authKey}`)
                .done(function (data) {
                    $("#Vasa1AuthStatus").text(`Ключ протухнет через ${data["Timeout"]}`)
                })
                .fail(function (request) {
                    const statusCode = request.status;
                    switch (statusCode){
                        case 401:
                            $("#Vasa1AuthStatus").text("Неверный ключ авторизации")
                            break
                        case 406:
                            $("#Vasa1AuthStatus").text("Логин не зарегистрирован")
                            break
                    }
                })
        }
    }, 1000)
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
            row += `<div class="column_${columnCounter}" id="${rows[rowCounter]}${columns[columnCounter]}"></div>`
        }

        board += `<div class="row_${rowCounter}">${row}</div>`
    }

    $("#Board").html(board)

}