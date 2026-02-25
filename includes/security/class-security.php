<?php
namespace OrgaPressLite\Security;

if (!defined('ABSPATH')) {
exit;
}

class Security
{
const OPTION_GROUP = 'orgapress_security';
const OPT_DISABLE_XMLRPC = 'orgapress_sec_disable_xmlrpc';
const OPT_HIDE_WP_VERSION = 'orgapress_sec_hide_wp_version';
const OPT_REST_PROTECT_USERS = 'orgapress_sec_rest_protect_users';

public static function init(): void
{
    add_action('admin_menu', [self::class, 'menu']);
    add_action('admin_init', [self::class, 'register_settings']);

    // Runtime-Hooks abhängig von Einstellungen
    if (get_option(self::OPT_DISABLE_XMLRPC, 1)) {
        add_filter('xmlrpc_enabled', '__return_false');
    }

    if (get_option(self::OPT_HIDE_WP_VERSION, 1)) {
        remove_action('wp_head', 'wp_generator');
    }

    if (get_option(self::OPT_REST_PROTECT_USERS, 1)) {
        add_filter('rest_endpoints', [self::class, 'protect_rest_users']);
    }

    // Enterprise Policy: Disable File Editor
    if (!defined('DISALLOW_FILE_EDIT')) {
        define('DISALLOW_FILE_EDIT', true);
    }
}

public static function menu(): void
{
    add_submenu_page(
        'orgapress-lite',
        __('Security', 'orgapress-lite'),
        __('Security', 'orgapress-lite'),
        'manage_options',
        'orgapress-security',
        [self::class, 'render_page']
    );
}

public static function register_settings(): void
{
    register_setting(self::OPTION_GROUP, self::OPT_DISABLE_XMLRPC, ['sanitize_callback' => 'absint']);
    register_setting(self::OPTION_GROUP, self::OPT_HIDE_WP_VERSION, ['sanitize_callback' => 'absint']);
    register_setting(self::OPTION_GROUP, self::OPT_REST_PROTECT_USERS, ['sanitize_callback' => 'absint']);
}

public static function render_page(): void
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Security Einstellungen', 'orgapress-lite'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields(self::OPTION_GROUP); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('XML-RPC deaktivieren', 'orgapress-lite'); ?></th>
                    <td>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPT_DISABLE_XMLRPC); ?>" value="1" <?php checked(get_option(self::OPT_DISABLE_XMLRPC, 1), 1); ?>>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('WordPress Version verbergen', 'orgapress-lite'); ?></th>
                    <td>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPT_HIDE_WP_VERSION); ?>" value="1" <?php checked(get_option(self::OPT_HIDE_WP_VERSION, 1), 1); ?>>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('REST API Benutzer-Endpunkt schützen', 'orgapress-lite'); ?></th>
                    <td>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPT_REST_PROTECT_USERS); ?>" value="1" <?php checked(get_option(self::OPT_REST_PROTECT_USERS, 1), 1); ?>>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Entfernt den öffentlichen Zugriff auf /wp/v2/users
 */
public static function protect_rest_users(array $endpoints): array
{
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\\d]+)']);
    }
    return $endpoints;
}

}
