<?php
namespace OrgaPressLite;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dependency Manager
 * Übernimmt das automatische Herunterladen, Installieren und Aktivieren von Drittanbieter-Plugins.
 */
class DependencyManager
{
    const THEMIFY_SLUG = 'themify-builder';
    const THEMIFY_FILE = 'themify-builder/themify-builder.php';
    const THEMIFY_URL  = 'https://downloads.wordpress.org/plugin/themify-builder.latest-stable.zip';

    public static function install_required_plugins(): void
    {
        if (self::is_themify_active()) {
            return;
        }

        if (!self::is_themify_installed()) {
            self::install_themify();
        }

        self::activate_themify();
    }

    public static function remove_unwanted_plugins(): void
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';

        $unwanted = [
            'hello.php', // Hello Dolly
            'akismet/akismet.php' // Akismet
        ];

        foreach ($unwanted as $plugin) {
            if (is_plugin_active($plugin)) {
                deactivate_plugins($plugin);
            }
            
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin;
            if (file_exists($plugin_path)) {
                delete_plugins([$plugin]);
            }
        }
    }

    /**
     * Gibt den aktuellen Status des Themify Builders zurück.
     * 
     * @return string 'active', 'installed' oder 'missing'
     */
    public static function get_themify_status(): string
    {
        if (self::is_themify_active()) {
            return 'active';
        }
        if (self::is_themify_installed()) {
            return 'installed';
        }
        return 'missing';
    }

    private static function is_themify_installed(): bool
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugins = get_plugins();
        return isset($plugins[self::THEMIFY_FILE]);
    }

    private static function is_themify_active(): bool
    {
        if (!function_exists('is_plugin_active')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return is_plugin_active(self::THEMIFY_FILE);
    }

    private static function install_themify(): bool
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        require_once ABSPATH . 'wp-admin/includes/template.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';

        // WordPress Filesystem initialisieren
        WP_Filesystem();
        global $wp_filesystem;

        $tmp_file = download_url(self::THEMIFY_URL);

        if (is_wp_error($tmp_file)) {
            return false;
        }

        $plugins_dir = WP_PLUGIN_DIR . '/';
        $unzip_result = unzip_file($tmp_file, $plugins_dir);
        
        @unlink($tmp_file);

        return !is_wp_error($unzip_result);
    }

    private static function activate_themify(): void
    {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        
        if (function_exists('wp_clean_plugins_cache')) {
            wp_clean_plugins_cache();
        }

        if (self::is_themify_installed() && !self::is_themify_active()) {
            // Explizite Pfadangabe für die Aktivierung
            $plugin_path = WP_PLUGIN_DIR . '/' . self::THEMIFY_FILE;
            if (file_exists($plugin_path)) {
                activate_plugin(self::THEMIFY_FILE);
            }
        }
    }
}

