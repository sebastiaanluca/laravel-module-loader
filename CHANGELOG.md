# Changelog

All Notable changes to `sebastiaanluca/laravel-module-loader` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased

### Added

- Added cache command
- Added clear cache command

### Changed

- Renamed `path` config entry to `directories`
- Only autoload test paths in development or test environments
- Refresh modules after creating a new one

### Fixed

- Ignore module paths that don't exist
- Providers not getting registered
- Register autoloading command not correctly using config paths
- Check for valid composer.json entries before merging config
- Don't write composer.json config when there was none in the first place
- Show error if module already exists when creating it
- Fixed wrong sprintf usage in ModuleException
- Fixed autoload config directories

## 0.2.10 (2018-08-06)

### Changed

- Moved event listener registration to the service provider `boot` method
- Renamed the `mapModelMorphAliases()` method
- Change order of called methods in module provider boot method
- Correctly register module views
- Correctly check if a module views' directory exists
- Use the correct method to check if directories exist

## 0.2.9 (2018-07-30)

- Fixed root directory tweak (last try, I hope 💩)

## 0.2.8 (2018-07-30)

- Fixed root directory tweak

## 0.2.7 (2018-07-30)

### Fixed

- Fixed root directory tweak

## 0.2.6 (2018-07-30)

### Fixed

- Check root project directory for modules

## 0.2.5 (2018-07-27)

### Added

- Added option to remove or keep existing module autoload entries

### Changed

- Don't use full paths in module `paths` config

### Fixed

- Fixed hardcoded module path reference

## 0.2.4 (2018-07-27)

### Changed

- Make service provider optional, just check for a `src/` directory

## 0.2.3 (2018-07-27)

### Fixed

- Fixed PSR 4 autoload directories (forgot to add `src/` 👀)

## 0.2.2 (2018-07-26)

### Fixed

- Add final new line to generated composer.json

## 0.2.1 (2018-07-26)

### Changed

- Only autoload or write autoload config for tests if the directory exists

## 0.2.0 (2018-07-26)

### Added

- Autoload factories
- Autoload seeders
- Write autoload config to composer.json
- Added command to refresh modules

### Changed

- Allow user to switch between default or runtime module autoloading 

## 0.1.0 (2018-07-25)

### Added

- Added module service provider
- Added module autoloading
- Added configurable scan paths
- Added automated router mapping in the module service provider
- Added configuration loading
- Added translations loader
- Added views loader
- Added factories loader
- Added migrations loader
- Added seeders loader
- Make configuration publishable
