<?php
#phpinfo();exit;

#readfile('index.html');
#exit;

if (function_exists('opcache_reset')) {
  opcache_reset();
}

define('DOCUMENT_ROOT_DIR', 'www');

require('../app/bootstrap.php');
