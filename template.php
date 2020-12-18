<!DOCTYPE html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
        integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
    </script>
    <title>Select searching criteria</title>
</head>

<body>
    <div class="container-fluid">
        <?php
    // mysqli
    $conn = new mysqli("localhost", "root", "rootR98&5", "sejm_kopia");

    if($conn -> connect_error) {
        die("Connection failed" . $conn->connect_error . " " . $time_now . ")");
    }
    
    $query = "SELECT id, data, posiedzenie, kto, SUBSTRING(tekst, 1, 100) as tekst, top from posiedzenia WHERE posiedzenie = 14 and YEAR(data) = 2012 and processed = 1";
    $result = $conn->query($query);
    if(!$result) die('Query problem');
    echo ("<table class='table'><thead><tr><th scope='col'>id</th><th class='w-25 p-3' scope='col'>data</th><th scope='col'>posiedzenie</th><th scope='col'>kto</th><th scope='col'>tekst</th><th scope='col'>top words 30</th></tr></thead><tbody>");
    while($row = $result->fetch_array(MYSQLI_ASSOC)) {
        echo ("<tr><th scope='row'>" . $row["id"] . "</th><td>" . $row["data"] . "</td><td>" . $row["posiedzenie"] . "</td><td>" . $row["kto"] . "</td><td>" . $row["tekst"] . "[...]</td><td>" . $row["kw_top30"] . "</td></tr>");
    };
    echo "</tbody></table>";
    $result->close();
    $conn->close();
    ?>
    
    </div>
</body>

</html>