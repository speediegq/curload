<?php session_start();
/* curload
 * Simple file uploading using POST requests
 * Licensed under the GNU Affero General Public License version 3.0
 */

/* This is an example page. For it to be displayed in the navigation bar, you must create a .name file. The name of the .name file is
 * simply the name of the PHP script with '.name' added on. In this case, that's 'about.php.name'
 * The file should contain the name of this page.
 *
 * This is potentially useful if you want terms of service, rules, report, etc.
 */
include "config.php";
include "core.php";

$Error = "";
$html = "";

$html = printHeader($html);
$html .= "\t\t\t<h1>About $instanceName</h1>\n";
$html .= "\t\t\t\t<p>$instanceDescription</p>\n";
$html .= "\t\t\t\t<p>It is free/libre software licensed under the GNU Affero General Public License version 3.0. You can have a copy of the software <a href=\"https://git.speedie.site/speedie/curload\">here.</a>\n</p>";
$html = printFooter($html);

print "$html";

?>
