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
class HyphenatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatingHyphenatorReturnsInstance()
    {
        $hyphenator = new h\Hyphenator();
        $this->assertInstanceOf('\Org\Heigl\Hyphenator\Hyphenator', $hyphenator);
        $this->assertAttributeInstanceof('\Org\Heigl\Hyphenator\Dictionary\DictionaryRegistry', '_dicts', $hyphenator);
        $this->assertAttributeInstanceof('\Org\Heigl\Hyphenator\Filter\FilterRegistry', '_filters', $hyphenator);
        $this->assertAttributeInstanceof('\Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry', '_tokenizers', $hyphenator);
    }

    public function testSettingOptions()
    {
        $hyphenator = new h\Hyphenator();
        $options = new h\Options();
        $this->assertAttributeEquals(null,'_options', $hyphenator);
        $hyphenator->setOptions($options);
        $this->assertAttributeSame($options, '_options', $hyphenator);
        $this->assertSame($options,$hyphenator->getOptions());
        $this->assertEquals(0,$hyphenator->getTokenizers()->count());
        $options->addTokenizer('whitespace');
        $this->assertSame($hyphenator,$hyphenator->setOptions($options));
        $this->assertAttributeSame($options, '_options', $hyphenator);
        $this->assertEquals(1,$hyphenator->getTokenizers()->count());

    }

    public function testGettingOption()
    {
        $h = new h\Hyphenator();
        $o = $h->getOptions();
        $this->assertInstanceOf('\ORg\Heigl\Hyphenator\Options', $o);
        $this->assertSame($o,$h->getOptions());
    }

    public function  testSettingDictionaries()
    {
        $hyphenator = new h\Hyphenator();
        $this->assertAttributeInstanceof('\Org\Heigl\Hyphenator\Dictionary\DictionaryRegistry', '_dicts', $hyphenator);
        $dict = new h\Dictionary\Dictionary('de');
        $this->assertSame($hyphenator,$hyphenator->addDictionary($dict));
        $this->assertEquals(1,$hyphenator->getDictionaries()->count());
        $this->assertSame($hyphenator,$hyphenator->addDictionary('en_US'));
        $this->assertEquals(2,$hyphenator->getDictionaries()->count());

    }
    public function  testSettingTokenizers()
    {
        $hyphenator = new h\Hyphenator();
        $dict = new h\Tokenizer\WhitespaceTokenizer();
        $hyphenator->addTokenizer($dict);
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry', $hyphenator->getTokenizers());
        $this->assertSame($dict, $hyphenator->getTokenizers()->getTokenizerWithKey(0));

    }
    public function  testSettingFilters()
    {
        $h = new h\Hyphenator();
        $h->getOptions()->setFilters(array());
        $f = new h\Filter\SimpleFilter();
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Filter\FilterRegistry', $h->getFilters());
        $this->assertEquals(0,$h->getFilters()->count());
        $this->assertSame($h,$h->addFilter($f));
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Filter\FilterRegistry', $h->getFilters());
        $this->assertEquals(1,$h->getFilters()->count());
        $this->assertSame($f, $h->getFilters()->getFilterWithKey(0));
        $this->assertSame($h,$h->addFilter('CustomMarkup'));
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Filter\FilterRegistry', $h->getFilters());
        $this->assertEquals(2,$h->getFilters()->count());
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Filter\CustomMarkupFilter', $h->getFilters()->getFilterWithKey(1));

    }

    public function testHomeDirectory()
    {
        $this->assertAttributeEquals(null,'_defaultHomePath','\Org\Heigl\Hyphenator\Hyphenator');
        $h = new h\Hyphenator();
        $baseDirectory1 = dirname(dirname(dirname(__DIR__))) . '/src/Org/Heigl/Hyphenator/share';
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory2 = __DIR__ . '/share/tmp1';
        mkdir($baseDirectory2);
        putenv('HYPHENATOR_HOME=' . $baseDirectory2 );
        $this->assertEquals($baseDirectory2, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory2, $h->getHomePath());
        rmdir($baseDirectory2);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory3 = __DIR__ . '/share/tmp2';
        mkdir($baseDirectory3);
        define('HYPHENATOR_HOME', $baseDirectory3 );
        $this->assertEquals($baseDirectory3, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory3, $h->getHomePath());
        rmdir($baseDirectory3);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory4 = __DIR__ . '/share/tmp3';
        mkdir($baseDirectory4);
        \Org\Heigl\Hyphenator\Hyphenator::setDefaultHomePath($baseDirectory4);
        $this->assertEquals($baseDirectory4, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory4, $h->getHomePath());
        rmdir($baseDirectory4);
        $this->assertEquals($baseDirectory1, \Org\Heigl\Hyphenator\Hyphenator::getDefaultHomePath());
        $this->assertEquals($baseDirectory1, $h->getHomePath());
        $baseDirectory5 = __DIR__ . '/share/tmp4';
        mkdir($baseDirectory5);
        $h->setHomePath($baseDirectory5);
        $this->assertAttributeEquals($baseDirectory5, '_homePath', $h);
        $this->assertEquals($baseDirectory5, $h->getHomePath());
        rmdir($baseDirectory5);
        $this->assertEquals($baseDirectory1, $h->getHomePath());
    }

    public function testSettingDefaultHomeDirectoryWithInvalidInfos()
    {
        $h = new h\Hyphenator();
        try{
            $h->setHomePath('foo');
            $this->fail('No Exception thrown');
        }catch(\Exception $e){
            $this->assertInstanceof('\Org\Heigl\Hyphenator\Exception\PathNotFoundException', $e);
        }
        try{
            $h->setHomePath(__FILE__);
            $this->fail('No Exception thrown');
        }catch(\Exception $e) {
            $this->assertInstanceof('\Org\Heigl\Hyphenator\Exception\PathNotDirException', $e);
        }
        try{
            \Org\Heigl\Hyphenator\Hyphenator::setDefaultHomePath('foo');
            $this->fail('No Exception thrown');
        }catch(\Exception $e){
            $this->assertInstanceof('\Org\Heigl\Hyphenator\Exception\PathNotFoundException', $e);
        }
        try{
            \Org\Heigl\Hyphenator\Hyphenator::setDefaultHomePath(__FILE__);
            $this->fail('No Exception thrown');
        }catch(\Exception $e) {
            $this->assertInstanceof('\Org\Heigl\Hyphenator\Exception\PathNotDirException', $e);
        }
    }

    public function testAutoloading()
    {
        $this->assertFalse(h\Hyphenator::__autoload('stuff'));
        $this->assertFalse(h\Hyphenator::__autoload('Org\Heigl\Hyphenator\Filters'));
        $this->assertFalse(h\Hyphenator::__autoload('Org\Heigl\Hyphenator\Onlyfortesting'));
        $this->assertTrue(h\Hyphenator::__autoload('Org\Heigl\Hyphenator\Anotheronefortesting'));
    }

    public function testRegisteringAutoload()
    {
        spl_autoload_unregister(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'));
        //$this->assertNotContains(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'),spl_autoload_functions());
        h\Hyphenator::registerAutoload();
        $this->assertContains(array('Org\Heigl\Hyphenator\Hyphenator', '__autoload'),spl_autoload_functions());
    }

    public function testHyphenatorInvocationSimple()
    {
        $h = h\Hyphenator::factory(__DIR__ . '/share/test2','de_DE');
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry', $h->getTokenizers());
        $t = $h->getTokenizers();
        $this->assertAttributeEquals(array(new h\Tokenizer\WhitespaceTokenizer(), new h\Tokenizer\PunctuationTokenizer()),'_registry', $t);
        $this->assertEquals('Do-nau-dampf-schiff-fahrt', $h->hyphenate('Donaudampfschifffahrt') );
        $this->assertEquals('Gü-ter-mäd-chen', $h->hyphenate('Gütermädchen') );
    }

    public function testHyphenatorInvocationWithoutFactory()
    {
        $o = new \Org\Heigl\Hyphenator\Options();
        $o->setHyphen('-')
          ->setDefaultLocale('de_DE')
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(5)
          ->setFilters('Simple')
          ->setTokenizers('Whitespace','Punctuation');
        \Org\Heigl\Hyphenator\Dictionary\Dictionary::setFileLocation(__DIR__ . '/share/test3/files/dictionaries/');
        $h = new \Org\Heigl\Hyphenator\Hyphenator();
        $h->setOptions($o);

        $this->assertEquals('We have some re-al-ly long words in ger-man like sau-er-stoff-feld-fla-sche.',$h->hyphenate('We have some really long words in german like sauerstofffeldflasche.'));
    }

    public function setup ()
    {
        //
    }
}
