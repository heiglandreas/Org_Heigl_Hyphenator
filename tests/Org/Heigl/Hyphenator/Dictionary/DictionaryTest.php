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
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.alpha
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest\Dictionary;

use Org\Heigl\Hyphenator\Dictionary\Dictionary;

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.alpha
 * @since     02.11.2011
 */
class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingDefaultFilePath()
    {
        $this->assertAttributeEquals('','_fileLocation', '\Org\Heigl\Hyphenator\Dictionary\Dictionary');
        Dictionary::setFileLocation('foo');
        $this->assertAttributeEquals('foo','_fileLocation', '\Org\Heigl\Hyphenator\Dictionary\Dictionary');
    }

    public function testParsingOnDictionaryCreationWorks()
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        @unlink(__DIR__.'/share/de.ini');
        $dict = Dictionary::factory('de');
        $this->assertTrue(file_Exists(__DIR__ . '/share/de.ini'));
        $this->assertTrue('UTF-8' == mb_detect_encoding(file_get_contents(__DIR__ . '/share/de.ini')));
    }

    public function testGettingPatterns()
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        $dict = Dictionary::factory('de');
        $result = $dict->getPatternsForWord('täßterei');
        $this->assertEquals(array('täßt'=>'0002'),$result);
    }

    public function testSettingPatterns()
    {
        $dictionary = new Dictionary();
        $dictionary->addPattern('test', '01234');
        $this->assertAttributeEquals(array('test'=>'01234'),'_dictionary',$dictionary);
    }

}
