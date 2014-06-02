CHANGELOG
=========

Release 1.2
-----------
Enhancements:
* [ORM] Added conditions to relations.

Bug fixes:


Release 1.1
-----------

Enhancements:
* Added callback methods to Model: beforeSave and afterSave.
* Added method Json::encode.
* Added DeleteDirectory util. 
* Added method Strings::trimToNull.
* Stats class collects HTTP request data.
* Added RequestContext class to keep information on controller.
* Added DynamicReturnTypePlugin config (PhpStorm support).
* [Mock] Stub multiple calls to a method.
* Added method Path::normalize.
* Added PDO attributes to config.
* Added interactive mode. 
* Controller can set up headers to send (Controller::header).
* Upgraded to PHPUnit 4.0.
* Added methods FluentArray::filterNotBlank and Arrays::filterNotBlank (issue #71).
* Added equivalent of PDO::ATTR_EMULATE_PREPARES to Ouzo (issue #76).
* Added method Functions::extract.
* Added method Date::formatTime.
* Added handle to nested controllers (Issue #80).
* Added new routes methods - Route::put and Route::delete.
* Extended ControllerTestCase with possibility to test response headers.
* New way to handle HTTP Auth Basic - using AuthBasicController.
* Added method Strings::appendPrefix.
* Added class ResponseMapper which translates HTTP code to HTTP header.
* Added OuzoException which is a wrapper for generic PHP excepion.
* Added class Error with exception code and message inside.
* Added API exceptions to handle appropriate HTTP error codes (InternalException, NotFoundException, UnauthorizedException, ValidationException).
* Added methods StringAssert::isNull and StringAssert::isNotNull.
* Added method RequestHeaders::accept for parsing Accept HTTP header.
* Class OuzoException allows to set additional headers.
* When authorization is invalid UnauthorizedException is thrown.
* [ModelGenerator] Added default table information.
* [ModelGenerator] Setting namespace in generated model class.
* [Core] Migrated Shell to Symfony Console.
* [Core] Removed Shell (now using Symfony Console).
* [Core] Support url with and without prefix in notices.
* [Utilities] Renamed Arrays::hasNestedValue to Arrays::hasNestedKey and Arrays::removeNestedValue to Arrays::removeNestedKey (issue #54).

Bug fixes:
* [Routes] Fixed invalid method name in generated uri helper for camelcase methods in controllers (issuse #69).
* [Mock] Fixed DynamicProxy so that it works with interfaces (issuse #70).
* [Mock] Fixed method matcher.
* [Routes] Fixed parsing default routing (issue #62).
* [Mock] Fixed mock verification.
* [Routes] Fixed regexp in matching routes (issue #75).
* [DB] Fixed error in PDO executor.
* [DB] Fixed sqlite error codes.
* [ORM] Fixed nested 'with' and one-to-many relations (issue #82).
* [Routes] Fixed nested resources in uri generator. 
* [Core] Support for redirect url.
* [Tests] Parsing url setting in method ControllerTestCase::get (issue #79).
* [Utilities] Fixed Arrays::getNestedValue when pass more keys than are in the array.
* [Tests] Fixed deleting sample config file in ConfigTest.
* [ORM] Throw exception if invalid sequence in model (issue #85).
* [Core] Fixed revert of null properties in config.
