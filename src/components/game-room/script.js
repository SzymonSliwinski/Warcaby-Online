"use strict";
var isFieldPicked = false;
var pickedId = null;
var possibleMovesIds = [];
var whoseTurn = '';
var isSecondPlayerJoined = 0;
var playerRequestIntervalId;
var updateTurnId;
const url = window.location.search;
const urlParam = new URLSearchParams(url);
const roomName = urlParam.get('room_name');
var myColor = getMyColor();
var myCheckersIds = [];
var isPossibleCapture = false;
var possibleCaptures = [];
var isWinner = false;
var alerts;
class Alerts {
    constructor(winner, loser, capture, noSecondPlayer) {
        this.winner = winner;
        this.loser = loser;
        this.capture = capture;
        this.noSecondPlayer = noSecondPlayer;
    }
}

function loadBoard(checkCaptures) {
    $('#container').load('../../game-state-files/' + roomName + '.html', function () {
        if (whoseTurn === myColor && checkCaptures === true) {
            checkForCaptures();
        }
    });
}

$.ajax({
    url: '/controller.php?get_language=true',
    type: 'get',
    success: function (data) {
        var jsonData = JSON.parse(data)
        alerts = new Alerts(jsonData.winner, jsonData.loser, jsonData.capture, jsonData.noSecondPlayer);
    }
});

loadBoard(true);
updatePlayersNicks();

$(document).ready(function () {
    updateTurnId = setInterval(updateTurn, 1000);
});

$(document).ready(function () {
    playerRequestIntervalId = setInterval(checkIsSecondPlayerJoined, 1000)
});

function checkIsWinner() {
    $.ajax({
        url: '/controller.php?check_is_win=true&room_name=' + roomName,
        type: 'get',
        success: function (data) {
            if (data != 0 && isSecondPlayerJoined == 1) {
                alert(alerts.winner + ": " + data);
                deleteGameFiles();
                isWinner = true;
                window.location.href = "http://localhost:8000/components/menu/menu.php";
            }
        }
    });
}

function checkForCaptures() {
    myCheckersIds = getMyCheckersIds(myColor);

    if (myColor == 'w') {
        possibleCaptures = getWhiteCapturesIds(myCheckersIds);
    }
    else if (myColor == 'b') {
        possibleCaptures = getBlackCapturesIds(myCheckersIds);
    }

    isPossibleCapture = false;
    if (possibleCaptures && possibleCaptures.length > 0) {
        ColorPossibleCapsFields();
        isPossibleCapture = true;
    }
}

function deleteGameFiles() {
    $.ajax({
        url: '/controller.php?delete_game_room_data=true&room_name=' + roomName,
        type: 'get',
        success: function () {
            window.location.href = "http://localhost:8000/components/menu/menu.php";
        }
    });
}

function updateTurn() {
    $.ajax({
        url: '/controller.php?get_turn=true&room_name=' + roomName,
        type: 'get',
        success: function (data) {
            if (data != 'w' && data != 'b' && isWinner === false) {
                clearInterval(updateTurnId);
                alert(alerts.loser)
                window.location.href = "http://localhost:8000/components/menu/menu.php";
            }

            if (myColor != whoseTurn && data === myColor) {
                loadBoard(true);
            }
            whoseTurn = data;
        }
    });
}

function checkIsSecondPlayerJoined() {
    $.ajax({
        url: '/controller.php?is_second_player_joined=false&room_name=' + roomName,
        type: 'get',
        success: function (data) {
            isSecondPlayerJoined = data;
            if (data == true) {
                clearInterval(playerRequestIntervalId);
                updatePlayersNicks();
            }
        }
    });
}

function updatePlayersNicks() {
    $.ajax({
        url: '/controller.php?update_player=false&room_name=' + roomName,
        type: 'get',
        success: function (data) {
            loadBoard(true);
        }
    });
}

function getMyColor() {
    $.ajax({
        url: '/controller.php?get_color=true' + '&room_name=' + roomName,
        type: 'get',
        success: function (data) {
            myColor = data;
        }
    });
}

