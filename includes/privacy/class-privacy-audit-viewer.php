<?php
namespace OrgaPressLite\Privacy;

if (!defined('ABSPATH')) {
exit;
}

class PrivacyAuditViewer
{
public static function init(): void
{
add_action('admin_menu', [self::class, 'menu']);
add_action('admin_init', [self::class, 'maybe_export_csv']);
}

public static function menu(): void
{
    add_submenu_page(
        'orgapress-lite',
        __('Consent Audit Log', 'orgapress-lite'),
        __('Consent Audit Log', 'orgapress-lite'),
        'manage_options',
        'orgapress-privacy-audit',
        [self::class, 'render']
    );
}

public static function maybe_export_csv(): void
{
    if (
        empty($_GET['orgapress_export_audit']) ||
        !current_user_can('manage_options') ||
        empty($_GET['_wpnonce']) ||
        !wp_verify_nonce($_GET['_wpnonce'], 'orgapress_export_audit')
    ) {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . PrivacyAudit::TABLE;
    $rows = $wpdb->get_results("SELECT * FROM {$table} ORDER BY created_at DESC", ARRAY_A);

    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=orgapress-consent-audit.csv');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['created_at', 'consent', 'ip_hash', 'user_agent_hash']);

    foreach ($rows as $row) {
        fputcsv($out, [
            $row['created_at'],
            $row['consent'],
            $row['ip_hash'],
            $row['user_agent_hash'],
        ]);
    }

    fclose($out);
    exit;
}

public static function render(): void
{
    global $wpdb;

    $table = $wpdb->prefix . PrivacyAudit::TABLE;
    $per_page = 20;
    $page = max(1, (int) ($_GET['paged'] ?? 1));
    $offset = ($page - 1) * $per_page;

    $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        )
    );

    $total_pages = (int) ceil($total / $per_page);
    $export_url = wp_nonce_url(
        add_query_arg('orgapress_export_audit', '1'),
        'orgapress_export_audit'
    );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Consent Audit Log', 'orgapress-lite'); ?></h1>
        <p><?php esc_html_e('GDPR-compliant proof of all consent changes (hash-based).', 'orgapress-lite'); ?></p>

        <p>
            <a href="<?php echo esc_url($export_url); ?>" class="button button-secondary">
                <?php esc_html_e('Export CSV', 'orgapress-lite'); ?>
            </a>
        </p>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Timestamp', 'orgapress-lite'); ?></th>
                    <th><?php esc_html_e('Consent (JSON)', 'orgapress-lite'); ?></th>
                    <th><?php esc_html_e('IP Hash', 'orgapress-lite'); ?></th>
                    <th><?php esc_html_e('User Agent Hash', 'orgapress-lite'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($rows) : ?>
                    <?php foreach ($rows as $row) : ?>
                        <tr>
                            <td><?php echo esc_html($row->created_at); ?></td>
                            <td><code><?php echo esc_html($row->consent); ?></code></td>
                            <td><code><?php echo esc_html($row->ip_hash); ?></code></td>
                            <td><code><?php echo esc_html($row->user_agent_hash); ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4"><?php esc_html_e('No entries found.', 'orgapress-lite'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1) : ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php
                    echo paginate_links([
                        'base'      => add_query_arg('paged', '%#%'),
                        'format'    => '',
                        'prev_text' => __('«', 'orgapress-lite'),
                        'next_text' => __('»', 'orgapress-lite'),
                        'total'     => $total_pages,
                        'current'   => $page,
                    ]);
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

}
