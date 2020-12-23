<?php
    session_start();
    if(!isset($_SESSION['user'])){
        $_SESSION = array();
        header("Location: login.php");
        exit();
    }
    require("mysqli_connect.php");
?>

<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Edytuj wypowiedzi</title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
    </script>
    <div class="container">
        <?php
        include "header.php";
        //mysqli
        $conn = get_connection();
       
        $checkqry = "SELECT id FROM sentyment WHERE pos_tekst_id = ?";

        if(isset($_POST['id'])) { 
            $id = $_POST['id'];
            $posiedzenie_link = $_POST['posiedzenie'];
            $tekst_form= $_POST['tekst'];
            $tekst_link = $_POST['tekst-link'];
            $top_form = $_POST['top-words'];
            $top_link = $_POST['top-words-link'];
            $data_link = $_POST['year'];
            $kto_link = $_POST['kto'];
            $sent_id_form = $_POST['sent-id'];
            $sent_tekst_form = $_POST['tekst-fragment'];
            $sent_temat_form = $_POST['temat'];
            $sent_sent_form = $_POST['sentyment'];
            $delete = $_POST['delete'];
            $page = $_POST['strona'];
            $processed_link = $_POST['processed'];
            $processed_form = $_POST['include'];
            (($sent_sent_form == '' &&  ($sent_temat_form != '' || $sent_tekst_form != '')) || ($sent_temat_form == '' && $sent_sent_form != '')) ? $sent_sent_form = 999 : $sent_sent_form;
            $processed_form == '' ? $processed_form = 0 : $processed_form = 1;
        
        } elseif(isset($_GET['id'])) {
            $id = $_GET['id'];
            $posiedzenie_link = $_GET['posiedzenie'];
            $tekst_link = $_GET['tekst'];
            $top_link = $_GET['top'];
            $kto_link = $_GET['kto'];
            $data_link = $_GET['year'];
            $processed_link = $_GET['processed'];
            $page = $_GET['strona'];
        }
        $checkqry = "SELECT p.posiedzenie, p.data, p.kto, p.tekst, p.strona, p.top, p.processed, s.id, s.tekst as fragment, s.temat, s.sentyment FROM posiedzenia p LEFT JOIN sentyment s on s.pos_tekst_id = p.id WHERE p.id = ?";
        if(isset($_POST['id'])){
        if($stmt = $conn->prepare($checkqry)) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($posiedzenie, $data, $kto, $tekst, $strona, $top, $processed, $sent_id, $fragment, $temat, $sent_sent);
            $stmt->fetch();

            $update_posiedzenia = "UPDATE posiedzenia SET tekst = ?, top = ?, processed = ? WHERE id = ?;";
            $conn = get_connection();
            if($stmt = $conn->prepare($update_posiedzenia)) {
                $stmt->bind_param('ssii', $tekst_form, $top_form, $processed_form, $id);
                $stmt->execute();
            } 
            if($delete != 1){
                if((is_null($temat) && is_null($fragment) && is_null($sent_sent)) && ($sent_sent_form != '' || $sent_temat_form != '' || $sent_tekst_form != '') && $sent_sent_form != '999') {
                    $insert_sentyment = "INSERT INTO sentyment (pos_tekst_id, tekst, temat, sentyment, userid) VALUES (?, ?, ?, ?, ?)";
                    if($stmt = $conn->prepare($insert_sentyment)) {
                        $stmt->bind_param('issii', $id, $sent_tekst_form, $sent_temat_form, $sent_sent_form, $_SESSION['user']);
                        $stmt->execute();
                    }
                } elseif($sent_sent_form != '' || $sent_temat_form != '' || $sent_tekst_form != '') { 
                    $update_sentyment = "UPDATE sentyment SET tekst = ?, temat = ?, sentyment = ?, userid = ? WHERE id = ?;";
                    if($stmt = $conn->prepare($update_sentyment)) {
                        $stmt->bind_param('ssiii', $sent_tekst_form, $sent_temat_form, $sent_sent_form, $_SESSION['user'], $sent_id_form);
                        $stmt->execute();
                    }
                }
            } else {
                $delqry = "DELETE FROM sentyment WHERE sentyment.id = ?";
                if($stmt = $conn->prepare($delqry)) {
                    $stmt->bind_param('i', $sent_id_form);
                    $stmt->execute();
                    $conn = get_connection();
                }

            }

            $conn = get_connection();
        }
    }


        $selqry = "SELECT p.posiedzenie, p.data, p.kto, p.tekst, p.strona, p.top, p.processed, s.id, s.tekst as fragment, s.temat, s.sentyment FROM posiedzenia p LEFT JOIN sentyment s on s.pos_tekst_id = p.id WHERE p.id = ?";
        if($stmt = $conn->prepare($selqry)) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($posiedzenie, $data, $kto, $tekst, $strona, $top, $processed, $sent_id, $fragment, $temat, $sent_sent);
        }
        
        function get_status($sentyment, $temat, $processed) {
            if($sentyment > 1 || (isset($temat) && $temat !=='' && (!isset($sentyment) || $sentyment === '' )) || (!isset($temat) && isset($sentyment) && $sentyment !== '')){
                return -1;
            } elseif(($processed == 1 || $processed == 2) && isset($sentyment)) {
                return 1;
            }  elseif($processed == 1 || $processed == 2){
                return 0;
            } else {
                return -999;
            }
        }
        if($sent_sent_form == 999){
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong class="h4">Wystąpił błąd w danych! Zmiany nie zostały zapisane.</strong><hr />Sprawdź, czy uzupełnione zostały pola <em><u>Sentyment</u></em> oraz <em><u>Temat</u></em>. Spróbuj ponownie zapisać zmiany.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
        }
        while($stmt->fetch()) {
            
        ?>
        <form action="edit_statement.php" method="POST" class="py-4">
            <div class="row align-items-center justify-content-between py-3 border rounded bg-light">
                <div class="col-1 text-center">
                    <a href="<?php echo "index.php?id=$id&posiedzenie=$posiedzenie_link&year=$data_link&kto=$kto_link&tekst=$tekst_link&top=$top_link&processed=$processed_link&strona=$page";?>"
                        class="btn btn-primary">Wróć</a>
                </div>
                <div class="col-2 g-2">
                    Posiedzenie: <span class="h4"><?php echo $posiedzenie; ?></span>
                </div>
                <div class="col-2 g-2">
                    Data: <span class="h4"><?php echo $data; ?></span>
                </div>
                <div class="col-4 g-2">
                    Kto: <span class="h4"><?php echo $kto; ?></span>
                </div>
                <div class="col-2 g-2">
                    <?php 
                    if(get_status($sent_sent, $temat, $processed) == -1 || get_status($sent_sent, $temat, $processed) == 1){
                        echo "<input class='form-check-input' type='checkbox' id='include' name='include-for-display' value='1' checked disabled>";
                        echo "<input type='hidden' name='include' value='1'>";
                    } elseif(get_status($sent_sent, $temat, $processed) == 0) {
                        echo "<input class='form-check-input' type='checkbox' id='include' name='include' value='1' checked>";
                    } else {
                        echo "<input class='form-check-input' type='checkbox' id='include' name='include' value='1'>";
                    }
                ?>
                    <label for="include" class="form-check-label">Istotne</label>
                </div>
            </div>
            <div class="row align-items-center justify-content-between py-2 mt-1 border rounded bg-light">
                <div class="col">
                    <label for="top-words">Popularne słowa</label>
                    <textarea class="form-control" id="top-words" name="top-words" rows="3"
                        placeholder="Popularne słowa" style="font-size: 20px;"><?php echo $top; ?></textarea>
                </div>
            </div>
            <div class="row align-items-center justify-content-between py-3 mt-1 border rounded bg-light">
                <div class="col">
                    <label for="tekst" class="col-sm-2 col-form-label">Tekst</label>
                    <textarea class="form-control" id="text" name="tekst" rows="20"><?php echo $tekst; ?></textarea>
                </div>
            </div>
            <div class="row align-items-center justify-content-between py-3 mt-1 border rounded bg-light">
                <div class="col-11">
                    <label for="text-part" class="form-label">Wypowiedź do oceny sentymentu</label>
                    <textarea class="form-control" id="text-part" name="tekst-fragment"
                        rows="7"><?php echo $fragment?></textarea>
                </div>
                <div class="col-1 align-self-center align-middle form-group">
                    <input class="form-check-input" type="checkbox" name="delete" id="delete" value="1">
                    <label for="delete" class="form-label h5 text-danger">Usuń</label>
                </div>
            <!-- </div>
            <div class="row align-items-center justify-content-between py-3 mt-1 rounded"> -->
                    <div class="col-11 mt-2">
                        <label for="topic" class="form-label">Temat</label>
                        <input type="text" class="form-control" id="topic" name="temat"
                            style="line-height: 30px; font-size: 21px;" value="<?php echo $temat; ?>">
                    </div>
                    <div class="col-1 mt-2">
                        <label for="sentyment" class="form-label">Sentyment</label>
                        <select class="form-select" aria-label=".form-select-example" name="sentyment"
                            style="line-height: 30px; font-size: 25px; text-align: center;">
                            <option disabled selected value> --- </option>
                            <option value="1" <?php echo ($sent_sent === 1 ? " selected" : "");?>>+1</option>
                            <option value="0" <?php echo ($sent_sent === 0 ? " selected" : "");?>>0</option>
                            <option value="-1" <?php echo($sent_sent === (-1) ? " selected" : "");?>>-1</option>
                        </select>
                        <!-- <input type="text" class="form-control" id="sentyment" name="sentyment" style="line-height: 30px; font-size: 25px; text-align: center;" value="<?php echo $sent_sent;?>"> -->
                    </div>
                    
                </div>
                <div class="row py-4 justify-content-between">
                    <div class="col-2 g-3">
                        <a href="<?php echo "index.php?id=$id&posiedzenie=$posiedzenie_link&year=$data_link&kto=$kto_link&tekst=$tekst_link&top=$top_link&processed=$processed_link&strona=$page";?>"
                            class="btn btn-primary align-middle">Wróć</a>
                    </div>
                    <div class="col-3 d-grid">
                        <button class="btn btn-danger" style="line-height: 4em;">Zapisz</button>
                    </div>
                    
            </div>
            
                    <input type="hidden" name="id" value="<?php echo $id;?>">
                    <input type="hidden" name="year" value="<?php echo $data_link;?>">
                    <input type="hidden" name="kto" value="<?php echo $kto_link;?>">
                    <input type="hidden" name="top-words-link" value="<?php echo $top_link;?>">
                    <input type="hidden" name="tekst-link" value="<?php echo $tekst_link;?>">
                    <input type="hidden" name="sent-id" value="<?php echo $sent_id;?>">
                    <input type="hidden" name="strona" value="<?php echo $page;?>">
                    <input type="hidden" name="processed" value="<?php echo $processed_link;?>">
                    <!-- <input type="hidden" name="include_link" value="<?php echo $processed_;?>"> -->
            </div>
        </form>

        <?php } 
            $stmt->close();
            $conn->close();
        
            //echo "status ". get_status($sent_sent, $temat, $processed). "processed ". $processed . "sentyment: $sent_sent ". gettype($sent_sent) . " sentyment is set: ".isset($sent_sent);
        ?>

    </div>

</body>

</html>