//w momencie klikniecia na pole wykonuje sie
$(document).on('click', 'td', function () {
    // jesli jest 2 graczy
    if (isSecondPlayerJoined == 1) {
        var id = $(this).attr('id');
        var fieldValue = $(this).html();
        //jesli nie ma mozliwego bicia
        if (isPossibleCapture === false) {
            //jesli gracz wybral swoje pole to zaznacza mozliwe ruchy
            //color + 1 oznaczenie damki
            if ((fieldValue == myColor || fieldValue == myColor + 1) || pickedId != null) {
                //jesli gracz mial zaznaczone pole i 
                //wybral jeden z mozliwych ruchów to wykonuje ruch
                if (isFieldPicked && IsPickedIdWasPossible(id, possibleMovesIds)) {
                    sendMove(id, 0);
                    return;
                }
                if (fieldValue == whoseTurn) {
                    PickFieldWithPossibleMoves(id, whoseTurn, false);
                }
                else if (fieldValue == whoseTurn + 1) {
                    PickFieldWithPossibleMoves(id, whoseTurn, true);
                }
            } else {
                return;
            }
        } else {
            if (clickedCheckerThatCaptures(id) === true) {
                //jesli kliknieto na pionek który bedzie bił
                ColorPossibleCapsFields()
                document.getElementById(id).style.backgroundColor = 'LightGreen';
                isFieldPicked = true;
                pickedId = id;
            }
            //jesli wczesniej wybrano pionek i kliknieto na jeden z mozliwych do bicia
            else if (isFieldPicked === true && clickedCheckerThatIsCaptured(id)) {
                sendMove(id, 1)
            } else {
                //jesli kliknieto na inny a jest bicie
                alert(alerts.capture);
            }
        }
    } else {
        alert(alerts.noSecondPlayer)
    }
});

function changeTurn() {
    $.ajax({
        url: '/controller.php?change_turn=true&room_name=' + roomName,
        type: 'get',
        success: function (data) {
            whoseTurn = data;
        }
    });
}

function sendMove(id, isRemove) {
    jQuery.ajax({
        url: '/controller.php?make_move=true',
        type: "post",
        data: {
            "oldId": pickedId,
            "newId": id,
            "roomName": roomName,
            "changeOnQueen": shouldChangeOnQueen(id, myColor),
            "isRemove": isRemove,
            "capturedCheckerId": parseInt(id) - (((parseInt(id) - parseInt(pickedId)) / parseInt(2)))
        },
        success: function (data) {
            whoseTurn = data;
            if (isRemove == 1) {
                myCheckersIds = [];
                possibleCaptures = [];
                changeTurn();
            }
            loadBoard(false);
            checkIsWinner();
            clearMovesValues();
        }
    });
}


function clickedCheckerThatIsCaptured(id) {
    for (var i = 0; i < possibleCaptures.length; i++) {
        if (possibleCaptures[i][0] == pickedId) {
            for (var j = 1; j < possibleCaptures[i].length; j++) {
                if (possibleCaptures[i][j] == id) {
                    return true;
                }
            }
        }
    }
    return false;
}

