<?php
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }
    if (isset($_SESSION['status'])) {
        if ($_SESSION['status'] === 'valid') {
            header("Location: home.php");
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>DSWD</title>

    <script>

    const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

    </script>

</head>
<body>
    <div class="main-container">
        <div class="box form-box">
            
            <?php
            require_once("../php/config.php");
            if (isset($_POST['submit'])){
                // RETRIEVE THE SUBMITTED USERNAME AND PASSWORD

                $username = $_POST['username'];
                $password = $_POST['password'];

                // PREPARE A STATEMENT
                $stmt = $conn->query("SELECT * FROM users WHERE Username = '$username'");
                // $stmt->bindParam(':username', $username);

                // EXECUTE THE STATEMENT
                // $stmt->execute();

                // FETCH USER ROW
                // $users = $stmt->fetch(PDO::FETCH_ASSOC);

                $users = $stmt->fetch_assoc();

                if ($users && password_verify($password, $users['Password'])){
                    if (password_verify($password, $users['Password'])){
                        //SUCCESSFUL LOGIN
                        $_SESSION['valid'] = $users['Email'];
                        $_SESSION['username'] = $users['Username'];
                        $_SESSION['fullname'] = $users['Fullname'];
                        $_SESSION['id'] = $users['Id'];
                        echo '<script>';
                        echo 'Toast.fire({
                            title: "Login Successfully!",
                            position: "top-end",
                            icon: "success",
                            showConfirmButton: "false",
                            timer: 1500,
                        }).then((result) => {
                            window.location.href = "home.php";
                        });';
                        echo '</script>';
                    }
                } 
                else {
                    echo "<div class='message'>
                          <p>Wrong username and password!</p>
                          </div> <br>";
                }
            }
            ?>
            
            <header class="title">Login</header>
                <form action="" method="post">
                    <div class="field-input">
                        <label for="username">Username: </label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div class="field-input">
                        <label for="password">Password: </label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="field-input">
                        <input type="submit" class="btn" name="submit" value="Login" required>
                    </div>
                    <div class="link">
                        Don't have an account? <a href="register.php">Register here.</a>
                    </div>
                </form>
        </div>
    </div>
</body>
</html>