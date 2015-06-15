CHANGELOG
=========

Release 1.5.0
--------
Enhancements:
* [Utilities] Extended Comparator::compareBy to support multiple expressions (issue #169).
* [ORM] Added possibility of using multiple Restrictions in Any::of for the same key.
* [Utilities] Added Suppliers::memoizeWithExpiration that returns supplier which caches the result of function.
* [Core] Minimal log level can be assigned to a particular class/name in logger configuration.
* [Core] Added Validatable::validateEmpty method.
* [ORM] Extended Restrictions::between with modes: inclusive, exclusive etc. (issue #176).
* [ORM] When using DbTransactionTestCase transactions are disabled (issue #178).
* [Core] Added support for CSRF token in forms.
* [Tools] Added method that lists all generated URI helper methods (GeneratedUriHelper::allGeneratedUriNames). 
* [Utilities] Implemented Optional class (issue #72).
* [ORM] Added support for [SELECT ... FOR UPDATE - with ModelQueryBuilder's lockForUpdate method](http://ouzo.readthedocs.org/en/latest/documentation/orm.html#locking).
* [ORM] Added support for DELETE USING.
* [MVC] Session is being closed when downloading or streaming file. 
* [MVC] Added support for UTF-8 characters in routes.

Bug fixes:
* [Localization] Fixed I18n::loadLabels not to load translation file if it was already loaded (issue #173).

Release 1.4.1
-------------
Enhancements:
* [ORM] New restrictions: isNull and isNotNull.
* [ORM] Added EmptyWhereClause class.
* [Utilities] Objects::getValue supports arrays now. It means that all functions depending on it (e.g. Functions::extract) supports arrays as well.
* [ORM] Added switch for model generator to utilize shorthand array syntax (issue #160).
* [ORM] Added switch for model generator to display output instead of saving file (issue #158).
* [ORM] Added support for [sorted hasMany relations](http://ouzo.readthedocs.org/en/latest/documentation/orm.html#sorted-hasmany-relation) (issue #171).
* [Tests] Added ArrayAssert::isEqualTo method.

Bug fixes:
* [Utilities] Added [Arrays::mapEntries](http://ouzo.readthedocs.org/en/latest/utils/arrays.html#mapentries) (issue #156).
* [ORM] Fixed null parameters in where clause (issue #161).
* [ORM] Fixed model generator namespace and folder name (issue #149).
* [Utilities] Added [Arrays::uniqueBy](http://ouzo.readthedocs.org/en/latest/utils/arrays.html#uniqueby) (issue #159).
* [Tests] Changed ArrayAssert::hasSize, so that it shows original array when assertion fails (issue #163).
* [ORM] Fixed insert primaryKey when sequence is empty (issue #174).
* [Utilities] Fixed Arrays::removeNestedKeys, so that it can handle null values.

Release 1.4.0
-------------
Enhancements:
* Extracted Ouzo Goodies, which can be used from now on as a separate project.
* Versioned documentation available at ouzo.readthedocs.org.
* [ORM] Added Any::of to produce OR operator (issue #141).
* [Utilities] Added Strings::substringBefore.
* [Tests] Added CatchException::get.

Bug fixes:
* [ORM] Fixed transaction rollback on exception (issue #115).
* [Tests] Better messages when assertThat::onMethod fails (issue #128).

Release 1.3
-----------
Enhancements:
* [Core] Added radio button to form builder.
* [Tests] Added ArrayAssert::hasEqualKeysRecursively method.
* [Utilities] Added Arrays::flattenKeysRecursively method.
* [Utilities] Added Files::getFilesRecursivelyWithSpecifiedExtension method.
* [Core] Added after init callback.
* [Localization] Added I18n::labels method to get all labels or specified label.
* [Tests] Changed CatchException asserts to fluent methods.
* [Utilities] Objects::getValue can access private fields.
* [Tests] ArrayAssert::onProperty can access private fields (issue #113).
* [Core] Added support for pluralization in translations (issue #111).
* [ORM] Added Model::selectDistinct method (issue #91).
* [ORM] Added support for model default values (issue #66).
* [Core] Displayed routes in table (issue #93).
* [ORM] Added TransactionalProxy.
* [Core] Migrate to PHPUnit 4.3.3 and adding assert adapter (issue #119).
* [Utilities] Added Strings::sprintAssoc method.
* [Extensions] Added HTTP Auth Basic extension.
* [Utilities] Added Strings::contains method.
* [Utilities] Added Functions::constant, Functions::notEquals, Functions::equals and Functions::throwException methods.
* [Mock] Added thenAnswer method.
* [Utilities] Added FluentFunctions class.
* [Core] Added RequestHeaders::all method.
* [Core] Added possibility to configure multiple loggers configurations.
* [ORM] Optimisation - do not select columns of models that will not be stored in fields.
* [Utilities] Added Validate class (issue #117).
* [Tests] Implement streamMediaFile in MockDownloadHandler.
* [Utilities] Added Files::copyContent method.
* [Core] Added possibility to group routes (#80).
* [ORM] Extended criteria API in query builder - Restriction (issue #68).
* [Utilities] Added Arrays::count method.
* [Mock] Added argument matcher.
* [Utilities] Added a default value to the StrSubstitutor.
* [Utilities] Added Functions::isInstanceOf.
* [Utilities] Added Date::formatTimestamp.
* [Utilities] Added Comparators.
* [Utilities] Enhanced the Clock class.
* [Core] Paths to model, controller and widget are configurable from config (issue #147).
* [Utilities] Added RequestHeaders::ip.
* [Core] Controller::renderAjaxView use current action as default (issue #104).

Bug fixes:
* [ORM] Added meaningful exception when Model::findById is invoked, but no primary key is defined (issue #121).
* [Utilities] Fixed generating model fields in correct order (issue #102).
* [Utilities] Fixed generating empty primary key when not in table (issue #98).
* [Utilities] Fixed Functions::notBlank (issue #106).
* [Core] Fixed throwing orginal message when throw UserException (issue #109).
* [Utilities] Fixed Arrays::flattenKeysRecursively (issue #110).
* [Core] Fixed parsing of Json inputs (#114).
* [ORM] Fixed zero as primary key.
* [Utilities] Fixed extractor for 'empty' values like 0 or empty array.
* [Core] Fixed HTTP request parameters priority.
* [Core] Fixed parse PUT HTTP request.
* [Core] Fixed uri ContentType letter case insensitive.
* [ORM] Fixed not fetching relation joined through hasMany (which resulted in an error).
* [Core] Fixed generating form name in ModelFormBuilder.
* [Mock] Fixed DynamicProxy uses uniqid (issue #127).
* [Core] Fixed invalid formatting of GeneratedUriHelper (issue #131).
* [Utilities] Fixed Boris (issue #136).
* [Core] Updated path resolver to return correct view depend on request headers.
* [ORM] Accept single param in Model::findBySql (issue #145).
* [ORM] Alias in update queries (issue #142).

Release 1.2
-----------
Enhancements:
* [ORM] Added conditions to relations.
* [Core] Added StdOutput logger.
* [Core] Added Error::getByCode.
* [Utilities] Added Functions::extractExpression.
* [Utilities] Added FluentArray::uniqueBy.
* [ORM] Ignore order, limit and offset for count queries.
* [ORM] Group by support in model.
* [Core] Improved handling of alerts.
* [Tests] Added StringAssert::isEmpty and StringAssert::isNotEmpty methods.
* [Tests] Added CatchException::hasMessage method.
* [Utilities] Added method Files::size.
* [Utilities] Added Functions::surroundWith.
* [Utilities] Added Joiner::mapValues.
* [Utilities] Added method Files::exists.
* [Tools] Improved model generator API.
* [Utilities] Added a new extractor in ArrayAssert.
* [Core] Added methods that extract protocol and host in Uri class (Uri::getProtocol and Uri::getHost).
* [Core] Replaced custom Ouzo loader with composer loader.

Bug fixes:
* [Tests] Fixed StreamStub write method.
* [Core] Fixed notices url in controller.
* [Mock] Fixed handling of params by reference (issue #89).
* [ORM] Set joined models on results only once for duplicated joins with different aliases.
* [Core] Trim url in Controller::redirect method.
* [ORM] Fixed relations fetching. Relations are fetched only once.
* [Core] Fixed interface in ConsoleCommand.
* [Core] Fixed logging of original error messages in error handler.
* [Utilities] Throw exception if empty extractor is called (issue #97). 
* [ORM] Fixed Model::__isset so that it works for lazy relations.
* [Utilities] Arrays::hasNestedKey: added a flag to treat null as a value (added const `TREAT_NULL_AS_VALUE`).
* [Core] Fixed json parsing in Uri class.
* [ORM] Fixed #100.

* * *

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
* Added OuzoException which is a wrapper for generic PHP exception.
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
* [Routes] Fixed invalid method name in generated uri helper for camel case methods in controllers (issues #69).
* [Mock] Fixed DynamicProxy so that it works with interfaces (issues #70).
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
