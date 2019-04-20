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

spl_autoload_register(function ($class) {
    if (strrpos($class, 'userTags') === false) {
        return;
    }

    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    include __DIR__ . '/../src/' . $class . '.php';
});
