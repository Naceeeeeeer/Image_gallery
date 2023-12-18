<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit image</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="Animated-Radial-Menu.css">
    <script defer type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script defer nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script defer src="Animated-Radial-Menu.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.0/fabric.min.js"></script>
    <meta charset="utf-8">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css"  />

</head>
<body>
    
<form id="form1" action="{{ route('image.update', $image->id) }}" method="POST">
    @csrf
    @method('PUT')
    <button name="Histogramme" type="submit" class="btn btn1" style="display: none;">Histogramme</button>
</form>
  <form id="form2" action="{{ route('image.update2', $image->id) }}" method="POST">
                @csrf
                @method('PUT')     
              <button name="Pallette" type="submit" style="display: none;" >Pallette </button>              
            </form>


<!--         

    <form id="form4" action="{{ route('search.image', $image->id)}}" method="POST">      
    @csrf
    @method('POST')
    <button name="Histogramme" type="submit" class="btn btn1" style="display: none;">Histogramme</button>
</form>
<form id="form5" action="{{ route('search_RF.image', $image->id)}}" method="POST">      
    @csrf
    @method('POST')
    <button name="Histogramme" type="submit" class="btn btn1" style="display: none;">Histogramme</button>
</form>
-->
<form id="form15" action="{{ route('image.delete', $image->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                      
                    </form>
 

    <div class="Animated-Radial-Menu">
        <div style =" text-align: center;
    background: linear-gradient(to right, #B799FF,#687EFF );
    border-radius: 10px;
    padding: 5px;
    margin: 5px 0;
">
    <img style="
    width:60vh;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin: 5px 4;
" id="image" src="{{ asset('images/' . $image->file_name) }}" alt="Uploaded Image"  style="width: 510px; height: 460px; ">

</div>
<div style="width:10vh;"></div>
        <ul class="menu">
            <div class="toggle"><ion-icon name="add-outline"></ion-icon></div>
            <li style="--i: 0; --clr: #ff2972;">
                <a title="Delete" href="#" onclick="submitForm('form15')">
                <ion-icon  name="trash-outline"></ion-icon>

                </a>
            </li>
            <li style="--i: 1; --clr: #fee800;">
                <a href="http://localhost:8000/index.php" title="Home">
                    <ion-icon  name="home-outline"></ion-icon>
                </a>
            </li>
            <li style="--i: 2; --clr: #04fc43;">
                <a href="#" title="Histogramm" onclick="submitForm('form1')">
                <ion-icon name="bar-chart-outline"></ion-icon>
                </a>
            </li>          
            <li style="--i: 3; --clr: #fe00f1;">

          
                <a  href="#" title="Palette" onclick="submitForm('form2')">
                <ion-icon  name="barcode-outline"></ion-icon>
                </a>
            </li>
            <li style="--i: 4; --clr: #00b0fe;">
            <form id="form3" action="{{ route('image.update1', $image->id) }}" method="POST" >
                  @csrf
                      @method('PUT')   
                
                <button name="Moment" type="submit" style="display: none;" >Moments</button> </form>
                <a href="#" title="Moment" onclick="submitForm('form3')">
                <ion-icon name="images-outline"></ion-icon>
                </a>
            </li>
            <li style="--i: 5; --clr: #fea600;">
                <a href="#" title="crop" id="crop">
                <ion-icon name="crop-outline"></ion-icon>
                </a>
            </li>
            <li style="--i: 6; --clr: #a529ff;">
                <a href="#" onclick="togglePopup2()">
                <ion-icon name="document-text-outline"></ion-icon>
                </a>
            </li>
            <li style="--i: 7; --clr: #01bdab;">
                <a href="#" title="search" onclick="togglePopup()">
                <ion-icon name="search-outline"></ion-icon>
               

                </a>
            </li>
        </ul>
    </div>
<style>
  @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@300;500;700;900&display=swap");
* {
  
  margin: 0;
  padding: 0;
  font-family: "Roboto", sans-serif;
  box-sizing: border-box;
}

.Animated-Radial-Menu {
  display: flex;
  justify-content:center;
  
  align-items: center;
  min-height: 100vh;
  background:#2f363e
  /* background-image: url('{{  asset('images/header.png') }}'); */
}
.Animated-Radial-Menu .menu {
  position: relative;
  width: 280px;
  height: 280px;
  display: flex;
  justify-content: center;
  align-items: center;
}
.Animated-Radial-Menu .menu .toggle {
  position: absolute;
  width: 60px;
  height: 60px;
  background: #2f363e;
  border: 2px solid #fff;
  border-radius: 50%;
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  z-index: 100;
  font-size: 2em;
  transition: transform 0.5s;
}
.Animated-Radial-Menu .menu li {
  position: absolute;
  left: 0;
  list-style: none;
  transition: 0.3s;
  transition-delay: calc(0.04s * var(--i));
  transform: rotate(0deg) translateX(110px);
  transform-origin: 140px;
}
.Animated-Radial-Menu .menu li a {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 60px;
  height: 60px;
  color: var(--clr);
  border: 2px solid var(--clr);
  border-radius: 50%;
  font-size: 1.5em;
  transform: rotate(calc(-45deg * var(--i)));
  transition: 0.3s;
}
.Animated-Radial-Menu .menu li a:hover {
  background: var(--clr);
  color: #333;
  box-shadow: 0 0 10px var(--clr), 0 0 30px var(--clr), 0 0 50px var(--clr);
}
.Animated-Radial-Menu .menu.active .toggle {
  transform: rotate(315deg);
}
.Animated-Radial-Menu .menu.active li {
  transform: rotate(calc(45deg * var(--i))) translateX(0px);
}/*# sourceMappingURL=Animated-Radial-Menu.css.map */
</style>
<script>
  function submitForm(formId) {
    document.getElementById(formId).submit();
  }
</script>
<script>
  let toggle = document.querySelector('.toggle')
let menu = document.querySelector('.menu')

toggle.onclick = ()=>{
    menu.classList.toggle('active')
}
</script>
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Crop image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="fermerModal()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8">  
                            <!-- Image source updated to use Blade variable -->
                            <img id="cropperImage" src="{{ asset('images/' . $image->file_name) }}">
                        </div>
                        <div class="col-md-4">
                            <div class="preview"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="fermerModal()">Cancel</button>
                <button type="button" class="btn btn-primary" id="applyCrop" onclick="soumettreFormulaire(); fermerModal();">Apply Crop</button>
                <form action="{{ route('enregistrer-image') }}" method="post" id="imageForm">
    @csrf
    <input type="hidden" name="image" id="imageInput">
</form>
            </div>
        </div>
    </div>
</div>
 <div id="popup" class="popup" style =" text-align: center;
     background:#2f363e;
    border-radius: 10px;
    padding: 5px;
    margin: 5px 0;
" >

<div class=" rounded-lg max-w-[500px]" >

            <label class="text-gray-600 text-sm" style="color: white;">
                how do you want to search ?
            </label><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><button onclick="togglePopup()" class="btn btn-danger">X</button>
            <div class="relative mt-2 max-w-xs text-gray-500">
                <div class="absolute inset-y-0 left-3 my-auto h-6 flex items-center border-r pr-2">
                    <select id="searchType" class="text-sm outline-none rounded-lg h-full">
                        <option>Simple Search</option>
                        <option>RF Search</option>
                      
                    </select>
                </div>
                <br>
                <input required type="number" id="numberOfResults" placeholder="Number of results :" class="w-full pl-[4.5rem] pr-3 py-2 appearance-none bg-transparent outline-none border focus:border-slate-600 shadow-sm rounded-lg" >

            </div>
        </div>
<br>
    <button type="submit" class="btn btn-success" style="height: 6vh;" onclick="submitFormBasedOnChoice()">Envoyer mon choix</button>


</div>

<script>
    function togglePopup() {
        var popup = document.getElementById("popup");
        popup.style.display = (popup.style.display === "none" || popup.style.display === "") ? "block" : "none";
    }
    
</script>

<script>
    function togglePopup2() {
        var popup = document.getElementById("popup2");
        popup.style.display = (popup.style.display === "none" || popup.style.display === "") ? "block" : "none";
    }
    
</script>

</body>

<script>
    var bs_modal = $('#modal');
    var image = document.getElementById('cropperImage');
    var cropper;

    bs_modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 3,
            preview: '.preview'
        });
    }).on('hidden.bs.modal', function() {
        cropper.destroy();
        cropper = null;
    });

    $("#crop").click(function() {
        bs_modal.modal('show');
    });

    $("#applyCrop").click(function() {
        // Get cropped data and handle it as needed
        var croppedData = cropper.getCroppedCanvas().toDataURL('image/jpeg');
        // Example: display cropped image on the page
        var croppedImageElement = document.createElement('img');
        croppedImageElement.style.position = 'relative';


        croppedImageElement.classList.add('custom-image-style');
        croppedImageElement.src = croppedData;
        document.body.appendChild(croppedImageElement);
        // Close the modal
        $("#imageInput").val(croppedData);
    soumettreFormulaire();
        fermerModal();
    });

    function soumettreFormulaire() {
    // Soumettez le formulaire
    document.getElementById('imageForm').submit();
}
    function fermerModal() {
        bs_modal.modal('hide');
    }
