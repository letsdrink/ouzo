ORM
===

Model definition
~~~~~~~~~~~~~~~~
This code will map ``Category`` class to a ``categories`` table with *id* as a primary key and one column *name*.

::

    class Category extends Model
    {
        public function __construct($attributes = array())
        {
            parent::__construct(array(
                'attributes' => $attributes,
                'fields' => array('name')
            ));
        }
    }

``Model`` constructor accepts the following parameters:

* ``table`` - defaults to pluralized class name. E.g. customer_orders for ``CustomerOrder``
* ``primaryKey`` - defaults to ``id``
* ``sequence`` - defaults to ``table_primaryKey_seq``
* ``hasMany`` specification of a has-many relation e.g. ``array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))``
* ``hasOne`` specification of a has-one relation e.g. ``array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))``
* ``belongsTo`` specification of a belongs-to relation e.g. ``array('name' => array('class' => 'Class', 'foreignKey' => 'foreignKey'))``
* ``fields`` - mapped column names
* ``attributes`` -  array of ``column => value``

Columns specified by **'fields'** parameter are exposed with magic getter and setter.

----

Working with model objects
~~~~~~~~~~~~~~~~~~~~~~~~~~

Creating new instances
----------------------
You can create an instance using Model's constructor or ```Model::newInstance`` method. They both take an array of attributes as an optional parameter.

::

    $user = new User();
    $user = new User(array('name' => 'bob'));

    $user = User::newInstance(array('name' => 'bob'));

Instances created using constructor and ``Model::newInstance`` method are not inserted into db. Validation is also not performed.

If you want to create, validate and save an instance, you can use ``Model::create`` method.

::

    $user = User::create(array('name' => 'bob'));

If validation fails, ``ValidationException`` is thrown.

Saving and updating
-------------------
You can save a new instance using ``insert`` method. It returns the value of the primary key of the newly inserted row.
You can update an existing object using ``update`` method.
If you are not sure if an object was already saved you can call ``insertOrUpdate`` method.

::

    $product = new Product();
    $product->name = 'Phone';

    $id = $product->insert();

    $product->name = 'Super Phone';
    $product->update();

    $product->name = 'Phone';
    $product->insertOrUpdate();

Update of multiple records
--------------------------
You can update specific columns in records matching given criteria.

::

    $affectedRows = User::where(array('name' => 'bob'))
                     ->update(array('name' => 'eric'));

Issued sql query:

.. code-block:: sql

    UPDATE users set name = ? WHERE name = ? Params: ['eric', 'bob']

Default field values
--------------------
You can define default values for fields in two ways - using **string** or **anonymous function**.

::

    [
        'description' => 'no desc',
        'name' => function() {
            return 'no name';
        }
    ]

Now if you create a new model object these fields will be set to their default values.

::

    class ModelWithDefaults extends Model {
        public function __construct($attributes = []) {
            parent::__construct([
                'attributes' => $attributes,
                'fields' => [
                    'description' => 'no desc',
                    'name' => function() {
                        return 'no name';
                    }
                ]
            ]);
        }
    }

    $modelWithDefaults = new ModelWithDefaults();
    echo $modelWithDefaults->description; // no desc
    echo $modelWithDefaults->name; // no name

Validation
----------
You can validate the state of objects with ``Model::validate`` method.
Just override it in you model and implement all necessary checks.

::

    public function validate()
    {
        parent::validate();
        $this->validateNotBlank($this->name, 'Name cannot be blank.', 'name');
        $this->validateTrue($this->accepted, 'Accepted should be true');
    }

Second parameter specifies the message that will be used in the case of error.
Third parameter specifies the field name so that the corresponding input can be highlighted in the html form.

You can check if a model object is valid by calling ``Model::isValid`` method.
If validation fails it returns false and sets errors attribute.
You can then see what was wrong calling ``getErrors`` (for error messages) or ``getErrorFields`` (for invalid fields).

