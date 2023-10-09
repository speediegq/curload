<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$Error = "";
$html = "";

if (isset($_REQUEST['e'])) $Error = htmlspecialchars($_REQUEST['e']);

if (checkIfAdminExists()) {
    header("Location: /");
    die();
}

$html = printHeader($html);
$html .= "\t\t\t<h1>Welcome</h1>\n";
$html .= "\t\t\t\t<p>Before curload can be used, a primary administrator user must be created.</p>\n";

$html .= "\t\t\t\t<form class=\"adminCreateForm\" action=\"create.php?redir=setup\" method=\"post\">\n";
$html .= "\t\t\t\t\t<label for=\"username\">Username</label>\n";
$html .= "\t\t\t\t\t<input type=\"text\" name=\"username\" placeholder=\"Username\">\n";
$html .= "\t\t\t\t\t<label for=\"password\">Password</label>\n";
$html .= "\t\t\t\t\t<input type=\"password\" name=\"password\" placeholder=\"Password\">\n";
$html .= "\t\t\t\t\t<input type=\"hidden\" name=\"type\" value=\"Admin\">\n";
$html .= "\t\t\t\t\t<input type=\"submit\" value=\"Create user\" name=\"create\">\n";
$html .= "\t\t\t\t</form>\n";

// handle errors
if ($Error == "password" || $Error == "username") {
    $html .= "\t\t\t\t<p class=\"adminError\">Invalid username or password.</p>\n";
} else if ($Error == "type") {
    $html .= "\t\t\t\t<p class=\"adminError\">Invalid type.</p>\n";
} else if ($Error == "denied") {
    $html .= "\t\t\t\t<p class=\"adminError\">You don't have permission to create a user of this type.</p>\n";
} else if ($Error == "exists") { // i mean, how the fuck would this happen anyway?
    $html .= "\t\t\t\t<p class=\"adminError\">This user already exists.</p>\n";
} else if ($Error == "uploads") {
    $html .= "\t\t\t\t<p class=\"adminError\">Invalid amount of uploads.</p>\n";
}

$html = printFooter($html);
print "$html";

?>
