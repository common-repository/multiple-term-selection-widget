<?php
/*
Plugin Name: Multiple Term Selection Widget
Plugin URI: http://wiboo.fr/wordpress
Description: Select multiple terms at once using this widget search !
Author: Xavier MOREAU (xDe6ug)
Author URI: http://www.lesitedexavier.fr
Licence: GPL2
Version: 1.0
*/

/*
	Copyright 2014 Xavier MOREAU (xDe6ug)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
*/

define('MTSW_PATH', WP_PLUGIN_DIR.'/multiple-term-selection-widget');
define('MTSW_PATH_CLASSES', MTSW_PATH.'/classes');
define('MTSW_PATH_VIEWS', MTSW_PATH.'/views');

define('MTSW_URL', WP_PLUGIN_URL.'/multiple-term-selection-widget');
define('MTSW_URL_STYLES', MTSW_URL.'/css');
define('MTSW_URL_SCRIPTS', MTSW_URL.'/js');
define('MTSW_URL_IMAGES', MTSW_URL.'/img');

require_once(MTSW_PATH_CLASSES.'/mtsw.php');
require_once(MTSW_PATH_CLASSES.'/mtsw_global_options.php');
require_once(MTSW_PATH_CLASSES.'/mtsw_widget.php');
require_once(MTSW_PATH_CLASSES.'/mtsw_options.php');
require_once(MTSW_PATH_CLASSES.'/mtsw_widget_options.php'); 

$GLOBALS['mtsw'] = MTSW::getInstance();

?>
