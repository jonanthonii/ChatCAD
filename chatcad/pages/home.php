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
    $replyto_id = $_POST['replyMessageId'] ? $_POST['replyMessageId'] : '';
    $replyto_firstname = $_SESSION['replyto_firstname'] ? $_SESSION['replyto_firstname'] : '';
    $replyto_message = $_SESSION['replyto_message'] ? $_SESSION['replyto_message'] : '';

    if(!empty($message) || $message != '') {
 
            $stmt = $pdo->prepare("INSERT INTO conversation (message, sender_id, channel, sent_date, status, replyto_id, replyto_message, replyto_firstname) VALUES (:message, :sender, :channel, :sent_date, :status, :replyto_id, :replyto_message, :replyto_firstname)");
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':sender', $sender);
            $stmt->bindParam(':sent_date', $sent_date);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':channel', $channel);
            $stmt->bindParam(':replyto_id', $replyto_id);
            $stmt->bindParam(':replyto_message', $replyto_message);
            $stmt->bindParam(':replyto_firstname', $replyto_firstname);
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

                            var replyOthers = "";
                            var replySelf = "";

                            if (row.replyto_id) {
                                if (row.replyto_firstname == row.firstname) {
                                    replySelf = "<div class='separate'><span class='name2'><b>You <span class='name2-ext'>replied to yourself</span></b></span>" +
                                    "<div class='mensahe2'><p class='reply-msg2'>" + row.replyto_message + "</p></div>";
                                    replyOthers = "<div class='separate'><span class='name'><b>" + row.firstname.toLowerCase() + "</b> replied to themself<b></b></span>" +
                                    "<div class='mensahe'><p class='reply-msg'>" + row.replyto_message + "</p></div>";
                                } else {
                                    replyOthers = "<div class='separate'><span class='name'><b>" + row.firstname.toLowerCase() + "</b> replied to <b>" + row.replyto_firstname.toLowerCase() + "</b></span>" +
                                    "<div class='mensahe'><p class='reply-msg'>" + row.replyto_message + "</p></div>";
                                    replySelf = "<div class='separate'><span class='name2'><b>You <span class='name2-ext'>replied to </span>" + row.replyto_firstname.toLowerCase() + "</b></span>" +
                                    "<div class='mensahe2'><p class='reply-msg2'>" + row.replyto_message + "</p></div>";
                                }
                            } else {
                                if(lastmessage !== row.employee_id) {
                                    replyOthers = "<div class='separate'><span class='name'><b>" + row.firstname.toLowerCase() + "</b></span>";   
                                }
                            }

                            


                            if (row.fullname !== <?php echo json_encode($_SESSION['current_employeefullname']); ?>) {
                                // var profile_picture = "../users/" + (row.profile_pic === "female.png" || row.profile_pic === "male.png" ? row.profile_pic : row.profile_pic);
                                var profile_picture = row.profile_pic === "female.png" || row.profile_pic === "male.png"
                                ? "../users/" + row.profile_pic
                                : "../users/" + row.employee_id + "/" + row.profile_pic;

                                convo.append(
                                    "<div class='msgbox-other' data-message-id='" + row.id + "'>" +
                                        (lastmessage !== row.employee_id ?
                                            (row.profile_pic ? 
                                                "<span class='pp'> <img src='" + profile_picture + "'>" + "</span>" :
                                                "Test") :
                                            "<span style='margin-left: 38px;'></span>") + 
                                            replyOthers +
                                    "<div class='mensahe'><p class='messagebox'>" + messageContent + "</p>" + 
                                    "<span class='reply-icon'><i class='bx bx-reply-all'></i><p class='reply-txt'>REPLY</p></span>" + // Added reply icon
                                    "</div></div>"
                                );
                            } else {
                                convo.append(
                                    replySelf +
                                    "<div class='msgbox-self' data-message-id='" + row.id + "'>" + 
                                    "<p class='messagebox'>" + row.message + "</p>" +
                                    "<span class='reply-icon'><i class='bx bx-reply-all'></i><p class='reply-txt'>REPLY</p></span></div>"
                                );
                            }

                            lastmessage = row.employee_id;
                            lastmsgsent = messageTime;
                        });

                        // Scroll to the bottom only if it's the initial load
                        if (isInitialLoad) {
                            convo.scrollTop(convo[0].scrollHeight);
                            isInitialLoad = false; // Set the flag to false after the initial scroll
                        }

                        // Add click event listener for reply icons
                        $('.reply-icon').on('click', function() {
                            var messageId = $(this).closest('[data-message-id]').data('message-id');
                            var replyBox = $('#reply-box');
                            console.log('Reply to message ID:', messageId); // For testing
                            // Store the message ID in the hidden input field
                            $('#replyMessageId').val(messageId);
                            $('#reply-form').submit(); // Submit the form
                        });

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
            };

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
                <form id="reply-form" action="" method="POST">
                    <input type="hidden" name="replyMessageId" id="replyMessageId" value="<?php if(isset($_POST['replyMessageId'])) echo $_POST['replyMessageId']; ?>">
                <?php if(isset($_POST['replyMessageId']) && $_POST['replyMessageId'] !== ''){ ?>
                <div class="reply-box" id="reply-box">
                <button class="cancel-reply"><i class='bx bx-x-circle'></i></button>
                    <h4><?php
                            $replyMessageId = $_POST['replyMessageId'];
                            
                            $getmsg = $pdo->prepare("SELECT c.*, u.firstname, u.lastname FROM conversation c INNER JOIN users u ON c.sender_id = u.employee_id WHERE c.id = $replyMessageId");
                            $getmsg->execute();

                            $rowmsg = $getmsg->fetch(PDO::FETCH_ASSOC);

                            if($rowmsg){
                                $replymessage = $rowmsg['message'];
                                $firstname = $rowmsg['firstname'];
                                $replierid = $rowmsg['sender_id'];
                            }

                            if($replierid == $_SESSION['current_employeeid']){
                                echo "<h6>Replying to yourself: </h6><p>" . htmlspecialchars($replymessage) . "</p>";
                            }else{
                                echo "<h6>Replying to " . $firstname . ": </h6><p>" . htmlspecialchars($replymessage) . "</p>";
                            }

                            $_SESSION['replyto_firstname'] = $firstname;
                            $_SESSION['replyto_message'] = $replymessage;
                    ?></h4>
                </div>
                <?php } ?>
                    <div class="typebox">
                        <!-- <input type="text" autocomplete="off" name="message" autofocus value="<?php if(isset($_POST['message'])){ echo $_POST['message']; }?>"> -->
                        <button type="submit" id="sendbtn" name="send">Send</button>
                        <input type="text" autocomplete="off" id="message-input" name="message" autofocus value="<?php if(isset($_POST['message'])){ echo $_POST['message']; }?>">
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

        if(onlineList && toggleOnlineBtn) {
            document.addEventListener('click', function(event){
                if (onlineList && toggleOnlineBtn && !onlineList.contains(event.target) && event.target !== toggleOnlineBtn) {
                    onlineList.classList.remove('ol-show');
                } else if (onlineList && toggleOnlineBtn) {
                    onlineList.classList.toggle('ol-show');
                }
            });
        }

        const cancelReplyButton = document.querySelector('.cancel-reply');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('sendbtn');

        messageInput.addEventListener('keydown', function(event) {
            if(event.key === 'Enter') {
                event.preventDefault();
                sendButton.click();
            }
        });


        if (cancelReplyButton) {
            cancelReplyButton.addEventListener('click', function() {
                document.getElementById('replyMessageId').value = '';
                document.getElementById('reply-form').submit(); // Submit the form
            });
        }
    });
</script>
</html>