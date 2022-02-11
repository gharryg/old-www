//samegame-offline.js
//by: Harrison Golden

//canvas
var gameCanvasE, gameCTX;
var gameCanvasWidth = 800;
var gameCanvasHeight = 400;

function init(fName, lName, hs, pb){
}

$(document).ready(function(){
    gameCanvasE = document.getElementById('game');
    gameCTX = gameCanvasE.getContext('2d');

    window.onerror = function(){
        alert("Sorry about this, but something has caused an error and the game must be reset.")
        window.location = "samegame";
    }

	gameCTX.fillStyle = "#FFFFFF";
	gameCTX.fillRect(0, 0, gameCanvasWidth, gameCanvasHeight);
	gameCTX.fillStyle = "#000000";
	gameCTX.font = "24px aerial";
    gameCTX.fillText("Welcome to Same Game.", 170, 120);
    gameCTX.fillText("The tournament is over.", 150, 230);
    gameCTX.fillText("Check out the final scores below!", 50, 270);

    $('#reset').prop("disabled", true);
    $('#undo').prop("disabled", true);
    $('#randomSeed').prop("disabled", true);
    $('#customSeed').prop("disabled", true);
    $('#loading').remove();
    $('#leaderboard').transition({ opacity: 1, delay: 2000});
});