<?php
    require_once("../php/config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/style.css"> 
    <title>DSWD</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    $(document).ready(function () {
    // Function to update the dropdown options
    function updateDropdown(dropdown, data) {
        dropdown.empty(); // Clear existing options
        dropdown.append('<option value="" selected disabled>None</option>');
        $.each(data, function (index, option) {
            dropdown.append('<option value="' + option.code + '">' + option.name + '</option>');
        });
    }

    // Province selection
    $("#province").on('change', function () {
        let selectedProvince = $(this).val();
        if (selectedProvince) { // Only fetch data if a province is selected
            $.ajax({
                type: 'POST',
                url: 'locationUtil.php',
                data: { province: selectedProvince },
                success: function (response) {
                    axios.get(`https://psgc.gitlab.io/api/provinces/${response}/municipalities/`)
                        .then((res) => {
                            // Update the municipalities dropdown
                            updateDropdown($('#municipality'), res.data);

                            // Clear and reload the barangay dropdown
                            $('#barangay').empty();
                        })
                        .catch((err) => {
                            console.log(err);
                            console.error('Error fetching municipalities');
                        });
                },
                error: function () {
                    console.error('Error fetching data from locationUtil.php');
                }
            });
        }
    });

    // Municipality selection
    $("#municipality").on('change', function () {
        let selectedMunicipality = $(this).val();
        axios.get(`https://psgc.gitlab.io/api/municipalities/${selectedMunicipality}/barangays/`)
            .then((res) => {
                // Update the barangays dropdown
                updateDropdown($('#barangay'), res.data);
            })
            .catch((err) => {
                console.log(err);
                console.error('Error fetching barangays');
            });
    });
});

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

    </script>

<?php

require_once("../php/config.php");

$target_dir = "uploads/";
$uploadOk = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Get the selected province, municipality, and barangay
    $selectedProvince = isset($_POST["province"]) ? $_POST["province"] : "";
    $selectedMunicipality = isset($_POST["municipality"]) ? $_POST["municipality"] : "";
    $selectedBarangay = isset($_POST["barangay"]) ? $_POST["barangay"] : "";

    // Check if the location is selected
    if (empty($selectedProvince) || empty($selectedMunicipality) || empty($selectedBarangay)) {
        echo "Please select a valid location.";
        exit;
    }

    // Define the upload directory based on the selected location
    $uploadDirectory = "uploads/$selectedProvince/$selectedMunicipality/$selectedBarangay/";

    // Create the directory structure if it doesn't exist
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // Count total files
    $countfiles = count($_FILES['fileToUpload']['name']);

    // Loop through each file
    for ($i = 0; $i < $countfiles; $i++) {
        $target_file = $uploadDirectory . basename($_FILES["fileToUpload"]["name"][$i]);
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is a PDF
        if ($fileType !== 'pdf') {
            echo '<script>alert("Sorry, only PDF files are allowed.");</script>';
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo '<script>alert("Sorry, file already exists.");</script>';
            $uploadOk = 0;
        }

        // Check file size / limit to 5 megabytes
        if ($_FILES["fileToUpload"]["size"][$i] > 5 * 1024 * 1024) {
            echo '<script>alert("Sorry, maximum file is 5MB.");</script>';
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo '<script>alert("Sorry, your file is not uploaded.");</script>';
        } else {
            // Try to upload file
            $province_code = $_POST['province'];
            $municipality_code = $_POST['municipality'];
            $barangay_code = $_POST['barangay'];
            // Get file extension
            $file_extension = pathinfo($_FILES["fileToUpload"]["name"][$i], PATHINFO_EXTENSION);
            // Generate a new file name with Barangay Code and "_01"
            $new_file_name = $barangay_code . "_01." . $file_extension;
            // Check for existing files with the same name and increment the suffix
            $suffix = 1;
            while (file_exists($uploadDirectory . $new_file_name)) {
                $suffix++;
                $new_file_name = $barangay_code . "_" . str_pad($suffix, 2, '0', STR_PAD_LEFT) . "." . $file_extension;
            }
            $target_file = $uploadDirectory . $new_file_name;

            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
                $conn->query("INSERT INTO lib_upload(province_code, municipality_code, barangay_code, date_uploaded, file_name) 
                VALUES ('$province_code', '$municipality_code','$barangay_code', now(), '$new_file_name')")
                 or die(mysqli_error($conn));
                echo "<script>The file " . htmlspecialchars($new_file_name) . " has been uploaded.<br></script>";
            } 
            else {
                 echo '<script>alert("Sorry, there was an error uploading your file.");</script>';
            }
        }
    }
}
?>

