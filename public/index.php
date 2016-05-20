<?php

use \quick\Quick;

require_once __DIR__ . '/../quick/Autoload.php';
$config = require __DIR__ . '/../config/config.php';

Quick::runWebApplication($config);