<?php
/* curload
 * Simple file uploading using POST requests and temporary keys
 * Licensed under the GNU Affero General Public License version 3.0
 */

function printHeader($title, $description, $Icon, $Stylesheet) {
    print "<!DOCTYPE html>\n";
    print "<html>\n";
    print "\t<head>\n";
    print "\t\t<meta name=\"description\" content=\"$description\">\n";
    print "\t\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />\n";
    print "\t\t<link rel=\"icon\" href=\"$Icon\" />\n";
    print "\t\t<link type=\"text/css\" rel=\"stylesheet\" href=\"$Stylesheet\"/>\n";
    print "\t\t<title>$title</title>\n";
    print "\t</head>\n";
    print "\t<body>\n";
    print "\t\t<div class=\"content\">\n";
}

function printFooter() {
    print "\t\t</div>\n";
    print "\t</body>\n";
    print "</html>\n";
}

function initServer() {
}

function main() {
    include "config.php";

    printHeader("curload", "Simply upload files", $Icon, $Stylesheet);

    print "\t\t\t<h1>speedie's super awesome file uploader junk</h1>\n";
    print "\t\t\t<form action=\"upload.php\" method=\"post\" enctype=\"multipart/form-data\">Select file to upload<br><input type=\"file\" name=\"file\" id=\"file\"><br><input type=\"text\" name=\"key\" placeholder=\"Upload key here\"><br><input type=\"submit\" value=\"Upload selected file\" name=\"web\"></form>\n";
    print "\t\t\t<p>Max file size: $maxFileSize MB</p>\n";
    print "\t\t\t<a href=\"https://git.speedie.site/speedie/curload\">source code</a>\n";

    print "\t\t\t<h2>oops i leaked admin tools</h2>\n";
    print "\t\t\t<form action=\"create.php\" method=\"post\">\n";
    print "\t\t\t\t<input type=\"text\" name=\"data\" placeholder=\"key name\">\n";
    print "\t\t\t\t<input type=\"text\" name=\"type\" placeholder=\"type\">\n";
    print "\t\t\t\t<input type=\"text\" name=\"key\" placeholder=\"admin key\">\n";
    print "\t\t\t\t<input type=\"text\" name=\"uploads\" placeholder=\"max uploads\">\n";
    print "\t\t\t\t<input type=\"submit\" value=\"make\">\n";
    print "\t\t\t</form>\n";

    printFooter();
}

main();

?>
