# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.3.0

### Added

- Return types.
- Many `InvalidArgumentException`s are thrown when you use invalid arguments. 
- Integration tests for `UploadedFile` and `ServerRequest`.

### Changed

- We dropped PHP7.0 support. 
- PSR-17 factories have been marked as internal. They do not fall under our BC promise until PSR-17 is accepted.  
- `UploadedFileFactory::createUploadedFile` does not accept a string file path. 

## 0.2.3

No changelog before this release

