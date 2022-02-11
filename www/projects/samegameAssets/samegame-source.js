//samegame.js
//by: Harrison Golden

//canvas
var gameCanvasE, gameCTX;
var gameCanvasWidth = 800;
var gameCanvasHeight = 400;

//coords
var mouseX, mouseY;
var gridX, gridY;

//session
var firstName, lastName, personalBest, highScore;

//game
var isPlaying = false;
var gridHeight = 10;
var gridWidth = 20;
var blockSize = gameCanvasWidth / gridWidth;
var selected = false;
var imageDump = new Array(11);
var blocks = new Array(gridWidth);
var blockImages = new Array(gridWidth);
var neighbors = new Array();
var blocks0 = new Array();
var seed;
var isCustomSeed = false;
var isFirstTime = true;

//block counts
var blocksA = 0;
var blocksB = 0;
var blocksC = 0;
var blocksD = 0;
var blocksE = 0;
var blocksLeft = 0;

function init(firstName0, lastName0, highScore0, personalBest0){
    gameCanvasE = document.getElementById('game');
    gameCTX = gameCanvasE.getContext('2d');
    $('#leaderboard').transition({ opacity: 1, delay: 1000});
	if(checkBrowser()){
		loadImages();
		firstName = firstName0;
		lastName = lastName0;
		personalBest = parseInt(personalBest0);
		highScore = parseInt(highScore0);
		preGame();
	}

    //taken from: http://davidwalsh.name/window-iframe
    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
    var eventer = window[eventMethod];
    var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";
    eventer(messageEvent,function(e) {
        updateSeed(false, e.data);
        isCustomSeed = true;
        startGame();
    },false);

    window.onerror = function(){
        alert("There has been an error and the page must be reloaded.")
        window.location = "samegame";
    }
}

//game handlers-------------------------------------------------------------

function preGame(){
	gameCTX.fillStyle = "#FFFFFF";
	gameCTX.fillRect(0, 0, gameCanvasWidth, gameCanvasHeight);
	gameCTX.fillStyle = "#000000";
	gameCTX.font = "24px aerial";
	if(firstName != -1){
		gameCTX.fillText("Welcome to Same Game.", 170, 120);
		gameCTX.fillText("Click 'New Game' to begin.", 140, 250);
	}
	else{
		gameCTX.fillText("Welcome to Same Game, Guest.", 95, 100);
		gameCTX.fillText("Sign in or register to have your", 55, 150);
		gameCTX.fillText("scores uploaded to the leaderboard.", 25, 180);
		gameCTX.fillText("Click 'New Game' to begin.", 140, 280);
	}
    clearScores();
    $('#loading').remove();
}

