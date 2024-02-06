<?php
    // $servername = "localhost";
    // $username = "root";
    // $password = "";
    // $dbname = "login-register";

    // try {
    //     $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    //     // set the PDO error mode to exception
    //     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //     // echo "Connected successfully";
    // } catch(PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
    // }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "login-register";

    // Create a database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>