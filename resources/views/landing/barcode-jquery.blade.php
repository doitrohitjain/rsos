<html>
  
<head>
    <title>Barcode Generator</title>
    <style>
        body {
            max-width: 100%;
            text-align: center;
        }
  
        .mainDiv {
            background: green;
            font-family: Arial;
            padding: 25px;
            max-height: 73s0px;
            width: 300px;
            text-align: justify;
            display: flex;
            flex-direction: column;
            margin: 20px auto;
        }
  
        .mainDiv .row {
            margin-bottom: 20px;
            overflow: hidden;
        }
  
        label {
            margin: 5px;
            color: lightgrey;
        }
  
        h2 {
            margin-bottom: 10px;
            color: white
        }
  
        .input_box {
            padding: 10px;
            border: none;
            background-color: white;
            width: 100%;
            margin-top: 5px;
        }
  
        .button {
            background-color: grey;
            padding: 10px 40px;
            color: white;
            border: none;
        }
    </style>
	
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	
	<script src="{!! asset('public/app-assets/js/barcode/jquery-barcode.js') !!}"></script>
	<script src="{!! asset('public/app-assets/js/barcode/jquery-barcode.min.js') !!}"></script>

</head>
  
<body>
    <h1>Barcode Generator using JQuery</h1>
    <div class="mainDiv">
        <div class="row">
            <label>Type The Text</label>
            <br/>
            <input type="text" id="textValue" value="01020223001" class="input_box">
        </div>
        <div class="row">
            <!-- Different types of barcode -->
            <div>
                <h2>Choose Barcode Type:</h2>
                <input type="radio" name="barcodeType" value="ean8" checked="checked">
                <label>EAN 8</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="ean13">
                <label>EAN 13</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="datamatrix">
                <label>Data Matrix (2D barcode)</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="upc">
                <label>UPC</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="code11">
                <label>code 11</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="code39">
                <label>code 39</label>
				<br/>
				
                <input type="radio" name="barcodeType" value="code93">
                <label>code 93</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="code128">
                <label>code 128</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="codabar">
                <label>codabar</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="std25">
                <label>standard 2 of 5 (industrial)</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="int25">
                <label>interleaved 2 of 5</label>
                <br/>
				
                <input type="radio" name="barcodeType" value="msi">
                <label>MSI</label>
                <br/>
            </div>
  
            <!-- Different renderer types of the barcode -->
            <div>
                <h2>Choose Barcode Format</h2>
                <input type="radio" name="rendererType" value="css" checked="checked">
                <label>CSS</label>
                <br/>
				
                <input type="radio" name="rendererType" value="canvas">
                <label>Canvas</label>
                <br/>
				
                <input type="radio" name="rendererType" value="bmp">
                <label>BMP</label>
                <br/>
				
                <input type="radio" name="rendererType" value="svg">
                <label>SVG</label>
                <br/>
            </div>
        </div>
        <div class="row">
            <input type="button" onclick="generateBarcode();"
                   value="Generate the barcode" class="button">
        </div>
        <div class="row">
            <div id="barcodeoutput"></div>
            <canvas id="canvasOutput" width="200" height="130"></canvas>
        </div>
    </div>
  
    <script type="text/javascript">
  
        //  Function to generate the barcode
        function generateBarcode() {
  
            // Taking input values from the user
            
            // Text value
            var inputValue = $("#textValue").val(); 
            // Barcode type
            var barcodeType = $("input[name=barcodeType]:checked").val(); 
            // Renederer type
            var rendererType = $("input[name=rendererType]:checked").val(); 
  
            // Settings to generate barcode
            var settings = {
                output: rendererType,
                bgColor: '#FFFFFF',
                color: '#000000',
                barWidth: '1.5',
                barHeight: '70',
                moduleSize: '5',
                posX: '15',
                posY: '30',
                addQuietZone: '1'
            };
  
            if (rendererType != 'canvas') {
                // If renderer type is not canvas, show barcodeoutput div and 
                // add output from barcode() function to that div
                $("#canvasOutput").hide();
                $("#barcodeoutput").html("").show();
                $("#barcodeoutput").barcode(inputValue, barcodeType, settings);
            } else {
                // If renderer type is canvas, create new canvas by 
                // clearing previous one, and add the output generated 
                // from barcode() function to new canvas
                createCanvas();
                $("#barcodeoutput").hide();
                $("#canvasOutput").show();
                $("#canvasOutput").barcode(inputValue, barcodeType, settings);
            }
        }
  
        // Function to clear canvas.
        function createCanvas() {
  
            // Get canvas element from HTML
            var canvas = $('#canvasOutput').get(0);
  
            // Add 2d context to canvas
            var ctx = canvas.getContext('2d');
  
            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.strokeRect(0, 0, canvas.width, canvas.height);
        }
    </script>
</body>
  
</html>