</head>
<body>

    <div class="nav">
        <div class="logo">
            <img src="https://1000logos.net/wp-content/uploads/2019/03/DSWD-Logo.png" alt="DSWD" style="width: 100px; height: 50px;">
            <p>Department of Social Welfare and Development</p>
        </div>
            <div class="right-links">
            
            <?php
                    
                ?>
                <a href="../components/home.php"><p>Dashboard</p></a>
                <a href="../components/gallery.php"><p>Gallery</p></a>
                <a href="#" onclick="confirmLogout()"><button class="btn">Logout</button></a>

            </div>
    </div>

    <div class="gallery-main-container">
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="dropdowns">
            <div class="dropdown-items">
                <label for="province">Province:</label>
                <?php

                    $apiUrl = 'https://psgc.gitlab.io/api/regions/030000000/provinces/';

                    // Perform the API call
                    $response = file_get_contents($apiUrl);

                    // Check if the call was successful
                    if ($response === false) {
                        die('Error fetching data from the API');
                    }

                    // Decode the JSON response
                    $data = json_decode($response, true);

                    // Check if the JSON decoding was successful
                    if ($data === null) {
                        die('Error decoding JSON data');
                    }

                    // Define a custom comparison function for sorting
                    function compareByName($a, $b) {
                        return strcmp($a['name'], $b['name']);
                    }

                    // Sort the $data array using the custom comparison function
                    usort($data, 'compareByName');
                ?>
                <select name="province" id="province" required>
                    <option value="" selected disabled>None</option>
                    <?php
                        // Iterate through the sorted provinces in the API response
                        foreach ($data as $province) {
                    ?>
                        <option value="<?php echo htmlentities($province['code']); ?>"><?php echo htmlentities($province['name']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="dropdown-items">
                <label for="municipality">Municipality:</label>
                    <select name="municipality" id="municipality" required>
                        <option value="none">None</option>
                    </select>
            </div>

            <div class="dropdown-items">
                <label for="barangay">Barangay:</label>
                    <select name="barangay" id="barangay" required>
                        <option value="none">None</option>
                    </select>
            </div>

            <div class="dropdown-items">
                <input type="file" id="fileToUpload[]" name="fileToUpload[]" multiple>
                <button type="submit" name="submit" value="UploadPDF" style="height: 25px;">Submit</button>
            </form>
            </div>    
        </div>
        <div class="gallery-tbls">
            <?php

            require_once("../php/config.php");
            
            // Set the time zone to Manila
            date_default_timezone_set('Asia/Manila');

            // Path to the directory where files are uploaded
            $uploadDirectory = "uploads/";

            // Display uploaded files in a table
            echo "<table border='1'>";
            echo "<tr><th>File Name</th><th>Date Uploaded</th><th>Action</th></tr>";

            $result = $conn->query("SELECT * FROM lib_upload");
            while ($row = $result->fetch_assoc()) {
            $fileName = $row['file_name'];
            $dateUploaded = $row['date_uploaded'];

            echo "<tr>";
            echo "<td>$fileName</td>";
            echo "<td>$dateUploaded</td>";
            echo '<td>
                    <span class="span-action">
                    <a class="btn-action" href="uploads/' . $fileName .'" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 32 32"><path fill="currentColor" d="M16 8.286C8.454 8.286 2.5 16 2.5 16s5.954 7.715 13.5 7.715c5.77 0 13.5-7.715 13.5-7.715S21.77 8.286 16 8.286m0 12.52c-2.65 0-4.807-2.156-4.807-4.806S13.35 11.193 16 11.193S20.807 13.35 20.807 16S18.65 20.807 16 20.807zm0-7.612a2.806 2.806 0 1 0 0 5.611a2.806 2.806 0 0 0 0-5.611"/></svg></a> 
                    <a class="btn-action"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="m12 17.192l3.308-3.307l-.708-.708l-2.1 2.1v-4.7h-1v4.7l-2.1-2.1l-.708.708zM4 20V6.915L6.415 4h11.15L20 6.954V20zM5.38 6.808H18.6L17.096 5H6.885z"/></svg></a>
                    <span>
                    </td>';
            echo "</tr>";
            }

            echo "</table>";
            
            ?>
        </div>

        <!-- <div class="pagination">
            <span class="pagination-btn">
                <button>Previous</button>
                <p>Page 1 out of 2</p>
                <button>Next</button>
            </span>
            <span class="pagination-total">
                Total Files: 404
            </span>
        </div> -->

    </div>
</body>
    
</html>