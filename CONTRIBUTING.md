# Contributing to OrgaPress Lite

First off, thank you for considering contributing to OrgaPress Lite! It's people like you that make it such a great tool.

## Code of Conduct
This project and everyone participating in it is governed by our [Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs
- Use the **GitHub Issue Tracker**.
- Describe the bug in detail, including your environment (WP version, PHP version).
- Provide a clear "Steps to Reproduce".

### Pull Requests
1. Fork the repo and create your branch from `main`.
2. If you've added code that should be tested, add tests.
3. Ensure the test suite passes.
4. Make sure your code follows the **WordPress Coding Standards (WPCS)**.
5. Use **Conventional Commits** for your commit messages (e.g., `feat: add new security header`, `fix: resolving media drag issues`).

## Coding Standards
- **PHP:** We follow PSR-12 and WordPress Coding Standards.
- **JavaScript:** ES6+ standards, avoiding jQuery where native API is sufficient (except for Media Library extensions where jQuery is mandatory).
- **CSS:** BEM naming convention for UI components.

## Branching Strategy
- `main`: Production-ready stable code.
- `develop`: Ongoing development.
- `feature/*`: New features.
- `hotfix/*`: Urgent security or bug fixes.
