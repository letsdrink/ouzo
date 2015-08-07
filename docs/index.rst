Welcome to Ouzo docs
====================

Tutorials
~~~~~~~~~

Start with `5 minutes tutorial`_, read about project structure and then dive deeper into more advanced Ouzo topics.

.. _`5 minutes tutorial`: https://github.com/letsdrink/ouzo-app

.. toctree::
   :maxdepth: 2
   :caption: Framework

   tutorials/project_structure_explained
   documentation/routes
   documentation/orm
   documentation/tests
   documentation/functional_programming
   documentation/autoloading_classes
   documentation/config
   utils/form_helper
   utils/i18n
   utils/session
   utils/model_form_builder

.. toctree::
   :maxdepth: 1
   :caption: Goodies

   utils/arrays
   utils/strings
   utils/objects
   utils/functions
   utils/fluent_array
   utils/fluent_iterator
   utils/fluent_functions
   utils/comparators
   utils/iterators
   utils/cache
   utils/suppliers
   utils/path
   utils/clock
   utils/joiner
   utils/time_ago

.. toctree::
   :maxdepth: 2
   :caption: Tools

   tools/model_generator

PhpStorm plugins
~~~~~~~~~~~~~~~~

* `Ouzo framework plugin`_
* `DynamicReturnTypePlugin`_ - for Mock and CatchException. You have to copy `dynamicReturnTypeMeta.json`_ to your project root.

.. _Ouzo framework plugin: http://plugins.jetbrains.com/plugin/7565
.. _DynamicReturnTypePlugin: http://plugins.jetbrains.com/plugin/7251
.. _dynamicReturnTypeMeta.json: https://github.com/letsdrink/ouzo/blob/master/dynamicReturnTypeMeta.json
