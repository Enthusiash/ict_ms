<?php
    session_start();
    require_once("../php/config.php");
    if (!isset($_SESSION['valid'])){
        header("Location: login.php");
        exit(); // Ensure script stops executing after redirect
    }

    // Retrieve data from the database grouped by province
    $data = array();
    $result = $conn->query("SELECT province_code, COUNT(*) AS count 
                            FROM lib_upload 
                            GROUP BY province_code");

    while ($row = $result->fetch_assoc()) {
        $provinceCode = $row['province_code'];
        // Fetch province name based on the code
        $provinceName = fetchProvinceName($provinceCode, $conn);
        $count = $row['count'];
        $data[$provinceName] = $count;
    }

    // Function to fetch province name based on province code
    function fetchProvinceName($provinceCode, $conn) {
        // You should adjust this query according to your table structure
        $query = "SELECT province_code FROM lib_upload WHERE province_code = '$provinceCode'";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['province_code'];
        } else {
            return "Unknown"; // Default name if province code not found
        }
    }

    // Convert data into JavaScript-friendly format
    $xValues = array_keys($data);
    $yValues = array_values($data);
    $barColors = ["red", "green","blue","orange","brown", "pink", "violet"];
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
</head>
<body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <div class="nav">
        <div class="logo">
            <img src="https://1000logos.net/wp-content/uploads/2019/03/DSWD-Logo.png" alt="DSWD" style="width: 100px; height: 50px;">
            <p>Department of Social Welfare and Development</p>
        </div>
        <div class="right-links">
            <a href="../components/home.php"><p>Dashboard</p></a>
            <a href="../components/gallery.php"><p>Gallery</p></a>
            <!-- JavaScript onclick function to trigger confirmation prompt -->
            <a href="#" onclick="confirmLogout()"><button class="btn">Logout</button></a>
        </div>
    </div>
    <main>  
        <canvas id="myChart"></canvas>
        <script>
            // Function to display a toast notification for logout confirmation
            function confirmLogout() {
                Swal.fire({
                    title: 'Logout',
                    text: "Are you sure you want to logout?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    position: "top-end",
                    confirmButtonText: 'Yes, logout',
                    toast: true, // Set to true to display as a toast
                    customClass: {
                        popup: 'logout-toast', // Apply custom CSS class for resizing
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show Toast notification after confirmation
                        showLogoutToast();
                        // Redirect to logout page after a short delay
                        setTimeout(function() {
                            window.location.href = "../php/logout.php";
                        }, 1500);
                    }
                });
            }

            // Function to display the logout toast notification
            function showLogoutToast() {
                Swal.fire({
                    title: 'Logging out',
                    icon: 'success',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
            }

            var xValues = <?php echo json_encode($xValues); ?>;
            var yValues = <?php echo json_encode($yValues); ?>;
            var barColors = <?php echo json_encode($barColors); ?>;

            new Chart("myChart", {
                type: "bar",
                data: {
                    labels: xValues,
                    datasets: [{
                        backgroundColor: barColors,                  
                        data: yValues
                    }]
                },
                options: {
                    legend: {display: false},
                    title: {
                        display: true,
                        text: "Household Assessment Form 2024"
                    }
                }
            });
        </script>
    </main>
</body>
</html>
