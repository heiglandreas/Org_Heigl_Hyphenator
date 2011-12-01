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
 * @version   2.0.beta
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest\Tokenizer;

use \Org\Heigl\Hyphenator\Tokenizer\TokenRegistry;
use \Org\Heigl\Hyphenator\Tokenizer\Token;
use \Org\Heigl\Hyphenator\Tokenizer\WordToken;
use \Org\Heigl\Hyphenator\Tokenizer\NonWordToken;
use \Org\Heigl\Hyphenator\Tokenizer\WhitespaceToken;

/**
 * This class tests the functionality of the class Token
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.beta
 * @since     02.11.2011
 */
class TokenRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testAddingToken()
    {
        $w = new Token('a');
        $t = new WordToken('a');
        $t1 = new WordToken('a');
        $r = new TokenRegistry();
        $this->assertAttributeEquals(array(), '_registry', $r);
        $this->assertSame($r, $r->add($t));
        $this->assertAttributeEquals(array($t), '_registry', $r);
        $this->assertSame($r, $r->add($t1));
        $this->assertAttributeEquals(array($t,$t1), '_registry', $r);
    }

    public function testGettingToken()
    {
        $t1 = new WordToken('a');
        $t2 = new WordToken('b');
        $r = new TokenRegistry();
        $r->add($t1);
        $r->add($t2);
        $this->assertSame($t2, $r->getTokenWithKey(1));
        $this->assertNull($r->getTokenWithKey(2));
    }

    public function testIteratorInterface()
    {
        $t1 = new WordToken('a');
        $t2 = new WordToken('b');
        $r = new TokenRegistry();
        $r->add($t1);
        $r->add($t2);
        $r->rewind();
        $this->assertEquals(0, $r->key());
        $this->assertSame($t1, $r->current());
        $r->next();
        $this->assertTrue($r->valid());
        $this->assertEquals(1, $r->key());
        $this->assertSame($t2, $r->current());
        $r->next();
        $this->assertFalse($r->valid());
    }

    public function testCountableInterface()
    {
        $t1 = new WordToken('a');
        $t2 = new WordToken('b');
        $r = new TokenRegistry();
        $r->add($t1);
        $this->assertEquals(1, $r->count());
        $r->add($t2);
        $this->assertEquals(2, $r->count());
    }

    public function testReplacement()
    {
        new Token('f');
        $wt1 = new WordToken('a');
        $wt2 = new WordToken('b');
        $wt3 = new WordToken('c');
        $wt4 = new WordToken('d');
        $wt5 = new WordToken('e');
        $r = new TokenRegistry();
        $r->add($wt1);
        $r->add($wt2);
        $r->add($wt3);
        $this->assertAttributeEquals(array($wt1, $wt2, $wt3), '_registry', $r);
        $r->replace($wt4, array());
        $this->assertAttributeEquals(array($wt1, $wt2, $wt3), '_registry', $r);
        $r->replace($wt2,array ( $wt4, 'foo', $wt5));
        $this->assertAttributeEquals(array($wt1, $wt4, $wt5, $wt3), '_registry', $r);

    }

    public function testConcatenation()
    {
        $r = new TokenRegistry();
        $r->add(new Token('test'))
          ->add(new Token(' '))
          ->add(new Token('oder'))
          ->add(new Token('}'))
          ->add(new Token(' '))
          ->add(new Token('so'))
          ->add(new Token(' '))
          ->add(new Token('ähnlich'));
        $this->assertEquals( 'test oder} so ähnlich', $r->concatenate());
    }
}
