//topographer.js
//by: Harrison Golden

//math
var mathMax, mathMin, increment, mathDifference, mathIncrement, equation;

//graph canvas
var graphCanvas, graphCTX;
var graphCanvasLength = 500;	//square
var axisLength;

//scale canvas
var scaleCanvas, scaleCTX;
var scaleCanvasWidth = 200;
var scaleCanvasHeight = 500;

//timer
var timerIndex;

//array/matrix
var math = new Array(graphCanvasLength);
var bitmap = new Array(graphCanvasLength);

//html 5
var colorMax, colorMiddle, colorMin;
var isHTML5 = false;

//called by <onLoad>
function init(){
	graphCanvas = document.getElementById('graphCanvas');
	graphCTX = graphCanvas.getContext('2d');
	scaleCanvas = document.getElementById('scaleCanvas');
	scaleCTX = scaleCanvas.getContext('2d');
	browserCheck();
	clearGraph();
	fillColorScale();
}

//called by <onClick>
function start(){
	if(checkFields()){
		timerIndex = Date.now();
		processData();
		doMath();
		generateBitmap();
		fillGraph();
		fillLabels();
	}
	else	alert("Please enter valid data.");
}

//called by <onClick> and init();
function clearAll(){
	document.getElementById('axisLength').value = "";
	document.getElementById('scaleMax').innerHTML = "Max = ?";
	document.getElementById('scaleMin').innerHTML = "Min = ?";
	document.getElementById('timer').innerHTML = "Time to graph = ?";
	clearGraph();
}

//called by clearAll(); and init();
function clearGraph(){
	graphCTX.fillStyle = '#FFFFFF';
	graphCTX.fillRect(0, 0, 500, 500);
	graphCTX.fillStyle = '#FFFFFF';
	graphCTX.moveTo(250, 0);
	graphCTX.lineTo(250, 500);
	graphCTX.stroke();
	graphCTX.moveTo(0, 250);
	graphCTX.lineTo(250, 250);
	graphCTX.stroke();
	graphCTX.fillStyle = "#FF0000";
	graphCTX.fillRect(251, 249, 250, 2);
	graphCTX.fillStyle = "#000000";
	graphCTX.font="15px Arial";
	graphCTX.fillText("Axis Length", 330, 240);
}

//called by start();
function processData(){
	axisLength = document.getElementById('axisLength').value;
	increment = axisLength / graphCanvasLength * 2;
	increment = Math.round(increment * 10000) / 10000;
	equation = document.getElementById('equation').value;
}

//called by start();
function doMath(){
	var x = (-1 * axisLength);
	var y = axisLength;
	var temp = 0;
	for(var r = 0;r < graphCanvasLength;r++){
		math[r] = new Array(graphCanvasLength);
		for(var c = 0;c < graphCanvasLength ;c++){
			if(equation == 0)	temp = Math.cos(-1 * ((x * x) + (y * y)));
			else if (equation == 1)	temp = (Math.cos(x) * Math.cos(y) * Math.pow(2.718, (-1 * Math.sqrt((x * x) + (y * y)) / 4)));
			else if (equation == 2)	temp = (Math.tan(Math.cos(Math.sin(x * y))) * Math.sin(Math.cos(x - y)) * Math.sin(Math.cos(x - y)));
			else if (equation == 3)	temp = Math.sin(x) * Math.sin(y);
			else if (equation == 4)	temp = (-4 * x) / (x * x + y * y + 1);
			math[r][c] = Math.round(temp * 100) / 100;
			x = Math.round((x + increment) * 10000) / 10000;
		}
		y = Math.round((y - increment) * 10000) / 10000;
		x = (-1 * axisLength);
	}
	mathMax = findMathMax();
	mathMin = findMathMin();
	mathDifference = mathMax - mathMin;
}

//called by start();
function generateBitmap(){
	var colorTemp, mathTemp, calcTemp;
	for(var r = 0;r < graphCanvasLength;r++){
		bitmap[r] = new Array(graphCanvasLength);
		for(var c = 0;c < graphCanvasLength;c++){
			mathTemp = math[r][c];
			calcTemp = (mathMax - mathTemp) / mathDifference;
			colorTemp = scaleCTX.getImageData(100, (498 * calcTemp), 1, 1);
			bitmap[r][c] = colorTemp;
		}
	}
}

//called by start();
function fillGraph(){
	for(var r = 0;r < graphCanvasLength;r++){
		for(var c = 0;c < graphCanvasLength;c++){
			graphCTX.putImageData(bitmap[r][c], r, c);
		}
	}
}

//called by start();
function fillLabels(){
	document.getElementById('scaleMax').innerHTML = "Max = " + mathMax.toString();
	document.getElementById('scaleMin').innerHTML = "Min = " + mathMin.toString();
	document.getElementById('timer').innerHTML = "Time to graph = " + (Date.now() - timerIndex) / 1000 + " seconds";
}

//called by doMath();
function findMathMax(){
	var winner = Number.MIN_VALUE;
	for(var r = 0;r < graphCanvasLength;r++){
		for(var c = 0;c < graphCanvasLength;c++){
			winner = Math.max(winner, math[r][c]);
		}
	}
	return winner;
}

//called by doMath();
function findMathMin(){
	var winner = Number.MAX_VALUE;
	for(var r = 0;r < graphCanvasLength;r++){
		for(var c = 0;c < graphCanvasLength;c++){
			winner = Math.min(winner, math[r][c]);
		}
	}
	return winner;
}

//called by start();
function checkFields(){
	if(document.getElementById('axisLength').value <= 0){
		document.getElementById('axisLength').value = "";
		return false;
	}
	else return true;
}

//called by <onKeyDown>
function keyDown(){
	if(window.event.keyCode == 13)	start();
}

//called by init();
function browserCheck(){
	var isChrome = navigator.userAgent.toLowerCase().indexOf("chrome") > -1;
	var browserName = navigator.appName;
	if(isChrome || browserName == "Opera")	isHTML5 = true;
	else	document.getElementById('colorInput').style.display = "none";
	if(isChrome){
		document.getElementById('colorMiddle').style.marginTop = "212px";
		document.getElementById('colorMin').style.marginTop = "213px";
		document.getElementById('canvasWrapper').style.width = "791px";
	}
	if(browserName == "Opera"){
		document.getElementById('canvasWrapper').style.width = "776px";
		document.getElementById('canvasWrapper').style.height = "552px";
	}
}

//called by init(); and <onChange>
function fillColorScale(){
	if(isHTML5){
		colorMax = document.getElementById('colorMax').value;
		colorMiddle = document.getElementById('colorMiddle').value;
		colorMin = document.getElementById('colorMin').value;
		var gradient = scaleCTX.createLinearGradient(100, 0, 100, 500);
		gradient.addColorStop(0, colorMax);
		gradient.addColorStop(0.5, colorMiddle);
		gradient.addColorStop(1, colorMin);
		scaleCTX.fillStyle = gradient;
		scaleCTX.fillRect(0, 0, 200, 500);
	}
	else{
		var image = new Image();
		image.src = "topographerAssets/color_scale.jpg";
		var canvas = document.getElementById('scaleCanvas');
		var graphCTX = canvas.getContext("2d");
		image.onload = function(){graphCTX.drawImage(image, 0, 0);};
	}
}