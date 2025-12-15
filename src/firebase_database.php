<?php

declare(strict_types=1);

use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Database;

function database(): Database
{
    static $db = null;
    if ($db === null) {
        $key = $_ENV['FIREBASE_CREDENTIALS'] ?? '';
        $kpath = dirname(__DIR__) . '/' . $key;
        if ($key === '' || !is_file($kpath)) {
            throw new RuntimeException('Невірний .env або немає service-account.json');
        }
        
        $factory = (new Factory)
            ->withServiceAccount($kpath)
            ->withDatabaseUri('https://web-lesson-12042-default-rtdb.firebaseio.com/');
        
        $db = $factory->createDatabase();
    }
    return $db;
}
