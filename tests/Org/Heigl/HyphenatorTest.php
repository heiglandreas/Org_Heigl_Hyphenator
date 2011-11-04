<?php
/**
 * Copyright (c) 2008-2011 Andreas Heigl<andreas@heigl.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.alpha
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest;

use \Org\Heigl\Hyphenator as h;

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Org_Heigl_Hyphenator */
require_once 'Org/Heigl/Hyphenator/Hyphenator.php';

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.alpha
 * @since     20.04.2009
 */
class HyphenatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingHyphenatorReturnsInstance()
    {
        $hyphenator = new h\Hyphenator();
        $this->assertInstanceOf('\Org\Heigl\Hyphenator\Hyphenator', $hyphenator);
    }

    public function testSettingOptions()
    {
        $hyphenator = new h\Hyphenator();
        $options = new h\Options\Options();
        $this->assertAttributeEquals(null,'_options', $hyphenator);
        $hyphenator->setOptions($options);
        $this->assertAttributeSame($options, '_options', $hyphenator);
        $this->assertSame($options,$hyphenator->getOptions());
    }

    public function  testSettingDictionaries()
    {
        $hyphenator = new h\Hyphenator();
        $this->assertAttributeInstanceof('\Org\Heigl\Hyphenator\Dictionary\DictionaryRegistry', '_dicts', $hyphenator);
        $dict = new h\Dictionary\Dictionary('de');
        $hyphenator->addDictionary($dict);

    }


    public function setup ()
    {
        //
    }
}
