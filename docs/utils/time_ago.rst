TimeAgo
=======

This class could be use to the formatting date in "time ago" format.
``TimeAgo`` class return key with calculated for concrete date e.g. ``timeAgo.justNow``.
Additionally returns parameters for the key e.g. key ``timeAgo.yesterdayAt`` has parameters named ``label`` which contains value.

**Example:**
::

    $currentDate = '2012-02-20 12:00';
    Clock::freeze($currentDate);
    $timeAgo = TimeAgo::create('2012-02-20 11:00');

    $timeAgo->key; //timeAgo.todayAt
    $timeAgo->params; //array('label' => '11:00')

.. note::

    Returned key can be interpreted with Ouzo :doc:`I18n <i18n>` mechanism.

----

timeAgo.justNow
~~~~~~~~~~~~~~~
Key which is returning when diff between current date and date is *less or equal than 60 seconds*.

**Params:** ``array()``

----

timeAgo.minAgo
~~~~~~~~~~~~~~
Key which is returning when diff between current date and date is *greater than 60 seconds* and *less or equal than 60 minutes*.

**Params:** ``array('label' => $minutesAgo)``

----

timeAgo.todayAt
~~~~~~~~~~~~~~~
Key which is returning when *day is the same* and diff between current date and date is *greater than 60 minutes* and *less or equal than 24 hours*.

**Params:** ``array('label' => $date->format('H:i'))``

----

timeAgo.yesterdayAt
~~~~~~~~~~~~~~~~~~~
Key which is returning when *day is yesterday*.

**Params:** ``array('label' => $date->format('H:i'))``

----

timeAgo.thisYear
~~~~~~~~~~~~~~~~
Key which is returning when *year is the same*.

**Params:** ``array('day' => $date->format('j'), 'month' => 'timeAgo.month.' . $date->format('n'))``

.. note::

    Parameter ``month`` returns key in format ``timeAgo.month.<number of month>`` which should be translated.
    Number of month is list which 1 is January ... 12 is December.

----

Key is a date
~~~~~~~~~~~~~
If key is a date e.g. ``2014-01-10`` this mean that *date is before the year*.

**Params:** ``array()``
