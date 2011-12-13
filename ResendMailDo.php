<?php

$allowed_ops = array("mail", "image_captcha");

include_once "config.php";
include_once "themes/$app_theme/header.php";
include_once "Functions.php";
include_once "Parameters.php";
include_once "MYSQLConnection.php";

InitCaptcha();

?>

<h2><?= _("##REQUESTSTATUS##") ?></h2>

<?php

// USER INPUT VALIDATION ------------------------------------------------------- 
// Some of the parameters were not set, the form was not used to get here
if (!isset($mail) || !isset($image_captcha)) {

    VariableNotSet();

// Some of the parameters are empty
} elseif ($mail == '' || $image_captcha == '') {

    EmptyVariable();

// The cookie has expired
} elseif (!isset($session_captcha)) {

    ExpiredCaptcha();

// We compare the cookie hash with the user entry
// If they are different, the user messed up
} elseif ($session_captcha <> $image_captcha) {

    WrongCaptcha();

// Invalid e-mail
} elseif (preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $mail) == 0) {

    InvalidEMail();

} else {

    // We query for a row in the NewUser table that matches with the
    // mail provided by the user
    $sel_q = "SELECT * FROM NewUser"
            . " WHERE mail='" . $mail . "'"
            . " ORDER BY token DESC LIMIT 0,1";

    // Searching ...
    $sel_r = AssistedMYSQLQuery($sel_q);

    // How much did we get?
    $sel_n = mysql_num_rows($sel_r);

    // If we get no results, then the user has been created already or
    // there has not been any new user request asociated to the e-mail
    // provided by the user
    if ($sel_n == 0) {

        NoRequests();
        
    // More than one result is IMPOSSIBLE, didn't we already limit it to 1 on
    // the SQL query?
    } elseif ($sel_n > 1) {

        RuntimeError();

    // If there is at least one resend petition, we continue  
    } elseif ($sel_n == 1) {

        // Reading query result
        while ($row = mysql_fetch_array($sel_r, MYSQL_ASSOC)) {

            // Getting user info to use it on mail
            $uid = $row['uid'];
            $mail = $row['mail'];
            $token = $row['token'];
            $givenName = $row['givenName'];

            // Sending e-mail ...
            $send = AssistedEMail("ResendMailDo", $mail);

            // If the mailing went OK ... 
            if ($send) {
                // We log the event
                WriteLog("ResendMailDo");
                // Print the good news to the user
                Success("ResendMailDo");
            } else {
                // We fail nicely, at least
                Fail("ResendMailDo");
            }
        }
    }
}

// Closing the connection
$mysqlx = AssistedMYSQLClose($mysqlc);

include_once "themes/$app_theme/footer.php";

?>