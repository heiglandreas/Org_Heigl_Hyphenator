Usage-Examples
##############

Prerequisites
=============

Before the ``Hyphenator`` can be used you have to
add the provided autoloader either using your own autoloader or by
invoking the ``Hyphenators`` own autoloader

::
    
    <?php
    require_once 'path/to/Org/Heigl/Hyphenator/Hyphenator.php';
    \Org\Heigl\Hyphenator\Hyphenator::registerAutoload()

Simple Example
==============

::
    
    <?php
    use \Org\Heigl\Hyphenator as h;
    $hyphenator = h\Hyphenator::factory();
    $result = $hyphenator->hyphenate('This is your text to be hyphenated');

Plainly create a ``Hyphenator``-Object via the
factory-method and invoke its ``hyphenate``-method.

It will be hard to make it more simple.

Simple example with non-standard Hyphenator-Home-folder
=======================================================

::

    <?php
    use \Org\Heigl\Hyphenator as h;
    $hyphenator = h\Hyphenator::factory('/path/to/home/directory');
    $result = $hyphenator->hyphenate('This is your text to be hyphenated');

Of course the Hyphenators home-folder has to be available in the
given location and has to be writeable to hte user executing the
hyphenator (normaly the webserver-user).

Invoke the ``Hyphenator`` manually
==================================

::
    
    <?php
    use \Org\Heigl\Hyphenator as h;
    $o = new h\Options();
    $o->setHyphen('-')
      ->setDefaultLocale('de_DE')
      ->setRightMin(2)
      ->setLeftMin(2)
      ->setWordMin(5)
      ->setFilters('Simple')
      ->setTokenizers('Whitespace','Punctuation');
    $h = new h\Hyphenator();
    $h->setOptions($o);
    echo $h->hyphenate('We have some really long words in german like sauerstofffeldflasche.');
    // prints We have some re-al-ly long words in ger-man like sau-er-stoff-feld-fla-sche.
    // Thanks to lsmith for the idea!

.. warning::

   Performance-Hint: Due to the sometimes rather large hyphenation-pattern-files it
   might be a good idea to cache the hyphenator after instantiation to
   reuse the once created instance.
   Reading the largest hyphenation-pattern-file takes up to one
   second on a 2.5GHz Intel Core2 Duo using 4GB RAM.