function getWhiteCapturesIds(checkersIds) {
    //jesli funkcja znajdzei mozliwe bicia zwraca tablice ["id pionka ktory bije", id po biciu, id po biciu...]
    var result = [];
    for (var i = 0; i < checkersIds.length; i++) {
        var id = checkersIds[i];
        var possibleCaps = [];

        if (parseInt(7) + parseInt(id) <= 63) {
            var left = $("#" + (parseInt(7) + parseInt(id))).text();
        }
        if (parseInt(9) + parseInt(id) <= 63) {
            var right = $("#" + (parseInt(9) + parseInt(id))).text();
        }
        if (id % 8 === 0 &&
            (right === getEnemyColor(myColor) || right === getEnemyColor(myColor) + 1)) {
            if (parseInt(18) + parseInt(id) < 64 && $("#" + (parseInt(18) + parseInt(id))).text() === '')
                possibleCaps.push(parseInt(18) + parseInt(id));
        }
        else if ((parseInt(id) + parseInt(1)) % 8 === 0 &&
            (left === getEnemyColor(myColor) || left === getEnemyColor(myColor) + 1)) {
            if (parseInt(14) + parseInt(id) < 64 && $("#" + (parseInt(14) + parseInt(id))).text() === '')
                possibleCaps.push(parseInt(14) + parseInt(id));
        }
        else if (id % 8 !== 0 && (parseInt(id) + parseInt(1)) % 8 !== 0) {
            if ((left === getEnemyColor(myColor) || left === getEnemyColor(myColor) + 1) &&
                (parseInt(7) + parseInt(id)) % 8 != 0 && $("#" + (parseInt(14) + parseInt(id))).text() === ''
                && parseInt(14) + parseInt(id) < 64) {
                possibleCaps.push(parseInt(14) + parseInt(id));
            }
            if ((right === getEnemyColor(myColor) || right === getEnemyColor(myColor) + 1)
                && ((parseInt(9) + parseInt(id)) + parseInt(1)) % 8 != 0 && $("#" + (parseInt(18) + parseInt(id))).text() === ''
                && parseInt(18) + parseInt(id) < 64) {
                possibleCaps.push(parseInt(18) + parseInt(id));
            }
        }

        var queenCaps = [];
        if ($("#" + id).text() == 'w1') {
            var queenIdArray = [parseInt(id)];
            queenCaps = getBlackCapturesIds(queenIdArray)[0];

            if (queenCaps)
                queenCaps.shift();
        }

        if ((possibleCaps && possibleCaps.length > 0) || (queenCaps && queenCaps.length > 0)) {
            //jesli znaleziono bicia jako pierwszy element zapisujemy id pionka ktory bedzie bił
            var possibleCapsWithId = [];
            possibleCapsWithId.push(id)
            possibleCapsWithId = possibleCapsWithId.concat(possibleCaps)

            if (queenCaps && queenCaps.length > 0)
                possibleCapsWithId = possibleCapsWithId.concat(queenCaps)

            result.push(possibleCapsWithId);
        }
    }
    return result;
}

function getBlackCapturesIds(checkersIds) {
    //jesli funkcja znajdzei mozliwe bicia zwraca tablice ["id pionka ktory bije", id po biciu, id po biciu...]
    var result = [];

    for (var i = 0; i < checkersIds.length; i++) {
        var id = checkersIds[i];
        var possibleCaps = [];

        if (parseInt(id) - parseInt(9) >= 0) {
            var left = $("#" + (parseInt(id) - parseInt(9))).text();
        }
        if (parseInt(id) - parseInt(7) >= 0) {
            var right = $("#" + (parseInt(id) - parseInt(7))).text();
        }
        if (id % 8 === 0 &&
            (right === getEnemyColor(myColor) || right === getEnemyColor(myColor) + 1)) {
            if (parseInt(id) - parseInt(14) >= 0 && $("#" + (parseInt(id) - parseInt(14))).text() === '')
                possibleCaps.push(parseInt(id) - parseInt(14));
        }
        else if ((parseInt(id) + parseInt(1)) % 8 === 0 &&
            (left === getEnemyColor(myColor) || left === getEnemyColor(myColor) + 1)) {
            if (parseInt(id) - parseInt(18) >= 0 && $("#" + (parseInt(id) - parseInt(18))).text() === '')
                possibleCaps.push(parseInt(id) - parseInt(18));
        }
        else if (id % 8 !== 0 && (parseInt(id) + parseInt(1)) % 8 !== 0) {
            if ((left === getEnemyColor(myColor) || left === getEnemyColor(myColor) + 1) &&
                (parseInt(id) - parseInt(9)) % 8 != 0 && $("#" + (parseInt(id) - parseInt(18))).text() === ''
                && parseInt(id) - parseInt(18) >= 0) {
                possibleCaps.push(parseInt(id) - parseInt(18));
            }
            if ((right === getEnemyColor(myColor) || right === getEnemyColor(myColor) + 1)
                && ((parseInt(id) - parseInt(7)) + parseInt(1)) % 8 != 0 && $("#" + (parseInt(id) - parseInt(14))).text() === ''
                && parseInt(id) - parseInt(14) >= 0) {
                possibleCaps.push(parseInt(id) - parseInt(14));
            }
        }

        var queenCaps = [];
        if ($("#" + id).text() == 'b1') {
            var queenIdArray = [parseInt(id)];
            queenCaps = getWhiteCapturesIds(queenIdArray)[0];
            if (queenCaps)
                queenCaps.shift();
        }
        if ((possibleCaps && possibleCaps.length > 0) || (queenCaps && queenCaps.length > 0)) {
            //jesli znaleziono bicia jako pierwszy element zapisujemy id pionka ktory bedzie bił
            var possibleCapsWithId = [];
            possibleCapsWithId.push(id)
            possibleCapsWithId = possibleCapsWithId.concat(possibleCaps)

            if (queenCaps && queenCaps.length > 0)
                possibleCapsWithId = possibleCapsWithId.concat(queenCaps)

            result.push(possibleCapsWithId);
        }
    }
    return result;
}

