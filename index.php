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
    
    function sanitize_filter($post_val){
        return filter_var($post_val, FILTER_SANITIZE_STRING);
    }

    $conn = get_connection();
    if(!isset($processed)){$processed = 1;};
    if(!isset($_GET['strona'])){
        $page = 1;
    } else {
        $posiedzenie = $_GET['posiedzenie'];
        $year = $_GET["year"];
        $kto = $_GET["kto"];
        $tekst = $_GET["tekst"];
        $top = $_GET["top"];
        $processed = $_GET["processed"];
        $page = $_GET['strona'];
        $delete = $_GET['delete'];
        $get = true;
        $id_get = $_GET['id'];
    }
    
    if (isset($_POST["posiedzenie"])){ 
        $posiedzenie = sanitize_filter($_POST['posiedzenie']);
        $year = sanitize_filter($_POST["year"]);
        $kto = sanitize_filter($_POST["kto"]);
        $tekst = sanitize_filter($_POST["tekst"]);
        $top = sanitize_filter($_POST["top"]);
        $processed = sanitize_filter($_POST["include"]);
        $post = true;
    }
    if ($top == ''){
        $top = 'NULL';
    }
    $processed_to_form = $processed;

    if($processed == 0){
        $processed = '';
        $join = 'LEFT';

    } elseif($processed == 2) {
        $join = '';
        //$processed = '';
    } else {
        $join = 'LEFT';
    }
    ?>
    <div class="container">
    <?php include "header.php"; ?>
        
        <form class="form-floating" id="search" action="index.php" method="POST">
            <div class="row align-items-center py-2">
                <div class="col-1 form-floating g-2">
                    <input class="form-control" type="search" id="posiedzenie" name="posiedzenie"
                        placeholder="Podaj numer posiedzenia">
                    <label for="posiedzenie">Posiedzenie</label>
                </div>
                <div class="col-1 form-floating g-2">
                    <input class="form-control" type="search" id="year" name="year" placeholder="Rok posiedzenia">
                    <label for="rok">Rok</label>
                </div>
                <div class="col-2 form-floating g-2">
                    <input class="form-control" type="search" id="kto" name="kto" placeholder="Kogo szukasz?">
                    <label for="kto" class="form-label">Osoba</label>
                </div>
                <div class="col-3 form-floating g-2">
                    <input class="form-control" type="search" id="tekst" name="tekst"
                        placeholder="Co zostało powiedziane?">
                    <label for="tekst" class="form-label">Tekst</label>
                </div>
                <div class="col-2 form-floating g-2">
                    <input class="form-control" type="search" id="top" name="top" placeholder="Podaj kluczowe słowa">
                    <label for="top" class="form-label">Słowa kluczowe</label>
                </div>
                <div class="col-2">
                    <select class="form-select" aria-label=".form-select-example" name="include">
                        <option value="0" <?php echo ($processed_to_form == 0 ? " selected" : "");?>>wszystkie</option>
                        <option value="1" <?php echo ($processed_to_form == 1 ? " selected" : "");?>>istotne</option>
                        <option value="2" <?php echo($processed_to_form == 2 ? " selected" : "");?>>sentyment</option>
                        <option value="3" <?php echo($processed_to_form == 3 ? " selected" : "");?>>bez sentymentu</option>
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
        <div class="row">
            <?php

        function get_status($sentyment, $temat, $processed) {
            if($sentyment > 1 || (isset($temat) && $temat !=='' && (!isset($sentyment) || $sentyment === '' )) || (!isset($temat) && isset($sentyment) && $sentyment !== '')){
                return -1;
            } elseif(($processed === 1  || $processed === 2 || $processed === 3) && isset($sentyment)) {
                return 1;
            }  elseif($processed === 1){
                return 0;
            } else {
                return -999;
            }
        }

    function like_stmt($param) {
        return "%$param%";
        }

    $limit = 20;
    
    
    if($post || $get){

    $params = array(
        'p.posiedzenie' => $posiedzenie, 
        'YEAR(p.data)' =>$year, 
        'p.kto' => $kto, 
        'p.text_css' => $tekst, 
        't.top' => $top, 
        'p.processed' => $processed,
        //'delete' => $delete    
    );

    if($top == 'NULL'){
        unset($params['t.top']);
    }

    if($processed == 3){
        $params['s.sentyment'] = 'NULL';
    }

    //$params['processed'] == 2 ? $params['processed'] = 1 : $params;
    //echo "paramsy: ". $params['processed'];

    function no_sentyment($param){
        if(isset($param)){
            return " and s.sentyment is null ";
        }
    }
    function query_constructor($params, $limit, $page, $join = 'LEFT'){
        $where = '';
        foreach($params as $param => $value){
            if($param == 's.sentyment'){
                continue;
            }
            if (in_array($param, array("YEAR(data)", "p.kto", "p.text_css", "t.top"))){
                if($param == "t.top" and $value == 'NULL'){
                    $param_clause = $param . " is NULL";
                } else {
                $param_clause = $param . " like ?";
                }
            } elseif($value == '') {
                $param_clause = $param . " like ?";
            } else {
                $param_clause = $param . " = ?";
            }
            if($where == ''){
                $where = $where . $param_clause;
            } else {
                $where = $where ." and " . $param_clause;
            }
        }
        if($limit != 0) {
                $query = "SELECT p.id, p.data, p.posiedzenie, p.kto, p.text_css, t.top, s.tekst, s.temat, s.sentyment, p.processed FROM posiedzenia p ".$join." JOIN sentyment s ON p.id = s.pos_tekst_id LEFT JOIN top t on t.pos_tekst_id = p.id WHERE " . $where . no_sentyment($params['s.sentyment']). "  ORDER BY p.data ASC, day(p.data) DESC, p.posiedzenie ASC, p.id ASC LIMIT ". (($page - 1) * $limit) .", " .$limit;
            } else {
            $query = "SELECT COUNT(p.id) FROM posiedzenia p ".$join." JOIN sentyment s ON p.id = s.pos_tekst_id LEFT JOIN top t on t.pos_tekst_id = p.id WHERE " . $where;
        }
        return $query;
    }
    //echo query_constructor($params, $limit, $page, $join);
    function parse_params($params) {
        $values = array();
        if($params['processed'] == 2 || $params['processed'] == 3){
            $params['processed'] = 1;
            unset($params['s.sentyment']);
        }
        foreach($params as $param=>$value) {
            if($param == 'delete'){continue;}
            if (in_array($param, array("YEAR(data)", "p.kto", "p.text_css", "t.top")) OR $value == ''){
                $value = "%$value%";
            }
            array_push($values, $value);
        }
        return $values;
    }
    function get_params_string($params){
        return str_repeat('s', count($params));
        }


    //get count of all rows returned by query
    $conn = get_connection();
    if ($stmt = $conn->prepare(query_constructor($params, 0, $page, $join))) {
        $qry_params = parse_params($params);
        $stmt->bind_param(get_params_string($params), ...$qry_params );
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($count);
        $stmt->fetch();
        //$conn = new mysqli("localhost", "root", "rootR98&5", "sejm_orka_all");
        //echo (query_constructor($params, 0, $page, $join));
    } else {
          echo "statement coud not be executed in counter\n";
        //   echo (query_constructor($params, 0, $page, $join));
    }

    if($delete==1){
        $conn = get_connection();
        $updateqry = "UPDATE posiedzenia SET processed = 0 WHERE id = ?";
        if($stmt = $conn->prepare($updateqry)){
            $stmt->bind_param('i', $id_get);
            $stmt->execute();
            $stmt->store_result();
        }
    }
    //create placeholder

    $conn = get_connection();
    if ($stmt = $conn->prepare(query_constructor($params, $limit, $page, $join))) {
        $qry_params = parse_params($params);
        $stmt->bind_param(get_params_string($params), ...$qry_params );
        $stmt->execute();
        $stmt->store_result();
        // echo(query_constructor($params, $limit, $page, $join));
    } else {
          echo "statement could not be executed";
    }
    $stmt->bind_result($id, $data, $posiedzenie, $kto, $tekst, $top, $sent_tekst, $temat, $sentyment, $processed);
    

    function print_status($sentyment, $temat, $processed) {
        if($sentyment > 1 || ($temat !== '' && $sentyment === '') || ($temat ==='' && $sentyment !== '')){
            return "<span class='text-danger'><svg class='bi' width='24' height='24' fill='currentColor'><use xlink:href='bootstrap-icons.svg#exclamation-circle-fill'/><span>";
        }
        if(($processed == 1 || $processed == 2) && !is_null($sentyment)) {
            return "<span class='text-success'><svg class='bi' width='24' height='24' fill='currentColor'><use xlink:href='bootstrap-icons.svg#chat-text-fill'/><span>";
        } 
        if($processed == 1){
            return "<span class='text-secondary'><svg class='bi' width='24' height='24' fill='currentColor'><use xlink:href='bootstrap-icons.svg#info-circle'/><span>";
        }
    }

    // function get_mid_pages($page){
    //     $mod = $page % 3;
    //     $pos = ($mod == 0) ? 2 : (($mod == 1) ? 3 : 1);
    //     foreach(range(0, 4) as $item){
    //         $pages = array();
    //         $pags[$item] = ($page + ($item - $pos));
    //     }
    //     return $pags;
    // }

    

    
    make_pagination($page, $count, $limit, map_params($params));
    $param_link = get_param_link(map_params($params));
    ?>
    <div class="container border border-1 rounded">
    <?php
    echo ("<table class='table table-striped table-hover'>
        <thead>
            <tr class='text-center'>
                <th>S</th>
                <th>data</th>
                <th>posiedz</th>
                <th>kto</th>
                <th>tekst</th>
                <th>kluczowe</th>
                <th colspan=2>akcja</th>
            </tr>
            </thead>
        <tbody>");
    while($stmt->fetch()){
        echo ("<tr ". ($processed != 1 ? ("class='text-secondary'") : ("") ).">
                <th scope='row' class='align-middle'>" . print_status($sentyment, $temat,  $processed) . "</th>
                <td style='width: 110px;'><p class='fw-light fs-6 text-center'>" . $data . "</p></td>
                <td><p class='fw-light text-center'>" . $posiedzenie . "</p></td>
                <td>" . $kto . "</td><td>" . text_limit($tekst, 300) . "</td>
                <td>" . $top . "</td>
                <td class='align-middle'>
                    <a href ='edit_statement.php?id=" . $id . "&". $param_link ."&&source=1&strona=".$page."'>
                    <svg class='bi' width='25' height='25' fill='currentColor'><use xlink:href='bootstrap-icons.svg#pencil-square'/></a></td>
                    <td class='align-middle'>");
                if(get_status($sentyment, $temat, $processed) == 0){
                    echo "<a href='index.php?id=".$id."&".$param_link."&strona=".$page."&delete=1' class='text-danger'><svg class='bi' width='25' height='25' fill='currentColor'><use xlink:href='bootstrap-icons.svg#trash'/></a>";
                }
          echo ("</td>
          </tr>");
    }
    echo "</tbody></table>";
    make_pagination($page, $count, $limit, map_params($params));
    $stmt->close();
    $conn->close();
}
    ?>
    </div>
    </div>
    </div>
</body>

</html>