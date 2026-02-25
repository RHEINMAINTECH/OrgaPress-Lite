# Security Policy

## Supported Versions

We only provide security updates for the latest stable version of OrgaPress Lite.

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | :white_check_mark: |
| < 1.0   | :x:                |

## Reporting a Vulnerability

**Please do not open public GitHub issues for security-related items.**

If you discover a security vulnerability within OrgaPress Lite, please send an e-mail to [lite@orgapress.com](mailto:lite@orgapress.com). All security vulnerabilities will be promptly addressed.

Please include the following information in your report:
- Type of issue (e.g., XSS, SQLi, SSRF, etc.)
- Full paths of source file(s) related to the manifestation of the issue
- The location of the affected code (tag/branch/commit or direct URL)
- Step-by-step instructions to reproduce the issue
- Proof of Concept (PoC) scripts or screenshots if possible

### Our Response Process
1. **Acknowledgement:** We will acknowledge receipt of your report within 48 hours.
2. **Investigation:** Our team will investigate and verify the vulnerability.
3. **Fix:** We will prepare a fix and test it against current supported versions.
4. **Disclosure:** Once the fix is released, we will provide credit to the reporter (unless requested otherwise) and publish a security advisory if necessary.

## Enterprise Hardening
OrgaPress Lite is designed with security in mind. By default, it:
- Enforces `DISALLOW_FILE_EDIT`
- Disables XML-RPC
- Obfuscates WordPress version strings
- Protects REST API user enumeration
