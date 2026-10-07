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

use UserTags\Config;
use UserTags\Content;
use UserTags\Ws;

if (!defined('PHPWG_ROOT_PATH')) {
    exit('Hacking attempt!');
}

$public_content = new Content(Config::getInstance());
add_event_handler('render_element_content',
    [$public_content, 'render_element_content'],
    EVENT_HANDLER_PRIORITY_NEUTRAL
);

$t4u_ws = new Ws();
add_event_handler('ws_add_methods',
    [$t4u_ws, 'addMethods']
);
