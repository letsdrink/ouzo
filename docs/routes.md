# Concept of routings
When your application receives a request e.g.:

```
GET /users/12
```

it needs to be matched to a controller and action. This case can be served by the following route rule:

```php
Route::get('/users/:id', 'users#show');
```

This request will be dispatched to the `users` controller's and `show` action with `[id => 12]` in `params`.

# Basic types of routes

## GET route

```php
Route::get('/users/add' 'users#add');
```

HTTP request method must be `GET`, then router finds `users` controller and `add` action.

## POST route

```php
Route::post('/users/create' 'users#create');
```

HTTP request method must be `POST`, then router finds `users` controller and `create` action. POST parameters are also available in `$this->params`.

## Any route

```php
Route::any('/users/show_items' 'users#show_items');
```

HTTP request must be one of `GET` or `POST`.

## Allow all route

```php
Route::allowAll('/api', 'api');
```

This type of route allows you to map an action in `api` controller to all http methods. E.g. the following request will be accepted:

* `GET /api/method1`
* `POST /api/method2`
* `DELETE /api/method3`

# Route parameters

In _Ouzo_ you can use parametrized URLs.

```php
Route::get('/users/show/id/:id/name/:name' 'users#show');
```

This route provides mapping between HTTP verbs to controller and action. Parameters will be available in `$this->params` as map - `[id => value, name => value]`. E.g.:
`GET /users/show/id/12/name/John` will dispatch to `users` controller, `show` action and map of parameters `[id => 12, name => John]`.

# Resource route

This type of route simplifies mapping of RESTful controllers. 

```php
Route::resource('phones');
```

This route creates a default REST routing:

```php
+-----------+------------------+----------------+--------------------+
| Verb      | Path             | Action         | Used for           |
+-----------+------------------+----------------+--------------------+
| GET       | /phones          | phones#index   | phonesPath()       |
| GET       | /phones/fresh    | phones#fresh   | freshPhonePath()   |
| POST      | /phones          | phones#create  | phonesPath()       |
| GET       | /phones/:id      | phones#show    | phonePath($id)     |
| GET       | /phones/:id/edit | phones#edit    | editPhonePath($id) |
| PATCH/PUT | /phones/:id      | phones#update  | phonePath($id)     |
| DELETE    | /phones/:id      | phones#destroy | phonePath($id)     |
+-----------+------------------+----------------+--------------------+
```

# Options

## except

It is possible to exclude some actions from routing. 'except' parameter specifies methods that will be excluded.

```php
Route::allowAll('/api', 'api', array('except' => array('new', 'select')));
```

## as

You can rename generated routes using `as` option:

```php
Route::get('/agents', 'agents#index', array('as' => 'my_name'));
```

# Shell tool

## Listing defined routes

_Ouzo_ provides a command tool to display all defined routes. You can execute `php shell.php \\Routes` in terminal to produce output with registered routes. This is a sample output:

```php
	               indexAgentsPath 	 ANY        /agents/index                            agents#index
	                    phonesPath 	 GET        /phones                                  phones#index
	                freshPhonePath 	            /phones/fresh                            phones#fresh
	                 editPhonePath 	            /phones/:id/edit                         phones#edit
	                     phonePath 	            /phones/:id                              phones#show
	                    phonesPath 	 POST       /phones                                  phones#create
	                     phonePath 	 PUT        /phones/:id                              phones#update
	                     phonePath 	 PATCH      /phones/:id                              phones#update
	                     phonePath 	 DELETE     /phones/:id                              phones#destroy
	                    myNamePath 	 GET        /agents                                  agents#index
	                showAgentsPath 	            /agents/show/id/:id/call_id/:call_id     agents#show
```

This tool can display routes per controller. Used with `-c` parameter - `php shell.php \\Routes -c="phones"`, produces output:

```php
	                    phonesPath 	 GET        /phones                                  phones#index
	                freshPhonePath 	            /phones/fresh                            phones#fresh
	                 editPhonePath 	            /phones/:id/edit                         phones#edit
	                     phonePath 	            /phones/:id                              phones#show
	                    phonesPath 	 POST       /phones                                  phones#create
	                     phonePath 	 PUT        /phones/:id                              phones#update
	                     phonePath 	 PATCH      /phones/:id                              phones#update
	                     phonePath 	 DELETE     /phones/:id                              phones#destroy

```

## Generating the UriHelper functions

Route tool can generate `UriHelper` functions too. Used with `-g`, parameter creates or overwrites file `application/helper/GeneratedUriHelper.php` which should be included in `UriHelper.php` in the same location. To generate this file use `php shell.php \\Routes -g`. E.g.:

Route: 

```php
Route::get('/agents', 'agents#index', array('as' => 'my_name'));
```

Displayed:

```php
myNamePath 	 GET        /agents                                  agents#index
```

Can be used in application:

```php
$agentsUrl = myNamePath();
```
