<?php
session_start();
if (isset($_SESSION["user"])) {
    if (($_SESSION["user"]) == "" || $_SESSION['usertype'] != 'd') {
        header("location: ../login.php");
    } else {
        $useremail = $_SESSION["user"];
    }
} else {
    header("location: ../login.php");
}

include("../connection.php");
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

if ($_POST) {
    if (isset($_POST["scheduletitle"])) {
        $title = $_POST["scheduletitle"];
        $scheduledate = $_POST["scheduledate"];
        $scheduletime = $_POST["scheduletime"];
        $endtime = $_POST["endtime"];
        $nop = $_POST["nop"];
        
        if (isset($_POST["scheduleid"]) && !empty($_POST["scheduleid"])) {
            // Update existing schedule
            $scheduleid = $_POST["scheduleid"];
            $sql = "UPDATE schedule SET title='$title', scheduledate='$scheduledate', scheduletime='$scheduletime', endtime='$endtime', nop='$nop' WHERE scheduleid='$scheduleid' AND docid='$userid'";
        } else {
            // Insert new schedule
            $sql = "INSERT INTO schedule (title, docid, scheduledate, scheduletime, endtime, nop) VALUES ('$title', '$userid', '$scheduledate', '$scheduletime', '$endtime', '$nop')";
        }
        $database->query($sql);

        header("location: schedule.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Schedule</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .enter-button {
            padding: 15px;
            margin: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        /* Overlay styles */
        .overlay {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
        }

        /* Popup styles */
        .popup {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 800px;
            max-height: 80vh; /* Ensures the modal doesn't exceed 80% of viewport height */
            overflow-y: auto; /* Adds vertical scrollbar if content exceeds height */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Optional: adds shadow for better visibility */
        }

        /* Style for close button */
        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            text-decoration: none;
            color: #333;
            background: #f1f1f1;
            padding: 5px 10px;
            border-radius: 50%;
        }

        /* Show the modal when targeted */
        .overlay:target {
            display: block;
        }


    </style>
</head>
<body>
    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <tr>
                    <td style="padding:10px" colspan="2">
                        <table border="0" class="profile-container">
                            <tr>
                                <td width="30%" style="padding-left:20px">
                                    <img src="../img/user.png" alt="" width="100%" style="border-radius:50%">
                                </td>
                                <td style="padding:0px;margin:0px;">
                                    <p class="profile-title"><?php echo substr($username, 0, 13) ?>..</p>
                                    <p class="profile-subtitle"><?php echo substr($useremail, 0, 22) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <a href="../logout.php"><input type="button" value="Log out" class="logout-btn btn-primary-soft btn"></a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-dashbord">
                        <a href="index.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text">Dashboard</p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-appoinment">
                        <a href="appointment.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text">My Appointments</p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-session menu-active menu-icon-session-active">
                        <a href="schedule.php" class="non-style-link-menu non-style-link-menu-active">
                            <div>
                                <p class="menu-text">My Sessions</p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-patient">
                        <a href="patient.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text">My Patients</p>
                            </div>
                        </a>
                    </td>
                </tr>
                <tr class="menu-row">
                    <td class="menu-btn menu-icon-settings">
                        <a href="settings.php" class="non-style-link-menu">
                            <div>
                                <p class="menu-text">Settings</p>
                            </div>
                        </a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0;margin:0;padding:0;margin-top:25px;">
                <tr>
                    <td width="13%">
                        <a href="schedule.php">
                            <button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top:11px;padding-bottom:11px;margin-left:20px;width:125px">
                                <font class="tn-in-text">Back</font>
                            </button>
                        </a>
                    </td>
                    <td>
                        <p style="font-size: 23px;padding-left:12px;font-weight: 600;">My Sessions</p>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px;color: rgb(119, 119, 119);padding: 0;margin: 0;text-align: right;">Today's Date</p>
                        <p class="heading-sub12" style="padding: 0;margin: 0;">
                            <?php
                            date_default_timezone_set('Asia/Kolkata');
                            $today = date('Y-m-d');
                            echo $today;
                            ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex;justify-content: center;align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;">
                        <p class="heading-main12" style="margin-left: 45px;font-size:18px;color:rgb(49, 49, 49)">My Sessions (<?php 
                        $list110 = $database->query("SELECT * FROM schedule WHERE docid=$userid");
                        echo $list110->num_rows; ?>)</p>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:0px;width: 100%;">
                        <center>
                            <table class="filter-container" border="0">
                                <form action="" method="get">
                                    <tr>
                                        <td>
                                            <input type="text" name="query" value="<?php echo isset($_GET["query"]) ? $_GET["query"] : '' ?>" placeholder="Search by session title" class="search-bar">
                                        </td>
                                        <td>
                                            <input type="submit" value="Search" class="login-btn btn-primary-soft btn" style="padding: 10px 20px;">
                                        </td>
                                    </tr>
                                </form>
                            </table>
                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="padding-top:10px;width: 100%;">
                        <center>
                        <a href="#addSessionModal" class="non-style-link">
                            <button class="login-btn btn-primary-soft btn">Add Session</button>
                        </a>

                        </center>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="table-container">
                                <table class="main-table" border="0">
                                    <thead>
                                        <tr>
                                            <th class="table-headin">Session Title</th>
                                            <th class="table-headin">Scheduled Date & Time</th>
                                            <th class="table-headin">End Time</th>
                                            <th class="table-headin">Max num that can be booked</th>
                                            <th class="table-headin">Events</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = isset($_GET["query"]) ? $_GET["query"] : '';
                                        $sql = "SELECT * FROM schedule WHERE title LIKE '%$query%' AND docid=$userid";
                                        $result = $database->query($sql);

                                        if ($result->num_rows == 0) {
                                            echo '<tr>
                                            <td colspan="5">
                                            <br><br><br><br>
                                            <center>
                                            <img src="../img/notfound.svg" width="25%">
                                            <br>
                                            <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                            <a class="non-style-link" href="schedule.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Sessions &nbsp;</button></a>
                                            </center>
                                            <br><br><br><br>
                                            </td>
                                            </tr>';
                                        } else {
                                            while ($row = $result->fetch_assoc()) {
                                                $scheduleid = $row["scheduleid"];
                                                $title = $row["title"];
                                                $scheduledate = $row["scheduledate"];
                                                $scheduletime = $row["scheduletime"];
                                                $endtime = $row["endtime"];
                                                $nop = $row["nop"];
                                                echo '<tr>
                                                <td> &nbsp;' . substr($title, 0, 30) . '</td>
                                                <td style="text-align:center;">
                                                ' . substr($scheduledate, 0, 10) . ' ' . substr($scheduletime, 0, 5) . '
                                                </td>
                                                <td style="text-align:center;">
                                                    ' . substr($endtime, 0, 5) . '
                                                </td>
                                                <td style="text-align:center;">
                                                    ' . $nop . '
                                                </td>
                                                <td>
                                                <div style="display:flex;justify-content: center;">
                                                <a href="?action=view&id=' . $scheduleid . '" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-view" style="padding: 12px 40px;margin-top: 10px;">View</button></a>
                                                &nbsp;&nbsp;&nbsp;
                                                <a href="?action=drop&id=' . $scheduleid . '&title=' . $title .'" class="non-style-link"><button class="btn-primary-soft btn button-icon btn-delete" style="padding: 12px 40px;margin-top: 10px;">Cancel</button></a>
                                                </div>
                                                </td>
                                                </tr>';
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </center>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Add Session Modal -->
    <div id="addSessionModal" class="overlay">
    <div class="popup">
        <a class="close" href="#">&times;</a>
        <h2>Add New Session</h2>
        <form action="" method="post">
            <table width="100%" border="0">
                <tr>
                    <td class="label-td">
                        <label for="title" class="form-label">Session Title: </label>
                        <input type="text" name="scheduletitle" class="input-text" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <label for="scheduledate" class="form-label">Scheduled Date: </label>
                        <input type="date" name="scheduledate" class="input-text" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <label for="scheduletime" class="form-label">Scheduled Time: </label>
                        <input type="time" name="scheduletime" class="input-text" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <label for="endtime" class="form-label">End Time: </label>
                        <input type="time" name="endtime" class="input-text" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td">
                        <label for="nop" class="form-label">Max Number of Bookings: </label>
                        <input type="number" name="nop" class="input-text" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Add Session" class="login-btn btn-primary-soft btn">
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>


    <?php
    if ($_GET) {
        $id = $_GET["id"];
        $action = $_GET["action"];
        if ($action == 'drop') {
            $title = $_GET["title"];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <h2>Are you sure?</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                            You want to delete this record<br>(' . substr($title, 0, 40) . ').
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <a href="deleteschedule.php?id=' . $id . '" class="non-style-link">
                                <button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;">Yes</button>
                            </a>
                            &nbsp;&nbsp;&nbsp;
                            <a href="schedule.php" class="non-style-link">
                                <button class="btn-primary btn" style="display: flex;justify-content: center;align-items: center;margin:10px;padding:10px;">No</button>
                            </a>
                        </div>
                    </center>
                </div>
            </div>
            ';
        } elseif ($action == 'view') {
            $sqlmain = "SELECT * FROM schedule WHERE scheduleid=$id AND docid=$userid";
            $result = $database->query($sqlmain);
            $row = $result->fetch_assoc();
            $scheduleid = $row["scheduleid"];
            $title = $row["title"];
            $scheduledate = $row["scheduledate"];
            $scheduletime = $row["scheduletime"];
            $endtime = $row["endtime"];
            $nop = $row["nop"];
            echo '
            <div id="popup1" class="overlay">
                <div class="popup">
                    <center>
                        <h2>View Details</h2>
                        <a class="close" href="schedule.php">&times;</a>
                        <div class="content">
                        </div>
                        <div style="display: flex;justify-content: center;">
                            <table width="80%" class="sub-table scrolldown add-doc-form-container" border="0">
                                <form action="" method="post">
                                    <input type="hidden" name="scheduleid" value="' . $scheduleid . '">
                                    <tr>
                                        <td>
                                            <p style="text-align: left;font-size: 25px;font-weight: 500;">Edit Session Details.</p><br><br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="title" class="form-label">Session Title: </label>
                                            <input type="text" name="scheduletitle" value="' . $title . '" class="input-text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="scheduledate" class="form-label">Scheduled Date: </label>
                                            <input type="date" name="scheduledate" value="' . $scheduledate . '" class="input-text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="scheduletime" class="form-label">Scheduled Time: </label>
                                            <input type="time" name="scheduletime" value="' . $scheduletime . '" class="input-text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="endtime" class="form-label">End Time: </label>
                                            <input type="time" name="endtime" value="' . $endtime . '" class="input-text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label-td" colspan="2">
                                            <label for="nop" class="form-label">Maximum number of bookings: </label>
                                            <input type="number" name="nop" value="' . $nop . '" class="input-text">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="submit" value="Save" class="login-btn btn-primary-soft btn">
                                        </td>
                                    </tr>
                                </form>
                            </table>
                        </div>
                    </center>
                </div>
            </div>
            ';
        }
    }
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modals = document.querySelectorAll('.overlay');
            var closeButtons = document.querySelectorAll('.close');

            closeButtons.forEach(function (button) {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    modals.forEach(function (modal) {
                        modal.style.display = 'none';
                    });
                });
            });
        });

    </script>
</body>
</html>


