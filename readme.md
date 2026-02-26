# OrgaPress Lite

OrgaPress Lite is the essential foundation for professional WordPress websites. It provides a curated selection of enterprise-grade features focusing on security, data privacy (GDPR/DSGVO), performance, and organized media management.

This plugin serves as the "Lite" version of the OrgaPress Enterprise Framework, streamlining the setup process for developers and agencies.

## 🚀 Key Features

### 🛠 Core & Editor
- **Automatic Dependency Management:** Automatically installs and activates the **Themify Builder** for a superior frontend design experience.
- **Classic Editor Enforcement:** Disables Gutenberg (Block Editor) and block-based widgets globally to ensure a consistent design environment and better performance.
- **Plugin Cleanup:** Automatically removes default "bloatware" like Hello Dolly and Akismet upon activation.

### 🛡 Security & Performance
- **XML-RPC Protection:** Disables XML-RPC to prevent brute-force and DDoS attacks.
- **REST API Hardening:** Protects user endpoints from unauthorized enumeration.
- **Information Hiding:** Removes the WordPress version generator tag from the header.
- **File Editor Disabled:** Enforces the `DISALLOW_FILE_EDIT` policy for production safety.

### ⚖️ Privacy (GDPR / DSGVO)
- **Privacy Pro System:** A sophisticated consent management system that allows categorizing services (Essential, Statistics, Marketing).
- **Conditional Script Loading:** Blocks third-party scripts (like Google Analytics or Pixels) based on user consent before they are executed.
- **Consent Audit Log:** Stores an anonymized, hash-based log of user consent changes for legal compliance.
- **Exportable Logs:** Download your consent logs as CSV directly from the dashboard.

### 📁 Media Management
- **Media Folders:** Organizes the WordPress Media Library with hierarchical folders (Taxonomies).
- **Drag & Drop:** Move files into folders directly within the Media Library UI.
- **Predefined Structure:** Comes with default folders for Marketing, Internal, and Press.

### ✉️ Reliable Mail (SMTP)
- **Built-in SMTP:** Configure your own mail server (Host, Port, Auth, Encryption) to ensure 100% email deliverability.
- **Testing Tool:** Integrated SMTP test mailer to verify settings instantly.

### 🔍 SEO & Roles
- **SEO Basics:** Simple, high-performance Meta Title and Description management for Posts and Pages.
- **Custom Roles:** Introduces the `OrgaPress Manager` role for client-side administration without full "Administrator" risks.

## 📦 Installation

1. Upload the `orgapress-lite` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to the **OrgaPress Lite** dashboard to complete the setup and install the Themify Builder.

## 📂 Project Structure

```text
├── includes/
│   ├── admin/             # Dashboard and Menu logic
│   ├── editor/            # Classic Editor integration
│   ├── media/             # Media folder taxonomy and JS
│   ├── privacy/           # Cookie banner, Privacy Pro, and Audit Log
│   ├── roles/             # Custom role management
│   ├── security/          # Hardening and policy enforcement
│   ├── seo/               # Meta tag management
│   ├── settings/          # SMTP configuration
│   └── class-core.php     # Main bootstrap and initialization
│   └── class-dependency-manager.php # Plugin installer
└── orgapress-lite.php     # Main Plugin File
```

## ⚖️ License

This project is licensed under the GPLv2 or later.

---
OrgaPress ([www.orgapress.com](https://orgapress.com)) is developed by **RheinMainTech GmbH** ([www.rheinmaintech.com](https://rheinmaintech.com)).