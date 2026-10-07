<?php
/*
 * This file is part of user_tags package
 *
 * Copyright(c) Nicolas Roudaire  https://www.phyxo.net/
 * Licensed under the GPL version 2.0 license.
 *
 * For the full copyright and license information, please view the COPYING
 * file that was distributed with this source code.
 */

/*
Plugin Name: User Tags
Version: 1.0.6
Description: Allow visitors to add tag to images
Plugin URI: http://piwigo.org/ext/extension_view.php?eid=441
Author: nikrou
Author URI: https://www.phyxo.net/
Has Settings: true
*/

use UserTags\Config;

if (!defined('PHPWG_ROOT_PATH')) {
    exit('Hacking attempt!');
}

define('T4U_PLUGIN_LANG', __DIR__ . '/');
define('T4U_HELP', 'user_tags_help');
define('T4U_TEMPLATE', __DIR__ . '/template');
define('T4U_CSS', PHPWG_PLUGINS_PATH . basename(__DIR__) . '/css');
define('T4U_IMGS', PHPWG_PLUGINS_PATH . basename(__DIR__) . '/imgs');
define('T4U_JS', PHPWG_PLUGINS_PATH . basename(__DIR__) . '/js');
define('T4U_WS', 'user_tags.tags.');

include_once __DIR__ . '/vendor/autoload.php';
include_once PHPWG_ROOT_PATH . 'admin/include/functions.php';

add_event_handler('init', function () {
    global $conf;

    Config::getInstance()->getConfigFronDB($conf['user_tags'] ?? '{}');
});

if (defined('IN_ADMIN')) {
    add_event_handler('get_admin_plugin_menu_links', Config::pluginAdminMenu(...));
    add_event_handler('get_popup_help_content', Config::getAdminHelp(...), EVENT_HANDLER_PRIORITY_NEUTRAL);
} else {
    include_once __DIR__ . '/public.php';
}
