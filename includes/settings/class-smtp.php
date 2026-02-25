<?php
namespace OrgaPressLite\Settings;

use PHPMailer\PHPMailer\PHPMailer;

if (!defined('ABSPATH')) {
exit;
}

class SMTP
{
public static function init(): void
{
add_action('phpmailer_init', [self::class, 'configure']);
add_action('admin_menu', [self::class, 'menu']);
add_action('admin_init', [self::class, 'register_settings']);
add_action('admin_init', [self::class, 'handle_test_mail']);
}

public static function handle_test_mail(): void
{
    if (!isset($_POST['orgapress_smtp_test_nonce']) || !wp_verify_nonce($_POST['orgapress_smtp_test_nonce'], 'orgapress_smtp_test')) {
        return;
    }

    if (!current_user_can('manage_options')) {
        return;
    }

    $to = sanitize_email($_POST['test_email_recipient']);
    $subject = 'OrgaPress SMTP Test';
    $message = 'Diese E-Mail bestätigt, dass Ihre OrgaPress SMTP-Einstellungen korrekt konfiguriert sind.';
    
    $sent = wp_mail($to, $subject, $message);

    add_settings_error(
        'orgapress_smtp',
        'smtp_test',
        $sent ? __('Test-E-Mail erfolgreich versendet!', 'orgapress-lite') : __('Versand fehlgeschlagen. Prüfen Sie die Log-Files.', 'orgapress-lite'),
        $sent ? 'updated' : 'error'
    );
}

public static function menu(): void
{
    add_submenu_page(
        'orgapress-lite',
        __('SMTP Einstellungen', 'orgapress-lite'),
        __('SMTP Einstellungen', 'orgapress-lite'),
        'manage_options',
        'orgapress-smtp',
        [self::class, 'render_page']
    );
}

public static function register_settings(): void
{
    register_setting('orgapress_smtp', 'orgapress_smtp_host', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('orgapress_smtp', 'orgapress_smtp_port', ['sanitize_callback' => 'absint']);
    register_setting('orgapress_smtp', 'orgapress_smtp_user', ['sanitize_callback' => 'sanitize_text_field']);
    register_setting('orgapress_smtp', 'orgapress_smtp_pass');
    register_setting('orgapress_smtp', 'orgapress_smtp_secure', ['sanitize_callback' => 'sanitize_text_field']);
}

public static function render_page(): void
{
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('SMTP Einstellungen', 'orgapress-lite'); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('orgapress_smtp'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Host</th>
                    <td><input type="text" name="orgapress_smtp_host" value="<?php echo esc_attr(get_option('orgapress_smtp_host')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Port</th>
                    <td><input type="number" name="orgapress_smtp_port" value="<?php echo esc_attr(get_option('orgapress_smtp_port', 587)); ?>"></td>
                </tr>
                <tr>
                    <th scope="row">Benutzer</th>
                    <td><input type="text" name="orgapress_smtp_user" value="<?php echo esc_attr(get_option('orgapress_smtp_user')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Passwort</th>
                    <td><input type="password" name="orgapress_smtp_pass" value="<?php echo esc_attr(get_option('orgapress_smtp_pass')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Verschlüsselung</th>
                    <td>
                        <select name="orgapress_smtp_secure">
                            <?php $val = get_option('orgapress_smtp_secure', 'tls'); ?>
                            <option value="tls" <?php selected($val, 'tls'); ?>>TLS</option>
                            <option value="ssl" <?php selected($val, 'ssl'); ?>>SSL</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>

        <hr>
        <h2><?php esc_html_e('SMTP Test-Versand', 'orgapress-lite'); ?></h2>
        <form method="post" action="">
            <?php wp_nonce_field('orgapress_smtp_test', 'orgapress_smtp_test_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e('Empfänger E-Mail', 'orgapress-lite'); ?></th>
                    <td><input type="email" name="test_email_recipient" value="<?php echo esc_attr(get_option('admin_email')); ?>" class="regular-text" required></td>
                </tr>
            </table>
            <?php submit_button(__('Test-E-Mail senden', 'orgapress-lite'), 'secondary', 'send_test_mail'); ?>
        </form>
    </div>
    <?php
}

public static function configure(PHPMailer $phpmailer): void
{
    $host = get_option('orgapress_smtp_host');
    if (empty($host)) {
        return;
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = sanitize_text_field($host);
    $phpmailer->Port       = (int) get_option('orgapress_smtp_port', 587);
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Username   = sanitize_text_field(get_option('orgapress_smtp_user'));
    $phpmailer->Password   = (string) get_option('orgapress_smtp_pass');
    $phpmailer->SMTPSecure = sanitize_text_field(get_option('orgapress_smtp_secure', 'tls'));
}

}