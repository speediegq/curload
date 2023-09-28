#!/bin/sh
curl -F "file=@Testfile.txt" -F "key=myKey" "http://localhost:1337/upload.php"
