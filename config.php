<?php
    $Stylesheet  = "index.css";
    $Icon        = "favicon.svg";
    $uploadDir   = "uploads/";
    $keyFile     = "passwords.txt";
    $tempKeyFile = "temporary_passwords.txt";
    $maxFileSize = "100";
    $sql         = true;
    $sqlDB       = "curload.db";
    $storeIP     = true;
    $storeAgent  = true;

    define('CONFIG_FILE', 'config.ini');

    if (!file_exists(CONFIG_FILE)) {
        return;
    }

    /* load config file */
    $configEntries = parse_ini_file(CONFIG_FILE);
    $Stylesheet = $configEntries['css'];
    $Icon = $configEntries['favicon'];
    $uploadDir = $configEntries['upload_dir'];
    $keyFile = $configEntries['key_file'];
    $tempKeyFile = $configEntries['temp_key_file'];
    $maxFileSize = $configEntries['max_size'];
    $sql = $configEntries['sql'];
    $sqlDB = $configEntries['sqldb'];
    $storeIP = $configEntries['store_ip'];
    $storeAgent = $configEntries['store_user_agent'];
?>
