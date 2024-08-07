<?php

include ('../config.php');

session_start();

if(!isset($_SESSION['current_employeeid'])){
    header('location: ../index.php');
}

date_default_timezone_set('Asia/Manila');

echo '<link rel="stylesheet" href="../css/profile.css?v=' . time() . '">';
echo '<link rel="stylesheet" href="../boxicons/css/boxicons.min.css">';
echo '<script src="../sweetalert2/dist/sweetalert2.all.min.js"></script>';
echo '<link rel="stylesheet" href="../sweetalert2/dist/sweetalert2.min.css">';
echo '<script src="../jquery-3.7.1.js"></script>';

use Oracle\Oci\Common\Region;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use Oracle\Oci\Common\Auth\SimpleAuthenticationDetailsProviderBuilder;
use Oracle\Oci\ObjectStorage\Model\PutObjectRequest;

// Your Oracle Cloud credentials and configuration
$region = 'ap-tokyo-1'; // Replace with your region
$namespaceName = 'nr7audjfcmkp'; // Replace with your namespace
$bucketName = 'QMS'; // Replace with your bucket name

$tenancyId = 'ocid1.tenancy.oc1..aaaaaaaaxffcmy2gt6ciqgxj624h2mago4oawx436hxhsuiczzt7tho6gnyq';
$userId = 'ocid1.user.oc1..aaaaaaaa3exinwywbiguoom4xkxfu5lwpinbf3tyf5byw7bonzkqijr3s5ka';
$fingerprint = 'e8:16:93:89:db:47:cf:35:8f:fb:55:0b:d6:79:4a:7c';
$privateKeyFile = '/path/to/your/private_key.pem'; // Path to your .pem file

// compartmentOCID= 'ocid1.compartment.oc1..aaaaaaaaxgdsmsb25llh4klwbb5ccop24skvnnfoxgxvinvevsggb4krxy7q'

// Authentication
$provider = SimpleAuthenticationDetailsProviderBuilder::builder()
    ->region(Region::fromRegionId($region))
    ->tenancyId($tenancyId)
    ->userId($userId)
    ->fingerprint($fingerprint)
    ->privateKeyFile($privateKeyFile)
    ->build();

// Create an Object Storage client
$objectStorageClient = new ObjectStorageClient($provider);

$uploadDir = '../users/';

if(!file_exists($uploadDir . '/' . $_SESSION['current_employeeid'])){
    mkdir($uploadDir . '/' . $_SESSION['current_employeeid'], 0777, true);
}

if(isset($_POST['submit'])){
    $pp = $_FILES['preselectbtn'];
    $employee_id = $_SESSION['current_employeeid'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $namechange = false;
    $picturechange = false;

    if((!empty($firstname) || $firstname != '') && (!empty($lastname) || $lastname != '')){
        if($firstname != $_SESSION['current_employeefirst'] || $middlename != $_SESSION['current_employeemiddle'] || $lastname != $_SESSION['current_employeelast']){
            $updatename = $pdo->prepare("UPDATE users SET firstname = :firstname, middlename = :middlename, lastname = :lastname WHERE employee_id = :employee_id");
            $updatename->bindParam(':firstname', $firstname);
            $updatename->bindParam(':middlename', $middlename);
            $updatename->bindParam(':lastname', $lastname);
            $updatename->bindParam(':employee_id', $employee_id);
            $updatename->execute();
            
            $namechange = true;
            $_SESSION['current_employeefirst'] = $firstname;
            $_SESSION['current_employeemiddle'] = $middlename;
            $_SESSION['current_employeelast'] = $lastname;
        }
    }else{
        echo "<script>
            $(document).ready(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Empty Fields',
                    html: 'Make sure at least first and last name are filled in.',
                    confirmButtonText: 'Proceed',
                    showConfirmButton: true,
                    color: 'red',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    confirmButtonColor: 'red',
                    
                }).then(() => {
                    window.location.href = 'profile.php';
                });
            });
            </script>";
    }

    if(isset($_FILES['preselectbtn'])){
        $fileName = $_FILES['preselectbtn']['name'];
        $tmpFileName = $_FILES['preselectbtn']['tmp_name'];
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        if(in_array($extension, ['jpg', 'jpeg', 'png'])){
            $newfileName = generateFilename($fileName);
            $uploadFile = $uploadDir . '/' . $employee_id . '/' . $newfileName;
            move_uploaded_file($tmpFileName, $uploadFile);

            $updatepp = $pdo->prepare("UPDATE users SET profile_pic = :profile_picture WHERE employee_id = :employee_id");
            $updatepp->bindParam(':profile_picture', $newfileName);
            $updatepp->bindParam(':employee_id', $employee_id);
            $updatepp->execute();

            $_SESSION['current_employeepp'] = $newfileName;
            $picturechange = true;
        }
    }

    if($namechange && $picturechange){
        echo "<script>
        $(document).ready(function () {
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                html: 'Your profile changes has been saved!',
                confirmButtonText: 'Proceed',
                showConfirmButton: true,
                color: 'green',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                confirmButtonColor: 'green',
                
            }).then(() => {
                window.location.href = 'profile.php';
            });
        });
        </script>";
    }elseif($namechange && !$picturechange){
        echo "<script>
        $(document).ready(function () {
            Swal.fire({
                icon: 'success',
                title: 'Name Updated',
                html: 'Successfully saved your name changes!',
                confirmButtonText: 'Proceed',
                showConfirmButton: true,
                color: 'green',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                confirmButtonColor: 'green',
                
            }).then(() => {
                window.location.href = 'profile.php';
            });
        });
        </script>";
    }elseif(!$namechange && $picturechange){
        echo "<script>
        $(document).ready(function () {
            Swal.fire({
                icon: 'success',
                title: 'Picture Updated',
                html: 'Successfully saved your profile picture!',
                confirmButtonText: 'Proceed',
                showConfirmButton: true,
                color: 'green',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                confirmButtonColor: 'green',
                
            }).then(() => {
                window.location.href = 'profile.php';
            });
        });
        </script>";
    }
}

