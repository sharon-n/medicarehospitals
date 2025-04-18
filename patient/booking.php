<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <title>Sessions</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    <?php
    session_start();

    if (isset($_SESSION["user"])) {
        if (empty($_SESSION["user"]) || $_SESSION['usertype'] != 'p') {
            header("location: ../login.php");
        } else {
            $useremail = $_SESSION["user"];
        }
    } else {
        header("location: ../login.php");
    }

    // Import database
    include("../connection.php");
    $userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["pid"];
    $username = $userfetch["pname"];

    date_default_timezone_set('Asia/Kolkata');
    $today = date('Y-m-d');
    ?>

    <div class="container">
        <div class="menu">
            <table class="menu-container" border="0">
                <!-- Profile details and menu links -->
            </table>
        </div>

        <div class="dash-body">
            <table border="0" width="100%" style="border-spacing: 0; margin: 0; padding: 0; margin-top: 25px;">
                <tr>
                    <td width="13%">
                        <a href="schedule.php">
                            <button class="login-btn btn-primary-soft btn btn-icon-back" style="padding-top: 11px; padding-bottom: 11px; margin-left: 20px; width: 125px">
                                <font class="tn-in-text">Back</font>
                            </button>
                        </a>
                    </td>
                    <td>
                        <form action="schedule.php" method="post" class="header-search">
                            <input type="search" name="search" class="input-text header-searchbar" placeholder="Search Doctor name or Email or Date (YYYY-MM-DD)" list="doctors">&nbsp;&nbsp;
                            <?php
                            echo '<datalist id="doctors">';
                            $list11 = $database->query("SELECT DISTINCT * FROM doctor");
                            $list12 = $database->query("SELECT DISTINCT * FROM schedule GROUP BY title");

                            while ($row00 = $list11->fetch_assoc()) {
                                echo "<option value='{$row00['docname']}'><br/>";
                            }
                            while ($row00 = $list12->fetch_assoc()) {
                                echo "<option value='{$row00['title']}'><br/>";
                            }

                            echo '</datalist>';
                            ?>
                            <input type="submit" value="Search" class="login-btn btn-primary btn" style="padding-left: 25px; padding-right: 25px; padding-top: 10px; padding-bottom: 10px;">
                        </form>
                    </td>
                    <td width="15%">
                        <p style="font-size: 14px; color: rgb(119, 119, 119); padding: 0; margin: 0; text-align: right;">
                            Today's Date
                        </p>
                        <p class="heading-sub12" style="padding: 0; margin: 0;">
                            <?php echo $today; ?>
                        </p>
                    </td>
                    <td width="10%">
                        <button class="btn-label" style="display: flex; justify-content: center; align-items: center;">
                            <img src="../img/calendar.svg" width="100%">
                        </button>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" style="padding-top: 10px; width: 100%;">
                        <!-- Page header -->
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="100%" class="sub-table scrolldown" border="0" style="padding: 50px; border: none;">
                                    <tbody>
                                        <?php
                                        if ($_GET && isset($_GET["id"])) {
                                            $id = $_GET["id"];
                                            $sqlmain = "SELECT * FROM schedule 
                                                        INNER JOIN doctor ON schedule.docid = doctor.docid 
                                                        WHERE schedule.scheduleid = $id  
                                                        ORDER BY schedule.scheduledate DESC";
                                            $result = $database->query($sqlmain);
                                            $row = $result->fetch_assoc();
                                            $scheduleid = $row["scheduleid"];
                                            $title = $row["title"];
                                            $docname = $row["docname"];
                                            $docemail = $row["docemail"];
                                            $scheduledate = $row["scheduledate"];
                                            $scheduletime = $row["scheduletime"];

                                            // Get the maximum appointment number for the specific doctor
                                            $docid = $row['docid'];
                                            $sql2 = "SELECT MAX(apponum) as max_apponum FROM appointment 
                                                     INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
                                                     WHERE schedule.docid = $docid";
                                            $result12 = $database->query($sql2);
                                            $row12 = $result12->fetch_assoc();
                                            $maxApponum = $row12['max_apponum'];
                                            $apponum = $maxApponum + 1;

                                            echo '
                                                <form action="booking-complete.php" method="post">
                                                    <input type="hidden" name="scheduleid" value="' . $scheduleid . '">
                                                    <input type="hidden" name="apponum" value="' . $apponum . '">
                                                    <input type="hidden" name="date" value="' . $today . '">
                                                    <input type="hidden" name="docname" value="' . $docname . '">
                                                    <input type="hidden" name="docemail" value="' . $docemail . '">
                                                    <input type="hidden" name="scheduledate" value="' . $scheduledate . '">
                                                    <input type="hidden" name="scheduletime" value="' . $scheduletime . '">
                                            ';

                                            echo '
                                            <td style="width: 50%;" rowspan="2">
                                                <div class="dashboard-items search-items">
                                                    <div style="width:100%">
                                                        <div class="h1-search" style="font-size: 25px;">
                                                            Session Details
                                                        </div><br><br>
                                                        <div class="h3-search" style="font-size: 18px; line-height: 30px;">
                                                            Doctor name: &nbsp;&nbsp;<b>' . $docname . '</b><br>
                                                            Doctor Email: &nbsp;&nbsp;<b>' . $docemail . '</b>
                                                        </div>
                                                        <div class="h3-search" style="font-size: 18px;">
                                                        </div><br>
                                                        <div class="h3-search" style="font-size: 18px;">
                                                            Session Title: ' . $title . '<br>
                                                            Session Scheduled Date: ' . $scheduledate . '<br>
                                                            Session Starts: ' . $scheduletime . '<br>
                                                            Appointment fee: <b>Ksh. 1500.00</b>
                                                        </div><br>
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="width: 25%;">
                                                <div class="dashboard-items search-items">
                                                    <div style="width:100%; padding-top: 15px; padding-bottom: 15px;">
                                                        <div class="h1-search" style="font-size: 20px; line-height: 35px; margin-left: 8px; text-align: center;">
                                                            Your Appointment Number
                                                        </div>
                                                        <center>
                                                            <div class="dashboard-icons" style="margin-left: 0px; width: 90%; font-size: 70px; font-weight: 800; text-align: center; color: var(--btnnictext); background-color: var(--btnice)">
                                                                ' . $apponum . '
                                                            </div>
                                                        </center>
                                                    </div>
                                                    <br>
                                                    <br>
                                                </div>
                                            </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="submit" class="login-btn btn-primary btn btn-book" style="margin-left: 10px; padding-left: 25px; padding-right: 25px; padding-top: 10px; padding-bottom: 10px; width: 95%; text-align: center;" value="Book now" name="booknow">
                                                </form>
                                                </td>
                                            </tr>';
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
</body>
</html>
