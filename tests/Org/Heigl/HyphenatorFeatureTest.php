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
 * @version   2.0.1
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest;

use \Org\Heigl\Hyphenator as h;

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     20.04.2009
 */
class HyphenatorFeatureTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * @dataProvider hyphenationOfSingleWordWithArrayOutputProvider
	 */
    public function testHyphenationOfSingleWordWithArrayOutput($word, $language, $expected)
    {
        $o = new h\Options();
        $o->setHyphen('-')
          ->setDefaultLocale($language)
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(5)
          ->setFilters('NonStandard')
          ->setTokenizers('Whitespace','Punctuation');
        
        $h = new h\Hyphenator();
        $h->setOptions($o);
        var_Dump($h->hyphenate($word));
        $this->assertEquals($expected, $h->hyphenate($word));
        
    }
    
    public function hyphenationOfSingleWordWithArrayOutputProvider()
    {
    	return array(
    			array('donaudampfschifffahrt', 'de_DE', array('do-naudampfschifffahrt', 'donau-dampfschifffahrt', 'donaudampf-schifffahrt', 'donaudampfschiff-fahrt')),
    		);
    }
}
