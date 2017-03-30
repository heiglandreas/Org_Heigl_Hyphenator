[![Build Status](https://travis-ci.org/heiglandreas/Org_Heigl_Hyphenator.png?branch=master)](https://travis-ci.org/heiglandreas/Org_Heigl_Hyphenator)
[![Latest Stable Version](https://poser.pugx.org/org_heigl/hyphenator/v/stable.png)](https://packagist.org/packages/org_heigl/hyphenator)
[![Total Downloads](https://poser.pugx.org/org_heigl/hyphenator/downloads.png)](https://packagist.org/packages/org_heigl/hyphenator)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/heiglandreas/Org_Heigl_Hyphenator/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/heiglandreas/Org_Heigl_Hyphenator/?branch=master)
[![Stories in Ready](https://badge.waffle.io/heiglandreas/Org_Heigl_Hyphenator.png?label=ready)](https://waffle.io/heiglandreas/Org_Heigl_Hyphenator)  

This library provides TeX-Hyphenation in PHP.

# Requirements:

This package has the following requirements:

* PHP-Version >= 5.6
* Multibyte-Extension loaded
* Input has to be UTF8-encoded.

On loading the ````\Org\Heigl\Hyphenator\Hyphenator```-class the internal encoding for
the Multibyte-String-Extension will be set to UTF8.

# Installation: 
 
There are three ways to install this package:

 * via PEAR 
 * via composer
 * copy the 'Org/Heigl/Hyphenator'-folder somewhere to your PHP-include-
   directory.

More information can be found in the doc-section

# Usage: 

```php
<?php
use \Org\Heigl\Hyphenator as h;
// Create a hyphenator-instance based on a given config-file
$hyphenator = h\Hyphenator::factory('/path/to/the/config/file.properties');
 
// And hyphenate a given string
$hyphenatedText = $hyphenator->hyphenate($string);
   
echo $hyphenatedText;
?>
```

# Documentation:
 
More documentation can be found at http://orgheiglhyphenator.readthedocs.org/en/latest/

Build-Status of the latest release can be found at http://travis-ci.org/#!/heiglandreas/Org_Heigl_Hyphenator

# Legal Stuff

Copyright (c) 2011-2016 Andreas Heigl<andreas@heigl.org>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

