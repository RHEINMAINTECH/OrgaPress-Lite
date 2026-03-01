<?php
namespace OrgaPressLite\Privacy;

if (!defined('ABSPATH')) {
exit;
}

class PrivacyPro
{
const OPTION_GROUP = 'orgapress_privacy_pro';
const OPTION_CATEGORIES = 'orgapress_consent_categories';
const OPTION_SERVICES = 'orgapress_consent_services';
const COOKIE_NAME = 'orgapress_consent_state';

public static function init(): void
{
    add_action('admin_menu', [self::class, 'menu']);
    add_action('admin_init', [self::class, 'register_settings']);
    add_action('wp_enqueue_scripts', [self::class, 'enqueue_frontend']);
    add_filter('script_loader_tag', [self::class, 'filter_script_tags'], 10, 3);
}

public static function menu(): void
{
    add_submenu_page(
        'orgapress-lite',
        __('Privacy Pro', 'orgapress-lite'),
        __('Privacy Pro', 'orgapress-lite'),
        'manage_options',
        'orgapress-privacy-pro',
        [self::class, 'render_page']
    );
}

public static function register_settings(): void
{
    register_setting(self::OPTION_GROUP, self::OPTION_CATEGORIES);
    register_setting(self::OPTION_GROUP, self::OPTION_SERVICES);

    if (!get_option(self::OPTION_CATEGORIES)) {
        update_option(self::OPTION_CATEGORIES, [
            'essential' => [
                'label' => __('Essential', 'orgapress-lite'),
                'required' => true,
            ],
            'analytics' => [
                'label' => __('Analytics', 'orgapress-lite'),
                'required' => false,
            ],
            'marketing' => [
                'label' => __('Marketing', 'orgapress-lite'),
                'required' => false,
            ],
        ]);
    }

    if (!get_option(self::OPTION_SERVICES)) {
        update_option(self::OPTION_SERVICES, []);
    }
}

public static function get_categories(): array
{
    $categories = (array) get_option(self::OPTION_CATEGORIES, []);
    /**
     * Filter: orgapress_privacy_categories
     * Ermöglicht Entwicklern, Kategorien zu erweitern oder zu verändern.
     */
    return apply_filters('orgapress_privacy_categories', $categories);
}

public static function get_services(): array
{
    $services = (array) get_option(self::OPTION_SERVICES, []);
    /**
     * Filter: orgapress_privacy_services
     * Ermöglicht Entwicklern, Services per Code zu registrieren.
     */
    return apply_filters('orgapress_privacy_services', $services);
}

public static function render_page(): void
{
    $categories = self::get_categories();
    $services   = self::get_services();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Privacy Pro – Services', 'orgapress-lite'); ?></h1>
        <p><?php esc_html_e('Enterprise-DSGVO: Kategorien, Services, Provider & Script-Handles.', 'orgapress-lite'); ?></p>

        <form method="post" action="options.php">
            <?php settings_fields(self::OPTION_GROUP); ?>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Service Key', 'orgapress-lite'); ?></th>
                        <th><?php esc_html_e('Label', 'orgapress-lite'); ?></th>
                        <th><?php esc_html_e('Kategorie', 'orgapress-lite'); ?></th>
                        <th><?php esc_html_e('Provider / Info URL', 'orgapress-lite'); ?></th>
                        <th><?php esc_html_e('Script Handles', 'orgapress-lite'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $key => $srv) : ?>
                        <tr>
                            <td><code><?php echo esc_html($key); ?></code></td>
                            <td>
                                <input type="text"
                                       name="<?php echo esc_attr(self::OPTION_SERVICES); ?>[<?php echo esc_attr($key); ?>][label]"
                                       value="<?php echo esc_attr($srv['label'] ?? ''); ?>">
                            </td>
                            <td>
                                <select name="<?php echo esc_attr(self::OPTION_SERVICES); ?>[<?php echo esc_attr($key); ?>][category]">
                                    <?php foreach ($categories as $catKey => $cat) : ?>
                                        <option value="<?php echo esc_attr($catKey); ?>" <?php selected($srv['category'] ?? '', $catKey); ?>>
                                            <?php echo esc_html($cat['label']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="url"
                                       name="<?php echo esc_attr(self::OPTION_SERVICES); ?>[<?php echo esc_attr($key); ?>][provider]"
                                       value="<?php echo esc_attr($srv['provider'] ?? ''); ?>"
                                       class="regular-text">
                            </td>
                            <td>
                                <input type="text"
                                       name="<?php echo esc_attr(self::OPTION_SERVICES); ?>[<?php echo esc_attr($key); ?>][handles]"
                                       value="<?php echo esc_attr(implode(',', (array) ($srv['handles'] ?? []))); ?>"
                                       class="regular-text">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p>
                <em><?php esc_html_e('Entwickler können Services & Kategorien per Filter erweitern.', 'orgapress-lite'); ?></em>
            </p>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

public static function enqueue_frontend(): void
{
    wp_enqueue_script(
        'orgapress-privacy-pro',
        ORGAPRESS_LITE_URL . 'includes/privacy/privacy-pro.js',
        [],
        ORGAPRESS_LITE_VERSION,
        true
    );

    wp_enqueue_script(
        'orgapress-privacy-pro-ui',
        ORGAPRESS_LITE_URL . 'includes/privacy/privacy-pro-ui.js',
        ['orgapress-privacy-pro'],
        ORGAPRESS_LITE_VERSION,
        true
    );

    wp_enqueue_style(
        'orgapress-privacy-pro-ui',
        ORGAPRESS_LITE_URL . 'includes/privacy/privacy-pro-ui.css',
        [],
        ORGAPRESS_LITE_VERSION
    );

    wp_localize_script(
        'orgapress-privacy-pro',
        'OrgaPressPrivacy',
        [
            'ajax_url'   => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('orgapress_privacy_nonce'),
            'cookie'     => self::COOKIE_NAME,
            'categories' => self::get_categories(),
            'services'   => self::get_services(),
            'labels'     => [
                'banner_text' => __('We use cookies to improve your experience. By clicking "Accept All", you consent to the use of ALL cookies. However, you may visit "Settings" to provide a controlled consent.', 'orgapress-lite'),
                'settings' => __('Settings', 'orgapress-lite'),
                'accept_all' => __('Accept All', 'orgapress-lite'),
                'privacy_settings' => __('Privacy Settings', 'orgapress-lite'),
                'modal_desc' => __('Choose which cookies you want to allow. Essential cookies are technically necessary for the operation of the website.', 'orgapress-lite'),
                'save_selection' => __('Save Selection', 'orgapress-lite'),
            ]
        ]
    );
}

public static function filter_script_tags(string $tag, string $handle, string $src): string
{
    $services = self::get_services();
    foreach ($services as $srv) {
        if (in_array($handle, (array) ($srv['handles'] ?? []), true)) {
            if (!self::has_consent($srv['category'])) {
                return sprintf(
                    '<script type="text/plain" data-category="%s" data-src="%s"></script>',
                    esc_attr($srv['category']),
                    esc_url($src)
                );
            }
        }
    }
    return $tag;
}

private static function has_consent(string $category): bool
{
    $categories = self::get_categories();
    if (!empty($categories[$category]['required'])) {
        return true;
    }
    if (empty($_COOKIE[self::COOKIE_NAME])) {
        return false;
    }
    $state = json_decode(wp_unslash($_COOKIE[self::COOKIE_NAME]), true);
    return !empty($state[$category]);
}

}