function generateFilename($extension) {
    $timestamp = time();
    $randomString = bin2hex(random_bytes(8)); // Generate random string
    return "{$timestamp}_{$randomString}.{$extension}";
 }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | ChatCAD</title>
</head>
<body>

    <div class="sidebar hidden" id="sidebar">
        <button class="toggle-btn" id="toggle-btn">☰</button>
        <ul>
            <li onclick="location.href='home.php'"><i class='bx bxs-home'></i><span class="menu-text">HOME</span></li>
            <li onclick="location.href='profile.php'"><i class='bx bxs-user-circle'></i><span class="menu-text">PROFILE</span></li>
            <li onclick="location.href='settings.php'"><i class='bx bxs-cog'></i><span class="menu-text">SETTINGS</span></li>
            <li onclick="location.href='../logout.php'"><i class='bx bx-log-out'></i><span class="menu-text">LOGOUT</span></li>
        </ul>
    </div>

    <div class="content">
        <div class="topcontent">
            <h1>Profile</h1>
        </div>
        <div class="body-content">
        <form action="" method="post" enctype="multipart/form-data">
        <div class="profile" id="profile">
            <div class="picture">
                <?php

                $stmt = $pdo->prepare("SELECT * FROM users WHERE employee_id = :employee_id");
                $stmt->bindParam(':employee_id', $_SESSION['current_employeeid']);
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if($row['profile_pic'] == 'male.png' || $row['profile_pic'] == 'female.png'){
                    echo '<img name="currentpic" id="currentpic" class="picture" style="z-index: 2" src="../users/' . $row['profile_pic'] . '">';
                }else{
                    echo '<img name="currentpic" id="currentpic" class="picture" style="z-index: 2" src="../users/' . $row['employee_id'] . '/' . $row['profile_pic'] . '">';
                }
                ?>
                <img name="preselectpic" id="preview" class="picture preview" style="z-index: 1" src="">
                <input type="file" name="preselectbtn" id="preselectbtn" accept="image/*" style="display: none;">
                <span class="edit-text" style="z-index: 3"><label for="preselectbtn"><i class='bx bxs-edit-alt'></i>Change Profile Picture</label></span>
            </div>
        </div>
        <div class="userinfo">
            <div class="fields">
                <input type="text" class="legends" value="FIRST NAME" disabled>
                <input type="text" class="name" name="firstname" id="firstname" autocomplete="off" spellcheck="false" value="<?php echo $row['firstname']; ?>">
            </div>

            <div class="fields">
                <input type="text" class="legends" value="MIDDLE NAME" disabled>
                <input type="text" class="name" name="middlename" id="middlename" autocomplete="off" spellcheck="false" value="<?php echo $row['middlename']; ?>">
            </div>

            <div class="fields">
                <input type="text" class="legends" value="LAST NAME" disabled>
                <input type="text" class="name" name="lastname" id="lastname" autocomplete="off" spellcheck="false" value="<?php echo $row['lastname']; ?>">
            </div>
        </div>
        <div class="savebtndiv">
                <input type="submit" name="submit" value="SAVE CHANGES">
            </div>
        </form>
        </div>
    </div>

</body>
<script>

    document.addEventListener('DOMContentLoaded', function() {

        function previewImage(event){
            var input = event.target;
            var reader = new FileReader();

            reader.onload = function(){
                var img = document.getElementById('preview');
                var currentpic = document.getElementById('currentpic');
                img.src = reader.result;
                img.style.display = "block";
                currentpic.style.display = "none";
            };

            reader.readAsDataURL(input.files[0]);
        }

        document.getElementById('preselectbtn').addEventListener('change', previewImage);

       const sidebar = document.getElementById('sidebar');
       const toggleBtn = document.getElementById('toggle-btn');
       
       toggleBtn.addEventListener('click', function() {
           sidebar.classList.toggle('hidden');
       });
    });
</script>
</html>