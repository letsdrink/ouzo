FormHelper
==========

View helper methods for generating form markup.

----

escapeText
~~~~~~~~~~
Convert special characters to HTML entities

**Parameters:** ``$text``

----

escapeNewLine
~~~~~~~~~~~~~
Changes new lines to ``&lt;br&gt;`` and converts special characters to HTML entities.

**Parameters:** ``$text``

----

linkTo
~~~~~~
Creates a link tag.

**Parameters:** ``$name``, ``$href``, ``$attributes = []``

**Example:**
::

    linkTo("Name", "url", ['class' => 'btn'])

**Result:**
::

    <a href="url" class="btn">Name</a>

----

linkButton
~~~~~~~~~~
Creates a button tag.

**Parameters:** ``$params``

----

labelTag
~~~~~~~~
Creates a label tag.

**Parameters:** ``$name``, ``$content``, ``$attributes = []``

**Example:**
::

    labelTag("name", "A Label", ['class' => 'pretty'])

**Result:**
::

    <label for="name" class="pretty">A Label</label>

----

hiddenTag
~~~~~~~~~
Creates a hidden input tag.

**Parameters:** ``$name``, ``$value``, ``$attributes = []``

**Example:**
::

    hiddenTag("name", "value", ['id' => 'my-id'])

**Result:**
::

    <input type="hidden" id="my-id" name="name" value="value">

----

textFieldTag
~~~~~~~~~~~~
Creates a text input tag.

**Parameters:** ``$name``, ``$value``, ``$attributes = []``

**Example:**
::

    textFieldTag("name", "value", ['id' => 'my-id'])

**Result:**
::

    <input type="text" id="my-id" name="name" value="value">

----

textAreaTag
~~~~~~~~~~~
Creates a textarea tag.

**Parameters:** ``$name``, ``$content``, ``$attributes = []``

**Example:**
::

    textAreaTag("name", "Content", ['id' => 'my-id'])

**Result:**
::

    <textarea id="my-id" name="name">Content</textarea>

----

checkboxTag
~~~~~~~~~~~
Creates a checkbox input tag.

**Parameters:** ``$name``, ``$value``, ``$checked``, ``$attributes = []``

**Example:**
::

    checkboxTag("name", "true", true,  ['class' => 'my-class'])

**Result:**
::

    <input name="name" type="hidden" value="0">
    <input type="checkbox" value="true" id="name" name="name" class="my-class" checked="">

----

selectTag
~~~~~~~~~
Creates a select tag.

**Parameters:** ``$name``, ``$items = []``, ``$value``, ``$attributes = []``, ``$promptOption = null``

**Example:**
::

    selectTag('status', ['bob' => 'Bob', 'fred' => 'Fred'], ['bob'], ['class' => "my-select"])

**Result:**
::

    <select id="status" name="status" class="my-select">
        <option value="bob" selected="">Bob</option>
        <option value="fred">Fred</option>
    </select>

----

passwordFieldTag
~~~~~~~~~~~~~~~~
Creates a password input tag.

**Parameters:** ``$name``, ``$value``, ``$attributes = []``

**Example:**
::

    passwordFieldTag("name", "value",  ['class' => 'my-class'])

**Result:**
::

    <input type="password" value="value" id="name" name="name" class="my-class" />

----

radioButtonTag
~~~~~~~~~~~~~~
Creates radio tag.

**Parameters:** ``$name``, ``$value``, ``$attributes = []``

**Example:**
::

    radioButtonTag('age', 33);

**Result:**
::

    <input type="radio" id="age" name="age" value="33"/>

----

formTag
~~~~~~~
Creates a form tag.

**Parameters:** ``$url``, ``$method = 'POST'``, ``$attributes = []``

**Example:**
::

    formTag('url', 'post', ['class' => "my-select"])

**Result:**
::

    <form class="my-select" action="url" method="POST">

----

endFormTag
~~~~~~~~~~
Creates end form tag.

**Example:**
::

    endFormTag()

**Result:**
::

    </form>

----

formFor
~~~~~~~
Creates :doc:`./model_form_builder` for specific model object.

**Parameters:** ``$model``
