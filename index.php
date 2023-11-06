<?php
require_once "vendor/autoload.php";
\touiteur\app\db\ConnectionFactory::setconfig("./conf.ini");
\touiteur\app\db\ConnectionFactory::makeConnection();