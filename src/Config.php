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

/**
 * @phpstan-type ConfigType array{permissions: array{tags_permission_add: string, tags_permission_delete: string, tags_existing_only: bool}}
 */
class Config
{
    public const CONFIG_KEY = 'user_tags';
    private const string PLUGIN_ROOT = __DIR__ . '/..';
    private const string PLUGIN_NAME = 'User Tags';

    /** @var ConfigType */
    private $defaultConfig = [
        'permissions' => [
            PermissionEnum::ADD->value => '',
            PermissionEnum::DELETE->value => '',
            PermissionEnum::EXISTING_TAGS_ONLY->value => false,
        ],
    ];

    /** @var ConfigType */
    private $config;

    protected static Config $instance;

    public function __construct()
    {
        $this->config = $this->defaultConfig;
    }

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    public function saveConfig(): void
    {
        conf_update_param(self::CONFIG_KEY, self::serialize($this->config));
    }

    public function getConfigFronDB(string $conf): void
    {
        $this->config = array_merge($this->defaultConfig, self::unserialize($conf));
    }

    public function setPermission(PermissionEnum $permission, string|bool $value): self
    {
        $this->config['permissions'][$permission->value] = $value;

        return $this;
    }

    public function getPermission(PermissionEnum $permission): string|bool
    {
        return $this->config['permissions'][$permission->value];
    }

    public function hasPermission(PermissionEnum $permission = PermissionEnum::ADD): bool
    {
        return $this->getPermission($permission) && is_autorize_status(get_access_type_status($this->getPermission($permission)));
    }

    /**
     * @param array<mixed> $menu
     *
     * @return array<mixed>
     */
    public static function pluginAdminMenu(array $menu): array
    {
        $menu[] = [
            'NAME' => self::PLUGIN_NAME,
            'URL' => get_root_url() . 'admin.php?page=plugin-user_tags',
        ];

        return $menu;
    }

    public static function getAdminHelp(string $help_content, string $page): string
    {
        if ($page !== T4U_HELP) {
            return $help_content;
        }

        return load_language('help/' . $page . '.html', self::PLUGIN_ROOT . '/', ['return' => true]);
    }

    /**
     * @param ConfigType $conf
     */
    public static function serialize(array $conf): string
    {
        return json_encode($conf, JSON_THROW_ON_ERROR);
    }

    /**
     * @return ConfigType
     */
    public static function unserialize(string $conf): array
    {
        return json_decode($conf, true, 512, JSON_THROW_ON_ERROR);
    }
}
