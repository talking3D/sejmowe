<?php

    require_once('config/config.php');

    $dbuser = defined('DB_USER') ? DB_USER : '';
    $dbpassword = defined('DB_PASSWORD') ? DB_PASSWORD: '';
    $dbhost = defined('DB_HOST') ? DB_HOST : '';
    $dbname = defined('DB_NAME') ? DB_NAME : '';
    
    try {
      $conn = new mysqli($dbhost, $dbuser, $dbpassword, $dbname);
      mysqli_set_charset($conn, 'utf8');
      if($conn->connect_error){
        echo "Wystąpił problem z połączeniem";
      }
    }
    catch(Exception $e) {
      print("Wyjątkowo... spróbuj później, gdyż: ". $e->getMessage());
    }
    catch(Throwable $e){
      print("Taki błąd nie wynika stąd - ". $e->getMessage());
      
    }
    

    function get_connection(){
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        mysqli_set_charset($conn, 'utf8');
        if($conn->connect_error){
            die("Niestety połącznie nie powiodło się. Prosimy o kontakt z administratorem");
        } else {
            return $conn;
        }
    }
?>