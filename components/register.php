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
            if(isset($_POST['submit'])){
                $fullname = $_POST['fullname'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];

                // COMPARE PASSWORDS
                if ($password !== $confirm_password) {
                    echo "<div class='message'>
                            <p>Password does not match!</p>
                          </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go back</button>";
                    return;
                }

                // HASHING PASSWORD USING BCRYPT
                $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // VERIFYING UNIQUE EMAIL
                $verify_query = $conn->query("SELECT Email FROM users WHERE Email = '$email'");
                // $verify_query->bindParam(':email', $email);
                // $verify_query->execute();

                if ($verify_query->num_rows != 0) {
                    echo "<div class='message'>
                            <p>This email is used, try another one!</p>
                        </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go back</button>";
                } else {
                    $insert_query = $conn->prepare("INSERT INTO users (Fullname, Username, Email, Password) VALUES (?, ?, ?, ?)");
                    $insert_query->bind_param('ssss', $fullname, $username, $email, $hashed_password);
                    $insert_query->execute();

                    echo '<script>';
                    echo 'Toast.fire({
                        title: "Registered Successfully!",
                        position: "top-end",
                        icon: "success",
                        showConfirmButton: "false",
                        timer: 1500,
                    }).then((result) => {
                        window.location.href = "login.php";
                    });';
                    echo '</script>';
                }
                // Close the PDO connection
                $conn->close();
            } else {

        ?>

            <header>Register</header>
                <form action="" method="post">
                    <div class="field-input">
                        <label for="fullname">Full Name: </label>
                        <input type="text" name="fullname" id="fullname" autocomplete="off" required>
                    </div>
                    <div class="field-input">
                        <label for="username">Username: </label>
                        <input type="text" name="username" id="username" autocomplete="off" required>
                    </div>
                    <div class="field-input">
                        <label for="email">Email: </label>
                        <input type="text" name="email" id="email" autocomplete="off" required>
                    </div>
                    <div class="field-input">
                        <label for="password">Password: </label>
                        <input type="password" name="password" id="password" autocomplete="off" required>
                    </div>
                    <div class="field-input">
                        <label for="confirm_password">Confirm Password: </label>
                        <input type="password" name="confirm_password" id="confirm_password" autocomplete="off" required>
                    </div>
                    <div class="field-input">
                        <input type="submit" class="btn" name="submit" value="Register" required>
                    </div>
                    <div class="link">
                        Already have an account? <a href="login.php">Login here.</a>
                    </div>
                </form>
        </div>
        <?php } ?>
    </div>
</body>
</html>