If your object has relations to other objects and you want to validate them altogether you can call 
``validateAssociated`` method passing other objects.

::

    public function validate()
    {
        parent::validate();
        $this->validateAssociated($this->child);
    }

Validation is provided by ``Validatable`` class. You can easily add validation to other classes by extending ``Validatable``.

----

Fetching objects
~~~~~~~~~~~~~~~~

findById
--------
Loads object for the given primary key. If object does not exist, exception is thrown

findByIdOrNull
--------------
Loads object for the given primary key. If object does not exist, null is returned.

findBySql
---------
Executes a native sql and returns an array of model objects created by passing every result row to the model constructor.

* ``$nativeSql`` - database specific sql
* ``$params`` - bind parameters

::

    User::findBySql('select * from users');
    User::findBySql('select * from users where login like ?', "%cat%");

Normally, there's no reason to use ``findBySql`` as Ouzo provides powerful query builder described in another section.

----

Relations
~~~~~~~~~
Relations are used to express associations between Models.
You can access relation objects using Model properties (just like other attributes).
Relation object are lazy-loaded when they are accessed for the first time and cached for subsequent use.

For instance, if you have a ``User`` model that belongs to a ``Group``:

::

    $group = Group::create(['name' => 'Admin']);
    $user = User::create(['login' => 'bob', 'group_id' => $group->id]);

You can access user's group as follows: ``echo $user->group->name;``

Ouzo supports 3 types of associations:

* **Belongs to** - expresses 1-1 relationship. It's specified by ``belongsTo`` parameter. Use ``belongsTo`` in a class that contains the foreign key.
* **Has one** - expresses 1-1 relationship. It's specified by ``hasOne`` parameter. Use ``hasOne`` in a class that contains the key referenced by the foreign key.
* **Has many** - expresses One-to-many relationship. It's specified by ``hasMany`` parameter.

Relations are defined by following parameters:

* **class** - name of the associated class.
* **foreignKey** - foreign key.
* **referencedColumn** - column referenced by the foreign key. By default it's the primary key of the referenced class.

Note that **foreignKey** and **referencedColumn** mean different things depending on the relation type.

Let's see an example.

We have products that are assigned to exactly one category, and categories that can have multiple products.

::

    class Category extends Model
    {
        public function __construct($attributes = array())
        {
            parent::__construct(array(
                'hasMany' => array(
                     'products' => array('class' => 'Product', 'foreignKey' => 'category_id')
                ),
                'attributes' => $attributes,
                'fields' => array('name')));
        }
    }

``foreignKey`` in ``Category`` specifies column in ``Product`` that references the ``categories`` table.
Parameter ``referencedColumn`` was omitted so the Category's primary key will be used by default.

::

    class Product extends Model
    {
        public function __construct($attributes = array())
        {
            parent::__construct(array(
                'attributes' => $attributes,
                'belongsTo' => array(
                    'category' => array('class' => 'Category', 'foreignKey' => 'category_id'),
                ),
                'fields' => array('description', 'name', 'category_id')));
        }
    }

``foreignKey`` in ``Product`` specifies column in ``Product`` that references the ``categories`` table.
Parameter ``referencedColumn`` was omitted so again the Category's primary key will be used.

Inline Relation
---------------
If you want to join your class with another class without specifying the relation in the constructor, you can pass a relation object to the ``join`` method

::

    User::join(Relation::inline(array(
      'class' => 'Animal',
      'foreignKey' => 'name',
      'localKey' => 'strange_column_in_users'
    )))->fetchAll();

Cyclic relations
----------------
Normally, it suffices to specify **class** and **foreignKey** parameters of a relation.
However, if your models have cycles in relations (e.g. User can have a relation to itself) you have to specify **referencedColumn** as well (Ouzo is not able to get primary key name of the associated model if there are cycles).

Conditions in relations
-----------------------
If you want to customize your relation you can use **conditions** mechanism. For example, to add a condition use string or array:

