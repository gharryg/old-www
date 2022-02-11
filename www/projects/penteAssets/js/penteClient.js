var canvas, context;
var boardX, boardY;

var whitePiece = new Image();
whitePiece.src = "penteAssets/images/checkerWhite.png";
var blackPiece = new Image();
blackPiece.src = "penteAssets/images/checkerBlack.png";
var loseOverlay = new Image();
loseOverlay.src = "penteAssets/images/loseScreen.png";
var winOverlay = new Image();
winOverlay.src = "penteAssets/images/winScreen.png";
var lightning = new Image();
lightning.src = "penteAssets/images/lightning.png";

var isConnected = false;
var isPlaying = false;
var isTurn = false;
var isFirstGame = true;
var isInQueue = false;
var hasTimedOut = false;

var playerCaptures = 0;
var opponentCaptures = 0;

var PenteServer;

var opponentName;

var inactivityTimer;

var statusE, playerE, opponentE, queueE, queueStatusE, totalPlayersE;

var board0 = "";


$(document).ready(function(){
    canvas = $('#pieces').get(0);
    canvas.addEventListener('click', onClick, false);
    context = canvas.getContext('2d');

    statusE = document.getElementById('status');
    playerE = document.getElementById('player');
    opponentE = document.getElementById('opponent');
    queueE = document.getElementById('queue');
    queueStatusE = document.getElementById('queueStatus');
    totalPlayersE = document.getElementById('total');

    setTimeout(function(){
        $('#overlay').transition({"opacity": '1'});
        $('#leaderboard').transition({"opacity": '1'});
        $('#board').transition({"opacity": '1'});
    }, 750);

    initWebsockets();

    if(jQuery.browser.mobile)   window.addEventListener('pagehide', mobileBlur, false);
});

function mobileBlur(){
    disconnect();
    setTimeout(function(){
        setStatus("You can not exit Pente on a mobile device. You have been disconnected.");
    }, 100);
}

function joinQueue(){
    if(!isInQueue && !hasTimedOut && isConnected){
        send("queue");
        isInQueue = true;
    }
}

function setupGame(color, name){
    updateQueueStatus("");
    if(isFirstGame){
        $('#overlay').transition({"opacity": '0'});
        setTimeout(function(){
            $('#overlay').remove();
        }, 300);
        isFirstGame = false;
    }
    else{
        playerE.innerHTML = "0";
        opponentE.innerHTML = "0";
        context.clearRect(0, 0, canvas.width, canvas.height);
    }
    opponentName = name;
    if(color == 1){
        setStatus("It's your turn.<br>Your opponent is " + opponentName + ".");
        isTurn = true;
    }
    else    setStatus("<br>It's " + opponentName +"'s turn.");
    isPlaying = true;
    isInQueue = false;
    $('#queue').transition({"opacity": '1'});
    setTimeout(function(){
        document.getElementById('queue').innerHTML = "The queue will appear when you rejoin the queue.";
    }, 300);
}

function onClick(e){
    if(isConnected && !isPlaying && !isInQueue) joinQueue();
    else{
        detectMousePos(e);
        if(!(boardX <= 0 || boardY <= 0 || boardX > 17 || boardY > 17 || !isTurn || !isPlaying))    send("click-" + boardX + "-" + boardY);
    }
}

function detectMousePos(e){
    var rect = canvas.getBoundingClientRect();
    boardX = Math.floor((e.clientX - rect.left + 20) / 40);
    boardY = Math.floor((e.clientY - rect.top + 20) / 40);
}

function updateGraphics(board){
    board0 = board;
    context.clearRect(0, 0, canvas.width, canvas.height);
    var ndx = 0;
     for(var c = 1;c <= 17;c++){
        for(var r = 1;r <= 17;r++){
            switch(board[ndx]){
                case "0":
                    break;
                case "1":
                    context.drawImage(whitePiece, c * 40 - 22, r * 40 - 19);
                    break;
                case "2":
                    context.drawImage(blackPiece, c * 40 - 22, r * 40 - 19);
                    break;
                default:
                    break;
            }
            ndx++;
        }
    }
    context.drawImage(lightning, board[ndx] * 40 - 18, board[ndx + 1] * 40 - 19);
}

function toggleTurn(result){
    if(isTurn){
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(function(){timeout();}, 330000);
        if(result == 1){
            context.drawImage(winOverlay, 0, 0);
            setTimeout(function(){
                updateGraphics(board0);
            }, 3000);
        }
        else{
            isTurn = false;
            setStatus("<br>It's " + opponentName +"'s turn.");
        }
    }
    else{
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(function(){timeout();}, 300000);
        if(result == 1){
            context.drawImage(loseOverlay, 0, 0);
            setTimeout(function(){
                updateGraphics(board0);
            }, 3000);
        }
        else{
            isTurn = true;
            setStatus("<br>It's your turn.");
        }
    }
    if(result == 1){
        isPlaying = false;
        isTurn = false;
        opponentCaptures = 0;
        opponentName = "";
        playerCaptures = 0;
        setStatus("<br>Click anywhere to play again.");
    }
}