function startGame(){
	if(!isPlaying){
        if(isFirstTime && !isCustomSeed) updateSeed(true);
        isFirstTime = false;
		$('#reset').prop('value', 'Reset');
        $('#undo').prop('disabled', true);
        gameCanvasE.addEventListener('click', click, false);
		clearScores();
        clearSelection();
		generateBlocks();
		fillImageArray();
		countColors();
		fillBoard();
        selected = false;
        neighbors = new Array();
        var request = new XMLHttpRequest();
        request.open("POST", "samegameAssets/session.php?function=clearScores", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send();
	}
	else{
		var r = confirm("Are you sure? Your current progress will be lost.")
		if(r == true){
			isPlaying = false;
			startGame();
		}
	}
}

function click(e){
	isPlaying = true;
	getCursorPosition(e);
	gridX = Math.floor(mouseX / blockSize);
	gridY = Math.floor(mouseY / blockSize);
    checkClickBounds();
	if(!selected){
		if(blocks[gridX][gridY].id != 5){
			findNeighbors(gridX, gridY);
            var len = neighbors.length;
			if(len > 1){
				updateImageArray();
                var selection = (Math.pow(len, 2) - (3 * len) + 4);
                var request = new XMLHttpRequest();
                request.open("POST", "samegameAssets/session.php?function=" + "putSelection" + "&selection=" + selection, true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send();
                document.getElementById('csv').innerHTML = selection;
				selected = true;
			}
			else    neighbors = new Array();
		}
	}
	else{
		if(isNeighbor(gridX, gridY)){
			blocks0 = jQuery.extend(true, [], blocks);

            //dummy functions
            checkScore();
            uploadScore();
            teehee();
            teeheeActual();

			updateScore();
			updateBlockArray();
			$('#undo').prop('disabled', false);
		}
		neighbors = new Array();
        clearSelection();
		selected = false;
		fillImageArray();
	}
	fillBoard();
	countColors();
	if(checkEndGame())	endGame();
}

function undo(){
    blocks = blocks0;
    undoScore();
    neighbors = new Array();
    selected = false;
    fillImageArray();
    countColors();
    fillBoard();
    $('#undo').prop('disabled', true);
}

//block handlers-------------------------------------------------------------

function generateBlocks(){  //generates beginning matrix of blocks
    Math.seedrandom(seed);  //applies seed to Math.random()
	for(var c = 0;c < gridWidth;c++){
		blocks[c] = new Array(gridHeight);
		for(var r = 0;r < gridHeight;r++){
			blocks[c][r] = new block(Math.floor(Math.random() * 5));
		}
	}

}

function updateBlockArray(){
    var len = neighbors.length;
    for(var i = 0;i < len;i++){ //replaces selected blocks in neighbors[] with white blocks
        x = neighbors[i].x;
        y = neighbors[i].y;
        blocks[x][y] = new block(5);
    }
    for(var c = 0;c < gridWidth;c++){
        for(var r = gridHeight - 1;r >= 0;r--)  if(blocks[c][r].id == 5) blocks[c].splice(r, 1); //removes white blocks
        var len = blocks[c].length;
        var diff = gridHeight - len;    //number of blocks to be added
        for(var i = 0;i < diff;i++)	blocks[c].unshift(new block(5));    //adds new white blocks to top of column
        empty(c);
    }
    var isNullRemain = true;
    while(isNullRemain){
        isNullRemain = false;
        for(var c = 0;c < gridWidth;c++)    if(blocks[c] == null)   blocks.splice(c, 1);   //removes null columns
        var blocksLength = blocks.length;
        var diff = gridWidth - blocksLength; //number of columns to be added
        var blank = new Array(gridHeight);
        for(var i = 0;i < gridHeight;i++)	blank[i] = new block(5);
        for(var i = 0;i < diff;i++)	blocks.push(blank); //adds all white columns to right hand side
        for(var c = 0;c < gridWidth;c++)	if(blocks[c] == null)	isNullRemain = true;  //repeats loop if necessary
    }
}

function empty(c){
    for(var r = 0;r < gridHeight;r++)	if(blocks[c][r].id != 5) return;
    blocks[c] = null;   //sets all-white columns to null
}

//images/graphics-------------------------------------------------------------

function fillImageArray(){  //fills blockImages[] to be drawn onto <canvas>
    for(var c = 0;c < gridWidth;c++){
        blockImages[c] = new Array(gridHeight);
        for(var r = 0;r < gridHeight;r++){
            blockImages[c][r] = imageDump[(blocks[c][r].image)];
        }
    }
}

function updateImageArray(){    //makes blocks in neighbors[] change to selected image
    var len = neighbors.length;
    var x, y;
    for(var i = 0;i < len;i++){
        x = neighbors[i].x;
        y = neighbors[i].y;
        blockImages[x][y] = imageDump[(blocks[x][y].imageS)];
    }
}

function fillBoard(){   //draws blockImages[] onto the <canvas>
	for(var c = 0;c < gridWidth;c++){
		for(var r = 0;r < gridHeight;r++){
            gameCTX.drawImage(blockImages[c][r], (c * blockSize), (r * blockSize));
		}
	}
}

//neighbor functions-------------------------------------------------------------

function findNeighbors(x, y){
	var temp = blocks[x][y].id;
	neighbors.push(new point(x, y));
	if(x != 0)	if(blocks[x - 1][y].id == temp)	if(!isNeighbor((x - 1), y))	findNeighbors((x - 1), y);  //left
	if(x != gridWidth - 1)	if(blocks[x + 1][y].id == temp)	if(!isNeighbor((x + 1), y))	findNeighbors((x + 1), y);  //right
	if(y != 0)	if(blocks[x][y - 1].id == temp)	if(!isNeighbor(x, (y - 1)))	findNeighbors(x, (y - 1));  //bottom
	if(y != gridHeight - 1)	if(blocks[x][y + 1].id == temp)	if(!isNeighbor(x, (y + 1)))	findNeighbors(x, (y + 1));  //top
}

function isNeighbor(x, y){  //checks to see if a block is in neighbors[]
	var len = neighbors.length;
	var temp;
	for(var i = 0;i < len;i++){
		temp = neighbors[i];
		if(temp.x == x && temp.y == y)	return true;
	}
	return false;
}

function hasNeighbor(x, y){
    var temp = blocks[x][y].id;
    if(x != 0)	if(blocks[x - 1][y].id == temp)	return true;    //left
    if(x != gridWidth - 1)	if(blocks[x + 1][y].id == temp)	return true;    //right
    if(y != 0)	if(blocks[x][y - 1].id == temp)	return true;    //bottom
    if(y != gridHeight - 1)	if(blocks[x][y + 1].id == temp)	return true;    //top
    return false;
}

//end game functions-------------------------------------------------------------

function checkEndGame(){
    for(var c = 0;c < gridWidth;c++)	for(var r = 0;r < gridHeight;r++)	if(blocks[c][r].id != 5)	if(hasNeighbor(c, r))	return false;
    return true;
}

function endGame(){
    updateScoreTable();
    gameCTX.fillStyle = "#FFFFFF";
    gameCTX.fillRect(0, 0, 800, 400);
    gameCTX.fillStyle = "#000000";
    gameCTX.font = "48px aerial";
    gameCTX.fillText("GAME OVER.", 190, 130);
    gameCTX.font = "24px aerial";
    gameCTX.fillText("Your score is " + parseInt(getScore()) + ".", 210, 175);
    gameCTX.fillText("Click 'Reset' to begin a new game.", 45, 280);
    gameCanvasE.removeEventListener('click', click, false);
    document.getElementById('undo').disabled = true;
    isPlaying = false;
    var score = getScore();
    if(firstName != -1){
        var request = new XMLHttpRequest();
        request.open("POST", "samegameAssets/session.php?function=submitGame&blocksLeft=" + blocksLeft, true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send();
        $('#leaderboard').transition({ opacity: 0 });
        setTimeout(function(){
            document.getElementById('leaderboard').contentDocument.location.reload();
            $('#leaderboard').transition({ opacity: 1, delay: 1000});
        }, 1000);
    }
}

//seed functions-------------------------------------------------------------

function generateRandomSeed(){
    var string = "";
    var possible = "BCDFGHJKLMNPQRSTVWXYZbcdfghjklmnpqrstvwxyz23456789";
    for(var i = 0;i < 16;i++){
        string += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return string;
}

function updateSeed(random, newSeed){
    if(random)  seed = generateRandomSeed();
    else seed = newSeed;
    document.getElementById("seed").innerHTML = "Board Seed: " + seed;
    Math.seedrandom(seed);
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=" + "putSeed" + "&seed=" + seed, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
}

function customSeed(){
    var newSeed = "";
    while(!checkSeed(newSeed)){
        newSeed = prompt("Please enter a new alphanumeric seed. Max length is 16 characters.","");
        if(!newSeed)    break;
    }
    if(newSeed){
        updateSeed(false, newSeed);
        isCustomSeed = true;
        startGame();
    }
}

function randomSeed(){
    updateSeed(true);
    startGame();
}

function checkSeed(newSeed){
    var len = newSeed.length;
    if(len == 0 || len > 16) return false;
    var char;
    var allowableChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 ";
    for(var i = 0;i < len;i++){
        char = newSeed.charAt(i);
        if(allowableChars.indexOf(char) == -1)  return false;
    }
    return true;
}

//custom objects-------------------------------------------------------------

function block(ref){
    if(ref == 0){   //A
        this.image = 0;
        this.imageS = 1;
        this.id = 0;
    }
    else if(ref == 1){  //B
        this.image = 2;
        this.imageS = 3;
        this.id = 1;
    }
    else if(ref == 2){  //C
        this.image = 4;
        this.imageS = 5;
        this.id = 2;
    }
    else if(ref == 3){  //D
        this.image = 6;
        this.imageS = 7;
        this.id = 3;
    }
    else if(ref == 4){  //E
        this.image = 8;
        this.imageS = 9;
        this.id = 4;
    }
    else if(ref == 5){  //empty block
        this.image = 10;
        this.id = 5
    }
}

function point(x1, y1){
    this.x = x1;
    this.y = y1;
}

//utility functions-------------------------------------------------------------

function loadImages(){
    const blocksFiles = ["blockA.jpg", "blockAS.jpg", "blockB.jpg", "blockBS.jpg", "blockC.jpg", "blockCS.jpg", "blockD.jpg", "blockDS.jpg", "blockE.jpg", "blockES.jpg", "blockW.jpg"];
    for(var i = 0;i <= 10;i++){
        imageDump[i] = new Image();
        imageDump[i].src = "/projects/samegameAssets/blocks/" + blocksFiles[i]
    }
}

function checkBrowser(){
    return true;
    if((navigator.userAgent.toLowerCase().indexOf('firefox') > -1)){
        gameCTX.fillStyle = "#FFFFFF";
        gameCTX.fillRect(0, 0, gameCanvasWidth, gameCanvasHeight);
        gameCTX.fillStyle = "#000000";
        gameCTX.font = "24px aerial";
        gameCTX.fillText("Due to the way Firefox", 150, 100);
        gameCTX.fillText("handles DOM events,", 195, 130);
        gameCTX.fillText("Same Game is not supported", 110, 160);
        gameCTX.fillText("on this browser.", 230, 190);
        gameCTX.fillText("Sorry for the inconvenience.", 105, 280);
        document.getElementById('reset').disabled = true;
        document.getElementById('undo').disabled = true;
        document.getElementById('customSeed').disabled = true;
        return false;
    }
    else return true;
}

//taken from: http://answers.oreilly.com/topic/1929-how-to-use-the-canvas-and-draw-elements-in-html5/
function getCursorPosition(e){
    var x, y;
    if (e.pageX || e.pageY){
        x = e.pageX;
        y = e.pageY;
    }
    else{
        x = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
        y = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
    }
    x -= gameCanvasE.offsetLeft;
    y -= gameCanvasE.offsetTop;
    mouseX = x;
    mouseY = y;
}

function countColors(){
    blocksA = 0;
    blocksB = 0;
    blocksC = 0;
    blocksD = 0;
    blocksE = 0;
    for(var c = 0;c < gridWidth;c++){
        for(var r = 0;r < gridHeight;r++){
            if(blocks[c][r].id == 0)	blocksA++;
            else if(blocks[c][r].id == 1)	blocksB++;
            else if(blocks[c][r].id == 2)	blocksC++;
            else if(blocks[c][r].id == 3)	blocksD++;
            else if(blocks[c][r].id == 4)	blocksE++;
        }
    }
    blocksLeft = blocksA + blocksB + blocksC + blocksD + blocksE;
    document.getElementById('a').innerHTML = blocksA;
    document.getElementById('b').innerHTML = blocksB;
    document.getElementById('c').innerHTML = blocksC;
    document.getElementById('d').innerHTML = blocksD;
    document.getElementById('e').innerHTML = blocksE;
    document.getElementById('bl').innerHTML = blocksLeft;
}

function checkClickBounds(){
    if(gridX > gridWidth)	gridX = gridWidth;
    if(gridY > gridHeight)	gridY = gridHeight;
}

//score keeping-------------------------------------------------------------

function updateScore(){
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=" + "postScores", false);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
    document.getElementById('ys').innerHTML = parseInt(request.responseText);
    document.getElementById('csv').innerHTML = 0;
}

function undoScore(){
    var score = getScore0();
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=undoScore", true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
    document.getElementById('ys').innerHTML = score;
    document.getElementById('csv').innerHTML = 0;
}

function updateScoreTable(){
    var score = getScore();
    if(score > personalBest){
        document.getElementById('pb').innerHTML = score;
        personalBest = score;
    }
    if(score > highScore){
        document.getElementById('hs').innerHTML = score;
        highScore = score;
    }
}

//dummy function
function checkScore(){
    var score = 12344567;
    var score0 = 1325683;
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=" + "uploadScores&score=" + score + "&score0" + score0, false);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
}

//server-side-------------------------------------------------------------

function getScore(){
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=getScore", false);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
    return request.responseText;
}

function getScore0(){
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=getScore0", false);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
    return request.responseText;
}

function clearScores(){
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=" + "putScores" + "&score=" + 0 + "&score0=" + 0, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
    document.getElementById('ys').innerHTML = 0;
    document.getElementById('csv').innerHTML = 0;
}

function clearSelection(){
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=" + "putSelection" + "&selection=" + 0, true);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    request.send();
    document.getElementById('csv').innerHTML = 0;
}

//dummy function
function uploadScore(){
    var score = 12344567;
    var score0 = 1325683;
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=" + "uploadScores&score=" + score + "&score0" + score0, false);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
}

//dummy function
function teehee(){
    var score = 12344567;
    var request = new XMLHttpRequest();
    var score0 = 1325683;
    request.open("POST", "samegameAssets/session.php?function=" + "uploadScores&score=" + score + "&score0" + score0, false);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
}

//dummy function
function teeheeActual(){
    var score = 12344567;
    var score0 = 1325683;
    var request = new XMLHttpRequest();
    request.open("POST", "samegameAssets/session.php?function=" + "uploadScores&score=" + score + "&score0" + score0, false);
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
}
