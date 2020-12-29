<?php
    session_start();
    if(!isset($_SESSION['user'])){
        $_SESSION = array();
        header("Location: login.php");
        exit();
    }
    require("mysqli_connect.php");
    require("functions.php");
    
?>

<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Wyszukaj wypowiedzi</title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
    </script>
<?php
    $conn = get_connection();
    $qry = "select tekst from posiedzenia where processed = 1 order by data DESC, posiedzenie ASC, id ASC limit 30";
    if($stmt = $conn->prepare($qry)){
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($tekst);
    }
    echo "<table>";
    while($stmt->fetch()){
        echo "<tr><td>".$tekst."</td></tr>";
    }
    echo "</table>";
?>
</body>
</html>