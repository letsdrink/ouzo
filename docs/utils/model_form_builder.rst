ModelFormBuilder
================

ModelFormBuilder simplifies implementation of forms for model objects.

::

  <? $form = formFor($this->user); ?>
    <?= $form->start(userPath($this->user->id), 'post'); ?>
  
    <?= $form->label('login'); ?>
    <?= $form->textField('login'); ?>
  
    <?= $form->passwordField('password'); ?>
  
    <?= $form->checkboxField('cool'); ?>
    <?= $form->hiddenField('hidden_field'); ?>
  
    <?= $form->textArea('description'); ?>
  
    <?= $form->selectField('role', ['admin' => 'Admin', 'user' => 'User']); ?>
  
  <?= $form->end(); ?>

In the first line
::

  <? $form = formFor($this->user); ?>
  
we create a form for *user* object. All methods in ModelFormBuilder take field name as the first parameter and optionally array of options (class, id etc.)

Input values are taken from model object.
Input names are inferred from model class name and field name.
For instance, User's field *login* will have ``name="user[login]"`` and ``id="user_login"``.


label
~~~~~
Creates a label tag.

**Parameters:** ``$field``, ``$options = []``

**Example:**
::

  $form->label("name", ['class' => 'pretty'])
  //=> <label for="name" class="pretty">A Label</label>
  // assuming that there's a translation for modelName.name e.g. user.name => A Label

hiddenField
~~~~~~~~~~~
Creates a hidden input tag.

**Parameters:** ``$field``, ``$options = []``

**Example:**
::

  $form->hiddenField("name", ['id' => 'my-id'])
  //=> <input type="hidden" id="my-id" name="user[name]" value="">

textField
~~~~~~~~~
Creates a text input tag.

**Parameters:** ``$name``, ``$value``, ``$attributes = []``

**Example:**
::

  $form->textField('login')
  //=> <input type="text" id="user_login" name="user[login]" value="thulium">

textArea
~~~~~~~~
Creates a textarea tag.

**Parameters:** ``$field``, ``$options = []``

**Example:**
::

  $form->textArea("name")
  //=> <textarea id="user_name" name="user[name]"></textarea>

checkboxField
~~~~~~~~~~~~~
Creates a checkbox input tag.

**Parameters:** ``$field``, ``$options = []``

**Example:**
::

  $form->checkboxField("cool",  ['class' => 'my-class'])
  //=>
  //<input type="checkbox" value="1" id="user_cool" name="user[cool]" class="my-class">
  //<input name="user[cool]" type="hidden" value="0">

selectField
~~~~~~~~~~~
Creates a select tag.

**Parameters:** ``$field``, ``$items = []``, ``$options = []``, ``$promptOption = null``

**Example:**
::

  $form->selectField('person', ['bob' => 'Bob', 'fred' => 'Fred'], ['class' => "my-select"], 'select person')
  //=>
  //<select id="user_person" name="user[person]" class="my-select">
  //  <option value="" selected="">select person</option>
  //  <option value="bob">Bob</option><option value="fred">Fred</option>
  //</select>

passwordField
~~~~~~~~~~~~~
Creates a password input tag.

**Parameters:** ``$field``, ``$options = []``

**Example:**
::

  $form->passwordField("name",  ['class' => 'my-class'])
  //=>
  //<input type="password" id="user_password" name="user[password]" value="value">
