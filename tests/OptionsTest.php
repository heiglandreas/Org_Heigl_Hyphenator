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
 * @subpackage Tests
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest;

use Org\Heigl\Hyphenator\Options;
use Mockery as M;
use PHPUnit\Framework\TestCase;

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
class OptionsTest extends TestCase
{
    public function testSettingHyphen()
    {
        $o = new Options();
        $this->assertAttributeEquals("\xAD", 'hyphen', $o);
        $this->assertEquals(chr(173), $o->getHyphen());
        $this->assertSame($o, $o->setHyphen('test'));
        $this->assertAttributeEquals('test', 'hyphen', $o);
        $this->assertEquals('test', $o->getHyphen());
    }

    public function testSettingNoHyphenateString()
    {
        $o = new Options();
        $this->assertAttributeEquals('', 'noHyphenateString', $o);
        $this->assertEquals('', $o->getNoHyphenateString());
        $this->assertSame($o, $o->setNoHyphenateString('test'));
        $this->assertAttributeEquals('test', 'noHyphenateString', $o);
        $this->assertEquals('test', $o->getNoHyphenateString());
    }

    public function testSettingLeftMin()
    {
        $o = new Options();
        $this->assertAttributeSame(2, 'leftMin', $o);
        $this->assertSame(2, $o->getLeftMin());
        $this->assertSame($o, $o->setLeftMin('test'));
        $this->assertAttributeSame(0, 'leftMin', $o);
        $this->assertSame(0, $o->getLeftMin());
        $this->assertSame($o, $o->setLeftMin(5));
        $this->assertAttributeSame(5, 'leftMin', $o);
        $this->assertSame(5, $o->getLeftMin());
    }

    public function testSettingRightMin()
    {
        $o = new Options();
        $this->assertAttributeSame(2, 'rightMin', $o);
        $this->assertSame(2, $o->getRightMin());
        $this->assertSame($o, $o->setRightMin('test'));
        $this->assertAttributeSame(0, 'rightMin', $o);
        $this->assertSame(0, $o->getRightMin());
        $this->assertSame($o, $o->setRightMin(5));
        $this->assertAttributeSame(5, 'rightMin', $o);
        $this->assertSame(5, $o->getRightMin());
    }


    public function testSettingMinWordSize()
    {
        $o = new Options();
        $this->assertAttributeSame(6, 'wordMin', $o);
        $this->assertSame(6, $o->getMinWordLength());
        $this->assertSame($o, $o->setMinWordLength(''));
        $this->assertAttributeSame(0, 'wordMin', $o);
        $this->assertSame(0, $o->getMinWordLength());
        $this->assertSame($o, $o->setMinWordLength(PHP_INT_MAX));
        $this->assertAttributeSame(PHP_INT_MAX, 'wordMin', $o);
        $this->assertSame(PHP_INT_MAX, $o->getMinWordLength());
    }

    public function testSettingCustomHyphen()
    {
        $o = new Options;
        $this->assertAttributeEquals('--', 'customHyphen', $o);
        $this->assertEquals('--', $o->getCustomHyphen());
        $this->assertSame($o, $o->setCustomHyphen('++'));
        $this->assertAttributeEquals('++', 'customHyphen', $o);
        $this->assertEquals('++', $o->getCustomHyphen());
    }

    public function testSettingFilters()
    {
        $o = new Options();
        $this->assertAttributeEquals(array(), 'filters', $o);
        $this->assertSame(array(), $o->getFilters());
        $this->assertSame($o, $o->setFilters(''));
        $this->assertAttributeEquals(array(), 'filters', $o);
        $this->assertSame(array(), $o->getFilters());
        $this->assertSame($o, $o->setFilters('filterA, filterB'));
        $this->assertAttributeEquals(array('filterA','filterB'), 'filters', $o);
        $this->assertSame(array('filterA', 'filterB'), $o->getFilters());
        $this->assertSame($o, $o->setFilters(''));
        $this->assertAttributeEquals(array(), 'filters', $o);
        $this->assertSame(array(), $o->getFilters());
        $this->assertSame($o, $o->setFilters(array('filterC','filterD')));
        $this->assertAttributeEquals(array('filterC','filterD'), 'filters', $o);
        $this->assertSame(array('filterC','filterD'), $o->getFilters());
    }

    public function testSettingFilterInstance()
    {
        $o = new Options();

        $filter = M::mock('Org\Heigl\Hyphenator\Filter\Filter');

        $this->assertAttributeEquals(array(), 'filters', $o);
        $this->assertSame(array(), $o->getFilters());
        $this->assertSame($o, $o->addFilter($filter));
        $this->assertAttributeEquals(array($filter), 'filters', $o);
    }