function clickedCheckerThatCaptures(id) {
    for (var i = 0; i < possibleCaptures.length; i++) {
        if (possibleCaptures[i][0] == id) {
            return true;
        }
    }
    return false;
}

function getEnemyColor(color) {
    if (color === 'b')
        return 'w';
    else if (color === 'w')
        return 'b';
}

function getMyCheckersIds(color) {
    var result = [];
    for (var i = 0; i < 64; i++)
        if ($("#" + i).text() == myColor || $("#" + i).text() == myColor + "1")
            result.push(i);
    return result;
}

function clearMovesValues() {
    ClearPickedFieldAndPossibleMoves();
    pickedId = null;
    isFieldPicked = false;
    possibleMovesIds = [];
    possibleCaptures = [];
    isPossibleCapture = false;
}

function shouldChangeOnQueen(id, color) {
    if (color == 'w' && id >= 56 && id <= 63) {
        return 1
    }
    else if (color == 'b' && id >= 0 && id <= 7) {
        return 1;
    }
    return 0;
}

function IsPickedIdWasPossible(id, possibleMovesIds) {
    for (var i = 0; i < possibleMovesIds.length; i++)
        if (possibleMovesIds[i] == id)
            return true;
    return false;
}

function PickFieldWithPossibleMoves(id, whoseTurn, isQueen) {
    if (pickedId && checkIsPossibleIdsAreInRange(possibleMovesIds)) {
        ClearPickedFieldAndPossibleMoves();
    }

    if (whoseTurn == 'w' && isQueen === false) {
        possibleMovesIds = GetPossibleMovesIdsForWhite(id);
    }
    else if (whoseTurn == 'b' && isQueen === false) {
        possibleMovesIds = GetPossibleMovesIdsForBlack(id);
    }
    else if (isQueen == true) {
        if (!GetPossibleMovesIdsForWhite(id) && !GetPossibleMovesIdsForBlack(id))
            possibleMovesIds = [];
        else if (!GetPossibleMovesIdsForWhite(id))
            possibleMovesIds = GetPossibleMovesIdsForBlack(id);
        else if (!GetPossibleMovesIdsForBlack(id))
            possibleMovesIds = GetPossibleMovesIdsForWhite(id);
        else
            possibleMovesIds = GetPossibleMovesIdsForWhite(id).concat(GetPossibleMovesIdsForBlack(id));
    }

    if (!possibleMovesIds) {
        pickedId = null;
        isFieldPicked = false;
        possibleMovesIds = [];
        return;
    }

    pickedId = id;
    isFieldPicked = true;

    if (possibleMovesIds != null && checkIsPossibleIdsAreInRange(possibleMovesIds)) {
        ColorPossibleMovesFields(id, possibleMovesIds);
    }
}

function checkIsPossibleIdsAreInRange(possibleMovesIds) {
    for (var i = 0; i < possibleMovesIds.length; i++) {
        if (possibleMovesIds[i] < 0)
            return false;
        else if (possibleMovesIds[i] < 0)
            return false;
        else if (possibleMovesIds[i] > 63)
            return false;
        else if (possibleMovesIds[i] > 63)
            return false;
    }

    return true;

}

