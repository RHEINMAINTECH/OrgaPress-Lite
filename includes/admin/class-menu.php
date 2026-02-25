<?php
namespace OrgaPressLite\Admin;

if (!defined('ABSPATH')) {
    exit;
}

class Menu
{
    public static function init(): void
    {
        add_action('admin_menu', [self::class, 'register_main_menu'], 9);
        add_action('admin_init', [self::class, 'handle_dashboard_actions']);
    }

    public static function handle_dashboard_actions(): void
    {
        if (!isset($_GET['action']) || !isset($_GET['_wpnonce'])) {
            return;
        }

        if ($_GET['action'] === 'orgapress_install_themify' && wp_verify_nonce($_GET['_wpnonce'], 'orgapress_themify_action')) {
            \OrgaPressLite\DependencyManager::install_required_plugins();
            wp_redirect(admin_url('admin.php?page=orgapress-lite&themify_updated=1'));
            exit;
        }
    }

    public static function register_main_menu(): void
    {
        $icon = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" font-weight="900" fill="white">O</text></svg>');

        add_menu_page(
            __('OrgaPress Lite', 'orgapress-lite'),
            'OrgaPress Lite',
            'manage_options',
            'orgapress-lite',
            [self::class, 'render_dashboard'],
            $icon,
            30
        );

        add_submenu_page(
            'orgapress-lite',
            __('Dashboard', 'orgapress-lite'),
            __('Dashboard', 'orgapress-lite'),
            'manage_options',
            'orgapress-lite',
            [self::class, 'render_dashboard']
        );
    }

