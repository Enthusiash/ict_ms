<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <title>DSWD</title>
    <script>

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    $(document).ready(function () {
        var userIdValue = localStorage.getItem('user_id');
        
        // Set the value of the hidden input field to the retrieved user_id value
        $("#user_id").val(userIdValue);
    });

    const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

    // Function to display toast notification for logout confirmation
    function confirmLogout(){
        Swal.fire({
            title: 'Logout',
            text: "Are you sure you want to logout?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            position: "top-end",
            confirmButtonText: 'Yes, logout',
            toast: true,
            customClass: {
                popup: 'logout-toast',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Show toast notication after confirmation
                showLogoutToast();
                // Redirect to logout page after the delay
                setTimeout(function(){
                    window.location.href = "../php/logout.php";
                    localStorage.removeItem('user_id');
                }, 1500);
                }
        });
    }

    // Function to display the logout toast notification
    function showLogoutToast() {
        Swal.fire({
            title: 'Logging out!',
            icon: 'success',
            position: 'top-end',
            showConfirmButton: false,
            timer: '1500'
        });
    }
    </script>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <img src="https://1000logos.net/wp-content/uploads/2019/03/DSWD-Logo.png" alt="DSWD" style="width: 100px; height: 50px;">
            <p>Department of Social Welfare and Development</p>
        </div>
        <div class="right-links">
        
            <a href="../components/home.php"><p>Dashboard</p></a>
            <a href="../components/gallery.php"><p>Gallery</p></a>
            <a href="../components/support.php"><p>Support</p></a>
            <a href="#" onclick="confirmLogout()"><button class="btn">Logout</button></a>

        </div>
    </div>

    <div class="support-main-container">
        <div class="supp-description">
            <p>
            A dynamic system empowers users with an intuitive interface and a suite of robust features tailored to enhance productivity. With a glance at our comprehensive dashboard, users can effortlessly view the total count of files, gaining valuable insights into their data repository. Seamlessly upload files of any format, enabling swift integration of new content into the system. Efficient file management capabilities allow for easy organization and categorization, ensuring streamlined access to essential documents. Users can seamlessly view and download files, facilitating collaboration and information sharing across teams.
            </p>
        </div>
        <div class="message-form">

        <?php 
            require('../php/config.php');

            if (isset($_POST['submit'])) {

                $user_id =$_POST['user_id'];
                $comments = $_POST['message'];
                    
                mysqli_query($conn, "INSERT INTO `lib_comments` (`id`, `user_id`, `comment_date`, `comments`) VALUES (NULL, '$user_id', now(), '$comments');");

                echo '<script>';
                echo 'Toast.fire({
                    title: "Comment Successfully!",
                    position: "top-end",
                    icon: "success",
                    showConfirmButton: "false",
                    timer: 1500,
                }).then((result) => {
                    window.location.href = "support.php";
                });';
                echo '</script>';
            }
        ?>

        <form method="POST">
            <p><label for="message">Feedbacks:</label></p>
            <textarea id="message" name="message" rows="4" cols="70"></textarea>
            <span class="span-supp">
            <input type="hidden" id="user_id" name="user_id">
            <input class="supp-btn" type="submit" name="submit" value="Submit">
            </span>
        </form>

        </div>
    </div>

    <div class="supp-footer">
            <p>
                MADE WITH EAGERNESS TO LEARN AND EXPLORE.
            </p>
    </div>

</body>
</html>