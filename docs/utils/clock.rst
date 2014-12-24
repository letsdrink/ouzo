Clock
===========

Clock is a better DateTime.

Clock has plus<interval>($count) and minus<interval>($count) methods that return a modified copy of a Clock object.

**Example:**
::

    $string = Clock::now()
        ->plusYears(1)
        ->plusMonths(2)
        ->minusDays(3)
        ->format();


Clock provides time travel and time freezing capabilities, making it simple to test time-dependent code.

Clock::freeze sets time to a specific point so that each subsequent call to Clock::now() will return fixed time.

**Example:**
::

    //given
    Clock::freeze('2011-01-02 12:34');

    //when
    $result = Clock::nowAsString('Y-m-d');

    //then
    $this->assertEquals('2011-01-02', $result);



You can obtain a Clock set to a specific point in time.

::

    $result = Clock::at('2011-01-02 12:34');


You can convert Clock to DateTime:

::

    $result = Clock::now()->toDateTime();

You can convert Clock to a string using the specified format:

::

    $result = Clock::now()->format('Y-m-d H:i:s');