    public static function render_dashboard(): void
    {
        $themify_status = \OrgaPressLite\DependencyManager::get_themify_status();
        ?>
        <style>
            .orgapress-dashboard { margin-top: 20px; max-width: 1200px; }
            .orgapress-header { background: #1e293b; color: #fff; padding: 40px; border-radius: 12px; margin-bottom: 30px; }
            .orgapress-header h2 { margin: 0 0 10px; color: #fff; font-size: 28px; }
            .orgapress-header p { margin: 0; opacity: 0.8; font-size: 16px; }
            .orgapress-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
            .orgapress-card { display: flex; flex-direction: column; background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); transition: transform 0.2s; }
            .orgapress-card:hover { transform: translateY(-4px); }
            .orgapress-card h3 { margin-top: 0; display: flex; align-items: center; gap: 10px; font-size: 18px; color: #0f172a; }
            .orgapress-card p { color: #64748b; line-height: 1.5; margin-bottom: 20px; flex-grow: 1; }
            .orgapress-card .button { border-radius: 6px; font-weight: 600; padding: 5px 15px; text-align: center; }
            .status-tag { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; text-transform: uppercase; font-weight: 700; margin-left: auto; }
            .status-active { background: #dcfce7; color: #166534; }
            .status-missing { background: #fee2e2; color: #991b1b; }

            /* Guide Modal Styles */
            .op-modal { display: none; position: fixed; z-index: 999999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(4px); overflow-y: auto; }
            .op-modal-content { background: #fff; margin: 5% auto; padding: 40px; border-radius: 16px; max-width: 800px; position: relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
            .op-modal-close { position: absolute; right: 25px; top: 20px; font-size: 30px; cursor: pointer; color: #94a3b8; line-height: 1; }
            .op-modal-close:hover { color: #0f172a; }
            .op-guide-section { margin-bottom: 30px; }
            .op-guide-section h4 { font-size: 20px; color: #1e293b; margin-bottom: 12px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
            .op-guide-section p { font-size: 15px; color: #475569; line-height: 1.7; }
            .op-guide-section ul { list-style: disc; margin-left: 20px; color: #475569; }
            .op-guide-section ul li { margin-bottom: 8px; }
        </style>
        <div class="wrap orgapress-dashboard">
            <div class="orgapress-header">
                <h2><?php esc_html_e('Willkommen bei OrgaPress Lite', 'orgapress-lite'); ?></h2>
                <p><?php esc_html_e('Die zentrale Schaltzentrale für Ihre Website-Sicherheit, Datenschutz und Performance.', 'orgapress-lite'); ?></p>
            </div>

            <div class="orgapress-grid">
                <div class="orgapress-card">
                    <h3><span class="dashicons dashicons-email-alt"></span> <?php esc_html_e('SMTP Versand', 'orgapress-lite'); ?></h3>
                    <p><?php esc_html_e('Konfigurieren Sie einen zuverlässigen E-Mail-Versand über SMTP, um sicherzustellen, dass Ihre Nachrichten ankommen.', 'orgapress-lite'); ?></p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=orgapress-smtp')); ?>" class="button button-primary"><?php esc_html_e('Konfigurieren', 'orgapress-lite'); ?></a>
                </div>

                <div class="orgapress-card">
                    <h3><span class="dashicons dashicons-lock"></span> <?php esc_html_e('Cookie Banner', 'orgapress-lite'); ?></h3>
                    <p><?php esc_html_e('Passen Sie das Erscheinungsbild und die Texte Ihres DSGVO-konformen Cookie-Banners an.', 'orgapress-lite'); ?></p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=orgapress-cookie')); ?>" class="button button-primary"><?php esc_html_e('Anpassen', 'orgapress-lite'); ?></a>
                </div>

                <div class="orgapress-card">
                    <h3><span class="dashicons dashicons-shield"></span> <?php esc_html_e('Sicherheit', 'orgapress-lite'); ?></h3>
                    <p><?php esc_html_e('Schützen Sie Ihre WordPress-Instanz vor Angriffen, deaktivieren Sie XML-RPC und verbergen Sie Versionen.', 'orgapress-lite'); ?></p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=orgapress-security')); ?>" class="button button-primary"><?php esc_html_e('Prüfen', 'orgapress-lite'); ?></a>
                </div>

                <div class="orgapress-card">
                    <h3>
                        <span class="dashicons dashicons-layout"></span> <?php esc_html_e('Themify Builder', 'orgapress-lite'); ?>
                        <?php if ($themify_status === 'active'): ?>
                            <span class="status-tag status-active">Aktiv</span>
                        <?php else: ?>
                            <span class="status-tag status-missing">Inaktiv</span>
                        <?php endif; ?>
                    </h3>
                    <p><?php esc_html_e('Der Themify Builder dient als intuitiver Frontend-Editor für Ihre Seiten und ermöglicht komplexes Design ohne Code.', 'orgapress-lite'); ?></p>
                    <?php if ($themify_status === 'active'): ?>
                        <button class="button disabled" disabled><?php esc_html_e('Bereits Aktiv', 'orgapress-lite'); ?></button>
                    <?php else: ?>
                        <?php 
                        $label = ($themify_status === 'installed') ? __('Aktivieren', 'orgapress-lite') : __('Installieren', 'orgapress-lite');
                        $url = wp_nonce_url(admin_url('admin.php?page=orgapress-lite&action=orgapress_install_themify'), 'orgapress_themify_action');
                        ?>
                        <a href="<?php echo esc_url($url); ?>" class="button button-primary"><?php echo esc_html($label); ?></a>
                    <?php endif; ?>
                </div>

                <div class="orgapress-card">
                    <h3><span class="dashicons dashicons-admin-settings"></span> <?php esc_html_e('Themify Einstellungen', 'orgapress-lite'); ?></h3>
                    <p><?php esc_html_e('Konfigurieren Sie die globalen Einstellungen, Werkzeuge und Performance-Optionen des Themify Builders.', 'orgapress-lite'); ?></p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=themify-builder')); ?>" class="button button-secondary"><?php esc_html_e('Builder-Settings öffnen', 'orgapress-lite'); ?></a>
                </div>

                <div class="orgapress-card">
                    <h3><span class="dashicons dashicons-sos"></span> <?php esc_html_e('Schnellstartanleitung', 'orgapress-lite'); ?></h3>
                    <p><?php esc_html_e('Erfahren Sie in wenigen Minuten, wie Sie das Beste aus OrgaPress Lite und dem integrierten Themify Builder herausholen.', 'orgapress-lite'); ?></p>
                    <button id="op-open-guide" class="button button-secondary"><?php esc_html_e('Anleitung öffnen', 'orgapress-lite'); ?></button>
                </div>
            </div>

            <div id="op-guide-modal" class="op-modal">
                <div class="op-modal-content">
                    <span class="op-modal-close" id="op-close-guide">&times;</span>
                    <h2><?php esc_html_e('Schnellstartanleitung OrgaPress Lite', 'orgapress-lite'); ?></h2>
                    
                    <div class="op-guide-section">
                        <h4><?php esc_html_e('Was macht OrgaPress Lite?', 'orgapress-lite'); ?></h4>
                        <p><?php esc_html_e('OrgaPress Lite ist das Fundament Ihrer Website. Es bündelt essenzielle Funktionen in einem schlanken Plugin:', 'orgapress-lite'); ?></p>
                        <ul>
                            <li><strong>SMTP:</strong> Sicherer E-Mail-Versand, damit Ihre Formular-Mails nicht im Spam landen.</li>
                            <li><strong>DSGVO:</strong> Ein intelligenter Cookie-Banner mit Audit-Log für rechtliche Sicherheit.</li>
                            <li><strong>Security:</strong> Schutz vor Brute-Force durch Absicherung der REST-API und Deaktivierung von XML-RPC.</li>
                            <li><strong>SEO:</strong> Meta-Tags und Titel-Optimierung direkt in jedem Beitrag.</li>
                        </ul>
                    </div>

                    <div class="op-guide-section">
                        <h4><?php esc_html_e('Warum Themify Integration?', 'orgapress-lite'); ?></h4>
                        <p><?php esc_html_e('Wir haben den Themify Builder integriert, weil er das perfekte Gleichgewicht zwischen Design-Freiheit und technischer Performance bietet. Er ersetzt den Standard-Gutenberg-Editor durch ein leistungsfähigeres System, das Layout-Konsistenz über die gesamte OrgaPress-Infrastruktur hinweg garantiert.', 'orgapress-lite'); ?></p>
                    </div>

                    <div class="op-guide-section">
                        <h4><?php esc_html_e('So nutzen Sie den Builder', 'orgapress-lite'); ?></h4>
                        <p><strong><?php esc_html_e('Backend-Modus:', 'orgapress-lite'); ?></strong> <?php esc_html_e('Unter dem klassischen Editor finden Sie den Builder-Bereich. Hier können Sie Module per Drag & Drop verschieben und die Struktur Ihrer Seite im Überblick gestalten.', 'orgapress-lite'); ?></p>
                        <p><strong><?php esc_html_e('Frontend-Modus:', 'orgapress-lite'); ?></strong> <?php esc_html_e('Besuchen Sie die Seite auf Ihrer Website und klicken Sie in der schwarzen Admin-Bar oben auf "Turn On Builder". Nun können Sie Texte und Bilder direkt visuell bearbeiten.', 'orgapress-lite'); ?></p>
                    </div>

                    <div style="text-align: right; margin-top: 20px;">
                        <button class="button button-primary" onclick="document.getElementById('op-guide-modal').style.display='none'"><?php esc_html_e('Verstanden', 'orgapress-lite'); ?></button>
                    </div>
                </div>
            </div>

            <script>
                (function() {
                    const modal = document.getElementById('op-guide-modal');
                    const btn = document.getElementById('op-open-guide');
                    const span = document.getElementById('op-close-guide');

                    btn.onclick = function() { modal.style.display = "block"; }
                    span.onclick = function() { modal.style.display = "none"; }
                    window.onclick = function(event) {
                        if (event.target == modal) { modal.style.display = "none"; }
                    }
                })();
            </script>
        </div>
        <?php
    }
}
