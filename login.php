<!doctype html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.79.0">
    <title>Logowanie</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/sign-in/">



    <!-- Bootstrap core CSS -->
    <!-- <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    <style>
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
    </style>


    <!-- Custom styles for this template -->
    <link href="css/signin.css" rel="stylesheet">
</head>

<body class="text-center">

    <main class="form-signin">
    <?php 
        if ($_SERVER['REQUEST_METHOD'] == 'POST') { //#1 
            require('process-login.php');
        } // End of the main Submit conditional.
       
    ?>
    
        <form method="post" action="login.php" name="loginform" id="loginform">
            <img class="mb-4" src="../assets/brand/bootstrap-logo.svg" alt="" width="72" height="57">
            <h1 class="h3 mb-3 fw-normal">Zaloguj się</h1>
            <label for="inputEmail" class="visually-hidden" name="email">Email</label>
            <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?> " required autofocus>
            <label for="inputPassword" class="visually-hidden">Hasło</label>
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Hasło" required value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>" >
            <!-- <div class="checkbox mb-3">
      <label>
        <input type="checkbox" value="remember-me"> Zapa
      </label>
    </div> -->
            <button class="w-100 btn btn-lg btn-primary" type="submit">Zaloguj</button>
            <p class="mt-5 mb-3 text-muted">&copy; 2020</p>
        </form>
    </main>
    <?php
        if(!isset($errorstring)) {
            
        }
    ?>



</body>

</html>