<h1>Pallette</h1>

@if(isset($pallette))
    <ul>
        <li>{{ $pallette->file_name }}</li>
    </ul>
@else
    <p>No histogram available</p>
@endif

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Colors</title>
    <style>
        .colorBox {
            width: 100px;
            height: 100px;
            margin: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>

<div id="colorContainer"></div>

<script>
    // Array of colors
    var colorsArray = {{ $pallette->file_name }}

    // Get the element with the id 'colorContainer'
    var colorContainer = document.getElementById('colorContainer');

    // Loop through each color and create a div for each
    colorsArray.forEach(function(color) {
        // Create an RGB color string
        var rgbColor = 'rgb(' + color[0] + ',' + color[1] + ',' + color[2] + ')';

        // Create a div element for the color
        var colorBox = document.createElement('div');
        colorBox.className = 'colorBox';
        colorBox.style.backgroundColor = rgbColor;

        // Append the color box to the container
        colorContainer.appendChild(colorBox);
    });
</script>

</body>
</html>
