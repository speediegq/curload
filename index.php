<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

include "config.php";

$html .= "<!DOCTYPE html>\n";
$html .= "<html>\n";
$html .= "\t<head>\n";
$html .= "\t\t<meta name=\"description\" content=\"$primaryTitle\">\n";
$html .= "\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n";
$html .= "\t\t<link rel=\"icon\" href=\"$Icon\" />\n";
$html .= "\t\t<link type=\"text/css\" rel=\"stylesheet\" href=\"$Stylesheet\"/>\n";
$html .= "\t\t<title>$primaryTitle</title>\n";
$html .= "\t</head>\n";
$html .= "\t<body>\n";
$html .= "\t\t<div class=\"content\">\n";

$html .= "\t\t\t<h1>speedie's super awesome file uploader junk</h1>\n";
$html .= "\t\t\t<form action=\"upload.php\" method=\"post\" enctype=\"multipart/form-data\">Select file to upload<br><input type=\"file\" name=\"file\" id=\"file\"><br><input type=\"text\" name=\"key\" placeholder=\"Upload key here\"><br><input type=\"submit\" value=\"Upload selected file\" name=\"web\"></form>\n";
$html .= "\t\t\t<p>Max file size: $maxFileSize MB</p>\n";
$html .= "\t\t\t<a href=\"https://git.speedie.site/speedie/curload\">source code</a>\n";
$html .= "\t\t\t<h2>oops i leaked admin tools</h2>\n";
$html .= "\t\t\t<form action=\"create.php\" method=\"post\">\n";
$html .= "\t\t\t\t<input type=\"text\" name=\"data\" placeholder=\"key name\">\n";
$html .= "\t\t\t\t<input type=\"text\" name=\"type\" placeholder=\"type\">\n";
$html .= "\t\t\t\t<input type=\"text\" name=\"key\" placeholder=\"admin key\">\n";
$html .= "\t\t\t\t<input type=\"text\" name=\"uploads\" placeholder=\"max uploads\">\n";
$html .= "\t\t\t\t<input type=\"submit\" value=\"make\">\n";
$html .= "\t\t\t</form>\n";

$html .= "\t\t</div>\n";
$html .= "\t</body>\n";
$html .= "</html>\n";

print "$html";

?>
