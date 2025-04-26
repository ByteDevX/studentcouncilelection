<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> @yield('title')  </title>
    <link rel="icon" type="image/x-icon" href="/images/LOGO UC.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            text-align: center;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        .bg-image {
            background: url('/images/Background.jpg');
            background-size: cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh; 
        }

        .google-icon {
            width: 50px;
            height: 26px;
        }

        .heading{
            font-size: 48px;
        }

        .heading-2{
            font-size: 64px;
        }

        .heading-3{
            font-size: 80px;
        }
        
        .text-shadow{
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .image-container {
            position: relative;
            overflow: hidden;
        }

        .exceed-image {
            position: relative;
            margin-top: -150px;
            width: 325px;
        }

        .exceed-image-2 {
            position: relative;
            margin-top: -150px;
            width: 400px;
        }

        .exceed-image-3 {
            position: relative;
            margin-top: -690px;
            width: 500px;
        }

        .exceed-image-4 {
            position: relative;
            margin-top: -150px;
            width: 450px;
        }

        .ontop-image {
            position: relative;
            margin-top: -120px;
        }

        .gradient {
            background: linear-gradient(to bottom, #FFFFFF, #F98700);
        }

        .orange-div {
            background-color: #FF7A00;
            color: white;
        }

        .orange-text {
            color: #FF7A00;
        }

        .green-div {
            background-color: #00CA45;
            color: white;
        }

        .center-div{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .start-div {
            display: flex;
            flex-direction: row; 
            align-items: top;
            justify-content: flex-start;
            text-align: start;
        }

        .custom-card {
            border-top-left-radius: 50px;
            border-top-right-radius: 50px;
        }

        .padding-main{
            padding-left: 80px;
            padding-right: 80px;
        }

        .no-gap{
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .percentage-1{
            position: relative;
            margin-left: -100px;
            z-index: 0;
        }

        .p-container{
            height: 95px;
            width: 200px;
            margin-left: -80px;
            margin-top: 50px;
            background-color: transparent;
            position: relative;
            z-index: 0;
        }

        .p-container-2{
            height: 95px;
            width: 200px;
            margin-left: 100px;
            margin-top: 25px;
            background-color: transparent;
            position: relative;
            z-index: 0;
        }

        .p-container .p-bar{
            position: absolute;
            height: 100%;
            width: 0;
            border-radius: 14px;
            animation: progress-animation 1s forwards 1s;
        }

        .p-container-2 .p-bar-2{
            position: absolute;
            height: 100%;
            width: 0;
            right: 0; 
            border-radius: 14px;
            animation: progress-animation 1s forwards 1s;
        }

        .green-bar{
            background-color: #00CA45;
        }

        .red-bar{
            background-color: #FF0000;
        }

        .orange-bar{
            background-color:rgb(255, 140, 33);
        }

        .p-text{
            opacity: 0;
            animation: text-animation 1s forwards 1s;
        }

        @keyframes progress-animation {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        @keyframes text-animation {
            0% { opacity: 0%; }
            100% { opacity: 100%; }
        }

        .ontop{
            z-index: 10;
        }

        .btn-success{
            background-color: #00CA45;
            border: solid #00CA45;
            color: white;
        }

        .btn-danger{
            background-color: #FF7A00;
            border: solid #FF7A00;
            color: white;
        }

        .btn-size{
            width: 20px;
            height: 20px;
        }

        .pagination-wrapper .text-sm {
            color: white !important;
        }

        .floating-warning {
    position: fixed;
    bottom: 24px;
    left: 10vw;
    width: 80vw;
    background: rgba(255, 140, 0, 0.6); /* Orange dengan 60% opacity */
    color: white;
    padding: 16px 24px;
    border-radius: 12px;
    font-size: 14px;
    z-index: 9999;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
.floating-warning strong {
    font-weight: 700;
}
        
    </style>
</head>
<body>
    <div class = "w-100 bg-image px-5">
        @if (env('APP_ENV') !== 'production')
    <div class="floating-warning">
        <p>
            Notice: This website is currently operating in a <strong>testing environment</strong> and is not intended for <strong>production use</strong>.
        </p>
        <hr style="border: 1px solid white; opacity: 0.5;">
        <p>
            Pemberitahuan: Situs web ini saat ini beroperasi dalam <strong>lingkungan pengujian</strong> dan tidak diperuntukkan untuk <strong>penggunaan produksi</strong>.
        </p>
    </div>
@endif
        <nav class="navbar p-2 ps-4" data-aos="fade-up" data-aos-duration="1000">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="/images/LOGO UC.png" alt="Logo" width="60" height="60">
                    <!-- <img src="/images/LOGO SC.png" alt="Logo" width="26" height="34"> -->
                    <img src="/images/LOGO SRB.png" alt="Logo" width="80" height="42">
                    <img src="/images/LOGO BUKU.png" alt="Logo" width="60" height="60">
                </a>
                
            </div>
            
        </nav>
        <div>
            @yield('content')
        </div>
    </div>

    <!-- <footer class = "col-12">
        <div class="row w-100 bg-white align-items-center pt-3 orange-text position-fixed bottom-0">
            <p class = "text-center fs-6"> Copyright © Universitas Ciputra Student Council </p>
        </div>
    </footer> -->

    <script>
        AOS.init();
        
    </script>  
    
    <script>
        function confirmation(form){
            swal({
                title: "Are you sure?",
                text: "Click OK to confirm your choice.",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((isOkay) => {
                if(isOkay) {
                    form.submit();
                } 
            });
            return false;
        }
    </script>

</body>
</html>
