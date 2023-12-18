<x-app-layout>
   

    <!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">

<style>
  .color{
  margin: 0;
            padding: 0;
            height: 100vh;
            background-image: url('{{  asset('images/header.png') }}');
            background-size: cover; /* Adjust as needed */
            background-position: center; /* Adjust as needed */
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: 'Arial', sans-serif;
}
    .image-list {
        max-width: 700px; /* Adjust the maximum width as needed */
    height: 250px;  /*Permet aux éléments de passer à la ligne si l'espace est insuffisant */
}
.custom-image {
    max-width: 700px; /* Adjust the maximum width as needed */
    height: 250px; /* This maintains the aspect ratio */
}

.image-item {
    margin: 5px; /* Marge entre les éléments */
    text-align: center; /* Centre le contenu horizontalement dans chaque élément */
}

.image-item img {
    max-width: 100%; /* Évite que les images ne dépassent de leur conteneur */
}

.image-item button {
    margin: 5px;
}

body {
  font-family: "Lato", sans-serif;
}
body.nav-open #main {
  margin-left: 250px; /* Ajustez la largeur du volet latéral selon votre préférence */
}

body.nav-open .navbar {
  margin-left: 250px; /* Ajustez la même largeur que le volet latéral */
}

.submenu {
  display: none;
}

a:hover + .submenu,
.submenu:hover {
  display: block;
}

.sidebar {
  height: 100%;
  width: 0;
  position:fixed;
  z-index: 1;
  top: 0;
  left: 0;
  /* background-color: #111; */
  background-image: url('{{  asset('images/header.png') }}');
  overflow-x: hidden;
  transition: 0.5s;
  padding-top: 60px;
}

.sidebar a {
  padding: 8px 8px 8px 32px;
  text-decoration: none;
  font-size: 25px;
  color: #818181;
  display: block;
  transition: 0.3s;
}


.sidebar a:hover {
  color: #f1f1f1;
}

.sidebar .closebtn {
  position: absolute;
  top: 0;
  right: 25px;
  font-size: 36px;
  margin-left: 50px;
}

/* .openbtn {
  font-size: 20px;
  cursor: pointer;
  background-color: #111;
  color: white;
  padding: 10px 15px;
  border: none;
}

.openbtn:hover {
  background-color: #444;
} */
/* Add the following CSS rules to move the container to the right */
.container {
    margin-left: 250px; /* Adjust the same width as the sidebar */
    
}

body.nav-open .container {
    margin-right: 0;
}
.openbtn {
  font-size: 20px;
  cursor: pointer;
  background-color: transparent; /* Supprimer la couleur d'arrière-plan */
  color: black; /* Changer la couleur du texte en noir */
  padding: 10px 15px;
  border: none;
}

.openbtn:hover {
  background-color: transparent; /* Arrière-plan transparent en survol */
  color: white; /* Changer la couleur du texte en blanc au survol */
}







#main {
  transition: margin-left .5s;
  padding: 16px;
}

/* On smaller screens, where height is less than 450px, change the style of the sidenav (less padding and a smaller font size) */
@media screen and (max-height: 450px) {
  .sidebar {padding-top: 15px;}
  .sidebar a {font-size: 18px;}
}
</style>
</head>

<body data-bs-theme="dark" class="album py-5 bg-body-tertiary grid-gallery" style="background-image: url('{{  asset('images/header.png') }}');
            background-size: cover; /* Adjust as needed */
            background-position: center; /* Adjust as needed */
            
          ">


<div class="color">
<div class="container">
    <div class="panel panel-primary">
    <div class="panel-heading">
            <h2> Welcome to your album</h2>
            <p class="lead text-body-secondary">Get started by uploading your images</p>
        </div>
        <div class="panel-body">
          
                <div class="image-list">
           
        <!-- <form action="{{ route('image.upload.post') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="image" class="form-control @error('image.*') is-invalid @enderror">
    <br>
    <button type="submit" class="btn btn-success">Upload</button>    </form> -->
    <form action="{{ route('image.upload.post') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="image[]" class="form-control @error('image.*') is-invalid @enderror" multiple>
    <br>
    <button type="submit" class="btn btn-success">Upload</button>
</form>

        </div>
    </div>
</section>    

            </div>
        </div>
    </div>

    
<div id="mySidebar" class="sidebar">
<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
<a href="http://localhost:8000/index.php/animal">Animals</a>
<!-- <div class="submenu">
  <a href="#">Cats</a>
  <a href="#">lions</a>
</div> -->
<a href="http://localhost:8000/index.php/nature">Nature</a>
<a href="http://localhost:8000/index.php/voiture">voitures</a>
<a href="http://localhost:8000/index.php/dashboard">other</a>

</div>

<div id="main">
 
</div>

<script>
function openNav() {
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
}

function closeNav() {
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft = "0";
}
</script>
<script>
function openNav() {
  document.body.classList.add('nav-open');
  document.getElementById("mySidebar").style.width = "250px";
  document.getElementById("main").style.marginLeft = "250px";
  document.querySelector('.navbar').style.marginLeft = "250px";
}

function closeNav() {
  document.body.classList.remove('nav-open');
  document.getElementById("mySidebar").style.width = "0";
  document.getElementById("main").style.marginLeft = "0";
  document.querySelector('.navbar').style.marginLeft = "0";
}
</script>

</body>
</html> 






</x-app-layout>





