::

    'hasOne' => array(
        'product_named_billy' => array(
            'class' => 'Test\Product',
            'foreignKey' => 'id_category',
            'conditions' => "products.name = 'billy'"
        )
    )

you can use a closure too:

::

    'products_ending_with_b_or_y' => array(
        'class' => 'Test\Product',
        'foreignKey' => 'id_category',
        'conditions' => function () {
            return new WhereClause("products.name LIKE ? OR products.name LIKE ?", array('%b', '%y'));
        }
    )

----

Query builder
~~~~~~~~~~~~~
It's a fluent interface that allows you to programmatically build queries.

Fully-fledged example:

::

    $orders = Order::alias('o')
            ->join('product->category', ['p', 'ct'])
            ->innerJoin('customer', 'c')
            ->where([
                'o.tax'  => array(7, 22)
                'p.name' => 'Reno',
                'ct.name' => 'cars'])
            ->with('customer->preferences')
            ->offset(10)
            ->limit(12)
            ->order(['ct.name asc', 'p.name desc'])
            ->fetchAll();

Where
-----

Single parameter
^^^^^^^^^^^^^^^^
Simplest way to filter records is to use where clause on Model class e.g.

::

    User::where(array('login' => 'ouzo'))->fetch();

In the above example we are searching for a user, who has login set to ouzo. You can check the log files (or use Stats class in debug mode) to verify that the database query is correct:

.. code-block:: sql

    SELECT users.* FROM users WHERE login = ? Params: ["ouzo"]

Alternative syntax:

::

    User::where('login = ?', 'ouzo')->fetch();

Multiple parameters
^^^^^^^^^^^^^^^^^^^
You can specify more than one parameter e.g.

::

    User::where(array('login' => 'ouzo', 'password' => 'abc'))->fetch();

Which leads to:

.. code-block:: sql

    SELECT users.* FROM users WHERE (login = ? AND password = ?) Params: ["ouzo", "abc"]

Alternative syntax:

::

    User::where('login = ? AND password = ?', array('ouzo', 'abc'))->fetch();

Restrictions
------------
You can specify restriction mechanism to build where conditions. Usage:

::

    Product::where(array('name' => Restrictions::like('te%')))->fetch()

Supported restrictions:

* **between**

``['count' => Restrictions::between(1, 3)]`` produces
``SELECT * FROM table WHERE (count >= ? AND count <= ?) Params: [1, 3]``

* **equalTo**

``['name' => Restrictions::equalTo('some name')]`` produces
``SELECT * FROM table WHERE name = ? Params: ["some name"]``

* **notEqualTo**

``['name' => Restrictions::notEqualTo('some name')]`` produces
``SELECT * FROM table WHERE name <> ? Params: ["some name"]``

* **greaterOrEqualTo**

``['count' => Restrictions::greaterOrEqualTo(3)]`` produces
``SELECT * FROM table WHERE count >= ? Params: [3]``

* **greaterThan**

``['count' => Restrictions::greaterThan(3)]`` produces
``SELECT * FROM table WHERE count > ? Params: [3]``

* **lessOrEqualTo**

``['count' => Restrictions::lessOrEqualTo(3)]`` produces
``SELECT * FROM table WHERE count <= ? Params: [3]``

* **lessThan**

``['count' => Restrictions::lessThan(3)]`` produces
``SELECT * FROM table WHERE count < ? Params: [3]``

* **like**

``['name' => Restrictions::like("some%")]`` produces
``SELECT * FROM table WHERE name LIKE ? Params: ["some%"]``

* **isNull**

``['name' => Restrictions::isNull()]`` produces
``SELECT * FROM table WHERE name IS NULL``

* **isNotNull**

``['name' => Restrictions::isNotNull()]`` produces
``SELECT * FROM table WHERE name IS NOT NULL``

Parameters chaining
-------------------
Where clauses can be chained e.g.

