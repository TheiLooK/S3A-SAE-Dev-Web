<?php
require_once "vendor/autoload.php";
\touiteur\app\db\ConnectionFactory::setconfig("./conf.ini");
session_start();
\touiteur\app\dispatch\Dispatcher::run();