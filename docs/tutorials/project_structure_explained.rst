Project structure explained
===========================

Let's walk through the code and see how it works.

----

Routes
~~~~~~

File ``myproject/config/routes.php`` contains configuration of routing.
You can run ``./console ouzo:routes`` to see all routes exposed by your app.

``Route::get('/', 'users#index');`` instructs Ouzo that requests to '/' are handled by method **index** in **UsersController**.

----

Controller
~~~~~~~~~~
::

    class UsersController extends Controller
    {
        public function init()
        {
            $this->layout->setLayout('sample_layout');
        }

        public function index()
        {
            $this->view->users = User::all();
            $this->view->render();
        }
        ...

Function **init** sets layout used by this controller. The default layout adds "Ouzo Framework!" banner and includes bootstrap files.

In the **index** function, we fetch and assign all users to the **users** view variable. 
You can access this variable in a view as a field (``$this->users``).

In the next line we render a view. By default view name is derived from controller and method names. In this case it will be ``Users/index`` which means file ``View/Users/index.phtml`` will be used.
You can render other views by passing a parameter to the render method.

::

    class UsersController extends Controller
    {
        ...
        public function edit()
        {
            $this->view->user = User::findById($this->params['id']);
            $this->view->render();
        }

        public function update()
        {
            $user = User::findById($this->params['id']);
            if ($user->updateAttributesIfValid($this->params['user'])) {
                $this->redirect(userPath($user->id), "User updated");
            } else {
                $this->view->user = $user;
                $this->view->render('Users/edit');
            }
        }
        ...

Method **edit** is called when edition page is requested. It assigns ``user`` variable and renders view.

Method **update** is called when updated user form is submitted. It loads a user by id and then tries to update it. If update succeeds we return redirect to the user page with message *"User updated"*.
If update fails we use ``$user`` variable containing new values to render edition page.
It's important that we use the same ``$user`` variable on which $user->updateAttributesIfValid was called.
It will contain values submitted by browser and validation errors that prevented successful update.

----

Model
~~~~~

::

    class User extends Model
    {
        public function __construct($attributes = [])
        {
            parent::__construct([
                'attributes' => $attributes,
                'fields' => ['login', 'password']
            ]);
        }

        public function validate()
        {
            parent::validate();
            $this->validateNotBlank($this->login, 'Login cannot be blank', 'login');
        }
    }

User class is mapped to the **users** table, primary key defaults to **id** and sequence to **users_id_seq**.
Parameter **fields** defines columns that will be exposed as model attributes.
You can pass additional options to override the default mapping.

::

    parent::__construct([
        'table' => 'other_name'
        'primaryKey' => 'other_id',
        'sequence' => 'other_sequence'
        'attributes' => $attributes,
        'fields' => ['login', 'password']
    ]);

Function **validate** is called by function **isValid** and **updateAttributesIfValid**.
**validateNotBlank** takes a value to validate, error message and a field that is highlighted in red when validation fails.

----

View
~~~~

``Application/View/Users/edit.phtml`` contains users edition page.

::

    <?php echo renderPartial('Users/_form', [
        'user' => $this->user,
        'url' => userPath($this->user->id),
        'method' => 'PUT',
        'title' => 'Edit user'
    ]);

Function **renderPartial** displays a fragment of php code using variables passed in the second argument.
By convention partials names start with underscore. We extracted ``Users/_form`` partial so that we can use the same form for user creation and update.

``Users/_form`` looks as follows:

::

    <?php echo showErrors($this->user->getErrors()); ?>

    <div class="panel panel-default">
        <div class="panel-heading"><?php echo $this->title; ?></div>
        <div class="panel-body">
            <?php $form = formFor($this->user); ?>
            <?php echo $form->start($this->url, $this->method, ['class' => 'form-horizontal']); ?>

            <div class="form-group">
                <?php echo $form->label('login', ['class' => 'control-label col-lg-2']); ?>

                <div class="col-lg-10">
                    <?php echo $form->textField('login') ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('password', ['class' => 'control-label col-lg-2']); ?>

                <div class="col-lg-10">
                    <?php echo $form->passwordField('password'); ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <?php echo linkButton(['name' => 'cancel', 'value' => 'Cancel', 'url' => usersPath(), 'class' => "btn btn-default"]); ?>
                </div>
            </div>

            <?php echo $form->end(); ?>
        </div>
    </div>

Function **showErrors** displays validation errors set on our model.
In the line #6 we create a form for the user model. Method ``$form->start`` displays form html element for the given url.

Lines:

::

    $form->label('login', ['class' => 'control-label col-lg-2']);
    //<label for="user_login" class="control-label col-lg-2">Login</label>
    $form->textField('login');
    //<input type="text" id="user_login" name="user[login]" value="thulium">

display label and text input for user's login.

Label text is taken from translations (``locales/en.php``) by a key that is a concatenation of the model and field names. In this case it's *'user.login'*.
