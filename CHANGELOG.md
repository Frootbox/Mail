# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.2] - 2023-11-28

### Fixed

- Improved network stability by changing from ```file_get_contents``` to ```curl``` when inlining images.

### Changed

- Updated dependencies.

## [0.1.1] - 2023-11-07

### Fixed

- Improve html5 compatibility when inlining images.

## [0.1] - 2023-07-18

### Changed

- Added optional parameters when sending envelopes.

### Added

- Added changelog.
- Added mail transport for localhost.
- Added option for inlining images when sending envelopes.
