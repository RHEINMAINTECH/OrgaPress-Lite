<?php
namespace OrgaPressLite\Privacy;

if (!defined('ABSPATH')) {
exit;
}

/**

* Privacy Audit Logger
* DSGVO-konformes Consent-Audit (hash-basiert)
  */
  class PrivacyAudit
  {
  const TABLE = 'orgapress_privacy_audit';

  public static function init(): void
  {
  add_action('init', [self::class, 'maybe_create_table']);
  add_action('wp_ajax_orgapress_log_consent', [self::class, 'log_consent']);
  add_action('wp_ajax_nopriv_orgapress_log_consent', [self::class, 'log_consent']);
  }

  public static function maybe_create_table(): void
  {
  global $wpdb;
  $table = $wpdb->prefix . self::TABLE;

   if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") === $table) {
       return;
   }

   require_once ABSPATH . 'wp-admin/includes/upgrade.php';

   $charset = $wpdb->get_charset_collate();
   $sql = "
       CREATE TABLE {$table} (
           id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
           created_at DATETIME NOT NULL,
           consent LONGTEXT NOT NULL,
           ip_hash CHAR(64) NOT NULL,
           user_agent_hash CHAR(64) NOT NULL,
           PRIMARY KEY (id),
           KEY created_at (created_at)
       ) {$charset};
   ";

   dbDelta($sql);

  }

  public static function log_consent(): void
  {
  check_ajax_referer('orgapress_privacy_nonce', 'nonce');

  if (empty($_POST['consent'])) {
  wp_send_json_error();
  }

   global $wpdb;
   $table = $wpdb->prefix . self::TABLE;

   $ip = $_SERVER['REMOTE_ADDR'] ?? '';
   $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

   $wpdb->insert(
       $table,
       [
           'created_at'       => current_time('mysql'),
           'consent'          => wp_json_encode(wp_unslash($_POST['consent'])),
           'ip_hash'          => hash('sha256', $ip),
           'user_agent_hash'  => hash('sha256', $ua),
       ],
       ['%s', '%s', '%s', '%s']
   );

   wp_send_json_success();

  }
  }
