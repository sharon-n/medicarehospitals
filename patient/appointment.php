<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Appointments</title>
    <style>
        .popup {
            animation: transitionIn-Y-bottom 0.5s;
        }
        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
<?php
    // Start session and validate user
    session_start();
    if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
        header("location: ../login.php");
        exit; // Ensure to stop further execution
    }

    $useremail = $_SESSION["user"];

    // Import database connection
    include("../connection.php");
    require __DIR__ . '/../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Fetch patient details
    $userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
    $userfetch = $userrow->fetch_assoc();

    if ($userfetch) {
        $userid = $userfetch["pid"];
        $username = $userfetch["pname"];
    } else {
        // Handle the case where the user is not found
        echo "User not found.";
        exit;
    }

    // Handle cancellation request
    if (isset($_GET['action']) && $_GET['action'] == 'drop' && isset($_GET['id'])) {
        $appoid = intval($_GET['id']);

        // Fetch appointment details for email
        $appointment_row = $database->query("SELECT schedule.scheduledate, schedule.scheduletime, doctor.docname, appointment.apponum 
                                            FROM appointment 
                                            INNER JOIN schedule ON appointment.scheduleid = schedule.scheduleid 
                                            INNER JOIN doctor ON schedule.docid = doctor.docid 
                                            WHERE appointment.appoid = $appoid AND appointment.pid = $userid");
        $appointment = $appointment_row->fetch_assoc();

        if ($appointment) {
            $scheduledate = $appointment['scheduledate'];
            $scheduletime = $appointment['scheduletime'];
            $docname = $appointment['docname'];
            $apponum = $appointment['apponum'];

            // Delete the appointment
            $delete_sql = "DELETE FROM appointment WHERE appoid = $appoid AND pid = $userid";
            if ($database->query($delete_sql) === TRUE) {
                // Send cancellation email
                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'medicarehospitalskenya@gmail.com'; // Your Gmail address
                    $mail->Password = 'uiof msdl mhnd mraf'; // Your Gmail App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    //Recipients
                    $mail->setFrom('medicarehospitalskenya@gmail.com', 'Medicare Hospital');
                    $mail->addAddress($useremail);

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Appointment Cancellation Confirmation';
                    $mail->Body    = "
                        <p>Dear $username,</p>
                        <p>Your appointment with Dr. $docname scheduled on $scheduledate at $scheduletime has been successfully cancelled.</p>
                        <p>Appointment Number: $apponum</p>
                        <p>Thank you for using our service.</p>
                    ";

                    $mail->send();
                } catch (Exception $e) {
                    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                // Redirect to avoid resubmission
                header("Location: " . $_SERVER['PHP_SELF']);
                exit;
            } else {
                // Handle deletion error
                echo "Error deleting record: " . $database->error;
            }
        } else {
            echo "Appointment not found.";
        }
    }

    // Construct SQL query to fetch appointments
    $sqlmain = "SELECT appointment.appoid, schedule.scheduleid, schedule.title, doctor.docname, doctor.specialties, patient.pname, 
                    schedule.scheduledate, schedule.scheduletime, appointment.apponum, appointment.appodate 
                FROM schedule 
                INNER JOIN appointment ON schedule.scheduleid = appointment.scheduleid 
                INNER JOIN patient ON patient.pid = appointment.pid 
                INNER JOIN doctor ON schedule.docid = doctor.docid  
                WHERE patient.pid = $userid";

    // Apply any additional filters or ordering as needed
    $sqlmain .= " ORDER BY appointment.appodate ASC";

    $result = $database->query($sqlmain);
    ?>
    <div class="container">
        <div class="menu">
            <!-- Your menu HTML code -->
        </div>
        <div class="dash-body">
            <table border="0" width="100%">
                <!-- Header and other HTML code -->
                <tr>
                    <td colspan="4">
                        <center>
                            <div class="abc scroll">
                                <table width="93%" class="sub-table scrolldown" border="0">
                                    <tbody>
                                        <?php
                                        if ($result->num_rows == 0) {
                                            echo '<tr>
                                                <td colspan="7">
                                                    <br><br><br><br>
                                                    <center>
                                                        <img src="../img/notfound.svg" width="25%">
                                                        <br>
                                                        <p class="heading-main12" style="margin-left: 45px;font-size:20px;color:rgb(49, 49, 49)">We couldn\'t find anything related to your keywords!</p>
                                                        <a class="non-style-link" href="appointment.php"><button class="login-btn btn-primary-soft btn" style="display: flex;justify-content: center;align-items: center;margin-left:20px;">&nbsp; Show all Appointments &nbsp;</button></a>
                                                    </center>
                                                    <br><br><br><br>
                                                </td>
                                            </tr>';
                                        } else {
                                            while ($row = $result->fetch_assoc()) {
                                                $scheduleid = $row["scheduleid"];
                                                $title = $row["title"];
                                                $docname = $row["docname"];
                                                $specialties = $row["specialties"];
                                                $scheduledate = $row["scheduledate"];
                                                $scheduletime = $row["scheduletime"];
                                                $apponum = $row["apponum"];
                                                $appodate = $row["appodate"];
                                                $appoid = $row["appoid"];

                                                echo '<tr>';
                                                echo '<td style="width: 25%;">
                                                        <div class="dashboard-items search-items">
                                                            <div style="width:100%;">
                                                                <div class="h3-search">
                                                                    Booking Date: ' . substr($appodate, 0, 30) . '<br>
                                                                    Reference Number: OC-000-' . $appoid . '
                                                                </div>
                                                                <div class="h1-search">
                                                                    ' . substr($title, 0, 21) . '<br>
                                                                </div>
                                                                <div class="h3-search">
                                                                    Appointment Number:<div class="h1-search">0' . $apponum . '</div>
                                                                </div>
                                                                <div class="h3-search">
                                                                    ' . substr($docname, 0, 30) . '<br>
                                                                    Specialties: ' . $specialties . '
                                                                </div>
                                                                <div class="h4-search">
                                                                    Scheduled Date: ' . $scheduledate . '<br>Starts: <b>@' . substr($scheduletime, 0, 5) . '</b> (24h)
                                                                </div>
                                                                <br>
                                                                <a href="?action=drop&id=' . $appoid . '" onclick="return confirm(\'Are you sure you want to cancel this booking?\');">
                                                                    <button class="login-btn btn-primary-soft btn" style="padding-top:11px;padding-bottom:11px;width:100%">
                                                                        <font class="tn-in-text">Cancel Booking</font>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>';
                                                echo '</tr>';
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
</body>
</html>

