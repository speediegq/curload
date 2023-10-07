<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

if (!$publicAccountCreation) {
    header("Location: /");
    die();
}

if (isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
    $Username = $_REQUEST['username'];
    $Password = generatePassword($_REQUEST['password']);

    if ($_REQUEST['password'] != $_REQUEST['cpassword']) {
        header("Location: register.php?e=mismatch");
        die();
    }

    // check if a user by the same name already exists
    $Database = createTables($sqlDB);
    $DatabaseQuery = $Database->query('SELECT * FROM users');
    while ($line = $DatabaseQuery->fetchArray()) {
        if ($line['username'] == "$Username" && $Username != "" && $line['username'] != "") {
            header("Location: register.php?e=exists");
            die();
        }
    }

    if ($storeAgent || $storeAgent == "true") $userAgent = getUserAgent();
    if ($storeCreated || $storeCreated == "true") $Created = date($dateFormat);
    if ($storeLastUsage || $storeLastUsage == "true") $lastUsed = date($dateFormat);
    if ($storeIP || $storeIP == "true") $ip = getIPAddress();

    $Database->exec("INSERT INTO users(username, password, usertype, primaryadmin, numberofuploads, uploadsleft, lastused, created, ip, useragent) VALUES('$Username', '$Password', '1', '0', '0', '-1', '$lastUsed', '$Created', '$ip', '$userAgent')");

    header("Location: login.php");
    die();
} else {
    $html = "";

    $html = printHeader($html);

    $html .= "\t\t\t<h1 id='registerHeader'>Welcome to $instanceName</h1>\n";
    $html .= "\t\t\t\t<p>To create an account, enter your desired user name and password.</p>\n";
    $html .= "\t\t\t\t<form action=\"register.php\">\n";
    $html .= "\t\t\t\t\t<input type=\"text\" name=\"username\" placeholder=\"Username\">\n";
    $html .= "\t\t\t\t\t<input type=\"password\" name=\"password\" placeholder=\"Password\">\n";
    $html .= "\t\t\t\t\t<input type=\"password\" name=\"cpassword\" placeholder=\"Confirm password\">\n";
    if (isset($Redirect)) $html .= "\t\t\t\t\t<input type=\"hidden\" name=\"redir\" value=\"$Redirect\">\n";
    $html .= "\t\t\t\t\t<input type=\"submit\" value=\"Create account\">\n";
    $html .= "\t\t\t\t</form>\n";

    if (isset($_REQUEST['e']) && $_REQUEST['e'] == "exists") {
        session_unset();
        session_destroy();

        $html .= "\t\t\t\t<p class=\"error\">An account by this name already exists.</p>\n";
    } else if (isset($_REQUEST['e']) && $_REQUEST['e'] == "mismatch") {
        session_unset();
        session_destroy();

        $html .= "\t\t\t\t<p class=\"error\">The two passwords do not match.</p>\n";
    }

    $html = printFooter($html);

    print "$html";
}
?>
