<?php

define('G_URL','localhost/delain/'); // soit finir par un /
define('G_CHE',__DIR__ . '/../'); // NE PAS TOUCHER
define('G_IMAGES','http://localhost/images/');
define('IMG_PATH',G_IMAGES);

// connexion base de données
define('SERVER_PROD',false); // if true, we'll use a service
define('SERVER_HOST','localhost');
define('SERVER_USERNAME','delain');
define('SERVER_PASSWORD','mypassword');
define('SERVER_DBNAME','delain');

// URL API
define('URL_API','http://localhost/api/');

// SMTP
define('SMTP_HOST','smtp.free.fr');    // The smtp server host/ip
define('SMTP_PORT',25);
define('STMP_USER','');
define('STMP_PASSWORD','');