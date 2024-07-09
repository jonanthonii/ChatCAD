<?php session_start();

date_default_timezone_set('Asia/Manila');

echo '<link rel="stylesheet" href="css/login.css?v=' . time() . '">';
echo '<link rel="stylesheet" href="boxicons/css/boxicons.min.css">';
echo '<script src="sweetalert2/dist/sweetalert2.all.min.js"></script>';
echo '<link rel="stylesheet" href="sweetalert2/dist/sweetalert2.min.css">';
echo '<script src="jquery-3.7.1.js"></script>';

include ('config.php');

if(isset($_SESSION['current_employeeid'])){
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
            iconColor: "#ffffff",
            title: "Saved logged in. Redirecting...",
            background: "#4CAF50",
            color: "#ffffff"
        }).then(() => {
            window.location.href = "pages/home.php";
        })
    }
    </script>';
}

if(isset($_POST['loginbtn'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(empty($username) || empty($password)){
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
                iconColor: "#ffffff",
                title: "Fields cannot be empty.",
                background: "#ffae00",
                color: "#ffffff"
            })
        }
        </script>';
    }else{
        $stmt = $pdo->prepare("SELECT * FROM users WHERE employee_id = :username");
        $stmt->execute(['username' => $username]);
    
        if($stmt->rowCount() > 0){
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if(password_verify($password, $user['password'])){
                $_SESSION['current_employeeid'] = $username;
                $_SESSION['current_employeefirst'] = $user['firstname'];
                $_SESSION['current_employeefullname'] = $user['firstname'] . ' ' . $user['lastname'];
                $_SESSION['current_employeepp'] = $user['profile_pic'];
                $_SESSION['current_employeebirthdate'] = $user['birthdate'];
                $_SESSION['current_employeeemail'] = $user['email'];
                $_SESSION['current_employeegender'] = $user['gender'];

                $current_time = date('Y-m-d H:i:s');

                $updatestmt = $pdo->prepare("UPDATE users SET last_online = :current_time WHERE employee_id = :username");
                $updatestmt->execute(['current_time' => $current_time, 'username' => $username]);

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
                        iconColor: "#ffffff",
                        title: "Login successful.",
                        background: "#4CAF50",
                        color: "#ffffff"
                    }).then(() => {
                        window.location.href = "pages/home.php";
                    })
                }
                </script>';
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
                        iconColor: "#ffffff",
                        title: "Incorrect password.",
                        background: "#f35516",
                        color: "#ffffff"
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
                    iconColor: "#ffffff",
                    title: "Account not found",
                    background: "#f35516",
                    color: "#ffffff"
                })
            }
            </script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Login | ChatCAD</title>
</head>
<body>
    <div class="header">
        <h1>ChatCAD</h1>
        <h6>version 0.1</h6>
    </div>

    <div class="content">
        <form action="" method="post">
            <div class="inputs">
                <label for="username">ID NUMBER :</label>
            </div>
            <div class="inputs">
                <input type="text" id="username" name="username" autocomplete="off" value="<?php if(isset($username)){ echo $username; }?>">
            </div>
            <div class="inputs" style="margin-top: 10px">
                <label for="password">PASSWORD :</label>
            </div>
            <div class="inputs">
                <input type="password" id="password" name="password" value="<?php if(isset($password)){echo $password; }?>">
            </div>

            <div class="inputs">
                <button type="submit" name="loginbtn">LOGIN</button>
            </div>
            
        </form>
    </div>

    <div class="toregistration">
        <button type"button" id="to-register" onclick="window.location.href='register.php'"><i class='bx bxs-plus-circle'></i>CREATE NEW ACCOUNT</button>
    </div>
    
</body>
<script>

if (window.location.search.includes('success=true')) {
    Swal.fire({
        title: 'Logout Successful!',
        icon: 'success',
        confirmButtonColor: '#629c41',
        color: '#629c41',
    }).then(() => {
        window.location.href = 'index.php';
    });
}

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