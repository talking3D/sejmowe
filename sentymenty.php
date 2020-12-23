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
    <title>Przeglądaj sentymenty</title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
    </script>
<?php 
    $conn = get_connection();
    if(!isset($_GET['strona'])){
        $page = 1;
    } else {
        $page = $_GET['strona'];
    }

    function get_sentyment($sentyment){
        if($sentyment == (-1)){
            return "<span class='text-danger m-1 mb-1'><svg class='bi' width='30' height='30' fill='currentColor'><use xlink:href='bootstrap-icons.svg#emoji-frown'/></span>";
        } elseif($sentyment == 0){
            return "<span class='text-secondary m-1 mb-1'><svg class='bi' width='30' height='30' fill='currentColor'><use xlink:href='bootstrap-icons.svg#emoji-neutral'/></span>";
        } elseif($sentyment == 1){
            return "<span class='text-success m-1 mb-1'><svg class='bi' width='30' height='30' fill='currentColor'><use xlink:href='bootstrap-icons.svg#emoji-smile'/></span>";
        }
    }
    
    $cntqry = "SELECT COUNT(*) FROM sentyment";
    if($stmt = $conn->prepare($cntqry)){
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
    }

    $conn = get_connection();
    $limit = 20;
    $select = "SELECT id, pos_tekst_id, tekst, temat, sentyment FROM sentyment LIMIT ?";
    if($stmt = $conn->prepare($select)){
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $stmt->bind_result($id, $tekst_id, $tekst, $temat, $sentyment);
    }
    ?>
    <div class="container">
    <?php 
        include "header.php";
        make_pagination($page, $count, $limit, $params);
    ?>
    <div class="container border border-1 rounded mt-4">
    <?php
        echo ("
        <table class='table table-striped table-hover'>
            <thead>
                <tr class='text-center'>
                    <th class='col-1'>sentyment</th>
                    <th class='col-4'>temat</th>
                    <th class='col-6'>tekst</th>
                    <th class='col-1'>akcja</th>
                </tr>
            </thead>
        <tbody>");
    while($stmt->fetch()){
        echo("
            <tr>
                <td class='align-middle text-center'>".get_sentyment($sentyment)."</td>
                <td class='align-middle h5'>".$temat."</td>
                <td>".$tekst."</td>
                <td class='align-middle text-center'>
                    <a href ='edit_statement.php?id=" . $tekst_id . "&". $param_link ."&strona=".$page."'>
                    <svg class='bi' width='25' height='25' fill='currentColor'><use xlink:href='bootstrap-icons.svg#pencil-square'/></a></td>
            </tr>
        ");
    }
    echo("</tbody></table>");
    echo("</div>");
    make_pagination($page, $count, $limit, $params);
    ?>
    </div>
</body>
</html>