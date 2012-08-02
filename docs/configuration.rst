Configuration-Options
#####################

The ``Org\Heigl\Hyphenator``-Package can be configured
using the :file:`Hyphenator.properties`-File inside
the defined ``HYPHENATOR_HOME``-Directory.

The ``HYPHENATOR_HOME``-Directory will be retrieved on
Instantiation of the ``Hyphenator`` by checking
the following possibilities.

#. Check for a folder defined via ``Org\Heigl\Hyphenator::setDefaultHomePath``.

#. Check for a PHP-constant ``HYPHENATOR_HOME``.

#. Check for an environment-variable ``HYPHENATOR_HOME``.

#. Use the in the package included :file:`share`-folder

You can also configure the ``Hyphenator`` via an
``Org\Heigl\Hyphenator\Options\Options``-Instance
that can either be retrieved from or replace an existing
Options-Instance in an existing Hyphenator-Object;

::

    $hyphenator = Hyphenator::factory();
    // Retrieve an existing Options-Instance
    $options = $hyphenator->getOptions();
    // Set an Options
    $options->setLeftMin(3);
    // You can also cascade that.
    $hyphenator->getOptions()->setLeftMin(3);
    // Or you can create a new Options-Object
    $options = new \Org\Heigl\Hyphenator\Options\Options();
    $options->setLeftMin(3);
    $hyphenator->setOptions($options);
    // CAVEAT: This will also set all other Options to the default values!

The following configuration-Options can be used.

The configuration file is named :file:`Hyphenator.properties`
and is located in the top-level of the ``HYPHENATOR_HOME``-Directory.

``hyphen``
    This character shall be used as Hyphen-Character.
    This defaults to the soft-hyphen-character U+00AD (``SOFT-HYPHEN``)
    This can also be set using
    ``Org\Heigl\Hyphenator\Option::setHyphen()``

``leftMin``
    How many characters have to be left unhyphenated to the left
    of the word.
    This has to be an integer value and defaults to 2
    This can also be set using
    ``Org\Heigl\Hyphenator\Option::setLeftMin()``

``rightMin``
    How many characters have to be left unhyphenated to the right
    of the word.
    This has to be an integer value and defaults to 2
    This can also be set using
    ``Org\Heigl\Hyphenator\Option::setRightMin()``

``wordMin``
    Words under the given length will not be hyphenated altogether.
    It makes sense to set option to a higher value than the sum of
    rightMin and leftMin.
    This defaults to 6
    This can also be set using
    ``Org\Heigl\Hyphenator\Option::setWordMin()``

``quality``
    How good shal the hyphenation be. The higher the number the
    better. THis can be any integer from 0 (no Hyphenation at all)
    through 9 (berst hyphernation).
    This defaults to 9.
    .. warning::
    Change this only if you know what you do!
    This can also be set using
    ``Org\Heigl\Hyphenator\Option::setQuality()``

``defaultLocale``
    This parameter defines what dictionary to use by default
    for hyphenation.
    This can also be set using
    ``Org\Heigl\Hyphenator\Option::setdefaultLocale()``
    This value will be overwritten by the second parameter of
    ``Org\Heigl\Hyphenator\Hyphenator::factory()``.

``tokenizers``
    A comma-separated list of tokenizers to use for splitting the
    text to be hyphenated into hypheable chunks.
    The tokenizers have to implement the
    ``Org\Heigl\Tokenizer\Tokenizer``-interface.
    The tokenizers can be given by using the Part of
    the Classname before the "Tokenizer". So for the ``WhitespaceTokeinzer``
    it would suffice to use "Whitespace" as name of the tokenizer.
    Tokenizers can also be set using
    ``Org\Heigl\Hyphenator\Hyphenator::addTokenizer()``

``filters``
    A comma-separated list of filters to use for postprocessing the
    hyphenated text
    The filters have to extend the
    ``Org\Heigl\Filter\Filter``-class.
    The filters can be given by using the Part of
    the Classname before the "Filter". So for the ``SimpleFilter``
    it would suffice to use "Simple" as name of the filter.
    Filters can also be set using
    ``Org\Heigl\Hyphenator\Hyphenator::addFilter()``