::

    User::where(array('login' => 'ouzo'))
        ->where(array('password' => 'abc'))
        ->fetch();

SQL query will be exactly the same as in the previous example.

OR operator
-----------
Where clauses are chained with AND operator. In order to have OR operator you need to use
``Any::of`` function e.g.

::

    User::where(Any::of(array('login' => 'ouzo', 'password' => 'abc')))
        ->fetch();

Query:

.. code-block:: sql

    SELECT users.* FROM users WHERE login = ? OR password = ? Params: ["ouzo", "abc"]

You can use parameters chaining as described in previous section and combine ``Any:of`` with standard ``where``.

Multiple values
---------------
If you want to search for any of values equal to given parameter:

::

    User::where(array('login' => array('ouzo', 'admin')))->fetch();

It results in:

.. code-block:: sql

    SELECT users.* FROM users WHERE login IN (?, ?) Params: ["ouzo", "admin"]

It is not possible to use alternative syntax for this type of query.


.. note::

    Please, remember that if you want to retrieve more than one record you need to use fetchAll instead of fetch:

    ::

        User::where(array('login' => array('ouzo', 'admin')))->fetchAll();

Retrieve all records
--------------------
All records of given type can be fetched by using empty where clause:

::

    User::where()->fetchAll();

Or shortened equivalent:

::

    User:all();

----

Join
~~~~

Types:

* ``Model::join`` or ``Model::leftJoin`` - left join,
* ``Model::innerJoin`` - inner join,
* ``Model::rightJoin`` - right join.

Relation definition
-------------------
As a first step relations have to be defined inside a Model class. Let's say there is User, which has one Product. User definition needs ``hasOne`` relation:

::

    class User extends Model
    {
        public function __construct($attributes = array())
        {
            parent::__construct(array(
                'attributes' => $attributes,
                'hasOne' => array('product' => array(
                                          'class' => 'Product',
                                          'foreignKey' => 'user_id')),
                'fields' => array('login', 'password')));
        }
    }

The relation name is ``product``, it uses ``Product`` class and is mapped by user_id column in the database.

Single join
-----------
Now ``join`` can be used to retrieve User together with Product:

::

    User::join('product')->fetch();

Query:

.. code-block:: sql

    SELECT users.*, products.* FROM users
    LEFT JOIN products ON products.user_id = users.id

Product can be referred from User object:

::

    $user = User::join('product')->fetch();
    echo $user->product->name;

Join can be combined with other parts of query builder (where, limit, offset, order etc.) e.g.

::

    User::join('product')->where(array('products.name' => 'app'))->fetch();

Query:

.. code-block:: sql

    SELECT users.*, products.* FROM users
    LEFT JOIN products ON products.user_id = users.id
    WHERE products.name = ? Params: ["app"]

Multiple joins / join chaining
------------------------------
You can chain join clauses:

::

    User::join('product')
       ->join('group')->fetchAll();

Nested joins
------------
You can join models through other models with nested joins.

Let's assume that you have Order that has Product and Product has Category:

::

    $order = Order::join('product->category')->fetch();

.. code-block:: sql

    SELECT orders.*, products.*, categories.*
    FROM orders
    LEFT JOIN products ON products.id = orders.product_id
    LEFT JOIN categories ON categories.id = products.category_id

Returned order will contain fetched product and that product will contain category.
The following code will echo category's name without querying db:

::

    echo $order->product->category->name;

----

Aliasing
~~~~~~~~
Normally if you want to reference a table in the query builder you have to use the table name.
When you join multiple Models it may be cumbersome. That is when aliases come in handy.

::

    $product = Product::alias('p')
            ->join('category', 'c')
            ->where(['p.name' => 'a', 'c.name' => 'phones'])
            ->fetch();

.. code-block:: sql

    SELECT p.*, c.*
    FROM products AS p
    LEFT JOIN categories AS c ON c.id = p.category_id
    WHERE p.name = 'a' and c.name = 'phones'

