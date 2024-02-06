<?php

require_once("../php/config.php");

    $response = "";

    if (isset($_POST['province']) && !empty($_POST['province'])) {
        $response = $_POST['province'];
    } else {
        $response = null;
    }

    echo $response;
?>
