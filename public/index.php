<?php

ini_set('display_errors', true);
error_reporting(-1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

Bbs\AppMain::run();
