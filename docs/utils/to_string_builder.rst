ToStringBuilder
===============

Assists in implementing ``__toString()`` methods.

To use this class write code as follows:

::

    namespace Application\Model;

    class Person
    {
        /** @var string */
        private $name;
        /** @var int */
        private $age;
        /** @var boolean */
        private $smoking;
        /** @var array */
        private $tags;

        public function __construct($name, $age, $smoking, $tags)
        {
            $this->name = $name;
            $this->age = $age;
            $this->smoking = $smoking;
            $this->tags = $tags;
        }

        public function __toString()
        {
            return (new ToStringBuilder($this))
                ->append('name', $this->name)
                ->append('age', $this->age)
                ->append('smoking', $this->smoking)
                ->append('tags', $this->tags)
                ->toString();
        }
    }

This will produce a ``__toString`` of the format: ``Application\Model\Person[name=jon,age=91,smoking=true,tags={tag1,tag2,another tag}]``

There are several possible styles:

----

ToStringStyle::defaultStyle()
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The default ``__toString`` style.

**Example:**

``Application\Model\Person[name=jon,age=91,smoking=true,tags={tag1,tag2,another tag}]``

----

ToStringStyle::noFieldNamesStyle()
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The no field names style.

**Example:**

``Application\Model\Person[jon,91,true,{tag1,tag2,another tag}]``

----

ToStringStyle::shortPrefixStyle()
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The short class name style.

**Example:**

``Person[name=jon,age=91,smoking=true,tags={tag1,tag2,another tag}]``

----

ToStringStyle::simpleStyle()
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The simple style.

**Example:**

``jon,91,true,{tag1,tag2,another tag}``

----

ToStringStyle::noClassNameStyle()
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The no class names style.

**Example:**

``[name=jon,age=91,smoking=true,tags={tag1,tag2,another tag}]``

----

ToStringStyle::multiLineStyle()
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The multi line style.

**Example:**

::

    Application\Model\Person[
      name=jon
      age=91
      smoking=true
      tags={tag1,tag2,another tag}
    ]


