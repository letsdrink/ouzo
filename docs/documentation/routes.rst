Routes
======

Concept of routings
~~~~~~~~~~~~~~~~~~~

When your application receives a request e.g.:

.. http:method:: GET /users/12


it needs to be matched to a controller and action. This case can be served by the following route rule:

::

    Route::get('/users/:id', 'users#show');

This request will be dispatched to the ``users`` controller's and ``show`` action with ``[id => 12]`` in ``params``.

----

Basic types of routes
~~~~~~~~~~~~~~~~~~~~~

GET route
---------

::

    Route::get('/users/add' 'users#add');

HTTP request method must be ``GET``, then router finds ``users`` controller and ``add`` action.

POST route
----------

::

    Route::post('/users/create' 'users#create');

HTTP request method must be ``POST``, then router finds ``users`` controller and ``create`` action. POST parameters are also available in ``$this->params``.

DELETE route
------------

::

    Route::delete('/users/destroy' 'users#destroy');

HTTP request method must be ``DELETE``, then router finds ``users`` controller and ``destroy`` action.

PUT route
---------

::
    Route::put('/users/update' 'users#update');

HTTP request method must be ``PUT``, then router finds ``users`` controller and ``edit`` action.

Any route
---------

::

    Route::any('/users/show_items' 'users#show_items');

HTTP request must be one of ``GET``, ``POST``, ``PUT``, ``PATCH`` or ``DELETE``.

Allow all route
---------------

::

    Route::allowAll('/api', 'api');

This type of route allows you to map an action in ``api`` controller to all http methods. E.g. the following request will be accepted:

.. http:method:: GET /api/method1
.. http:method:: POST /api/method2
.. http:method:: DELETE /api/method3

----

Route parameters
~~~~~~~~~~~~~~~~

In Ouzo you can use parametrized URLs.

::

    Route::get('/users/show/id/:id/name/:name' 'users#show');

This route provides mapping between HTTP verbs to controller and action. Parameters will be available in ``$this->params`` as map - ``[id => value, name => value]``.
E.g.:

.. http:method:: GET /users/show/id/12/name/John

will dispatch to ``users`` controller, ``show`` action and map of parameters ``[id => 12, name => John]``.

----

Resource route
~~~~~~~~~~~~~~

This type of route simplifies mapping of RESTful controllers. 

::

    Route::resource('phones');

This route creates a default REST routing:

::

    +-----------------+-----------+--------------------------------------+-------------------+
    | URL Helper      | HTTP Verb | Path                                 | Controller#Action |
    +-----------------+-----------+--------------------------------------+-------------------+
    | phonesPath      | GET       | /phones                              | phones#index      |
    | freshPhonePath  | GET       | /phones/fresh                        | phones#fresh      |
    | editPhonePath   | GET       | /phones/:id/edit                     | phones#edit       |
    | phonePath       | GET       | /phones/:id                          | phones#show       |
    | phonesPath      | POST      | /phones                              | phones#create     |
    | phonePath       | PUT       | /phones/:id                          | phones#update     |
    | phonePath       | PATCH     | /phones/:id                          | phones#update     |
    | phonePath       | DELETE    | /phones/:id                          | phones#destroy    |
    +-----------------+-----------+--------------------------------------+-------------------+

----

Options
~~~~~~~

except
------

It is possible to exclude some actions from routing. 'except' parameter specifies methods that will be excluded.

::

    Route::allowAll('/api', 'api', ['except' => ['new', 'select']]);

as
--

You can rename generated routes using ``as`` option:

::

    Route::get('/agents', 'agents#index', ['as' => 'my_name']);

----

Console tool
~~~~~~~~~~~~

Listing defined routes
----------------------

Ouzo provides a command tool to display all defined routes. You can execute ``./console ouzo:routes`` in terminal to produce output with registered routes. This is a sample output:

::

    +-----------------+-----------+--------------------------------------+-------------------+
    | URL Helper      | HTTP Verb | Path                                 | Controller#Action |
    +-----------------+-----------+--------------------------------------+-------------------+
    | indexIndexPath  | GET       | /                                    | index#index       |
    |                 | ALL       | /users                               | users             |
    |                 |           |   except:                            |                   |
    |                 |           |     new                              |                   |
    |                 |           |     select_outbound_for_user         |                   |
    | indexAgentsPath | GET       | /agents/index                        | agents#index      |
    | indexAgentsPath | POST      | /agents/index                        | agents#index      |
    |                 | ALL       | /photos                              | photos            |
    | indexAgentsPath | ANY       | /agents/index                        | agents#index      |
    | phonesPath      | GET       | /phones                              | phones#index      |
    | freshPhonePath  | GET       | /phones/fresh                        | phones#fresh      |
    | editPhonePath   | GET       | /phones/:id/edit                     | phones#edit       |
    | phonePath       | GET       | /phones/:id                          | phones#show       |
    | phonesPath      | POST      | /phones                              | phones#create     |
    | phonePath       | PUT       | /phones/:id                          | phones#update     |
    | phonePath       | PATCH     | /phones/:id                          | phones#update     |
    | phonePath       | DELETE    | /phones/:id                          | phones#destroy    |
    | myNamePath      | GET       | /agents                              | agents#index      |
    | showAgentsPath  | GET       | /agents/show/id/:id/call_id/:call_id | agents#show       |
    +-----------------+-----------+--------------------------------------+-------------------+

This tool can display routes per controller. Used with ``-c`` parameter - ``./console ouzo:routes -c=phones``, produces output:

::

    +-----------------+-----------+--------------------------------------+-------------------+
    | URL Helper      | HTTP Verb | Path                                 | Controller#Action |
    +-----------------+-----------+--------------------------------------+-------------------+
    | phonesPath      | GET       | /phones                              | phones#index      |
    | freshPhonePath  | GET       | /phones/fresh                        | phones#fresh      |
    | editPhonePath   | GET       | /phones/:id/edit                     | phones#edit       |
    | phonePath       | GET       | /phones/:id                          | phones#show       |
    | phonesPath      | POST      | /phones                              | phones#create     |
    | phonePath       | PUT       | /phones/:id                          | phones#update     |
    | phonePath       | PATCH     | /phones/:id                          | phones#update     |
    | phonePath       | DELETE    | /phones/:id                          | phones#destroy    |
    +-----------------+-----------+--------------------------------------+-------------------+

Generating the UriHelper functions
----------------------------------

Route tool can generate ``UriHelper`` functions too. Used with ``-g``, parameter creates or overwrites file ``Application/Helper/GeneratedUriHelper.php`` which should be included in ``UriHelper.php`` in the same location. To generate this file use ``./console ouzo:routes -g``. E.g.:

Route: 

::

    Route::get('/agents', 'agents#index', ['as' => 'my_name']);

Displayed:

::

    | myNamePath  | GET       | /agents                        | agents#index      |

Can be used in application:

::

    $agentsUrl = myNamePath();
