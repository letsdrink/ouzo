Model generator
===============

Model generator is a console tool for creating Model classes for existing tables.
Generator reads information about database table and transforms it into Ouzo Model class.

.. note::

    Support for: MySQL and PostgreSQL.

Basic example
~~~~~~~~~~~~~

Change current path to project directory (e.g. myproject):

``cd myproject``

Generate Model class body for table **users** containing three columns: *id*, *login*, *password*:

``./console ouzo:model_generator -t users``

The command should output a model class **User**:

::

    ---------------------------------
    Database name: thulium_1
    Class name: PhoneParam
    Class namespace: Model
    ---------------------------------
    <?php
    namespace Model;

    use Ouzo\Model;

    /**
     * @property string login
     * @property string password
    */
    class User extends Model
    {
        private $_fields = ['login', 'password';

        public function __construct($attributes = [])
        {
            parent::__construct([
                'table' => 'users',
                'primaryKey' => 'id',
                'attributes' => $attributes,
                'fields' => $this->_fields
            ]);
        }
    }
    Saving class to file: '/path/to/myproject/Application/Model/User.php'

As you can see ``$_fields`` lists all ``users`` table columns (except for id which is specified by ``primaryKey`` parameter).

.. note::

    You could save the generated class to a file by specifying ``-f=/path/to/file.php`` option. If not specified namespace and class name is used.

Options
~~~~~~~

::

     --table (-t)          Table name
     --class (-c)          Class name. If not specified class name is generated based on table name
     --file (-f)           Class file path. If not specified namespace and class name is used
     --namespace (-s)      Class namespace (e.g 'Model\MyModel'). Hint: Remember to escape backslash (\\)! (default: "Model")
     --remove_prefix (-p)  Remove prefix from table name when generating class name (default: "t")

.. note::

    If no option is specified application will print the help message.
