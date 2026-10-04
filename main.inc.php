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

include_once __DIR__ . '/include/autoload.inc.php';

$plugin_config = userTags\Config::getInstance();
$plugin_config->load_config();

if (defined('IN_ADMIN')) {
    add_event_handler('get_admin_plugin_menu_links',
        'userTags\Config::plugin_admin_menu'
    );
    add_event_handler('get_popup_help_content',
        'userTags\Config::get_admin_help',
        EVENT_HANDLER_PRIORITY_NEUTRAL,
        2
    );
} else {
    include_once __DIR__ . '/public.php';
}

/** @var array{id: string} $plugin */
set_plugin_data($plugin['id'], $plugin_config);
