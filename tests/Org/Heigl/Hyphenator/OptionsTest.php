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
 * @version   2.0
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest;

use \Org\Heigl\Hyphenator\Options\Options;

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Org_Heigl_Hyphenator */
require_once 'Org/Heigl/Hyphenator/Options/Options.php';

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0
 * @since     02.11.2011
 */
class OptionsTest extends \PHPUnit_Framework_TestCase
{
    public function testSettingHyphen()
    {
        $o = new Options();
        $this->assertAttributeEquals("\xAD",'_hyphen',$o);
        $this->assertEquals(chr(173),$o->getHyphen());
        $this->assertSame($o,$o->setHyphen('test'));
        $this->assertAttributeEquals('test','_hyphen',$o);
        $this->assertEquals('test',$o->getHyphen());
    }

    public function testSettingNoHyphenateString()
    {
        $o = new Options();
        $this->assertAttributeEquals('','_noHyphenateString',$o);
        $this->assertEquals('',$o->getNoHyphenateString());
        $this->assertSame($o,$o->setNoHyphenateString('test'));
        $this->assertAttributeEquals('test','_noHyphenateString',$o);
        $this->assertEquals('test',$o->getNoHyphenateString());
    }

    public function testSettingLeftMin()
    {
        $o = new Options();
        $this->assertAttributeSame(2,'_leftMin',$o);
        $this->assertSame(2,$o->getLeftMin());
        $this->assertSame($o,$o->setLeftMin('test'));
        $this->assertAttributeSame(0,'_leftMin',$o);
        $this->assertSame(0,$o->getLeftMin());
        $this->assertSame($o,$o->setLeftMin(5));
        $this->assertAttributeSame(5,'_leftMin',$o);
        $this->assertSame(5,$o->getLeftMin());
    }

    public function testSettingRightMin()
    {
        $o = new Options();
        $this->assertAttributeSame(2,'_rightMin',$o);
        $this->assertSame(2,$o->getRightMin());
        $this->assertSame($o,$o->setRightMin('test'));
        $this->assertAttributeSame(0,'_rightMin',$o);
        $this->assertSame(0,$o->getRightMin());
        $this->assertSame($o,$o->setRightMin(5));
        $this->assertAttributeSame(5,'_rightMin',$o);
        $this->assertSame(5,$o->getRightMin());
    }

    public function testCustomizedMarker()
    {
        $o = new Options();
        $this->assertAttributeSame(false,'_markCustomized', $o);
        $this->assertAttributeSame('<!--cm-->','_customizedMarker', $o);
        $this->assertSame(false, $o->isMarkCustomized());
        $this->assertSame('', $o->getCustomizedMark());
        $this->assertSame($o,$o->markCustomized('test'));
        $this->assertAttributeSame(true,'_markCustomized', $o);
        $this->assertAttributeSame('<!--cm-->','_customizedMarker', $o);
        $this->assertSame(true, $o->isMarkCustomized());
        $this->assertSame('<!--cm-->', $o->getCustomizedMark());
        $this->assertSame($o,$o->setCustomizedMark('foo'));
        $this->assertAttributeSame('foo','_customizedMarker', $o);
        $this->assertSame('foo', $o->getCustomizedMark());
        $this->assertSame($o,$o->markCustomized(false));
        $this->assertAttributeSame('foo','_customizedMarker', $o);
        $this->assertSame('', $o->getCustomizedMark());
    }

    public function testSettingShortestPattern()
    {
        $o = new Options();
        $this->assertAttributeSame(2,'_shortestPattern',$o);
        $this->assertSame(2,$o->getMinPatternSize());
        $this->assertSame($o, $o->setMinPatternSize(''));
        $this->assertAttributeSame(0,'_shortestPattern', $o);
        $this->assertSame(0,$o->getMinPatternSize());
        $this->assertSame($o, $o->setMinPatternSize(PHP_INT_MAX));
        $this->assertAttributeSame(PHP_INT_MAX,'_shortestPattern', $o);
        $this->assertSame(PHP_INT_MAX,$o->getMinPatternSize());

    }

    public function testSettingLongestPattern()
    {
        $o = new Options();
        $this->assertAttributeSame(10,'_longestPattern',$o);
        $this->assertSame(10,$o->getMaxPatternSize());
        $this->assertSame($o, $o->setMaxPatternSize(''));
        $this->assertAttributeSame(0,'_longestPattern', $o);
        $this->assertSame(0,$o->getMaxPatternSize());
        $this->assertSame($o, $o->setMaxPatternSize(PHP_INT_MAX));
        $this->assertAttributeSame(PHP_INT_MAX,'_longestPattern', $o);
        $this->assertSame(PHP_INT_MAX,$o->getMaxPatternSize());

    }

    public function testSettingMinWordSize()
    {
        $o = new Options();
        $this->assertAttributeSame(6,'_wordMin',$o);
        $this->assertSame(6,$o->getMinWordLength());
        $this->assertSame($o, $o->setMinWordLength(''));
        $this->assertAttributeSame(0,'_wordMin', $o);
        $this->assertSame(0,$o->getMinWordLength());
        $this->assertSame($o, $o->setMinWordLength(PHP_INT_MAX));
        $this->assertAttributeSame(PHP_INT_MAX,'_wordMin', $o);
        $this->assertSame(PHP_INT_MAX,$o->getMinWordLength());

    }

    public function testSettingCustomHyphens()
    {
        $o = new Options();
        $this->assertAttributeSame(array('&shy;','&#173;','-/-','-'),'_specialStrings',$o);
        $this->assertSame(array('&shy;','&#173;','-/-','-'),$o->getCustomHyphens());
        $this->assertSame($o, $o->setCustomHyphens(array()));
        $this->assertAttributeSame(array(),'_specialStrings',$o);
        $this->assertSame(array(),$o->getCustomHyphens());
        $this->assertSame($o, $o->setCustomHyphens(array('foo')));
        $this->assertAttributeSame(array('foo'),'_specialStrings',$o);
        $this->assertSame(array('foo'),$o->getCustomHyphens());
        $this->assertSame($o, $o->addCustomHyphen('bar'));
        $this->assertAttributeSame(array('foo','bar'),'_specialStrings',$o);
        $this->assertSame(array('foo','bar'),$o->getCustomHyphens());

    }
}