<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php"; /* config.php includes configuration options */
include "core.php"; /* core.php includes core functions */

// We declare these variables first
$Error = "";
$html = "";

// If an error was reported, assign it to variable $Error
if (isset($_REQUEST['e'])) $Error = $_REQUEST['e'];

// Print some HTML, bar and a basic heading and description paragraph
// \t - Tab character
// \n - New line character
// .= - Append to variable
$html = printHeader($html);
$html .= "\t\t\t<h1>$instanceName</h1>\n";
$html .= "\t\t\t\t<p>$instanceDescription</p>\n";

// If logged in ...
if (isset($_COOKIE[$cookieTypeName]) || ($publicUploading || $publicUploading == "true")) {
    $html .= printFileUploadForm($html, $Error);
} else {
    $html .= "\t\t\t\t<p>To upload a file, <a href=\"login.php\">log in using your key</a> and select a file to upload. After uploading, you will receive a link to the file stored on the servers.</p>\n";
    $html .= "\t\t\t\t<p>You can also upload a file using <code>curl</code> (or any POST request):<br><br><code>curl -F \"file=@myfile\" -F \"key=mykey\" \"https://dl.speedie.site/upload.php\"</code>.</p>\n";
}

// End the content div and print footer
$html = printFooter($html);

// Finally print it all out at once
print "$html";

?>
