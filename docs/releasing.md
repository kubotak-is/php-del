# Release Guide

PHP-DEL is published to Packagist from Git tags. `composer.json` does not
contain a hard-coded package version.

## Versioning

Use Semantic Versioning:

- Patch: compatible bug fixes
- Minor: backward-compatible features
- Major: breaking behavior or runtime requirement changes

Raising the minimum PHP version is a breaking change and requires a major
release. The PHP 8.2 minimum introduced after `1.0.0` should therefore be
released as `2.0.0`.

## Pre-Release Checklist

1. Confirm the branch contains only intended release changes.
2. Update README and migration documentation.
3. Validate Composer metadata:

   ```sh
   docker compose run --rm php82 composer validate --strict
   ```

4. Run all supported PHP versions:

   ```sh
   task analyse
   task test-all
   ```

5. Confirm there are no dependency security advisories:

   ```sh
   docker compose run --rm php82 composer audit
   ```

6. Review the final diff and merge the release changes to `main`.

## Create the Release

From an up-to-date `main` branch:

```sh
git switch main
git pull --ff-only
git tag -a 2.0.0 -m "Release 2.0.0"
git push origin 2.0.0
```

Annotated tags are preferred because they preserve release metadata.

## Packagist

Packagist derives versions from repository tags. After pushing:

1. Open the package page:
   <https://packagist.org/packages/kubotak-is/php-del>
2. Confirm the new version appears.
3. Confirm the PHP requirement matches `composer.json`.
4. Install the tagged version in a clean test project.

If the package is not refreshed automatically, use Packagist's manual update
action and verify the GitHub/Packagist integration.

## Post-Release Verification

```sh
composer show kubotak-is/php-del --all
```

Verify:

- The expected version is listed.
- The source reference matches the tag.
- PHP and extension requirements are correct.
- Installation succeeds on the minimum supported PHP version.

Review PHP's
[officially supported versions](https://www.php.net/supported-versions.php)
at least annually and after each new PHP release. Update `composer.json`,
Docker Compose, Taskfile, and the GitHub Actions matrix together. Raising the
minimum PHP version requires a major release.
