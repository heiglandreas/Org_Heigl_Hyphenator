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

use \Org\Heigl\Hyphenator\Dictionary\DictionaryRegistry;
use \Org\Heigl\Hyphenator\Dictionary as d;

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
class DictionaryRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testAddingDictionary()
    {
        $registry = new DictionaryRegistry();
        $this->assertAttributeEquals(array(), '_registry', $registry);
        $dict = new d\Dictionary();
        $registry->add($dict);
        $this->assertAttributeEquals(array($dict), '_registry', $registry);
        $this->assertSame($dict, $registry->getDictionaryWithKey(0));
    }

    public function testGettingPatternsForWord()
    {
        $registry = new DictionaryRegistry();
        $dict1 = new d\Dictionary();
        $dict1->addPattern('te', '012')
              ->addPattern('et', '112')
              ->addPattern('at', '234');
        $dict2 = new d\Dictionary();
        $dict2->addPattern('at', '123')
              ->addPattern('es', '010')
              ->addPattern('st', '110');
        $registry->add($dict1)
                 ->add($dict2);
        $expected = array('te' => '012','es'=>'010','st'=>'110');
        $this->assertEquals($expected, $registry->getHyphenationPattterns('test'));
    }

    public function testRegistryImplementsItterator()
    {
        $registry = new DictionaryRegistry();
        $this->assertInstanceof('\Iterator', $registry);
        $this->assertInstanceof('\Countable', $registry);
    }

    public function testIteratorAndCountable()
    {
        $registry = new DictionaryRegistry();
        $registry->add(new d\Dictionary())
                 ->add(new d\Dictionary());
        $this->assertEquals(1, $registry->count());
        $dictionary = new d\Dictionary();
        $dictionary->addPattern('test', 'test1');
        $registry->add($dictionary);
        $this->assertEquals(2, $registry->count());
        $registry->rewind();
        $this->assertEquals(new d\Dictionary(), $registry->current());
        $this->assertEquals(0, $registry->key());
        $registry->next();
        $this->assertTrue($registry->valid());
        $registry->next();
        $this->assertFalse($registry->valid());
    }

    public function testGettingDictionaryById()
    {
        $registry = new DictionaryRegistry();
        $dictionary1 = new d\Dictionary();
        $dictionary1->addPattern('test', 'test1');
        $registry->add($dictionary1);
        $dictionary2 = new d\Dictionary();
        $dictionary2->addPattern('test1', 'test12');
        $registry->add($dictionary2);
        $this->assertEquals($dictionary2, $registry->getDictionaryWithKey(1));
        $this->assertEquals($dictionary1, $registry->getDictionaryWithKey(0));
        $this->assertEquals(null, $registry->getDictionaryWithKey(2));
    }
}
