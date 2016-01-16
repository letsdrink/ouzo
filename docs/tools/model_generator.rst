Model generator
===============

Model generator is a console tool for creating Model classes for existing database tables.
Generator reads information about database table and transforms it into Ouzo's Model class.

.. note::

    Currently there is a support for MySQL and PostgreSQL.

----

Basic example
~~~~~~~~~~~~~

Change current path to project directory (e.g. myproject):

``cd myproject``

Generate Model class body for table **users** containing three columns: *id*, *login*, *password*:

``./console ouzo:model_generator users``

The command should output a model class **User**:

::

    ---------------------------------
    Database name: thulium_1
    Class name: PhoneParam
    Class namespace: Application\Model
    ---------------------------------
    <?php
    namespace Application\Model;

    use Ouzo\Model;

    /**
     * @property string login
     * @property string password
    */
    class User extends Model
    {
        private $_fields = array('login', 'password');

        public function __construct($attributes = array())
        {
            parent::__construct(array(
                'table' => 'users',
                'primaryKey' => 'id',
                'attributes' => $attributes,
                'fields' => $this->_fields
            ));
        }
    }
    Saving class to file: '/path/to/myproject/Application/Model/User.php'

As you can see ``$_fields`` lists all ``users`` table columns (except for id which is specified by ``primaryKey`` parameter).

.. note::

    You could save the generated class to a file by specifying ``-f=/path/to/file.php`` option. If not specified namespace and class name is used.

----

Arguments
~~~~~~~~~

::

    table                     Table name.


Options
~~~~~~~

::

    --class (-c)          Class name. If not specified class name is generated based on table name.
    --file (-f)           Class file path. If not specified namespace and class name is used.
    --namespace (-s)      Class namespace (e.g 'Model\MyModel'). Hint: Remember to escape backslash (\\)! (default: "Model").
    --remove-prefix (-p)  Remove prefix from table name when generating class name (default: "t").
    --output-only (-o)    Do not save file on disk, only display output.
    --short-arrays (-a)   Use shorthand array syntax (PHP 5.4).

.. note::

    If no option is specified application will print the help message.
