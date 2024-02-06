<?php 
session_start();
require_once('../php/config.php');

// if (isset($_POST['selectedProvince'])) {
//     $selectedProvince = $_POST['selectedProvince'];
//     $_SESSION['selectedProvinceSession'] = $selectedProvince;
//     echo $_SESSION['selectedProvinceSession'];
// } else {
//     echo "No selected province";
// }

if (isset($_POST['selectedProvince'])) {
    $selectedProvince = $_POST['selectedProvince'];
    $_SESSION['selectedProvinceSession'] = $selectedProvince;
    echo $_SESSION['selectedProvinceSession'] . " selected";
} 
if (isset($_POST['selectedMunicipality'])) {
    $selectedMunicipality = $_POST['selectedMunicipality'];
    $_SESSION['selectedMunicipalitySession'] = $selectedMunicipality;
    echo $_SESSION['selectedMunicipalitySession'] . " selected";
} 
if (isset($_POST['selectedBarangay'])) {
    $selectedBarangay = $_POST['selectedBarangay'];
    $_SESSION['selectedBarangaySession'] = $selectedBarangay;
    echo $_SESSION['selectedBarangaySession'] . " selected";
} 

else {
    echo "Invalid Selection";
}

?>