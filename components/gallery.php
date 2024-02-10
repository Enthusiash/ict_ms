<?php session_start();
require_once('../php/config.php') ?>
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

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
        
    $(document).ready(function () {

    $("#userId").val(localStorage.getItem('user_id'));

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

            // Set an item in local storage
            if (!localStorage.getItem('selectedProvince')) {
            // If username does not exist, set it
                localStorage.setItem('selectedProvince', selectedProvince);
            } else {
                // If username already exists, update it
                localStorage.setItem('selectedProvince', selectedProvince);
            }

            $.ajax({
                type: "POST",
                url: 'filterUtil.php',
                data: { 
                    selectedProvince: localStorage.getItem('selectedProvince'),
                    selectedMunicipality: localStorage.getItem('selectedMunicipality'),
                    selectedBarangay: localStorage.getItem('selectedBarangay'),
                },
                success: function (response) {
                    console.log(response);
                    localStorage.removeItem('selectedProvince');
                },
                error: function () {
                    console.error('Encountered error');
                }
            });

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

            if (!localStorage.getItem('selectedMunicipality')) {
                localStorage.setItem('selectedMunicipality', selectedMunicipality);
            } else {
                localStorage.setItem('selectedMunicipality', selectedMunicipality);
            }

            $.ajax({
                type: "POST",
                url: 'filterUtil.php',
                data: { 
                    selectedMunicipality: localStorage.getItem('selectedMunicipality'),
                },
                success: function (response) {
                    console.log(response);
                    localStorage.removeItem('selectedMunicipality');
                },
                error: function () {
                    console.error('Encountered error');
                }
            });

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

    $("#barangay").on('change', function () {
        let selectedBarangay = $(this).val();

            if (!localStorage.getItem('selectedBarangay')) {
                localStorage.setItem('selectedBarangay', selectedBarangay);
            } else {
                localStorage.setItem('selectedBarangay', selectedBarangay);
            }

            $.ajax({
                type: "POST",
                url: 'filterUtil.php',
                data: { 
                    selectedBarangay: localStorage.getItem('selectedBarangay'),
                },
                success: function (response) {
                    console.log(response);
                    localStorage.removeItem('selectedBarangay');
                },
                error: function () {
                    console.error('Encountered error');
                }
            });
    });

    // Search button click event
    $("#searchBtn").on('click', function () {
                let selectedProvince = $("#province").val();
                let selectedMunicipality = $("#municipality").val();
                let selectedBarangay = $("#barangay").val();

                if (selectedProvince) { // Check if province is selected
                    filterFiles(selectedProvince, selectedMunicipality, selectedBarangay);
                    // var url = 'gallery.php?province=' + encodeURIComponent(selectedProvince) + '&municipality=' + encodeURIComponent(selectedMunicipality) + '&barangay=' + encodeURIComponent(selectedBarangay);
                    
                    // // Redirect the user to the gallery page with filtered parameters
                    // window.location.href = url;
                    // alert("Test");
                } else {
                    console.error('No location selected');
                }
            });
});

    

    // Function to filter files based on selected location
    function filterFiles(selectedProvince, selectedMunicipality, selectedBarangay) {
            // Make AJAX call to filter files
            $.ajax({
                type: "POST",
                url: 'filterUtil.php',
                data: { 
                    selectedProvince: selectedProvince,
                    selectedMunicipality: selectedMunicipality,
                    selectedBarangay: selectedBarangay
                },
                success: function (response) {
                    console.log(response);
                    // Optionally, you can reload the table with filtered files here
                },
                error: function () {
                    console.error('Encountered error');
                }
            });
        }
    
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
                            localStorage.removeItem('user_id');
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
            $userId = $_POST['userId'];

            // Get file extension
            $file_extension = pathinfo($_FILES["fileToUpload"]["name"][$i], PATHINFO_EXTENSION);

            // Generate a new file name with Barangay Code and "_01"
            $new_file_name = $barangay_code . "_01." . $file_extension;

            // Check for existing files with the same name and increment the suffix
            $suffix = 1;
            while (file_exists($uploadDirectory . $new_file_name)) {
                // Generate a hash for the current file
                $current_file_hash = hash_file('md5', $_FILES["fileToUpload"]["tmp_name"][$i]);
                // Generate a hash for the existing file
                $existing_file_hash = hash_file('md5', $uploadDirectory . $new_file_name);

                // Compare hashes to determine if files are the same
                if ($current_file_hash === $existing_file_hash) {
                    echo '<script>alert("Sorry, file already exists.");</script>';
                    $uploadOk = 0;
                    break;
                }

                $suffix++;
                $new_file_name = $barangay_code . "_" . str_pad($suffix, 2, '0', STR_PAD_LEFT) . "." . $file_extension;
            }

            // If no duplicate found, proceed with uploading
            if ($uploadOk == 1) {
                $target_file = $uploadDirectory . $new_file_name;
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
                    $conn->query("INSERT INTO lib_upload(province_code, municipality_code, barangay_code, date_uploaded, user_id, file_name) 
                    VALUES ('$province_code', '$municipality_code','$barangay_code', now(), '$userId', '$new_file_name')")
                        or die(mysqli_error($conn));
                    echo '<script>alert("Uploads Successfully!");</script>';
                } else {
                    echo '<script>alert("Sorry, there was an error uploading your file.");</script>';
                }
            } else {
                echo '<script>alert("Sorry, your file is not uploaded.");</script>';
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
                <a href="../components/support.php"><p>Support</p></a>
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
                    <select name="municipality" id="municipality">
                        <option value="none">None</option>
                    </select>
            </div>

            <div class="dropdown-items">
                <label for="barangay">Barangay:</label>
                    <select name="barangay" id="barangay">
                        <option value="none">None</option>
                    </select>
            </div>

            <span>
                <button id="searchBtn"><svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" viewBox="0 0 24 24"><g fill="none" fill-rule="evenodd"><path d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="currentColor" d="M2 10.5a8.5 8.5 0 1 1 15.176 5.262l3.652 3.652a1 1 0 0 1-1.414 1.414l-3.652-3.652A8.5 8.5 0 0 1 2 10.5M10.5 6a1 1 0 0 0 0 2a2.5 2.5 0 0 1 2.5 2.5a1 1 0 1 0 2 0A4.5 4.5 0 0 0 10.5 6"/></g></svg></button>
            </span>

            <div class="dropdown-items">
                <input type="file" id="fileToUpload[]" name="fileToUpload[]" multiple>
                <button type="submit" name="submit" value="UploadPDF" style="height: 25px;">Submit</button>
                <input type="hidden" name="userId" id="userId">
            </form>
            </div>    
        </div>
        <div class="gallery-tbls">
            <?php
            require_once("../php/config.php");
            // Set the time zone to Manila
            date_default_timezone_set('Asia/Manila');

            // Display uploaded files in a table
            echo "<table border='1'>";
            echo "<tr><th>File Name</th><th>Date Uploaded</th><th>Uploader</th><th>Action</th></tr>";


            $provinceCode = isset($_SESSION['selectedProvinceSession']) ? $_SESSION['selectedProvinceSession'] : '';
            $municipalityCode = isset($_SESSION['selectedMunicipalitySession']) ? $_SESSION['selectedMunicipalitySession'] : '';
            $barangayCode = isset($_SESSION['selectedBarangaySession']) ? $_SESSION['selectedBarangaySession'] : '';

            // Pagination variables
            $results_per_page = 10;

            // Get current page number
            if (!isset($_GET['page'])) {
                $page = 1;
            } else {
                $page = $_GET['page'];
            }

            // Calculate SQL LIMIT starting number for the results on the displaying page
            $this_page_first_result = ($page - 1) * $results_per_page;
            
            if ($provinceCode != '') {
                $queryString = "SELECT lib_upload.id AS id, province_code, municipality_code, barangay_code, date_uploaded, users.Fullname as uploader, file_name FROM `lib_upload` INNER JOIN users ON users.Id = lib_upload.user_id WHERE province_code = '$provinceCode' AND is_Archive = 0 LIMIT $this_page_first_result, $results_per_page";
                // Count the total number of records in the table
                $sql_count = "SELECT COUNT(*) AS total_records FROM lib_upload WHERE province_code = '$provinceCode'";
                if ($municipalityCode != '') {
                    $queryString = "SELECT lib_upload.id AS id, province_code, municipality_code, barangay_code, date_uploaded, users.Fullname as uploader, file_name FROM `lib_upload` INNER JOIN users ON users.Id = lib_upload.user_id WHERE province_code = '$provinceCode' AND municipality_code = '$municipalityCode' AND is_Archive = 0 LIMIT $this_page_first_result, $results_per_page";
                    $sql_count = "SELECT COUNT(*) AS total_records FROM lib_upload WHERE province_code = '$provinceCode' AND municipality_code = '$municipalityCode'";
                    if ($barangayCode != '') {
                        $queryString = "SELECT lib_upload.id AS id, province_code, municipality_code, barangay_code, date_uploaded, users.Fullname as uploader, file_name FROM `lib_upload` INNER JOIN users ON users.Id = lib_upload.user_id WHERE province_code = '$provinceCode' AND municipality_code = '$municipalityCode' AND '$barangayCode' AND is_Archive = 0 LIMIT $this_page_first_result, $results_per_page";
                        $sql_count = "SELECT COUNT(*) AS total_records FROM lib_upload WHERE province_code = '$provinceCode' AND municipality_code = '$municipalityCode' AND '$barangayCode'";
                    }
                }
            } else if ($provinceCode == '')  {
                $queryString = "SELECT * FROM lib_upload WHERE province_code = ''";
                $sql_count = 0;
            } else {
                $queryString = "SELECT * FROM lib_upload WHERE province_code = ''";
                $sql_count = 0;
            }

            if ($sql_count > 0) {
                $result_count = $conn->query($sql_count);
                $row_count = $result_count->fetch_assoc();
                $total_records = $row_count['total_records'];

                // Calculate the total number of pages
                $last_page = ceil($total_records / $results_per_page);
            } else {
                $page = 0;
                $last_page = 0;
            }

            session_destroy();

            // $result = $conn->query("SELECT * FROM lib_upload");
            $result = $conn->query($queryString);
            while ($row = $result->fetch_assoc()) {
            $selectedProvince = $row['province_code'];
            $selectedMunicipality = $row['municipality_code'];
            $selectedBarangay = $row['barangay_code'];
            $fileName = $row['file_name'];
            $dateUploaded = $row['date_uploaded'];
            $uploader = $row['uploader'];
            $id = $row['id'];

            // http://localhost/login-register/components/uploads/037700000/037701000/037701001/037701001_01.pdf
            $location = "http://localhost/login-register/components/uploads/$selectedProvince/$selectedMunicipality/$selectedBarangay/$fileName";
            
            echo "<tr>";
            echo "<td>$fileName</td>";
            echo "<td>$dateUploaded</td>";
            echo "<td>$uploader</td>";
            echo '<td>
                    <span class="span-action">
                    <a class="btn-action" href="'. $location .'" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 32 32"><path fill="currentColor" d="M16 8.286C8.454 8.286 2.5 16 2.5 16s5.954 7.715 13.5 7.715c5.77 0 13.5-7.715 13.5-7.715S21.77 8.286 16 8.286m0 12.52c-2.65 0-4.807-2.156-4.807-4.806S13.35 11.193 16 11.193S20.807 13.35 20.807 16S18.65 20.807 16 20.807zm0-7.612a2.806 2.806 0 1 0 0 5.611a2.806 2.806 0 0 0 0-5.611"/></svg></a> 
                    <a class="btn-action" href="archive.php?id='. $id .'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="m12 17.192l3.308-3.307l-.708-.708l-2.1 2.1v-4.7h-1v4.7l-2.1-2.1l-.708.708zM4 20V6.915L6.415 4h11.15L20 6.954V20zM5.38 6.808H18.6L17.096 5H6.885z"/></svg></a>
                    <span>
                    </td>';
            echo "</tr>";
            }

            echo "</table>";

            ?>
        </div>
        

        <div class="pagination">
            <span class="pagination-btn">
                <?php 
                    if ($page > 1) {
                        $prev_page = $page - 1;
                        echo "<button><a href='gallery.php?page=" . $prev_page . "'>Previous</a></button>";
                    }
                    echo $last_page == 0 ? "<p>No result Found</p>" : "<p>Page ".$page." out of ".$last_page."</p>";
                    if ($page < $last_page) {
                        $next_page = $page + 1;
                        echo "<button><a href='gallery.php?page=" . $next_page . "'>Next</a></button>";
                    }
                ?>
            </span>
        </div>

    </div>
</body>
    
</html>
