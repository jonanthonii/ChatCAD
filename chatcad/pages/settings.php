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

if(isset($_POST['submit'])){
    $employee_id = $_SESSION['current_employeeid'];
    $currentpw = $_POST['currentpw'];
    $newpw = $_POST['newpw'];
    $hashed_newpw = password_hash($newpw, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE employee_id = :employee_id");
    $stmt->execute(['employee_id' => $employee_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if(password_verify($currentpw, $row['password'])){
        if($currentpw != $newpw){

            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE employee_id = :employee_id");
            $stmt->execute(['password' => $hashed_newpw, 'employee_id' => $employee_id]);

            echo "<script>
            $(document).ready(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Password Updated',
                    html: 'Your password has been changed!',
                    confirmButtonText: 'OK',
                    showConfirmButton: true,
                    color: 'green',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    confirmButtonColor: 'green',
                    
                }).then(() => {
                    window.location.href = 'settings.php';
                });
            });
            </script>";
            
        }else{
            echo "<script>
            $(document).ready(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Repeated Password',
                    html: 'Make sure to enter a different password.',
                    confirmButtonText: 'Understood',
                    showConfirmButton: true,
                    color: 'red',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    confirmButtonColor: 'red',
                    
                });
            });
            </script>";
        }
    }else{
        echo "<script>
            $(document).ready(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Incorrect Password',
                    html: 'The current password you entered is incorrect. Please try again.',
                    confirmButtonText: 'Understood',
                    showConfirmButton: true,
                    color: 'red',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false,
                    confirmButtonColor: 'red',
                    
                });
            });
            </script>";
    }





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
            <h1>Settings</h1>
        </div>
        <div class="body-content">
            <form action="" method="post" enctype="multipart/form-data">
            <div class="userinfo">
                <div class="fields">
                    <input type="text" class="legends" value="Current Password" disabled>
                    <input type="password" class="name" name="currentpw" required id="currentpw" autocomplete="off" spellcheck="false" placeholder="*******">
                </div>

                <div class="fields">
                    <input type="text" class="legends" value="New Password" disabled>
                    <input type="password" class="name" name="newpw" id="newpw" required autocomplete="off" spellcheck="false" placeholder="*******">
                </div>
            </div>
            <div class="savebtndiv">
                    <input type="submit" name="submit" value="UPDATE PASSWORD">
                </div>
            </form>
        </div>
    </div>

</body>
<script>

    document.addEventListener('DOMContentLoaded', function() {
       const sidebar = document.getElementById('sidebar');
       const toggleBtn = document.getElementById('toggle-btn');
       
       toggleBtn.addEventListener('click', function() {
           sidebar.classList.toggle('hidden');
       });
    });
</script>
</html>