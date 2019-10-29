<?php 
namespace NeroOssidiana {

    class DbConfig {
        
        const username = "root";
        const password = "asdfghjkl";
        const serverName  = "localhost";
        const dbName = "NeroOssidiana";
        const dsn = "mysql:host=". self::serverName .";dbname=". self::dbName;
    }
}