If you want to alias tables in nested join you can pass array of aliases as a second parameter of ``join`` method.

::

    $orders = Order::alias('o')
            ->join('product->category', array('p', 'c'))
            ->where([
                'o.tax'  => 7
                'p.name' => 'Reno',
                'c.name' => 'cars'])
            ->fetchAll();

----

With
~~~~
``ModelQueryBuilder::with`` method instructs ouzo to fetch results with their relations.

The following code will return products with their categories.

::

    $products = Product::where()->with('category')->fetchAll();

Ouzo will query db for products, then load all corresponding categories with one query.

.. code-block:: sql

    SELECT products.* FROM products
    SELECT categories.* FROM categories WHERE id IN (?, ?, ..,) Params: [product1.category_id, product2.category_id, ..., productN.category_id]

You can chain ``with`` methods.
You can also use ``with`` to fetch nested relations.

::

    $orders = Order::where()
       ->with('product->category')
       ->fetchAll();

Ouzo will first load all matching orders, then their products, and then products' categories:

.. code-block:: sql

    SELECT orders.* FROM orders
    SELECT products.* FROM products WHERE id IN (?, ?, ...)
    SELECT categories.* FROM categories WHERE id IN (?, ?, ...)

For ``hasOne`` and ``belongsTo`` relations you can use ``join`` instead.
However, joins with ``hasMany`` relations will not fetch associated objects so ``with`` is the only way of fetching them eagerly.

----

Count
~~~~~

Count all records
-----------------
Counting all records of given type:

::

    User::count()

As a result integer with size is returned. Query:

.. code-block:: sql

    SELECT count(*) FROM users

Count with where
----------------
Count method accepts same arguments as where e.g.

::

    User::count(array('login' => 'ouzo'));

Query:

.. code-block:: sql

    SELECT count(*) FROM users WHERE login = ? Params: ["ouzo"]

----

Limit and offset
~~~~~~~~~~~~~~~~

Limit
-----
In order to limit number of records to retrieve use ``limit`` method with integer argument:

::

    User::where()->limit(10)->fetch();

It returns first 10 records:

.. code-block:: sql

    SELECT users.* FROM users LIMIT ? Params: [10]

----

Offset
------
Usually used with ``limit`` method, it sets offset (integer) from which records will be retrieved:

::

    User::where()->offset(5)->fetch();

Query:

.. code-block:: sql

    SELECT users.* FROM users OFFSET ? Params: [5]

Combined with ``limit``:

::

    User::where()->limit(10)->offset(5)->fetch();

Query:

.. code-block:: sql

    SELECT users.* FROM users LIMIT ? OFFSET ? Params: [10, 5]

----

Order
~~~~~

Order by one column
-------------------
To sort the result:

::

    User::where()->order('login')->fetch();

Query:

.. code-block:: sql

    SELECT users.* FROM users ORDER BY login

Order by multiple columns
-------------------------
If array is given as an argument the method sorts by multiple columns:

::

    User::where()->order(array('login', 'id'))->fetch();

Query:

.. code-block:: sql

    SELECT users.* FROM users ORDER BY login, id

Sort direction
--------------
Ascending or descending:

::

    User::where()->order(array('login asc', 'id desc'))->fetch();

Query:

.. code-block:: sql

    SELECT users.* FROM users ORDER BY login asc, id desc

----

Transactions
~~~~~~~~~~~~
You can control transactions manually:

::

    Db::getInstance()->beginTransaction();
    try {
        Db::getInstance()->commitTransaction();
        //do something
    } catch (Exception $e) {
        Db::getInstance()->rollbackTransaction();
    }

You can run a callable object in a transaction:

::

    $result = Db::getInstance()->runInTransaction(function() {
       //do something
       return $result;
    });

You can also proxy an object so that all methods become transactional:

::

    $user = new User(['name' => 'bob']);
    $transactionalUser = Db::transactional($user);

    $transactionalUser->save(); //runs in a transaction
