<!-- resources/views/images.blade.php -->
<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <meta charset="utf-8">
    <title>Gallery :3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
.hh{
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
    .row{
      /* background: #000; */
    }
    .grid-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(850px, 1fr));
            gap: 20px;
        }

        .grid-gallery .grid-item {
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .grid-gallery .grid-item img {
            width: 100%;
            height: auto;
            transition: transform 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .grid-gallery .grid-item:hover img {
            transform: scale(1.1);
        }

        /* Votre CSS pour FancyBox */
        .fancybox-slide {
            padding: 15px;
            
        }
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        width: 100%;
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }

      .btn-bd-primary {
        --bd-violet-bg: #712cf9;
        --bd-violet-rgb: 112.520718, 44.062154, 249.437846;

        --bs-btn-font-weight: 600;
        --bs-btn-color: var(--bs-white);
        --bs-btn-bg: var(--bd-violet-bg);
        --bs-btn-border-color: var(--bd-violet-bg);
        --bs-btn-hover-color: var(--bs-white);
        --bs-btn-hover-bg: #6528e0;
        --bs-btn-hover-border-color: #6528e0;
        --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
        --bs-btn-active-color: var(--bs-btn-hover-color);
        --bs-btn-active-bg: #5a23c8;
        --bs-btn-active-border-color: #5a23c8;
      }

      .bd-mode-toggle {
        z-index: 1500;
      }

      .bd-mode-toggle .dropdown-menu .active .bi {
        display: block !important;
      }

      
/* Style de base */


/* Style au survol */
a:hover {
    background-color: #2980b9; /* Nouvelle couleur de fond au survol */
}

/* Style pour un look plus 3D */
a {
    box-shadow: 0 4px 8px rgba(0, 0, 0,)
}
    </style>    
</head>

<body>


<header data-bs-theme="dark">          
    <div class="collapse text-bg-dark" id="navbarHeader">
    <div class="container">
    <div class="row">
    <div class="container">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
            <li>    
        <div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
        <a href="#" class="navbar-brand d-flex align-items-center"></a>
        </div></div></li>
        </ul>
           @if (Route::has('login'))
                <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-500 underline"><div class="text-end"><button type="button" class="btn btn-outline-light me-2">Dashborad</button></div></a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline"><div class="text-end"><button type="button" class="btn btn-outline-light me-2">Login</button></div></a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline"><div class="text-end"><button type="button" class="btn btn-warning">Sign-up</button></div></a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
      </ul>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>



    
<!-- <div class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a href="#" class="navbar-brand d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" aria-hidden="true" class="me-2" viewBox="0 0 24 24"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
            <strong>Gallerey dyalna!</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</div> -->
    
</header>


 <div data-bs-theme="dark" class="album py-5 bg-body-tertiary grid-gallery" style="background-image: url('{{  asset('images/header.png') }}');
            background-size: cover; /* Adjust as needed */
            background-position: center; /* Adjust as needed */
           
          ">
        <div class="image-list">
            <form action="{{ route('nature.upload.post') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="image" class="form-control @error('image.*') is-invalid @enderror">
                <br>
                <button type="submit" class="btn btn-success">Upload</button>
                <a href="http://localhost:8000/index.php/dashboard" style="display: inline-block;
    padding: 6px 14px;
    text-decoration: none;
    background-color: #3498db; /* Couleur de fond */
    color: #fff; /* Couleur du texte */
    border-radius: 5px; /* Coins arrondis */
    transition: background-color 0.3s ease;">retour</a>
            </form>
        </div>
        <div class="container">
            <div class="row">
                @if (session('success'))
                    <div>{{ session('success') }}</div>
                @endif
                @foreach ($natures as $image)
                <div class="col-md-4 grid-item">
    <div class="card shadow-sm">
        <a data-fancybox="gallery" href="{{ asset('images/' . $image->file_name) }}" class="fancybox-link">
            <img src="{{ asset('images/' . $image->file_name) }}" alt="Uploaded Image" class="card-img-top">
        </a>
        <div class="card-body">
            <!-- <p class="card-text">{{ $image->file_name }}</p> -->
            <div class="d-flex justify-content-between align-items-center">
                <div  class="btn-group" style="">
                    <form action="{{ route('nature.delete', $image->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-secondary delete-btn">Delete</button>
                    </form>
                    <form action="{{ route('nature.edit', $image->id) }}" class="edit-form">
                        <button type="submit" class="btn btn-sm btn-outline-secondary edit-btn">Edit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
 </div>

                @endforeach
            </div>
        </div>
    </div>
        </main>

    </body>


 </div>
    </html>