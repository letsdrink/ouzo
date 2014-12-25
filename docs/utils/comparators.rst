Comparators
===========

Comparators are used to determine the order of objects in ``Arrays::sort``. It is a flexible mechanism to compare objects.
Ouzo provides various comparators out of the box and the ability to write your custom comparators.

``Comparator`` class is a facade which contains all comparators:
* natural
* reverse
* compareBy
* compound

Natural order
~~~~~~~~~~~~~

As simple as it gets:

::

    Arrays::sort([1, 3, 2], Comparator::natural());

It sorts given array in a natural order, so the result would be ``1, 2, 3``.

Reverse
~~~~~~~

It is a comparator according to which order of elements is reversed. It expects another comparator as a parameter. E.g.

::

    Arrays::sort([1, 3, 2], Comparator::reverse(Comparator::natural()));

Result is obviously a reversed array of natural order, which is ``3, 2, 1``. Any comparator may be passed as a parameter.
Combining comparators? Just imagine the possibilities!

Compare by
~~~~~~~~~~

Compares objects by using values computed using given expression. Expression should comply with format accepted by
``Functions::extractExpression``.

Imagine you have ``Product`` and you want to sort it by its ``name`` property. No problem:

::

    $product1 = new Product(['name' => 'b']);
    $product2 = new Product(['name' => 'c']);
    $product3 = new Product(['name' => 'a']);

    $result = Arrays::sort([$product1, $product2, $product3], Comparator::compareBy('name'));

In case you haven't heard of Ouzo's assertions, here is the simplest way to test if the above is true:

::

    Assert::thatArray($result)->onProperty('name')->containsExactly('a', 'b', 'c');

Compound
~~~~~~~~

Combines comparators into one, ordered by first comparator. If two values are equal according to the first comparator (tie),
then tie breakers resolve conflicts. Second provided comparator is the first tie breaker, third is the second tie breaker and so on.

Example:

::

    $product1 = new Product(['name' => 'a', 'description' => '2']);
    $product2 = new Product(['name' => 'b', 'description' => '2']);
    $product3 = new Product(['name' => 'a', 'description' => '1']);

    Arrays::sort([$product1, $product2, $product3],
        Comparator::compound(
            Comparator::reverse(Comparator::compareBy('name')),
            Comparator::compareBy('description')
        )
    );

Now, let's analyze it:
# products are sorted by ``name`` property (a, a, b)
# reversed (b, a, a)
# there is a conflict (a = a)
# so a tie breaker goes to work
# ties are sorted by 'description' property (b, a1, a2)

Voila!

Custom comparators
~~~~~~~~~~~~~~~~~~

If you want to write your own comparator the only thing you need to do is to create a class with ``__invoke`` method
implemented.

Comparator returns an integer less than, equal to, or greater than zero if the first argument is considered to be
respectively less than, equal to, or greater than the second.

Take a look at ``Ouzo\Utilities\Comparator`` classes for more details.
