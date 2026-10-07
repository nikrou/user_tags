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

if (!defined('PHPWG_ROOT_PATH')) {
    exit('Hacking attempt!');
}

class user_tags_maintain extends PluginMaintain
{
    #[Override]
    public function uninstall(): void
    {
        conf_delete_param(Config::CONFIG_KEY);
    }
}
