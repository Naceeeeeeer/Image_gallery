<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css"/>
    <link href="https://unpkg.com/@tailwindcss/custom-forms/dist/custom-forms.min.css" rel="stylesheet" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Image</title>
    <style>
      

body{
  margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('{{  asset('images/header.png') }}');
            background-size: cover;
            background-position: center; 
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: 'Arial', sans-serif;
}
form {
    text-align: center;
    background: linear-gradient(to right, #B799FF,#687EFF );
    border-radius: 10px;
    padding: 5px;
    max-width: 700px;
    margin: 0 auto;
}

/* Optional: Add some styling for the form elements if needed */
input, button {
    margin: 10px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
}

/* body {
    margin: 0;
    padding: 0;
    height: 100vh;
    background: linear-gradient(to bottom, #FFA500, #000000);
    
    display: flex;
    justify-content: center;
    align-items: center;
    color: white; 
    font-family: 'Arial', sans-serif;
}




form {
    text-align: center;
    background-color: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 600px; 
    margin: 0 auto; 
} */

img {
    max-width: 100%;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin: 5px 0;
}

/* Optional: Add some styling for the form elements if needed */
input, button {
    margin: 10px;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
}

    </style>
 </head>
<body >
    <form action="{{ route('image.update1', $image->id) }}" method="POST">
        <img  src="{{ asset('histograms/'.'Histogramme_' . $image->file_name .'.png') }}" alt="Uploaded Image">
        
    </form>
</body>
</html> 
