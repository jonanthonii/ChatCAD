<?php

session_start();

echo '<link rel="stylesheet" href="css/register.css?v=' . time() . '">';
echo '<link rel="stylesheet" href="boxicons/css/boxicons.min.css">';
echo '<script src="sweetalert2/dist/sweetalert2.all.min.js"></script>';
echo '<link rel="stylesheet" href="sweetalert2/dist/sweetalert2.min.css">';
echo '<script src="jquery-3.7.1.js"></script>';

include ('config.php');

date_default_timezone_set('Asia/Manila');

if(isset($_POST['registerbtn'])){
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $employeeid = $_POST['employeeid'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if(empty($employeeid) || empty($password) || empty($firstname) || empty($lastname) || empty($email) || empty($gender) || empty($cpassword)){
        echo '<script>
        window.onload = function(){
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer)
                    toast.addEventListener("mouseleave", Swal.resumeTimer)
                }
            })
    
            Toast.fire({
                icon: "error",
                iconColor: "#f35516",
                title: "Fields cannot be empty.",
                background: "#ffffff",
                color: "#f35516"
            })
        }
        </script>';
    }else{
        $stmt = $pdo->prepare("SELECT * FROM users WHERE employee_id = :employeeid");
        $stmt->execute(['employeeid' => $employeeid]);
    
        if($stmt->rowCount() > 0){
            echo '<script>
            window.onload = function(){
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener("mouseenter", Swal.stopTimer)
                        toast.addEventListener("mouseleave", Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: "error",
                    iconColor: "#f35516",
                    title: "Account already exists.",
                    background: "#ffffff",
                    color: "#f35516"
                })
            }
            </script>';
        }else{

            $today = new DateTime();
            $birthday = new DateTime($birthdate);
            $age = $today->diff($birthday)->y;

            if($age > 18){
                if($password !== $cpassword){
                    echo '<script>
                    window.onload = function(){
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener("mouseenter", Swal.stopTimer)
                                toast.addEventListener("mouseleave", Swal.resumeTimer)
                            }
                        })
        
                        Toast.fire({
                            icon: "error",
                            iconColor: "#f35516",
                            title: "Invalid password. Please try again.",
                            background: "#fffffff",
                            color: "#f35516"
                        })
                    }
                    </script>';
                }else{
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $current_time = date("Y-m-d H:i:s", microtime(true));
        
                    $stmt = $pdo->prepare("INSERT INTO users (firstname, middlename, lastname, email, gender, birthdate, employee_id, password, created_on, profile_pic) VALUES (:firstname, :middlename, :lastname, :email, :gender, :birthdate, :employeeid, :password, :created_on, :profile_pic)");
                    $stmt->execute([
                        'firstname' => $firstname,
                        'middlename' => $middlename,
                        'lastname' => $lastname,
                        'email' => $email,
                        'gender' => $gender,
                        'birthdate' => $birthdate,
                        'employeeid' => $employeeid,
                        'password' => $hashed_password,
                        'created_on' => $current_time,
                        'profile_pic' => $gender.'.png'
                    ]);
        
                    echo '<script>
                    window.onload = function(){
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener("mouseenter", Swal.stopTimer)
                                toast.addEventListener("mouseleave", Swal.resumeTimer)
                            }
                        })
        
                        Toast.fire({
                            icon: "success",
                            iconColor: "#4CAF50",
                            title: "Registration successful.",
                            background: "#ffffff",
                            color: "#4CAF50"
                        }).then(() => {
                            window.location.href = "index.php";
                        })
                    }
                    </script>';
                }
            }else{
                echo '<script>
                window.onload = function(){
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener("mouseenter", Swal.stopTimer)
                            toast.addEventListener("mouseleave", Swal.resumeTimer)
                        }
                    })
    
                    Toast.fire({
                        icon: "error",
                        iconColor: "#f35516",
                        title: "Input a valid birthdate.",
                        background: "#ffffff",
                        color: "#f35516"
                    })
                }
                </script>';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Registration | ChatCAD</title>
</head>
<body>
    <div class="header">
        <h1>ChatCAD</h1>
        <h6>version 0.1</h6>
    </div>

    <div class="content">
        <div class="enclosed">
            <form action="" method="post">
                <div class="labels">
                    <input type="text" value="FIRST NAME :" disabled>
                    <input type="text" value="MIDDLE NAME :" disabled>
                    <input type="text" value="LAST NAME :" disabled>
                </div>
                <div class="inputs">
                    <input type="text" id="firstname" name="firstname" autocomplete="off" value="<?php if(isset($firstname)){ echo $firstname; }?>">
                    <input type="text" id="middlename" name="middlename" autocomplete="off" value="<?php if(isset($middlename)){ echo $middlename; }?>">
                    <input type="text" id="lastname" name="lastname" autocomplete="off" value="<?php if(isset($lastname)){ echo $lastname; }?>">
                </div>

                <div class="labels"  style="margin-top: 10px">
                    <input type="text" value="GENDER :" disabled>
                    <input type="text" value="BIRTH DATE :" disabled>
                    <input type="text" value="EMAIL ADDRESS :" disabled>
                </div>
                <div class="inputs">
                    <select id="gender" name="gender" autocomplete="off">
                        <option value=""> </option>
                        <option value="Male" <?php if(isset($gender) && $gender == "Male"){ echo "selected"; } ?>>Male</option>
                        <option value="Female" <?php if(isset($gender) && $gender == "Female"){ echo "selected"; } ?>>Female</option>
                    </select>
                    <input type="date" id="birthdate" name="birthdate" autocomplete="off" value="<?php if(isset($birthdate)){ echo $birthdate; }?>">
                    <input type="email" id="email" name="email" autocomplete="off" value="<?php if(isset($email)){ echo $email; }?>">
                </div>

                <div class="labels"  style="margin-top: 10px">
                    <input type="text" value="EMPLOYEE ID :" disabled>
                    <input type="text" value="PASSWORD :" disabled>
                    <input type="text" value="CONFIRM PASSWORD :" disabled>
                </div>
                <div class="inputs">
                    <input type="text" id="employeeid" name="employeeid" autocomplete="off" value="<?php if(isset($employeeid)){ echo $employeeid; }?>">
                    <input type="password" id="password" name="password" value="<?php if(isset($password)){ echo $password; }?>">
                    <input type="password" id="cpassword" name="cpassword" value="<?php if(isset($cpassword)){ echo $cpassword; }?>">
                </div>

                <div class="inputs">
                    <button type="submit" name="registerbtn">REGISTER</button>
                </div>   
            </form>
        </div>
    </div>

    <div class="toregistration">
        <button type"button" id="to-login" onclick="window.location.href='index.php'"><i class='bx bxs-log-in-circle'></i>LOGIN EXISTING ACCOUNT</button>
    </div>
    
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const toRegisterBtn = document.getElementById('to-register');
    const toLoginBtn = document.getElementById('to-login');
        if (toRegisterBtn) {
            toRegisterBtn.addEventListener('click', function() {
                document.body.classList.add('fade-out');
                setTimeout(() => {
                    window.location.href = 'register.php';
                }, 500);
            });
        }

        if (toLoginBtn) {
            toLoginBtn.addEventListener('click', function() {
                document.body.classList.add('fade-out');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 500);
            });
        }
    });
</script>
</html>