#!/bin/sh
curl -F "file=@Testfile.txt" -F "key=${1:-myKey}" "http://localhost:1337/upload.php"
