# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- `get_sc_post_types` filter to manipulate the post types where the meta box will be shown.

## [1.2.2] - 2025-01-28
### Fixed
- Sticky posts will no longer be included in the feed by default.

## [1.2.1] - 2022-03-01
### Added
- Composer support.
- Added publiccode.yaml.
### Fixed
- Generated release file should now no longer include a dist/ folder.
## [1.2.0] - 2022-06-14
- Public release on GitHub.
### Added
- Added EUPL - EUROPEAN UNION PUBLIC LICENCE v. 1.2 license to source code.
- CHANGELOG.md.
- GitHub Actions workflow: release. This action runs for every newly pushed git tag, creates a new GitHub release with an installable WordPress plugin file.
- Added plugin-update-checker to enable updates from a WordPress website.
