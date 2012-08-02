Prerequisites
#############

This Hyphenation-Package has the following requirements:

- PHP >= 5.3
- mbstring-extension
- Currently all input has to be UTF-8 encoded

.. warning::

   CAVEAT: On loading ``Org\Heigl\Hyphenator\Hyphenator``
   the internal encoding of the ``mbstring``-extension will
   be set to UTF-8. When you are using something different you have to
   call ``mb_internal_encoding('UTF-8')`` before invoking any
   of the Hyphenators methods. Otherwise the results might be completely
   unpredictable!
   