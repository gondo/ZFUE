<?php

define('APPLICATION_ENV_DEVELOP', 'develop');
define('APPLICATION_ENV_LIVE', 'live');

define('APPLICATION_ENV', APPLICATION_ENV_DEVELOP);

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/..'));
define('APPLICATION_ROOT', realpath(dirname(__FILE__) . '/../..'));

set_include_path(get_include_path() . PATH_SEPARATOR . APPLICATION_ROOT . '/library' . PATH_SEPARATOR . APPLICATION_ROOT . PATH_SEPARATOR . APPLICATION_PATH);