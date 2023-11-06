<?php
require_once "vendor/autoload.php";

date_default_timezone_set('Europe/Paris');
session_start();

\touiteur\app\db\ConnectionFactory::setconfig("./conf.ini");
\touiteur\app\dispatch\Dispatcher::run();