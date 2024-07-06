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

$uploadDir = '../users/';

if(!file_exists($uploadDir . '/' . $_SESSION['current_employeeid'])){
    mkdir($uploadDir . '/' . $_SESSION['current_employeeid'], 0777, true);
}

if(isset($_POST['submit'])){
    $pp = $_FILES['preselectbtn'];
    $employee_id = $_SESSION['current_employeeid'];

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

            echo "<script>
                $(document).ready(function () {
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Picture Updated',
                        html: 'You have successfully changed your profile picture!',
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
            <li><i class='bx bxs-cog' ></i><span class="menu-text">SETTINGS</span></li>
            <li onclick="location.href='../logout.php'"><i class='bx bx-log-out'></i><span class="menu-text">LOGOUT</span></li>
        </ul>
    </div>

    <div class="content">
        <form action="" method="post" enctype="multipart/form-data">
        <div class="profile" id="profile">
            <div class="picture">
                <?php
                if($_SESSION['current_employeepp'] == 'male.png' || $_SESSION['current_employeepp'] == 'female.png'){
                    echo '<img name="currentpic" id="currentpic" class="picture" style="z-index: 2" src="../users/' . $_SESSION['current_employeepp'] . '">';
                }else{
                    echo '<img name="currentpic" id="currentpic" class="picture" style="z-index: 2" src="../users/' . $_SESSION['current_employeeid'] . '/' . $_SESSION['current_employeepp'] . '">';
                }
                ?>
                <img name="preselectpic" id="preview" class="picture preview" style="z-index: 1" src="">
                <input type="file" name="preselectbtn" id="preselectbtn" accept="image/*" style="display: none;">
                <span class="edit-text" style="z-index: 3"><label for="preselectbtn"><i class='bx bxs-edit-alt'></i>Change Profile Picture</label></span>
            </div>
        </div>
        <div class="userinfo">
            <div class="name">
            <input type="text" name="fullname" id="fullname" value="<?php echo $_SESSION['current_employeefullname'] ?>">
            </div>
            <div class="username">
            <input type="text" name="username" id="username" value="<?php echo $_SESSION['current_employeeid'] ?>">
            </div>
            <div class="email">
            <input type="email" name="email" id="email" value="<?php echo $_SESSION['current_employeeemail'] ?>">
            </div>
        </div>
        <div class="savebtndiv">
                <input type="submit" name="submit" value="SAVE CHANGES">
            </div>
        </form>
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