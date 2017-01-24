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
 * @version   2.0.1
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
 * @version   2.0.1
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

    public function testParsingOnDictionaryCreationDoesNotWorks()
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        @unlink(__DIR__.'/share/de.ini');
        $dict = Dictionary::factory('de');
        $this->assertFalse(file_Exists(__DIR__ . '/share/de.ini'));
    }

    public function testParsingWrongLocaleWorks()
    {
        Dictionary::setFileLocation(__DIR__ . '/../share/test3/files/dictionaries');
        $dict = Dictionary::factory('de-de');
        $this->assertAttributeNotEquals(array(),'_dictionary',$dict);
    }

    public function testGettingPatterns()
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        $dict = Dictionary::factory('de-DE');
        $result = $dict->getPatternsForWord('täßterei');
        $this->assertEquals(array('täßt'=>'00020'),$result);
    }

    public function testSettingPatterns()
    {
        $dictionary = new Dictionary();
        $dictionary->addPattern('test', '01234');
        $this->assertAttributeEquals(array('test'=>'01234'),'_dictionary',$dictionary);
    }

    public function testCreationOfNotExistentLocale()
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        $dictionary = Dictionary::factory('xx_XX');
        $this->assertAttributeEquals(array(),'_dictionary',$dictionary);
        $result = $dictionary->getPatternsForWord('Donaudampfschifffahrtskapitänsmütze');
        $this->assertEquals(array(),$result);
    }

    public function testParsingDicFilesWorks()
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        @unlink(__DIR__.'/share/de_TE.ini');
        $dict = Dictionary::parseFile('de_TE');
        $this->assertTrue(file_Exists($dict));
        $this->assertTrue('UTF-8' == mb_detect_encoding(file_get_contents($dict)));
        $this->assertEquals(file_get_contents(__DIR__.'/share/de_TE.default.ini'),file_get_contents($dict));
        try{
            $dict = Dictionary::parseFile('foobar');
            $this->fail('This should have raised an exception!');
        }catch(\Org\Heigl\Hyphenator\Exception\PathNotFoundException $exception){
            $this->assertTrue(true);
        }
    }

    /**
     * @dataProvider localeUnificationProvider
     *
     * @param $parameter
     * @param $expected
     */
    public function testLocaleUnification($parameter, $expected)
    {
        $obj = new \Org\Heigl\Hyphenator\Dictionary\Dictionary();
        $method = \UnitTestHelper::getMethod($obj, '_unifyLocale');
        $result = $method->invoke($obj,$parameter);

        $this->assertEquals($expected, $result);

    }

    public function localeUnificationProvider()
    {
        return array(
            array('de', 'de'),
            array('DE', 'de'),
            array('de de', 'de_DE'),
            array('fo_BA', 'fo_BA'),
            array('DE,de', 'de_DE'),
            array('fooBar', 'fooBar'),
        );
    }
}
