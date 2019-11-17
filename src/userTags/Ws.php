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

include_once(PHPWG_ROOT_PATH . 'admin/include/functions.php');

class Ws
{
  public function addMethods($arr) {
    load_language('plugin.lang', T4U_PLUGIN_LANG);
    $service = &$arr[0];

    $service->addMethod(T4U_WS . 'list', [$this, 'tagsList'],
                        ['q' => []],
                        'retrieves a list of tags than can be filtered'
                        );

    $service->addMethod(T4U_WS . 'update', [$this, 'updateTags'],
                        ['image_id' => [],
                            'tags' => ['default' => []]
                        ],
                        'Updates (add or remove) tags associated to an image (POST method only)'
                        );
  }

  public function tagsList($params, &$service) {
    $query = 'SELECT id AS tag_id, name AS tag_name FROM ' . TAGS_TABLE;
    if (!empty($params['q'])) {
      $query .= sprintf(' WHERE LOWER(name) like \'%%%s%%\'', strtolower(pwg_db_real_escape_string($params['q'])));
    }

    $tagslist = $this->__makeTagsList($query);
    unset($tagslist['__associative_tags']);
    usort($tagslist, function($a, $b) {
      return strcasecmp($a['name'], $b['name']);
    });

    return $tagslist;
  }

  public function updateTags($params, &$service) {
    if (!$service->isPost()) {
      return new PwgError(405, "This method requires HTTP POST");
    }

    if (!Config::getInstance()->hasPermission('add') && !Config::getInstance()->hasPermission('delete')) {
      return ['error' => l10n('You are not allowed to add nor delete tags')];
    }

    if (empty($params['tags'])) {
      $params['tags'] = [];
    }
    $message = [];

    $query = 'SELECT tag_id, name AS tag_name';
    $query .= ' FROM ' . IMAGE_TAG_TABLE . ' AS it';
    $query .= ' JOIN ' . TAGS_TABLE . ' AS t ON t.id = it.tag_id';
    $query .= sprintf(' WHERE image_id = %s', pwg_db_real_escape_string($params['image_id']));

    $current_tags = $this->__makeTagsList($query);
    $current_tags_ids = array_keys($current_tags['__associative_tags']);
    if (empty($params['tags'])) {
      $tags_to_associate = [];
    } else {
      $tags_to_associate = explode(',', $params['tags']);
    }

    $removed_tags = array_diff($current_tags_ids, $tags_to_associate);
    $new_tags = array_diff($tags_to_associate, $current_tags_ids);

    if (count($removed_tags) > 0) {
      if (!Config::getInstance()->hasPermission('delete')) {
        $message['error'][] = l10n('You are not allowed to delete tags');
      } else {
        $message['info'] = l10n('Tags updated');
      }
    }
    if (count($new_tags) > 0) {
      if (!Config::getInstance()->hasPermission('add')) {
        $message['error'][] = l10n('You are not allowed to add tags');
        $tags_to_associate = array_diff($tags_to_associate, $new_tags);
      } else {
        $message['info'][] = l10n('Tags updated');
      }
    }

    if (empty($message['error'])) {
      if (empty($tags_to_associate)) { // remove all tags for an image
        $query = 'DELETE FROM ' . IMAGE_TAG_TABLE;
        $query .= sprintf(' WHERE image_id = %d', pwg_db_real_escape_string($params['image_id']));
        pwg_query($query);
      } else {
        $tag_ids = get_tag_ids(implode(',', $tags_to_associate));
        set_tags($tag_ids, $params['image_id']);
      }
    }

    return $message;
  }

  private function __makeTagsList($query) {
    $result = pwg_query($query);

    $tagslist = [];
    $associative_tags = [];
    while ($row = pwg_db_fetch_assoc($result)) {
      $associative_tags['~~' . $row['tag_id'] . '~~'] = $row['tag_name'];
      $tagslist[] = ['id' => '~~' . $row['tag_id'] . '~~',
          'name' => $row['tag_name']
      ];

    }
    $tagslist['__associative_tags'] = $associative_tags;

    return $tagslist;
  }
}