function GetPossibleMovesIdsForWhite(id) {
    var result = [];
    // zwraca tablice z id pol na które można ruszyc pionek
    if (parseInt(7) + parseInt(id) <= 63) {
        var left = document.getElementById(parseInt(7) + parseInt(id)).innerHTML;
    }
    if (parseInt(9) + parseInt(id) <= 63) {
        var right = document.getElementById(parseInt(9) + parseInt(id)).innerHTML;
    }
    if (id % 8 === 0 && !right) {
        if (parseInt(9) + parseInt(id) < 64) {
            return result = [
                parseInt(9) + parseInt(id),
            ];
        }
    }
    else if ((parseInt(id) + parseInt(1)) % 8 === 0 && !left) {
        if (parseInt(7) + parseInt(id) < 64) {
            return result = [
                parseInt(7) + parseInt(id),
            ];
        }
    }
    else if (id % 8 !== 0 && (parseInt(id) + parseInt(1)) % 8 !== 0) {
        if (!left && !right) {
            if (parseInt(7) + parseInt(id) < 64 && parseInt(9) + parseInt(id) < 64) {
                return result = [
                    parseInt(7) + parseInt(id),
                    parseInt(9) + parseInt(id)
                ];
            }
        }
        else if (left && !right) {
            if (parseInt(9) + parseInt(id) < 64) {
                return result = [
                    parseInt(9) + parseInt(id),
                ];
            }
        }
        else if (right && !left) {
            if (parseInt(7) + parseInt(id) < 64) {
                return result = [
                    parseInt(7) + parseInt(id),
                ];
            }
        }
    }
    else
        return []
}

function GetPossibleMovesIdsForBlack(id) {
    // zwraca tablice z id pol na które można przesunac pionek
    var result = [];
    if (parseInt(id) - parseInt(9) > 0) {
        var left = document.getElementById(parseInt(id) - parseInt(9)).innerHTML;
    }
    if (parseInt(id) - parseInt(7) > 0) {
        var right = document.getElementById(parseInt(id) - parseInt(7)).innerHTML;
    }

    if (id % 8 === 0 && !right) {
        if (parseInt(id) - parseInt(7) > 0) {
            return result = [
                parseInt(id) - parseInt(7),
            ];
        }
    }
    else if ((parseInt(id) + parseInt(1)) % 8 === 0 && !left) {
        if (parseInt(id) - parseInt(9) > 0) {
            return result = [
                parseInt(id) - parseInt(9),
            ];
        }
    }
    else if (id % 8 !== 0 && (parseInt(id) + parseInt(1)) % 8 !== 0) {
        if (!left && !right) {
            if (parseInt(id) - parseInt(9) > 0 && parseInt(id) - parseInt(7) > 0) {
                return result = [
                    parseInt(id) - parseInt(7),
                    parseInt(id) - parseInt(9)
                ];
            }
        }
        else if (left && !right) {
            if (parseInt(id) - parseInt(7) > 0) {
                return result = [
                    parseInt(id) - parseInt(7),
                ];
            }
        }
        else if (right && !left) {
            if (parseInt(id) - parseInt(9) > 0) {
                return result = [
                    parseInt(id) - parseInt(9),
                ];
            }
        }
    }
    else
        return []
}

function ClearPickedFieldAndPossibleMoves() {
    document.getElementById(pickedId).style.backgroundColor = 'rgb(54, 52, 52)';
    for (var i = 0; i < possibleMovesIds.length; i++)
        document.getElementById(possibleMovesIds[i]).style.backgroundColor = 'rgb(54, 52, 52)';
}

function ColorPossibleMovesFields(id) {
    document.getElementById(id).style.backgroundColor = 'green';
    for (var i = 0; i < possibleMovesIds.length; i++)
        document.getElementById(possibleMovesIds[i]).style.backgroundColor = 'yellow';
}

function ColorPossibleCapsFields() {
    for (var i = 0; i < possibleCaptures.length; i++) {
        document.getElementById(possibleCaptures[i][0]).style.backgroundColor = 'green';
        for (var j = 0; j < possibleCaptures[i].length; j++) {
            if (j != 0)
                document.getElementById(possibleCaptures[i][j]).style.backgroundColor = 'red';
        }
    }
}