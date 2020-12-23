<?php 
    function get_button($script){
        $fname = basename($script, ".php");
        $sactive = 'btn btn-sm btn-success ms-2 disabled';
        $sdefault = 'btn btn-sm btn-outline-secondary ms-2';
        if($fname == 'index'){
           $szukaj = $sactive;
           $sentymenty = $sdefault;
        } elseif($fname == 'sentymenty') {
            $szukaj = $sdefault;
            $sentymenty = $sactive;
        }
        if($fname == 'edit_statement'){
            echo "<a href='index.php' class='btn btn btn-danger ms-2 disabled'><strong>Uwaga!</strong> Edycja</a>";
            $szukaj = $sdefault;
            $sentymenty = $sdefault;
        }
        echo "<a href='index.php' class='".$szukaj."'>Wyszukiwarka</a>";
        echo "<a href='sentymenty.php' class='".$sentymenty."'>Sentymenty</a>";
    }
?>
<div class="row justify-content-between border border-1 rounded">
    <nav class="navbar navbar-light bg-light">
        <div class="col-4">
            <?php get_button($_SERVER['SCRIPT_NAME']); ?>
        </div>
        <div class="col-4 text-end">
            <form action="logout.php" method="POST">
                <button name="logout" class="btn btn-outline-danger ms-2" type="submit">Wyloguj <svg class="bi" width="24" height="24" fill="currentColor"><use xlink:href="bootstrap-icons.svg#arrow-bar-right"/></button>
            </form>
            </div>
    </nav>
</div>