function addCapture(num){
    var captures = parseInt(num);
    if(isTurn){
        playerCaptures = captures;
        playerE.innerHTML = playerCaptures;
    }
    else{
        opponentCaptures = captures;
        opponentE.innerHTML = opponentCaptures;
    }
}

function timeout(){
    hasTimedOut = true;
    disconnect();
    alert("You have gone 5 minutes without any activity. You have been disconnected.");
}

function setStatus(message){
    statusE.innerHTML = message;
}

function sendFriendRequest(queueID){
    if(isInQueue){
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(function(){timeout();}, 300000);
        send("request-" + queueID);
        setStatus("Waiting for an opponent to choose you.");
        updateQueueStatus("Waiting for an opponent to choose you.")
        $('#queue').transition({"opacity": '.5'});
    }
}

function updateQueue(queue){
    queueE.innerHTML = queue;
    $('#queue').transition({"opacity": '1'});
}

function updateQueueStatus(message){
    queueStatusE.innerHTML = message;
}

//websocekts------------------------------------------------------------------------------------------------------------
function initWebsockets(){
    if(!isConnected){
        if(isFirstGame){
            var html = document.getElementById('playerID').innerHTML;
            $('#playerID').remove();
            var playerID = parseInt(html);
        }
        try{
            var host = "ws://localhost:8008";
            PenteServer = new WebSocket(host);

            PenteServer.onopen = function(message){
                try{
                    PenteServer.send("id-" + playerID);
                }
                catch(e){
                    alert("Unable to communicate with server. Error: " + e.message);
                }

                isConnected = true;
                inactivityTimer = setTimeout(function(){timeout();}, 300000);
                setStatus("Click anywhere on the board to join the queue.<br>Scroll down to see game info.");
            };

            PenteServer.onmessage = function(message){
                var data = message.data.split('-');
                switch(data[0]){
                    case 'total':
                        totalPlayersE.innerHTML = data[1];
                        break;
                    case 'match':
                        clearTimeout(inactivityTimer);
                        inactivityTimer = setTimeout(function(){timeout();}, 300000);
                        setupGame(data[1], data[2]);
                        break;
                    case 'queue':
                        clearTimeout(inactivityTimer);
                        inactivityTimer = setTimeout(function(){timeout();}, 300000);
                        setStatus("You have been added to the queue. Scroll down to see the player queue.");
                        updateQueueStatus("Choose an opponent.");
                        break;
                    case 'opponentDC':
                        clearTimeout(inactivityTimer);
                        inactivityTimer = setTimeout(function(){timeout();}, 300000);
                        isPlaying = false;
                        isTurn = false;
                        context.drawImage(winOverlay, 0, 0);
                        setStatus("Click anywhere on the board to join the queue.");
                        alert("Your opponent has disconnected.");
                        break;
                    case 'movePlayed':
                        var board = data[1].split(',');
                        updateGraphics(board);
                        toggleTurn(data[2]);
                        break;
                    case 'badMove':
                        alert("You can't play there!");
                        break;
                    case 'error':
                        disconnect();
                        alert("There has been a server error. You have been disconnected. Please report this incident.");
                        break;
                    case 'capture':
                        clearTimeout(inactivityTimer);
                        inactivityTimer = setTimeout(function(){timeout();}, 300000);
                        addCapture(data[1]);
                        break;
                    case 'ban':
                        window.location = "/banned";
                        break;
                    case 'list':
                        updateQueue(data[1]);
                        break;
                    case 'backstabber':
                        setStatus("Your friend has left you. Select a new opponent.");
                        updateQueueStatus("Your friend has left you. Select a new opponent.")
                        $('#queue').transition({"opacity": '1'});
                        break;
                    case 'badRequest':
                        setStatus("Can't play yourself!<br>Select a different opponent.");
                        updateQueueStatus("Can't play yourself! Select a different opponent.")
                        $('#queue').transition({"opacity": '1'});
                        break;
                    case 'request':
                        setStatus("<br>" + data[1] + " wants to play.");
                        updateQueueStatus(data[1] + " wants to play.")
                        break;
                    case 'reload':
                        $('#leaderboard').transition({"opacity": '0'});
                        setTimeout(function(){
                            document.getElementById('leaderboard').contentWindow.location.reload();
                        }, 300);
                        setTimeout(function(){
                            $('#leaderboard').transition({"opacity": '1'});
                        }, 750);
                        break;
                    case 'broadcast':
                        alert(data[1]);
                        break;
                    default:
                        break;
                }
            };

            PenteServer.onclose = function(message){
                clearTimeout(inactivityTimer);
                isConnected = false;
                isPlaying = false;
                isTurn = false;
                setStatus("<br>No connection to the server.");
                $('#totalWrapper').remove();
            };

            PenteServer.onerror = function(error){
                console.log("An error has occurred on the server: " + error + " Please report this incident.");
            };
        }
        catch(e){
            alert("Unable to connect to the server. " + e.message);
        }
    }
}

function send(message){
    try{
        PenteServer.send(message);
    }
    catch(e){
        alert("Unable to communicate with the server. " + e.message);
    }
}

function disconnect(){
    if(PenteServer != null){
        PenteServer.close();
        PenteServer = null;
    }
}
