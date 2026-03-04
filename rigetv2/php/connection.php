<?php
    $host ="82.165.6.246";
    $dbname ="pg25_234759";
    $user= "pg25alexanderbrough";
    $password ="pgab_739984";

    $connection =pg_connect("host=$host dbname=$dbname user=$user password=$password");

    if ($connection){
        echo "";
    }
    else {
        die("killing process " . pg_last_error());
        echo "connection failed";
    }
?>