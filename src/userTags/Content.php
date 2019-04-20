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

 namespace userTags;

class Content
{
  public function __construct($config) {
    $this->plugin_config = &$config;
  }

  public function render_element_content($content, $picture) {
    global $template;

    $template->set_prefilter('picture', [__CLASS__, 'picture_prefilter']);

    if ($this->plugin_config->hasPermission('add')) {
      load_language('plugin.lang', T4U_PLUGIN_LANG);

      $template->assign('T4U_JS', T4U_JS);
      $template->assign('T4U_CSS', T4U_CSS);
      $template->assign('T4U_IMGS', T4U_IMGS);
      $template->assign('T4U_PERMISSION_DELETE', $this->plugin_config->hasPermission('delete'));
      $template->assign('T4U_UPDATE_SCRIPT', get_root_url() . 'ws.php?format=json&method=' . T4U_WS . 'update');
      $template->assign('T4U_UPDATE_METHOD', T4U_WS . 'update');
      $template->assign('T4U_LIST_SCRIPT', get_root_url() . 'ws.php?format=json&method=' . T4U_WS . 'list');
      $template->assign('T4U_IMAGE_ID', $picture['id']);
      $template->assign('T4U_REFERER', urlencode($picture['url']));
      $template->assign('T4U_PERMISSION_DELETE', $this->plugin_config->hasPermission('delete'));
      if ($this->plugin_config->hasPermission('existing_tags_only')) {
        $template->assign('T4U_ALLOW_CREATION', 'false');
      } else {
        $template->assign('T4U_ALLOW_CREATION', 'true');
      }

      $related_tags = [];
      $_tpl_vars = $template->get_template_vars('related_tags');
      if (!empty($_tpl_vars)) {
        foreach ($_tpl_vars as $id => $tag_infos) {
          $related_tags['~~' . $tag_infos['id'] . '~~'] = $tag_infos['name'];
        }
        $template->assign('T4U_RELATED_TAGS', $related_tags);
      }

      $template->set_filename('add_tags', T4U_TEMPLATE . '/add_tags.tpl');
      $template->assign_var_from_handle('PLUGIN_PICTURE_AFTER', 'add_tags');
    }

    return $content;
  }

  public static function picture_prefilter($source, $smarty) {
    $pattern = '{if $display_info.tags and isset($related_tags)}';
    $replace = '{if $display_info.tags}';

    return str_replace($pattern, $replace, $source);
  }
}
