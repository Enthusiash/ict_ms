<?php 
    require_once("../php/config.php");

    if (isset($_GET['id'])){
        $queryString = "UPDATE lib_upload SET is_Archive = 1 WHERE id = ". $_GET['id'];

        $result = $conn->query($queryString);

        header("Location: gallery.php");
    }

?>