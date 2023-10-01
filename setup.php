<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";
include "core.php";

$Error = "";
$html = "";

if (isset($_REQUEST['e'])) $Error = $_REQUEST['e'];

if (checkIfAdminExists()) {
    header("Location: /");
    die();
}

$html = printHeader($html);
$html .= "\t\t\t<h1>Welcome</h1>\n";
$html .= "\t\t\t\t<p>Before curload can be used, a primary administrator must be created.</p>\n";
$html .= "\t\t\t\t<p class='error'>Please note that the primary administrator key cannot trivially be changed later.</p>\n";

$html .= "\t\t\t\t<form class=\"adminCreateForm\" action=\"create.php?redir=setup\" method=\"post\">\n";
$html .= "\t\t\t\t\t<label for=\"data\">Key</label>\n";
$html .= "\t\t\t\t\t<input type=\"text\" name=\"data\" placeholder=\"Key\">\n";
$html .= "\t\t\t\t\t<input type=\"hidden\" name=\"type\" value=\"Admin\">\n";
$html .= "\t\t\t\t\t<input type=\"submit\" value=\"Create key\" name=\"create\">\n";
$html .= "\t\t\t\t</form>\n";

// handle errors
if ($Error == "data") {
    $html .= "\t\t\t\t<p class=\"adminError\">Invalid key.</p>\n";
} else if ($Error == "type") {
    $html .= "\t\t\t\t<p class=\"adminError\">Invalid type.</p>\n";
} else if ($Error == "denied") {
    $html .= "\t\t\t\t<p class=\"adminError\">You don't have permission to create a key of this type.</p>\n";
} else if ($Error == "exists") {
    $html .= "\t\t\t\t<p class=\"adminError\">This key already exists.</p>\n";
} else if ($Error == "uploads") {
    $html .= "\t\t\t\t<p class=\"adminError\">Invalid amount of uploads.</p>\n";
}

$html = printFooter($html);
print "$html";

?>
