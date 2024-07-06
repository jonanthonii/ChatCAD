<?php
ob_start();
session_start();
include ('../config.php');

date_default_timezone_set('Asia/Manila');

if(!isset($_SESSION['current_employeeid'])) {
    header("Location: ../index.php");
}

echo '<link rel="stylesheet" href="../css/home.css?v=' . time() . '">';
echo '<link rel="stylesheet" href="../boxicons/css/boxicons.min.css">';
echo '<script src="../sweetalert2/dist/sweetalert2.all.min.js"></script>';
echo '<link rel="stylesheet" href="../sweetalert2/dist/sweetalert2.min.css">';
echo '<script src="../jquery-3.7.1.js"></script>';

if(isset($_POST['send'])){
    $message = trim($_POST['message']);
    $sender = $_SESSION['current_employeeid'];
    $sent_date = date('Y-m-d H:i:s');
    $status = "SENT";
    $channel = "ALL";

    if(!empty($message) || $message != '') {

    $stmt = $pdo->prepare("INSERT INTO conversation (message, sender_id, channel, sent_date, status) VALUES (:message, :sender, :channel, :sent_date, :status)");
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':sender', $sender);
    $stmt->bindParam(':sent_date', $sent_date);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':channel', $channel);
    $stmt->execute();

    header("Location: home.php");
    ob_flush();
    ob_clean();

    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | ChatCAD</title>
    <script type="text/javascript">
        $(document).ready(function() {

            var isInitialLoad = true;

            function fetchMessages() {
                $.ajax({
                    url: 'fetch/fetch_messages.php', // Ensure this path is correct
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var convo = $('#convo');
                        convo.empty(); // Clear the conversation box
                        var currentDate = null;
                        var lastmessage = '';
                        var lastmsgsent = '';

                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        $.each(data, function(index, row) {
                            var messageDate = new Date(row.sent_date).toISOString().slice(0, 10);
                            var messageTime = new Date(row.sent_date).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                            // Check if the date has changed to display the date header
                            if (currentDate !== messageDate) {
                                currentDate = messageDate;
                                convo.append("<h2 class='date-header'>" + new Date(currentDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + "</h2>");
                            }

                            if (lastmsgsent !== messageTime) {
                                convo.append("<h2 class='time'>" + messageTime + " </h2>");
                            }

                            var messageContent = $('<div/>').text(row.message).html()

                            if (row.fullname !== <?php echo json_encode($_SESSION['current_employeefullname']); ?>) {
                                // var profile_picture = "../users/" + (row.profile_pic === "female.png" || row.profile_pic === "male.png" ? row.profile_pic : row.profile_pic);
                                var profile_picture = row.profile_pic === "female.png" || row.profile_pic === "male.png"
                                ? "../users/" + row.profile_pic
                                : "../users/" + row.employee_id + "/" + row.profile_pic;

                                convo.append(
                                    "<div class='msgbox-other'>" +
                                        (lastmessage !== row.fullname ?
                                            (row.profile_pic ? 
                                                "<span class='pp'> <img src='" + profile_picture + "'>" + "</span>" :
                                                "Test") +
                                            "<div class='separate'><span class='name'>" + row.fullname.toLowerCase() + "</span>" :
                                            "<span style='margin-left: 38px;'></span>") + 
                                    "<p class='messagebox'>" + messageContent + "</p>" + 
                                    "</div></div>"
                                );
                            } else {
                                convo.append("<div class='msgbox-self'><p class='messagebox'>" + row.message + "</p></div>");
                            }

                            lastmessage = row.fullname;
                            lastmsgsent = messageTime;
                        });

                        // Scroll to the bottom only if it's the initial load
                        if (isInitialLoad) {
                            convo.scrollTop(convo[0].scrollHeight);
                            isInitialLoad = false; // Set the flag to false after the initial scroll
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error status:', xhr.status);
                        console.error('Error message:', xhr.responseText);
                        console.error('Error:', error);
                    }
                });
            }

            function fetchOnline(){
                $.ajax({
                    url: 'fetch/fetch_online.php',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var online = $('#online-list');
                        online.empty();

                        if(data.error) {
                            console.error(data.error);
                            return;
                        }

                        $.each(data, function(index, row) {
                            var timeLimit = new Date().getTime() - 3 * 60 * 1000;
                            var profile = row.profile_pic === "female.png" || row.profile_pic === "male.png" ? "../users/" + row.profile_pic : "../users/" + row.employee_id + "/" + row.profile_pic;
                            var fullName = row.firstname.toLowerCase() + ' ' + row.lastname.toLowerCase();
                            var lastOnline = new Date(row.last_online).getTime();

                            if(lastOnline && timeLimit && lastOnline >= timeLimit) {
                                online.append('<div class="online-user"><img src="' + profile + '"><p>' + fullName + '</p></div>');
                            } else {
                                online.append('<div class="offline-user"><img src="' + profile + '"><p>' + fullName + '</p></div>');
                            }
                        });
                    }
                });
            } ;

            function updateLastOnline() {
                if(!document.hidden){
                    $.ajax({
                        url: 'fetch/update_last_online.php',
                        method: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            console.log('Last online updated successfully');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error status:', xhr.status);
                            console.error('Error message:', xhr.responseText);
                            console.error('Error:', error);
                        }
                    });
                }
            };

            setInterval(fetchMessages, 3000);
            fetchMessages(); // Initial fetch

            setInterval(fetchOnline, 3000);
            fetchOnline();

            window.addEventListener('focus', updateLastOnline);
            setInterval(fetchOnline, 30000);
            updateLastOnline();
        });
    </script>
</head>
<body>
    <!-- <div class="bar">
        <button id="logout" onclick="location.href='../logout.php'"><i class='bx bx-log-out'></i>Logout</button>
    </div> -->

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
        <div class="topcontent">
            <h1>Home</h1>
            <button class="toggle-online-btn" id="toggle-online-btn"><i class='bx bxs-circle'></i>Online List</button>
        </div>
        <div class="inclusion">
            <div class="leftside">
                <div class="convo" id="convo"></div>
                <form action="" method="POST">
                    <div class="typebox">
                        <!-- <input type="text" autocomplete="off" name="message" autofocus value="<?php if(isset($message)){ echo $message; }?>"> -->
                        <button type="submit" name="send">Send</button>
                        <input type="text" autocomplete="off" id="emoji-input" name="message" autofocus value="<?php if(isset($message)){ echo $message; }?>">
                    </div>
                </form>
            </div>
            <div class="rightside">

                <div class="online-list" id="online-list"></div>
            </div>
        </div>
    </div>

</body>
<script>

    document.addEventListener('DOMContentLoaded', function() {

        const convo = document.getElementById('convo');
        convo.scrollTop = convo.scrollHeight;

        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-btn');

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
        });

        const toggleOnlineBtn = document.getElementById('toggle-online-btn');
        const onlineList = document.getElementById('online-list');

        // toggleOnlineBtn.addEventListener('click', function() {
        //     onlineList.classList.toggle('ol-show');
        // });

        document.addEventListener('click', function(event){
            if (onlineList && toggleOnlineBtn && !onlineList.contains(event.target) && event.target !== toggleOnlineBtn) {
                onlineList.classList.remove('ol-show');
            } else if (onlineList && toggleOnlineBtn) {
                onlineList.classList.toggle('ol-show');
            }
        });


    });
</script>
</html>