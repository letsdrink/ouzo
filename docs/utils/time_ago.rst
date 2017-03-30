TimeAgo
=======

This class is intended to format date in a "time ago" manner.
``TimeAgo`` class returns key calculated for date e.g. current date returns ``timeAgo.justNow``.
Additionally it returns array with parameters for the key e.g. ``timeAgo.yesterdayAt`` has parameter named ``label`` which contains value.

**Example:**
::

    $currentDate = '2012-02-20 12:00';
    Clock::freeze($currentDate);
    $timeAgo = TimeAgo::create('2012-02-20 11:00');

    $timeAgo->getKey(); //timeAgo.todayAt
    $timeAgo->getParams(); //['label' => '11:00']

.. note::

    Returned key can be used with Ouzo's :doc:`I18n <i18n>` methods to do the translation .

----

timeAgo.justNow
~~~~~~~~~~~~~~~
Returned when difference between current date and given date is *less or equal than 60 seconds*.

**Params:** ``none``

----

timeAgo.minAgo
~~~~~~~~~~~~~~
Returned when difference between current date and given date is *greater than 60 seconds* and *less or equal than 60 minutes*.

**Params:** ``['label' => $minutesAgo]``

----

timeAgo.todayAt
~~~~~~~~~~~~~~~
Returned when *day is the same* and difference between current date and given date is *greater than 60 minutes* and *less or equal than 24 hours*.

**Params:** ``['label' => $date->format('H:i')]``

----

timeAgo.yesterdayAt
~~~~~~~~~~~~~~~~~~~
Returned when *day is yesterday*.

**Params:** ``['label' => $date->format('H:i')]``

----

timeAgo.thisYear
~~~~~~~~~~~~~~~~
Returned when *year is the same*.

**Params:** ``['day' => $date->format('j'), 'month' => 'timeAgo.month.' . $date->format('n')]``

.. note::

    Parameter ``month`` has value in a format: ``timeAgo.month.<number of month>``.
    Number of month is between 1..12 (January..December).

----

Date is returned
~~~~~~~~~~~~~~~~
If date is returned e.g. ``2014-01-10`` it means that *date is before the current year*.

**Params:** ``none``
