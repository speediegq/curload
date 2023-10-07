<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$html = "";
$html = printHeader($html);

$Username = "";
$Password = "";
$ID = -1;
$Primary = 0;
$IsCurrentUser = false;

// make sure a username and password is specified for authentication
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    $Username = $_SESSION['username'];
    $Password = $_SESSION['password'];
} else {
    print "Username and password must be specified.";
    die();
}

if (isset($_REQUEST['id'])) {
    $ID = $_REQUEST['id'];
} else {
    $ID = -1; // use the username and password to determine
}

$Authorized = 0;

$Database = createTables($sqlDB);
$DatabaseQuery = $Database->query('SELECT * FROM users');

// check permissions
while ($line = $DatabaseQuery->fetchArray()) {
    if ($ID == -1 && $line['username'] == $Username && $Username != "" && $line['password'] != "" && $Password == $line['password']) {
        $ID = $line['id'];
        $SelUsername = $line['username'];
        $IsCurrentUser = true;
        $Authorized = 1;

        break;
    } else if ($line['username'] == $Username && $Username != "" && $line['password'] != "" && $Password == $line['password']) { // We're logged into an admin account
        $UserDatabaseQuery = $Database->query('SELECT * FROM users');
        $Primary = $line['primaryadmin'];
        $IsCurrentUser = false;

        while ($uline = $UserDatabaseQuery->fetchArray()) {
            if ($ID == $uline['id'] && ($Primary && $uline['usertype'] == 2 || $uline['usertype'] != 2)) {
                $SelUsername = $uline['username'];
                $Authorized = 1;
                break;
            }
        }
    }
}

if ($Authorized == 0) {
    die();
}

$html .= "\t\t\t<h1>Account options</h1>\n";
$html .= "\t\t\t\t<p>This is where you can change account options.</p>\n";

if ($allowPasswordChange || $IsCurrentUser) {
    $html .= "\t\t\t\t<h2>Change password</h2>\n";
    $html .= "\t\t\t\t\t<p>If you need to change your password, you can do so here:</p>\n";
    $html .= "\t\t\t\t\t<form action=\"change.php\" method=\"post\" class=\"changePass\">\n";

    if ($IsCurrentUser) {
        $html .= "\t\t\t\t\t\t<label for=\"curpass\">Current password</label>\n";
        $html .= "\t\t\t\t\t\t<input type=\"password\" name=\"curpass\" placeholder=\"Current password\">\n";
    }

    $html .= "\t\t\t\t\t\t<label for=\"newpass\">New password</label>\n";
    $html .= "\t\t\t\t\t\t<input type=\"password\" name=\"newpass\" placeholder=\"New password\">\n";
    $html .= "\t\t\t\t\t\t<label for=\"newpassc\">Confirm</label>\n";
    $html .= "\t\t\t\t\t\t<input type=\"password\" name=\"newpassc\" placeholder=\"Confirm\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"pass\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"id\"\" value=\"$ID\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"submit\" value=\"Change password\" name=\"change\">\n";
    $html .= "\t\t\t\t\t</form>\n";
}

if ($allowUsernameChange || !$IsCurrentUser) {
    $html .= "\t\t\t\t<h2>Change username</h2>\n";
    $html .= "\t\t\t\t\t<p>If you need to change your username, you can do so here:</p>\n";
    $html .= "\t\t\t\t\t<form action=\"change.php\" method=\"post\" class=\"changeUser\">\n";

    if ($IsCurrentUser) {
        $html .= "\t\t\t\t\t\t<label for=\"curusername\">Current username</label>\n";
        $html .= "\t\t\t\t\t\t<input type=\"text\" name=\"curusername\" placeholder=\"Current username\">\n";
    }

    $html .= "\t\t\t\t\t\t<label for=\"newusername\">New username</label>\n";
    $html .= "\t\t\t\t\t\t<input type=\"text\" name=\"newusername\" placeholder=\"New username\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"username\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"id\"\" value=\"$ID\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"submit\" value=\"Change username\" name=\"change\">\n";
    $html .= "\t\t\t\t\t</form>\n";
}

if (!$IsCurrentUser) {
    $html .= "\t\t\t\t<h2>Administrator: Change type</h2>\n";
    $html .= "\t\t\t\t\t<p>If you need to change the type, you can do so here:</p>\n";

    $html .= "\t\t\t\t\t<form action=\"change.php\" method=\"post\" class=\"changeType\">\n";
    $html .= "\t\t\t\t\t\t<label for=\"type\">New type</label>\n";
    $html .= "\t\t\t\t\t\t<select name=\"type\" required>\n";
    if ($Primary == 1) $html .= "\t\t\t\t\t\t\t<option value=\"2\">Administrator</option>\n";
    $html .= "\t\t\t\t\t\t\t<option value=\"1\" selected=\"selected\">User</option>\n";
    $html .= "\t\t\t\t\t\t</select>\n";
    $html .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"type\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"id\"\" value=\"$ID\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"submit\" value=\"Change type\" name=\"change\">\n";
    $html .= "\t\t\t\t\t</form>\n";

    $html .= "\t\t\t\t<h2>Administrator: Change uploads left</h2>\n";
    $html .= "\t\t\t\t\t<p>If you need to change the number of uploads left, you can do so here:</p>\n";

    $html .= "\t\t\t\t\t<form action=\"change.php\" method=\"post\" class=\"changeUploads\">\n";
    $html .= "\t\t\t\t\t\t<label for=\"uploads\">New uploads</label>\n";
    $html .= "\t\t\t\t\t\t<input type=\"number\" name=\"uploads\" value=\"1\">\n";
    $html .= "\t\t\t\t\t\t<label for=\"user\">No limit</label>\n";
    $html .= "\t\t\t\t\t\t<input type=\"checkbox\" name=\"user\" value=\"true\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"action\" value=\"uploads\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"hidden\" name=\"id\"\" value=\"$ID\">\n";
    $html .= "\t\t\t\t\t\t<input type=\"submit\" value=\"Change uploads\" name=\"change\">\n";
    $html .= "\t\t\t\t\t</form>\n";
}

$html = printFooter($html);
print "$html";

?>
