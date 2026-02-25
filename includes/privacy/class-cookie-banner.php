<?php
namespace OrgaPressLite\Privacy;

if (!defined('ABSPATH')) {
exit;
}

class CookieBanner
{
const OPTION_GROUP = 'orgapress_cookie';
const OPTION_ENABLED = 'orgapress_cookie_enabled';
const OPTION_TEXT = 'orgapress_cookie_text';
const OPTION_BUTTON = 'orgapress_cookie_button';
const OPTION_POSITION = 'orgapress_cookie_position';

public static function init(): void
{
    add_action('admin_menu', [self::class, 'menu']);
    add_action('admin_init', [self::class, 'register_settings']);
    add_action('wp_footer', [self::class, 'render']);
}

public static function menu(): void
{
    add_submenu_page(
        'orgapress-lite',
        __('Cookie Banner', 'orgapress-lite'),
        __('Cookie Banner', 'orgapress-lite'),
        'manage_options',
        'orgapress-cookie',
        [self::class, 'render_page']
    );
}

public static function register_settings(): void
{
    register_setting(self::OPTION_GROUP, self::OPTION_ENABLED, ['sanitize_callback' => 'absint']);
    register_setting(self::OPTION_GROUP, self::OPTION_TEXT, ['sanitize_callback' => 'sanitize_text_field']);
    register_setting(self::OPTION_GROUP, self::OPTION_BUTTON, ['sanitize_callback' => 'sanitize_text_field']);
    register_setting(self::OPTION_GROUP, self::OPTION_POSITION, ['sanitize_callback' => 'sanitize_text_field']);
}

public static function render_page(): void
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Cookie Banner Einstellungen', 'orgapress-lite'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields(self::OPTION_GROUP); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Aktiviert', 'orgapress-lite'); ?></th>
                    <td>
                        <input type="checkbox" name="<?php echo esc_attr(self::OPTION_ENABLED); ?>" value="1" <?php checked(get_option(self::OPTION_ENABLED, 1), 1); ?>>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Banner Text', 'orgapress-lite'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="<?php echo esc_attr(self::OPTION_TEXT); ?>" value="<?php echo esc_attr(get_option(self::OPTION_TEXT, __('Diese Website verwendet Cookies gemäß DSGVO.', 'orgapress-lite'))); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Button Text', 'orgapress-lite'); ?></th>
                    <td>
                        <input type="text" class="regular-text" name="<?php echo esc_attr(self::OPTION_BUTTON); ?>" value="<?php echo esc_attr(get_option(self::OPTION_BUTTON, __('Akzeptieren', 'orgapress-lite'))); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e('Position', 'orgapress-lite'); ?></th>
                    <td>
                        <?php $pos = get_option(self::OPTION_POSITION, 'bottom'); ?>
                        <select name="<?php echo esc_attr(self::OPTION_POSITION); ?>">
                            <option value="bottom" <?php selected($pos, 'bottom'); ?>><?php esc_html_e('Unten', 'orgapress-lite'); ?></option>
                            <option value="top" <?php selected($pos, 'top'); ?>><?php esc_html_e('Oben', 'orgapress-lite'); ?></option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

public static function render(): void
{
    if (!get_option(self::OPTION_ENABLED, 1)) {
        return;
    }
    if (isset($_COOKIE['orgapress_cookie_consent'])) {
        return;
    }

    $text = esc_html(get_option(self::OPTION_TEXT, __('Diese Website verwendet Cookies gemäß DSGVO.', 'orgapress-lite')));
    $button = esc_html(get_option(self::OPTION_BUTTON, __('Akzeptieren', 'orgapress-lite')));
    $position = get_option(self::OPTION_POSITION, 'bottom') === 'top' ? 'top:0;' : 'bottom:0;';
    ?>
    <div id="orgapress-cookie-banner" style="position:fixed;<?php echo esc_attr($position); ?>width:100%;background:#222;color:#fff;padding:15px;text-align:center;z-index:9999;">
        <span><?php echo $text; ?></span>
        <button style="margin-left:10px;" onclick="document.cookie='orgapress_cookie_consent=1;path=/;max-age='+(60*60*24*365);this.parentNode.remove();">
            <?php echo $button; ?>
        </button>
    </div>
    <?php
}

}
