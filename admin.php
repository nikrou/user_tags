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

load_language('plugin.lang', T4U_PLUGIN_LANG);

$me = userTags\Config::getInstance();
$save_config = false;

$status_options[null] = '----------';
foreach (get_enums(USER_INFOS_TABLE, 'status') as $status) {
  $status_options[$status] = l10n('user_status_' . $status);
}

if (!empty($_POST['submit'])) {
  if (isset($_POST['permission_add'], $status_options[$_POST['permission_add']])
      && $_POST['permission_add'] != $me->getPermission('add')) {
    $me->setPermission('add', $_POST['permission_add']);
    $page['infos'][] = l10n('Add permission updated');
    $save_config = true;
  }

  if (!empty($_POST['existing_tags_only'])
      && $_POST['existing_tags_only'] != $me->getPermission('existing_tags_only')) {
    $me->setPermission('existing_tags_only', 1);
    $save_config = true;
  } elseif (!isset($_POST['existing_tags_only']) && $me->getPermission('existing_tags_only') != 0) {
    $me->setPermission('existing_tags_only', 0);
    $save_config = true;
  }

  if (isset($_POST['permission_delete'], $status_options[$_POST['permission_delete']])
      && $_POST['permission_delete'] != $me->getPermission('delete')) {
    $me->setPermission('delete', $_POST['permission_delete']);
    $page['infos'] = l10n('Delete permission updated');
    $save_config = true;
  }

  if ($save_config) {
    $me->save_config();
  }
}

$template->set_filenames(['plugin_admin_content' => T4U_TEMPLATE . '/admin.tpl']);
$template->assign('T4U_CSS', T4U_CSS);

$template->assign('T4U_PERMISSION_ADD', $me->getPermission('add'));
$template->assign('T4U_PERMISSION_DELETE', $me->getPermission('delete'));
$template->assign('T4U_EXISTING_TAG_ONLY', $me->getPermission('existing_tags_only'));
$template->assign('STATUS_OPTIONS', $status_options);
$template->assign_var_from_handle('ADMIN_CONTENT', 'plugin_admin_content');

$template->assign('U_HELP', get_root_url() . 'admin/popuphelp.php?page=readme');
