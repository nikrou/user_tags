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

if (!defined('PHPWG_ROOT_PATH')) {
  die('Hacking attempt!');
}

define('T4U_PLUGIN_ROOT', dirname(__FILE__));

include_once T4U_PLUGIN_ROOT . "/include/constants.inc.php";
include_once T4U_PLUGIN_ROOT . "/include/autoload.inc.php";

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
  include_once T4U_PLUGIN_ROOT . '/public.php';
}

set_plugin_data($plugin['id'], $plugin_config);
