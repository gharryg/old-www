<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . "/required/phpHead.php");
?>
<!DOCTYPE html>
<html>
    <head>
    	<title>gharryg.com | Topographical Grapher</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/head.php") ?>
        <script type="text/javascript" src="topographerAssets/topographer.js"></script>
        <link href="topographerAssets/topographer.css" rel="stylesheet" type="text/css">
    </head>
    <body onLoad="init();">
    	<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/topBar.php") ?>
        <?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/noscript.php") ?>
        <div id="content">
        	<div class="title">Topographical Grapher.</div>
        	<section>
                <div id="canvasWrapper">
                    <div id="colorInput">
                        <input id="colorMax" type="color" value="#FF0000" onChange="fillColorScale();">
                        <br/>
                        <input id="colorMiddle" type="color" value="#FF00FF" onChange="fillColorScale();">
                        <br/>
                        <input id="colorMin" type="color" value="#FFFF00" onChange="fillColorScale();">
                    </div>
                    <div id="scale">
                        <div id="scaleMax">Max = ?</div>
                        <canvas id="scaleCanvas" width="200" height="500"></canvas>
                        <div id="scaleMin">Min = ?</div>
                    </div>
                    <canvas id="graphCanvas" width="500" height="500"></canvas>
                </div>
                <div id="dataInput">
                    Equation:
                    <select id="equation">
                        <option value="0">z = cos(-(x * x + y * y))</option>
                        <option value="1">z = cos(x) * cox(y) * 2.718^(-sqrt((x * x + y * y) / 4))</option>
                        <option value="2">z = tan(cos(sin(x * y))) * sin(cos(x - y)) * sin(cos(x - y))</option>
                        <option value="3">z = sin(x) * sin(y)</option>
                        <option value="4">z = -4x / (x * x + y * y + 1)</option>
                    </select>
                    <br>
                    Axis Length: <input type="number" step="0.5" min="0" id="axisLength" onKeydown="keyDown();"/> **Axis Length &lt; 0**<br/>
                    <input type="button" onClick="clearAll();" value="Clear"/>
                    <input type="button" onClick="start();" value="Graph"/>
                    <br>
                    <div id="timer">Time to graph = ?</div>
                </div>
                <div id="info">
                    <h3>What is a Topographical Graph?</h3>
                    A Topographical Graph is a 3D graph in a 2D context.
                    <h3>What am I doing?</h3>
                    First, choose the equation of the graph you want to see. Then enter an axis length to determine how much of the graph you will see. <strong>The axis length must be larger than zero.</strong> Then click Graph. If you are using Google Chrome or Opera, feel free to change the colors of the scale with the color changers on the right. When you are done, click Clear to clean the page and repeat the process.
                    <h3>It's slow.</h3>
                    Yes. I know. This script is having to calculate 250,000 values and then pull a specific color for each one and then put that color onto the canvas. Here is a list of browsers from best to worst to view this page.
                    <ol>
                        <li>Opera</li>
                        <li>Firefox</li>
                        <li>Safari</li>
                        <li>Google Chrome</li>
                    </ol>
                </div>
            </section>
			<?php include_once($_SERVER['DOCUMENT_ROOT'] . "/required/footer.php") ?> 
        </div>
	</body>
</html>