</script>
      <br>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/4.5.0/fabric.min.js"></script>


<script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.2/dist/chart.umd.js" integrity="sha384-eI7PSr3L1XLISH8JdDII5YN/njoSsxfbrkCTnJrzXt+ENP5MOVBxD+l6sEG4zoLp" crossorigin="anonymous"></script><script src="dashboard.js"></script></body>
    
    <style>/* Ajoutez du CSS pour le style de votre popup ici */
        .popup {
            display: none;
            position: fixed;
            width:100vh;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
    </style>

<form id="form4" method="post">
    <!-- Other form fields go here -->
@csrf 
    <!-- Add a hidden input field for numberOfResults -->
    <input type="hidden" name="numberOfResults" id="numberOfResults" value="" >
    
    <button type="button" onclick="submitFormBasedOnChoice()" style="display:none">Submit Form</button>
</form>

<form id="form5" method="post">
@csrf 

    <input type="hidden" name="numberOfResults" id="numberOfResults" value="">
    <button type="button" onclick="submitFormBasedOnChoice()" style="display:none">Submit Form</button>
</form>

<script>
    function submitFormBasedOnChoice() {
        var searchType = document.getElementById("searchType").value;
        var numberOfResults = document.getElementById("numberOfResults").value;
        if (searchType === "Simple Search") {
            document.getElementById("form4").numberOfResults.value = numberOfResults;
            document.getElementById("form4").action = "{{ route('search.image', $image->id) }}" ;
            document.getElementById("form4").submit();
        } else {
            document.getElementById("form5").numberOfResults.value = numberOfResults;
            document.getElementById("form5").action = "{{ route('search_RF.image', $image->id) }}" ;
            document.getElementById("form5").submit();
        }
    }
</script>

<div id="popup2" class="popup" style =" text-align: center;
     background:#2f363e;
    border-radius: 10px;
    padding: 5px;
    margin: 5px 0;
">

<div class=" p-4 rounded-lg max-w-[500px]" >

            <label class="text-gray-600 text-sm"style="color: white;">
                choose the  data type :  
            </label><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><button onclick="togglePopup2()" class="btn btn-danger" >X</button>
            <div class="relative mt-2 max-w-xs text-gray-500">
                
<div class="container">
   
   <!-- <img id="image" src="{{ asset('images/' . $image->file_name) }}" alt="Uploaded Image" style="max-width: 800px; max-height:600px; padding-right: 20px;"> -->
   <!-- <button type="button" class="btn btn-primary" id="crop">Crop</button> -->
  

                <form action="{{ route('showHistogram') }}" method="GET" class="nav-link d-flex align-items-center gap-2">
   @csrf

   <svg class="tri" width="30px" height="30px"><use xlink:href="#file-earmark-text"/></svg>

   <button type="submit" class="btn btn1" style="color: white;"> Histogram Data</button>
</form>
<form action="{{ route('showPallette') }}" method="GET" class="nav-link d-flex align-items-center gap-2">
               <svg class="tri" width="30px" height="30px"><use xlink:href="#file-earmark-text"/></svg>
               <button type="submit" class="btn btn1" style="color: white;"> Pallettes Data</button>
               </form>
               <form action="{{ route('showMoment') }}" method="GET" class="nav-link d-flex align-items-center gap-2">
               <svg class="tri" width="30px" height="30px"><use xlink:href="#file-earmark-text"/></svg>
               <button type="submit" class="btn btn1" style="color: white;"> Moment Data</button>
               </form>
</div>


                </div>

            </div>
        </div>



</div>

</html>

























   









<style type="text/css">
    img {
        display: block;
        max-width: 100%;
    }
    .preview {
        overflow: hidden;
        width: 160px; 
        height: 160px;
        margin: 10px;
        border: 1px solid red;
    }
    .custom-image-style {
    width: 300px; /* par exemple, largeur de 300 pixels */
    height: 200px; /* par exemple, hauteur de 200 pixels */
    border: 1px solid #ccc; /* exemple de bordure */
    /* Ajoutez d'autres styles selon vos besoins */
}

</style>

