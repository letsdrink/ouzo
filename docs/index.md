# Welcome to Ouzo docs

###Tutorials:

* [5 minutes tutorial](https://github.com/letsdrink/ouzo-app)
* [[Skeleton-app-explained]]
    * [Model](skeleton_app_explained.md#model)
    * [View](skeleton_app_explained.md#view)
    * [Controller](skeleton_app_explained.md#controller)

###Documentation:

* [Routes](routes.md)
* [ORM](orm.md)
    * [Models](orm.md#wiki-model-definition)
    * [Validation](orm.md#wiki-validation)
    * [Relations](orm.md#wiki-relations)
    * [QueryBuilder](orm.md#wiki-query-builder)
    * [Joins](orm.md#wiki-join)
    * [Eager Fetching ](orm.md#wiki-with)
    * [Transactions ](orm.md#wiki-transactions)
    
* [Tests](tests.md)

    * [Controllers tests](tests.md#wiki-controller-tests)
    * [Models tests](tests.md#wiki-models-tests)
    * [Assertions for arrays](tests.md#wiki-array-assertions)
    * [Assertions for exceptions](tests.md#wiki-exception-assertions)
    * [Assertions for strings](tests.md#wiki-string-assertions)
    * [Mocking](tests.md#wiki-mocking)

Form builders:

* [FormHelper](form_helper.md) - view helper methods for generating form markup
* [ModelFormBuilder](model_form_builder.md) - view helper methods for generating form markup for model objects


Utilities:

* [Arrays](arrays.md) - Helper functions for arrays.
* [FluentArray](fluent_array.md) - Interface for manipulating arrays in a chained fashion.
* [Strings](strings.md) - Helper functions for strings.
* [Objects](objects.md)- Helper functions that can operate on any PHP object.
* [Functions](functions.md) - Static utility methods returning closures that can be used with Arrays and FluentArray.
* [FluentFunctions](fluent_functions.md) - Fluent utility for function composition.
* [Cache](cache.md) - General-purpose cache.
* [Path](path.md) - Helper functions for path operations.
* [Session](session.md) - HTTP session handling.
* [I18n](i18n.md) - Localizations and translations.

Other topics:

* [Functional programming with ouzo](functional_programming.md)
* [Autoloading classes](autoloading_classes.md)

Tools:

* [Model Generator](model_generator.md) - console tool for creating Model classes for existing tables. 


###PhpStorm plugins:
 * [Ouzo framework plugin](http://plugins.jetbrains.com/plugin/7565?pr=)
 * [DynamicReturnTypePlugin](http://plugins.jetbrains.com/plugin/7251) - for Mock and CatchException. You have to copy [dynamicReturnTypeMeta.json ](https://github.com/letsdrink/ouzo/blob/master/dynamicReturnTypeMeta.json) to your project root.