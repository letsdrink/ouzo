Model generator is a console tool for creating Model classes for existing tables. 
Generator reads information about database table and transforms it into Ouzo Model class.

Support for: MySQL and PostgreSQL.

### Basic example
Change current path to project directory (e.g. myproject):

`cd myproject`

Generate Model class body for table **users** containing three columns: _id_, _login_, _password_:

`php shell.php \\ModelGenerator -t=users`

The command should output a model class **User**:
~~~~
<?php
namespace Model;

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
            'fields' => $this->_fields));
    }
}
~~~~
As you can see $_fields lists all `users` table columns (except for id which is specified by `'primaryKey'` parameter).

You could save the generated class to a file by specifying `-f=/path/to/file.php` option.

### Model Generator options

`php shell.php \\ModelGenerator OPTIONS`

OPTIONS are:

* `-t=table_name` _[required]_ - table name for a Model that you want generate
* `-c=ClassName` _[optional]_ - specify this option to override Model class name
* `-f=/path/to/file.php` _[optional]_ - specify a destination file for the generated class. **If file already exists, it will not be overwritten**.
* `-p=prefix` _[optional]_ _[default: **t** ]_ - table prefix. Prefix is omitted in class name. E.g. for **t_users** table, **User** Model class will be generated. 

If no option is specified application will print the help message

