<?php
    function make_pagination($page=0, $count, $limit=20, $params){
        $pages = ceil($count/$limit);
        $active_page = $page;
        $last_page = $pages;
        $prev_page = $page - 1;
        $next_page = $page + 1;
    
        $param_link = get_param_link($params);
    
        echo "<nav aria-label='Nawigacja'><ul class='pagination pagination-sm justify-content-center m-2'>";
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

    function get_param_link($params){
        if(isset($params)){
        $param_link = '';
        foreach($params as $param => $value){
            $param == 'YEAR(data)' ? $param = "year" : $param;
            $param == 'p.tekst' ? $param = 'tekst' : $param;
            //$param == 'processed' ? $value = sanitize_filter($_POST['include']) : $param;
            if($param_link == ''){
                $param_link = $param . "=". $value;
            } else {
                $param_link = $param_link . "&" . $param . "=" . $value;
            }
        }
        return $param_link;
        } else {
            return '';
        }
    }

   

?>