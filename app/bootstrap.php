<?php
// Load Config
require_once 'config/config.php';

// Load Helpers
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';


function ranjbar($calassName){
    require_once 'libraries/' . $calassName . '.php';
}

spl_autoload_register('ranjbar');