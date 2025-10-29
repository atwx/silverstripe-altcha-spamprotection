# SilverStripe Altcha Spam Protection

This is a SilverStripe module that integrates the Altcha library with userforms to provide simple server-side CAPTCHA-like challenges for form spam protection.

## Overview

The package provides an `AltchaField` that can be added to SilverStripe forms. It creates challenges via the altcha-org/altcha library and validates solutions on the server side.

## Requirements

- PHP 8.1+
- SilverStripe 6

The package depends on `altcha-org/altcha` (see `composer.json`).

## Installation

Install the package using Composer from the root of your SilverStripe project:

```bash
composer require atwx/silverstripe-altcha-spamprotection
```

If necessary, run your project's dev/build step (for example `vendor/bin/sake dev/build`) or your usual deployment steps to ensure any configuration is picked up.

## Configuration

Minimal `altcha.yml`:

```yaml
SilverStripe\SpamProtection\Extension\FormSpamProtectionExtension:
  default_spam_protector: Atwx\SilverstripeAltchaSpamprotection\Protectors\AltchaSpamProtector
Atwx\SilverstripeAltchaSpamprotection\Forms\AltchaField:
  hmac_key: 'your-very-secret-hmac-key'
```

`AltchaField` exposes the following configuration options via SilverStripe Config (`_config/*.yml` or programmatically):

- `AltchaField.hmac_key` (string) — HMAC key for Altcha. This is required; the constructor throws if it is not set.
- `AltchaField.algorithm` (string) — Hash algorithm to use (`SHA-1`, `SHA-256`, `SHA-512`). Default: `SHA-256`.
- `AltchaField.default_max_number` (int) — Default maximum number for the challenge RNG (default: `1000000`).
- `AltchaField.default_salt_length` (int) — Default salt length for generated challenges (default: `12`).
- `AltchaField.expires_interval` (string) — ISO 8601 duration for challenge expiry (e.g. `PT20S` for 20 seconds). Default: `PT20S`.
- `AltchaField.challenge_endpoint` (string) — Endpoint to fetch challenge options from. Default: `/_altchaspamprotection/challengeoptions`.
- `AltchaField.debug` (bool|null) — Debug flag; if `null` the field uses `Director::isDev()`.

Note: Set `hmac_key` to a secure secret. This key is necessary for Altcha to generate and verify server signatures.
