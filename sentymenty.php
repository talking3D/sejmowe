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
    isset($_POST['temat']) ? $temat = $_POST['temat'] : $temat = "%";
    isset($_POST['tekst']) ? $tekst = $_POST['tekst'] : $tekst = "%";
    isset($_POST['sentyment']) ? $sent = $_POST['sentyment'] : $sent = "%";
    isset($_POST['autor']) ? $author = $_POST['autor'] : $author = "%";

    $conn = get_connection();
    if(!isset($_GET['strona'])){
        $page = 1;
    } else {
        $page = $_GET['strona'];
        $tekst = $_GET['tekst'];
        $temat = $_GET['temat'];
        $sent = $_GET['sentyment'];
        $author = $_GET['autor'];
        $sent_id = $_GET['sent_id'];
    }
    $tekst = "%$tekst%";
    $temat = "%$temat%";

    $params = array('tekst' => $tekst, 'temat' => $temat, 'sentyment' => $sent, 'autor' => $author);
    function get_sentyment($sentyment){
        if($sentyment == (-1)){
            return "<span class='text-danger m-1 mb-1'><svg class='bi' width='30' height='30' fill='currentColor'><use xlink:href='bootstrap-icons.svg#emoji-frown'/></span>";
        } elseif($sentyment == 0){
            return "<span class='text-secondary m-1 mb-1'><svg class='bi' width='30' height='30' fill='currentColor'><use xlink:href='bootstrap-icons.svg#emoji-neutral'/></span>";
        } elseif($sentyment == 1){
            return "<span class='text-success m-1 mb-1'><svg class='bi' width='30' height='30' fill='currentColor'><use xlink:href='bootstrap-icons.svg#emoji-smile'/></span>";
        }
    }
    //getting users whos sentiments where upladed to database
    $conn = get_connection();
    $q_users = "SELECT DISTINCT u.first_name FROM users u JOIN sentyment s on s.userid = u.userid";
    if($user_stmt = $conn->prepare($q_users)){
        $user_stmt->execute();
        $user_stmt->store_result();
        $user_stmt->bind_result($first_name);
    }

    $cntqry = "SELECT COUNT(*) FROM sentyment s JOIN users u ON s.userid = u.userid WHERE s.temat LIKE ? AND s.tekst LIKE ? AND s.sentyment LIKE ? AND u.first_name LIKE ? ";
    if($stmt = $conn->prepare($cntqry)){
        $stmt->bind_param('ssss', $temat, $tekst, $sent, $author);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($count);
        $stmt->fetch();
    }
    $conn = get_connection();
    $limit = 20;
    $limit_start = (($page - 1) * $limit);

    $select = 'SELECT s.id, s.pos_tekst_id, s.tekst, s.temat, s.sentyment, u.first_name FROM sentyment s JOIN users u ON s.userid = u.userid 
    WHERE s.temat LIKE ? AND s.tekst LIKE ? AND s.sentyment LIKE ? AND u.first_name LIKE ? ORDER BY id DESC LIMIT ?, ?';
    if($stmt = $conn->prepare($select)){
        $stmt->bind_param('ssssii', $temat, $tekst, $sent, $author, $limit_start, $limit);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($id, $tekst_id, $tekst, $temat, $sentyment, $user);
    }
    ?>
    <div class="container">
    <?php 
        include "header.php";
        ?>
        <form class="form-floating" id="search" action="sentymenty.php" method="POST">
            <div class="row align-items-center py-2">
                <div class="col-4 form-floating g-2">
                    <input class="form-control" type="search" id="temat" name="temat"
                        placeholder="Wpisz temat posiedzenia">
                    <label for="posiedzenie">Temat</label>
                </div>
                <div class="col-4 form-floating g-2">
                    <input class="form-control" type="search" id="tekst" name="tekst" placeholder="Treść sentymentu">
                    <label for="rok">Tekst</label>
                </div>
                <div class="col-1 form-floating g-2">
                        <label for="sentyment" class="form-label text-secondary">Sentyment</label>
                        <select class="form-select" aria-label=".form-select-example" name="sentyment">
                            <option value="%"></option>
                            <option value="1">+1</option>
                            <option value="0">0</option>
                            <option value="-1">-1</option>
                        </select>
                        <!-- <input type="text" class="form-control" id="sentyment" name="sentyment" style="line-height: 30px; font-size: 25px; text-align: center;" value="<?php echo $sent_sent;?>"> -->
                </div>
                <div class="col-1 form-floating g-2">
                        <label for="autor" class="form-label text-secondary">Kto</label>
                        <select class="form-select" aria-label=".form-select-example" name="autor">
                            <option value="%"></option>
                            <?php 
                                while($user_stmt->fetch()){
                                    echo("<option value=".$first_name.">".$first_name."</option>");
                                }
                        ?>
                        </select>
                </div>
                <div class="col-1">
                    <button class="btn btn-primary float-start">
                        <svg width="1.5em" height="1.5em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z" />
                            <path fill-rule="evenodd"
                                d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    <?php
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
                    <th class='col-5'>tekst</th>
                    <th class='col-1'>kto</th>
                    <th class='col-1'>akcja</th>
                </tr>
            </thead>
        <tbody>");
    while($stmt->fetch()){
        echo("
            <tr id=".$id.">
                <td class='align-middle text-center'>".get_sentyment($sentyment)."</td>
                <td class='align-middle h5'>".$temat."</td>
                <td>".text_limit($tekst, 300)."</td>
                <td  class='align-middle text-center'>".$user."</td>
                <td class='align-middle text-center'>
                    <a href ='edit_statement.php?id=" . $tekst_id . "&". get_param_link($params) ."&sent_id=".$id."&strona=".$page."&source=2#".$id."'>
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