    /**
     * @expectedException \UnexpectedValueException
     * @dataProvider settingSoemthingElseThanFilterFailsProvider
     */
    public function testSettingSoemthingElseThanFilterFails($filter)
    {
        $o = new Options();

        $o->addFilter($filter);
    }

    public function settingSoemthingElseThanFilterFailsProvider()
    {
        return array(
            array(M::mock('Org\Heigl\Hyphenator\Tokenizer\Tokenizer')),
            array(new \Exception()),
        );
    }

    public function testSettingTokenizerInstance()
    {
        $o = new Options();

        $tokenizer = M::mock('Org\Heigl\Hyphenator\Tokenizer\Tokenizer');

        $this->assertAttributeEquals(array(), 'tokenizers', $o);
        $this->assertSame(array(), $o->getTokenizers());
        $this->assertSame($o, $o->addTokenizer($tokenizer));
        $this->assertAttributeEquals(array($tokenizer), 'tokenizers', $o);
    }
    /**
     * @expectedException \UnexpectedValueException
     * @dataProvider settingSoemthingElseThanTokenizerFailsProvider
     */
    public function testSettingSoemthingElseThanTokenizerFails($tokenizer)
    {
        $o = new Options();

        $o->addTokenizer($tokenizer);
    }

    public function settingSoemthingElseThanTokenizerFailsProvider()
    {
        return array(
            array(M::mock('Org\Heigl\Hyphenator\Filter\Filter')),
            array(new \Exception()),
        );
    }


    public function testSettingTokenizers()
    {
        $o = new Options();
        $this->assertAttributeEquals(array(), 'tokenizers', $o);
        $this->assertSame(array(), $o->getTokenizers());
        $this->assertSame($o, $o->setTokenizers(''));
        $this->assertAttributeEquals(array(), 'tokenizers', $o);
        $this->assertSame(array(), $o->getTokenizers());
        $this->assertSame($o, $o->setTokenizers('filterA, filterB'));
        $this->assertAttributeEquals(array('filterA','filterB'), 'tokenizers', $o);
        $this->assertSame(array('filterA', 'filterB'), $o->getTokenizers());
        $this->assertSame($o, $o->setTokenizers(''));
        $this->assertAttributeEquals(array(), 'tokenizers', $o);
        $this->assertSame(array(), $o->getTokenizers());
        $this->assertSame($o, $o->setTokenizers(array('filterC','filterD')));
        $this->assertAttributeEquals(array('filterC','filterD'), 'tokenizers', $o);
        $this->assertSame(array('filterC','filterD'), $o->getTokenizers());
    }

    public function testCreatingOptionViaFactory()
    {
        try {
            Options::factory('foo');
            $this->fail('Foo should not be readable');
        } catch (\Org\Heigl\Hyphenator\Exception\PathNotFoundException $e) {
            $this->assertTrue(true);
        }
        try {
            Options::factory(__DIR__ . '/share/unparseable.ini');
            $this->fail('The given file should not be parseable');
        } catch (\Org\Heigl\Hyphenator\Exception\InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
        $o = Options::factory(__DIR__ . '/share/onlydist.ini');
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Options', $o);
        $o = Options::factory(__DIR__ . '/share/parseable.ini');
        $this->assertInstanceof('\Org\Heigl\Hyphenator\Options', $o);
        $this->assertAttributeEquals('test', 'hyphen', $o);
        $this->assertAttributeEquals('test', 'noHyphenateString', $o);
        $this->assertAttributeEquals(5, 'leftMin', $o);
        $this->assertAttributeEquals(5, 'rightMin', $o);
        $this->assertAttributeEquals(5, 'wordMin', $o);
        $this->assertAttributeEquals(5, 'quality', $o);
        $this->assertAttributeEquals('test', 'customHyphen', $o);
        $this->assertAttributeEquals(array('test1','test2'), 'tokenizers', $o);
        $this->assertAttributeEquals(array('test3','test4'), 'filters', $o);
        $this->assertAttributeEquals('test', 'defaultLocale', $o);
    }

    public function testDefaultLocale()
    {
        $o = new Options();
        $this->assertEquals('en_EN', $o->getDefaultLocale());
        $this->assertSame($o, $o->setDefaultLocale('test'));
        $this->assertAttributeEquals('test', 'defaultLocale', $o);
        $this->assertEquals('test', $o->getDefaultLocale());
    }
}
