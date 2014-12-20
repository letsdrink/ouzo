View helper methods for generating form markup.


## escapeText
Convert special characters to HTML entities

**Parameters:** `$text`

## escapeNewLine
Changes new lines to &lt;br&gt; and converts special characters to HTML entities.

**Parameters:** `$text`

## linkTo
Creates a link tag.

**Parameters:** `$name`, `$href`, `$attributes = array()`

**Example:**
```php
linkTo("Name", "url", array('class' => 'btn')) 
//=> <a href="url" class="btn">Name</a>
```

## labelTag
Creates a label tag.

**Parameters:** `$name`, `$content`, `$attributes = array()`

**Example:**
```php
labelTag("name", "A Label", array('class' => 'pretty')) 
//=> <label for="name" class="pretty">A Label</label>
```

## hiddenTag
Creates a hidden input tag.

**Parameters:** `$name`, `$value`, `$attributes = array()`

**Example:**
```php
hiddenTag("name", "value", array('id' => 'my-id')) 
//=> <input type="hidden" id="my-id" name="name" value="value">
```

## textFieldTag
Creates a text input tag.

**Parameters:** `$name`, `$value`, `$attributes = array()`

**Example:**
```php
textFieldTag("name", "value", array('id' => 'my-id')) 
//=> <input type="text" id="my-id" name="name" value="value">
```

## textAreaTag
Creates a textarea tag.

**Parameters:** `$name`, `$content`, `$attributes = array()`

**Example:**
```php
textAreaTag("name", "Content", array('id' => 'my-id')) 
//=> <textarea id="my-id" name="name">Content</textarea>
```

## checkboxTag
Creates a checkbox input tag.

**Parameters:** `$name`, `$value`, `$checked`, `$attributes = array()`

**Example:**
```php
checkboxTag("name", "true", true,  array('class' => 'my-class')) 
//=>
//<input name="name" type="hidden" value="0">
//<input type="checkbox" value="true" id="name" name="name" class="my-class" checked="">

```

## selectTag
Creates a select tag.

**Parameters:** `$name`, `$items = array()`, `$value`, `$attributes = array()`, `$promptOption = null`

**Example:**
```php
selectTag('status', array('bob' => 'Bob', 'fred' => 'Fred'), array('bob'), array('class' => "my-select"))
//=>
//<select id="status" name="status" class="my-select">
//  <option value="bob" selected="">Bob</option>
//  <option value="fred">Fred</option>
//</select>

```

## passwordFieldTag
Creates a password input tag.

**Parameters:** `$name`, `$value`, `$attributes = array()`

**Example:**
```php
passwordFieldTag("name", "value",  array('class' => 'my-class')) 
//=>
//<input type="password" value="value" id="name" name="name" class="my-class" />

```


## formTag
Creates a form tag.

**Parameters:** `$url`, `$method`, `$attributes = array()`

**Example:**
```php
formTag('url', 'post', array('class' => "my-select"))
//=>
//<form class="my-select" action="url" method="POST">

```