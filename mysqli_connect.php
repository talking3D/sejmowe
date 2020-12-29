<?php
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'rootR98&5');
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'sejm_kopia');

    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        mysqli_set_charset($conn, 'utf8');
        if($conn->connect_error){
            echo "Wystąpił problem z połączeniem";
        }
    }
    catch(Exception $e) {
        //print "Wystąpił wyjątek: ". $e->getMessage();
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