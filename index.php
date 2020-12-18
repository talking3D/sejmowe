<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <title>Select searching criteria</title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
    </script>
    <?php 
    function get_connection(){
        $conn = new mysqli('localhost', 'root', 'rootR98&5', 'sejm_kopia');
        if($conn->connect_error){
            die("Niestety połącznie nie powiodło się. Prosimy o kontakt z administratorem");
        } else {
            return $conn;
        }
    }
    function sanitize_filter($post_val){
        return filter_var($post_val, FILTER_SANITIZE_STRING);
    }

    //$conn = get_connection();
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
        echo "delete wysłany";
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
    $processed_to_form = $processed;

    if($processed == 0){
        $processed = '';
        $join = 'LEFT';

    } elseif($processed == 2) {
        $join = '';
        $processed = '';
    } else {
        $join = 'LEFT';
    }
    ?>
    <div class="container">
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
                    <select class="form-select form-select-sm" aria-label=".form-select-sm example" name="include">
                        <option value="0" <?php echo ($processed_to_form == 0 ? " selected" : "");?>>Wszystkie</option>
                        <option value="1" <?php echo ($processed_to_form == 1 ? " selected" : "");?>>Istotne</option>
                        <option value="2" <?php echo($processed_to_form == 2 ? " selected" : "");?>>Sentyment</option>
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

        function sanitizeString($var){
            $var = stripslashes($var);
            $var = strip_tags($var); 
            $var = htmlentities($var); 
            return $var;
        }

        function get_status($sentyment, $temat, $processed) {
            if($sentyment > 9 || (isset($temat) && $temat !=='' && (!isset($sentyment) || $sentyment === '' )) || (!isset($temat) && isset($sentyment) && $sentyment !== '')){
                return -1;
            } elseif($processed === 1 && isset($sentyment)) {
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
    // mysqli
    // $conn = new mysqli("localhost", "root", "rootR98&5", "sejm_kopia");
    // if($conn -> connect_error) {
    //     die("Connection failed" . $conn->connect_error . " " . $time_now . ")");
    // }
    $params = array(
        'posiedzenie' => $posiedzenie, 
        'YEAR(data)' =>$year, 
        'kto' => $kto, 
        'p.tekst' => $tekst, 
        'top' => $top, 
        'processed' => $processed,
        //'delete' => $delete    
    );

    function query_constructor($params, $limit, $page, $join = 'LEFT'){
        $where = '';
        foreach($params as $param => $value){
            //if($param == 'delete'){continue;}
            if (in_array($param, array("YEAR(data)", "kto", "p.tekst", "top"))){
                $param_clause = $param . " like ?";
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
                $query = "SELECT p.id, p.data, p.posiedzenie, p.kto, p.tekst, p.top, s.tekst, s.temat, s.sentyment, p.processed FROM posiedzenia p ".$join." JOIN sentyment s ON p.id = s.pos_tekst_id WHERE " . $where .  "  ORDER BY p.data DESC, p.posiedzenie ASC, p.id ASC LIMIT ". (($page - 1) * $limit) .", " .$limit;
            } else {
            $query = "SELECT COUNT(p.id) FROM posiedzenia p ".$join." JOIN sentyment s ON p.id = s.pos_tekst_id WHERE " . $where;
        }
        return $query;
    }
    //echo query_constructor($params, $limit, $page, $join);
    
    function parse_params($params) {
        $values = array();
        foreach($params as $param=>$value) {
            if($param == 'delete'){continue;}
            if (in_array($param, array("YEAR(data)", "kto", "p.tekst", "top")) OR $value == ''){
                $value = "%$value%";
            }
            array_push($values, $value);
        }
        return $values;
    }

    //get count of all rows returned by query
    $conn = get_connection();
    if ($stmt = $conn->prepare(query_constructor($params, 0, $page, $join))) {
        $qry_params = parse_params($params);
        $stmt->bind_param('ssssss', ...$qry_params );
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        //$conn = new mysqli("localhost", "root", "rootR98&5", "sejm_kopia");
    } else {
          echo "statement coud not be executed in counter";
    }

    if($delete==1){
        $conn = get_connection();
        $updateqry = "UPDATE posiedzenia SET processed = 0 WHERE id = ?";
        echo "aktuLIZUJĘ wiersz $id_get";
        if($stmt = $conn->prepare($updateqry)){
            $stmt->bind_param('i', $id_get);
            $stmt->execute();
            echo "zaktualizowano $id";
        }
    }
    //create placeholder

    $conn = get_connection();
    if ($stmt = $conn->prepare(query_constructor($params, $limit, $page, $join))) {
        $qry_params = parse_params($params);
        $stmt->bind_param('ssssss', ...$qry_params );
        $stmt->execute();
    } else {
          echo "statement could not be executed";
    }
    $stmt->bind_result($id, $data, $posiedzenie, $kto, $tekst, $top, $sent_tekst, $temat, $sentyment, $processed);
    

    
    function text_limit($text, $max_chars)  {
        if (strlen($text) > $max_chars) {
            return substr($text, 0, $max_chars) . "[...]";
        } else {
            return $text;
        }
        }
    function print_status($sentyment, $temat, $sent_tekst, $processed) {
        if($sentyment > 9 || ($temat != '' && $sentyment === '') || ($temat ==='' && $sentyment != '')){
            return "<span class='text-danger'><svg class='bi' width='24' height='24' fill='currentColor'><use xlink:href='bootstrap-icons.svg#exclamation-circle-fill'/><span>";
        }
        if($processed == 1 && $sentyment != '') {
            return "<span class='text-success'><svg class='bi' width='24' height='24' fill='currentColor'><use xlink:href='bootstrap-icons.svg#chat-text-fill'/><span>";
        } 
        if($processed == 1){
            return "<span class='text-secondary'><svg class='bi' width='24' height='24' fill='currentColor'><use xlink:href='bootstrap-icons.svg#info-circle'/><span>";
        }
    }

    function get_mid_pages($page){
        $mod = $page % 3;
        $pos = ($mod == 0) ? 2 : (($mod == 1) ? 3 : 1);
        foreach(range(0, 4) as $item){
            $pages = array();
            $pags[$item] = ($page + ($item - $pos));
        }
        return $pags;
    }

    function get_param_link($params){
        $param_link = '';
        foreach($params as $param => $value){
            ($param == 'YEAR(data)') ? $param = "year" : $param;
            if($param_link == ''){
                $param_link = $param . "=". $value;
            } else {
                $param_link = $param_link . "&" . $param . "=" . $value;
            }
        }
        return $param_link;
    }


    function make_pagination($page=0, $count, $limit=20, $params){
        $pages = ceil($count/$limit);
        $active_page = $page;
        $last_page = $pages;
        $prev_page = $page - 1;
        $next_page = $page + 1;
    
        $param_link = get_param_link($params);
    
        echo "<nav aria-label='Nawigacja'><ul class='pagination pagination-sm justify-content-center'>";
        //pierwszy przycisk paginacji
        echo "<li class='page-item ". (($page == 1) ? "disabled" : "") ."'><a class='page-link' href='?".$param_link."&strona=". $prev_page."' tabindex='-1' aria-disabled='true'>Poprzednia</a></li>";
        //środek paginacji
        
        $max_cells = ($pages <= 9 ? $pages : 9);
        if($pages <=9){
            for($r=1; $r<$max_cells; $r++){
                echo "<li class='page-item ".(($r == $active_page) ? " active" : "")."'><a class='page-link' href='?". $param_link ."&strona=". $r ."'>". $r ."</a></li>";
            }
        } elseif($page < 5 || $page > ($pages - 4)) {
            echo "<li class='page-item ".(($active_page == 1) ? " active" : "")."'><a class='page-link' href='?". $param_link ."&strona=1'>1</a></li>";  
            for($page_item = 2; $page_item<6; $page_item++){
                echo "<li class='page-item ".(($page_item == $active_page) ? " active" : "")."'><a class='page-link' href='?". $param_link ."&strona=". $page_item ."'>". $page_item ."</a></li>";
            }
            echo "<li class='page-item disabled'><a class='page-link' href='#'>...</a></li>";
            for($page_item = $pages-4; $page_item < $pages; $page_item++){
                echo "<li class='page-item ".(($page_item == $active_page) ? " active" : "")."'><a class='page-link' href='?". $param_link ."&strona=". $page_item ."'>". $page_item ."</a></li>";
            }
            
        } else {
            echo "<li class='page-item'><a class='page-link' href='?". $param_link ."&strona=1'>1</a></li>";
            echo "<li class='page-item disabled'><a class='page-link' href='#'>...</a></li>";
            $midpages = get_mid_pages($page);
            foreach($midpages as $page_item){
                echo "<li class='page-item ".(($page_item == $active_page) ? " active" : "")."'><a class='page-link' href='?". $param_link ."&strona=". $page_item ."'>". $page_item ."</a></li>";
            }
            echo "<li class='page-item disabled'><a class='page-link' href='#'>...</a></li>";
        }
            echo "<li class='page-item ".(($active_page == $last_page) ? " active" : "")."'><a class='page-link' href='?". $param_link ."&strona=". $last_page ."'>". $last_page ."</a></li>";
        echo "<li class='page-item ". (($page == $last_page) ? "disabled" : "") ."'><a class='page-link' href='?". $param_link ."&strona=". $next_page."'>Następna</a></li>";
        echo "</ul></nav>";
    }
   

    make_pagination($page, $count, $limit, $params);
    $param_link = get_param_link($params);
    echo ("<table class='table table-hover caption-top'>
        <thead>
            <tr>
                <th scope='col'>S</th>
                <th scope='col'>data</th>
                <th scope='col'>posiedz</th>
                <th scope='col'>kto</th>
                <th scope='col-2'>tekst</th>
                <th scope='col-3'>kluczowe</th>
                <th scope='col-3'>akcja</th>
            </tr>
            </thead>
        <tbody>");
    while($stmt->fetch()){
        echo ("<tr ". ($processed != 1 ? ("class='text-secondary'"): ("") ).">
                <th scope='row' class='align-middle'>" . print_status($sentyment, $temat, $sent_tekst, $processed) . "</th>
                <td class='col-2'><p class='fw-light'>" . $data . "</p></td>
                <td><p class='fw-light'>" . $posiedzenie . "</p></td>
                <td class='col-2'>" . $kto . "</td><td class='col-5'>" . text_limit($tekst, 300) . "</td>
                <td>" . $top . "</td>
                <td align='center'>
                    <a  href ='edit_statement.php?id=" . $id . "&". $param_link ."&strona=".$page."'>
                    <svg class='bi' width='25' height='25' fill='currentColor'><use xlink:href='bootstrap-icons.svg#pencil-square'/></a>");
                if(get_status($sentyment, $temat, $processed) == 0){
                    echo "<a  href='index.php?id=".$id."&".$param_link."&strona=".$page."&delete=1' class='text-danger'><svg class='bi' width='25' height='25' fill='currentColor'><use xlink:href='bootstrap-icons.svg#trash'/></a>";
                }
          echo ("</td>
          </tr>");
    }
    echo "</tbody></table>";
    make_pagination($page, $count, $limit, $params);
    $stmt->close();
    $conn->close();
}
    ?>

        </div>
    </div>
</body>

</html>