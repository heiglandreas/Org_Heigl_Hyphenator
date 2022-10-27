Installation
############

Installation using packagist
============================

The ``Hyphenator``-package can be installed via packagist.

.. code-block:: bash

    php composer.phar require org_heigl/hyphenator

Installation from a downloaded package
======================================

This installation is not much more complicated and probably the
best way for hosted installations

#. Take the :file:`Org`-Folder and place it somewhere your include-path reaches
   it.
#. Optionally you can copy the folder :file:`Org/Heigl/Hyphenator/shared`
   to any location you like and set the ``HYPHERNATOR_HOME``-Environment
   Variable or PHP-constant to that path before invoking the Hyphenator
   for the first time.
#. Register the autoloader by calling Org\Heigl\Hyphenator\Hyphenator::registerAutoload()
#. Hyphenate!

Installed Hyphenation-Patterns
==============================

This package includes hyphenation-patterns for the following locales.
These are taken from the svn-directory of the OpenOffice.org-CVS hosted at
apache.org. For more inforamtions have a look at `https://svn.apache.org/repos/asf/incubator/ooo/trunk/main/dictionaries <None>`_.
This Link will break as soon as the OpenOffice.org-Project comes out
of the apache-incubator.

- af_ZA
- bg_BG
- ca
- cs_CZ
- da_DK
- de_AT
- de_CH
- de_DE
- el_GR
- en_GB
- en_UK
- es
- et_EE
- fr
- gl
- hr_HR
- hu_HU
- is_ID
- is
- it_IT
- lt
- lt_LT
- lv_LV
- nb_NO
- nn_NO
- nl_NL
- pl_PL
- pt_BR
- pt_PT
- ro_RO
- ru_RU
- sh
- sk_SK
- sl_SI
- sr
- sv
- te_IN
- uk_UA
- zu_ZA

These are the hyphenation-files that are included in OpenOffice.org.
If you found another hyphenation-file, feel free to contact me or
the OpenOffice.org-Team!

All other locales will simply not be hyphenated but the string to be
hyphenated will be returned "AS IS"
