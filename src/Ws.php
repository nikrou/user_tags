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

namespace UserTags;

use PwgError;
use PwgServer;

class Ws
{
    /**
     * @param array{0: PwgServer} $arr
     */
    public function addMethods(array $arr): void
    {
        load_language('plugin.lang', T4U_PLUGIN_LANG);
        /** @var PwgServer */
        $service = &$arr[0];

        $service->addMethod(T4U_WS . 'list', $this->tagsList(...),
            ['q' => []],
            'retrieves a list of tags than can be filtered'
        );

        $service->addMethod(T4U_WS . 'update', $this->updateTags(...),
            ['image_id' => [],
                'tags' => ['default' => []],
            ],
            'Updates (add or remove) tags associated to an image (POST method only)',
            null,
            ['post_only' => true]
        );
    }

    /**
     * @param array{q: string} $params
     *
     * @return array<int<0, max>, array{id: string, name: mixed}>
     */
    public function tagsList(array $params, PwgServer $service): array
    {
        $query = 'SELECT id AS tag_id, name AS tag_name FROM ' . TAGS_TABLE;
        if (!empty($params['q'])) {
            $query .= sprintf(' WHERE LOWER(name) like \'%%%s%%\'', strtolower((string) pwg_db_real_escape_string($params['q'])));
        }

        $tagslist = $this->makeTagsList($query);
        unset($tagslist['__associative_tags']);
        usort($tagslist, fn ($a, $b) => strcasecmp((string) $a['name'], (string) $b['name']));

        return $tagslist;
    }

    /**
     * @param array{tags: string, image_id: int} $params
     *
     * @return PwgError|array{error: string}|array{error: string[], info: string[]}
     */
    public function updateTags($params, PwgServer $service)
    {
        if (!$service->isPost()) {
            return new PwgError(405, 'This method requires HTTP POST');
        }

        if (!Config::getInstance()->hasPermission(PermissionEnum::ADD) && !Config::getInstance()->hasPermission(PermissionEnum::DELETE)) {
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

        $current_tags = $this->makeTagsList($query);
        $current_tags_ids = array_keys($current_tags['__associative_tags']);
        if (empty($params['tags'])) {
            $tags_to_associate = [];
        } else {
            $tags_to_associate = explode(',', (string) $params['tags']);
        }

        $removed_tags = array_diff($current_tags_ids, $tags_to_associate);
        $new_tags = array_diff($tags_to_associate, $current_tags_ids);

        if (count($removed_tags) > 0) {
            if (!Config::getInstance()->hasPermission(PermissionEnum::DELETE)) {
                $message['error'][] = l10n('You are not allowed to delete tags');
            } else {
                $message['info'][] = l10n('Tags deleted');
            }
        }

        if (count($new_tags) > 0) {
            if (!Config::getInstance()->hasPermission(PermissionEnum::ADD)) {
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

    /**
     * @return array{__associative_tags: array<string, mixed>, ...<int, array{id: string, name: mixed}>}
     */
    private function makeTagsList(string $query): array
    {
        $result = pwg_query($query);

        $tagslist = [];
        $associative_tags = [];
        while ($row = pwg_db_fetch_assoc($result)) {
            $associative_tags['~~' . $row['tag_id'] . '~~'] = $row['tag_name'];
            $tagslist[] = ['id' => '~~' . $row['tag_id'] . '~~',
                'name' => $row['tag_name'],
            ];
        }
        $tagslist['__associative_tags'] = $associative_tags;

        return $tagslist;
    }
}
