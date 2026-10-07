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
use UserTags\PermissionEnum;

if (!defined('PHPWG_ROOT_PATH')) {
    exit('Hacking attempt!');
}

load_language('plugin.lang', T4U_PLUGIN_LANG);

$config = Config::getInstance();
$save_config = false;

$status_options[null] = '----------';
foreach (get_enums(USER_INFOS_TABLE, 'status') as $status) {
    $status_options[$status] = l10n('user_status_' . $status);
}

if (!empty($_POST['submit'])) {
    if (isset($_POST[PermissionEnum::ADD->value], $status_options[$_POST[PermissionEnum::ADD->value]])
        && $_POST[PermissionEnum::ADD->value] !== $config->getPermission(PermissionEnum::ADD)) {
        $config->setPermission(PermissionEnum::ADD, $_POST[PermissionEnum::ADD->value]);
        $page['infos'][] = l10n('Add permission updated');
        $save_config = true;
    }

    if (!empty($_POST[PermissionEnum::EXISTING_TAGS_ONLY->value])
        && $_POST[PermissionEnum::EXISTING_TAGS_ONLY->value] !== $config->getPermission(PermissionEnum::EXISTING_TAGS_ONLY)) {
        $config->setPermission(PermissionEnum::EXISTING_TAGS_ONLY, true);
        $save_config = true;
    } elseif (!isset($_POST[PermissionEnum::EXISTING_TAGS_ONLY->value]) && $config->getPermission(PermissionEnum::EXISTING_TAGS_ONLY) !== false) {
        $config->setPermission(PermissionEnum::EXISTING_TAGS_ONLY, false);
        $save_config = true;
    }

    if (isset($_POST[PermissionEnum::DELETE->value], $status_options[$_POST[PermissionEnum::DELETE->value]])
        && $_POST[PermissionEnum::DELETE->value] !== $config->getPermission(PermissionEnum::DELETE)) {
        $config->setPermission(PermissionEnum::DELETE, $_POST[PermissionEnum::DELETE->value]);
        $page['infos'][] = l10n('Delete permission updated');
        $save_config = true;
    }

    if ($save_config) {
        $config->saveConfig();
    }
}

/** @var Template $template */
$template->set_filenames(['plugin_admin_content' => T4U_TEMPLATE . '/admin.tpl']);
$template->assign('T4U_CSS', T4U_CSS);

$template->assign('PERMISSION_ENUM', PermissionEnum::cases());
$template->assign('T4U_PERMISSION_ADD', $config->getPermission(PermissionEnum::ADD));
$template->assign('T4U_PERMISSION_DELETE', $config->getPermission(PermissionEnum::DELETE));
$template->assign('T4U_EXISTING_TAG_ONLY', $config->getPermission(PermissionEnum::EXISTING_TAGS_ONLY));
$template->assign('STATUS_OPTIONS', $status_options);
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

$template->assign('U_HELP', get_root_url() . 'admin/popuphelp.php?page=' . T4U_HELP);
