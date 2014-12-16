# Welcome to Ouzo docs

###Tutorials:
* [5 minutes tutorial](https://github.com/letsdrink/ouzo-app)
* [[Skeleton-app-explained]]
    * [Model](Skeleton-app-explained#wiki-model)
    * [View](Skeleton-app-explained#wiki-view)
    * [Controller](Skeleton-app-explained#wiki-controller)

###Documentation:
* [[Routes]]
* [[ORM]]
    * [Models](ORM#wiki-model-definition)
    * [Validation](ORM#wiki-validation)
    * [Relations](ORM#wiki-relations)
    * [QueryBuilder](ORM#wiki-query-builder)
    * [Joins](ORM#wiki-join)
    * [Eager Fetching ](ORM#wiki-with)
    * [Transactions ](ORM#wiki-transactions)
* [[Tests]]
    * [Controllers tests](Tests#wiki-controller-tests)
    * [Models tests](Tests#wiki-models-tests)
    * [Assertions for arrays](Tests#wiki-array-assertions)
    * [Assertions for exceptions](Tests#wiki-exception-assertions)
    * [Assertions for strings](Tests#wiki-string-assertions)
    * [Mocking](Tests#wiki-mocking)

Form builders:
* [[FormHelper]] - view helper methods for generating form markup
* [[ModelFormBuilder]] - view helper methods for generating form markup for model objects

Utilities:
* [[Arrays]] - Helper functions for arrays.
* [[FluentArray]] - Interface for manipulating arrays in a chained fashion.
* [[Strings]] - Helper functions for strings.
* [[Objects]]- Helper functions that can operate on any PHP object.
* [[Functions]] - Static utility methods returning closures that can be used with Arrays and FluentArray.
* [[FluentFunctions]] - Fluent utility for function composition.
* [[Cache]] - General-purpose cache.
* [[Session]] - HTTP session handling.
* [[Path]] - Helper functions for path operations. 
* [[I18n]] - Localizations and translations.

Other topics
* [Functional programming with ouzo](Functional-programming-with-ouzo)
* [[Autoloading classes]]

Tools:
* [[Model Generator]] - console tool for creating Model classes for existing tables. 


###PhpStorm plugins:
 * [Ouzo framework plugin](http://plugins.jetbrains.com/plugin/7565?pr=)
 * [DynamicReturnTypePlugin](http://plugins.jetbrains.com/plugin/7251) - for Mock and CatchException. You have to copy [dynamicReturnTypeMeta.json ](https://github.com/letsdrink/ouzo/blob/master/dynamicReturnTypeMeta.json) to your project root.