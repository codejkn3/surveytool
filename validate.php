<?php

    $DSN_CONFIG_FILE = 'config/Database.ini';
    $databaseConfig = parse_ini_file($DSN_CONFIG_FILE);

        if (! file_exists($DSN_CONFIG_FILE)) {
            throw new RuntimeException('Database config file not found: ' . $DSN_CONFIG_FILE);
        }
        $databaseConfig = parse_ini_file($DSN_CONFIG_FILE);
        if (! isset($databaseConfig['dsn'])) {
            throw new RuntimeException("Database config parameter 'dsn' not found in config file: " . $DSN_CONFIG_FILE);
        }

        $username = null;
        $password = null;

        if (preg_match('/^(mysql|pgsql)/', $databaseConfig['dsn'], $matches)) {
            $driver = $matches[1];
            if (! isset($databaseConfig['username'])) {
                throw new RuntimeException("Database config parameter 'username' not found in config file: " . $DSN_CONFIG_FILE);
            }
            if (! isset($databaseConfig['password'])) {
                throw new RuntimeException("Database config parameter 'password' not found in config file: " . $DSN_CONFIG_FILE);
            }
            $username = $databaseConfig['username'];
            $password = $databaseConfig['password'];
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ];
        } else {
            if (! isset($databaseConfig['filename'])) {
                throw new RuntimeException("Database config parameter 'filename' not found in config file: " . $DSN_CONFIG_FILE);
            }
            if (! is_writable(dirname($databaseConfig['filename']))) {
                throw new RuntimeException('Data directory not writable by web server: ' . dirname($databaseConfig['filename']) . '/');
            }
            if (! is_writable(dirname($databaseConfig['filename'])) || (file_exists($databaseConfig['filename']) && ! is_writable($databaseConfig['filename']))) {
                throw new RuntimeException('Database file not writable by web server: ' . $databaseConfig['filename']);
            }
        }
        try {
            $pdo = new PDO($databaseConfig['dsn'], $username, $password,$options);
            if ($driver == 'sqlite') {
                $pdo->exec('PRAGMA foreign_keys = ON;');
            }

        } catch (PDOException $e) {
            throw new PDOException('PDOException: ' . $e->getMessage());
        }

        if (isset($_GET['input_survey_name'])) {
            $new_survey_name = strtolower($_GET['input_survey_name']);
            $params = ['survey_name' => $new_survey_name];
        }
        else {
            print "err: no survey name given";
        }

        $sql = "select survey_name from survey where lower(survey_name) = :survey_name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchColumn();
        if ($results) { //Found
            print "true";
        }
        else {
            print "false"; 
        }
        

?>
