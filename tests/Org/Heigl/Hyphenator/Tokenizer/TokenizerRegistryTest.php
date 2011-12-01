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

use \Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry;

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
class TokenizerRegistryTest extends \PHPUnit_Framework_TestCase
{
public function testAddingTokenizer()
    {
        $t1 = new TestTokenizer();
        $t2 = new Test1Tokenizer();
        $r = new TokenizerRegistry();
        $this->assertAttributeEquals(array(), '_registry', $r);
        $this->assertSame($r, $r->add($t1));
        $this->assertAttributeEquals(array($t1), '_registry', $r);
        $this->assertSame($r, $r->add($t2));
        $this->assertAttributeEquals(array($t1,$t2), '_registry', $r);
        $this->assertSame($r, $r->cleanup());
        $this->assertAttributeEquals(array(), '_registry', $r);
    }



    public function testGettingToken()
    {
        $t1 = new TestTokenizer();
        $t2 = new Test1Tokenizer();
        $r = new TokenizerRegistry();
        $r->add($t1);
        $r->add($t2);
        $this->assertSame($t2, $r->getTokenizerWithKey(1));
        $this->assertNull($r->getTokenizerWithKey(2));
    }

    public function testIteratorInterface()
    {
        $t1 = new TestTokenizer();
        $t2 = new Test1Tokenizer();
        $r = new TokenizerRegistry();
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
        $t1 = new TestTokenizer();
        $t2 = new Test1Tokenizer();
        $r = new TokenizerRegistry();
        $r->add($t1);
        $this->assertEquals(1, $r->count());
        $r->add($t2);
        $this->assertEquals(2, $r->count());
    }

    public function testTokenizing()
    {
        $t1 = new TestTokenizer();
        $t2 = new Test1Tokenizer();
        $r = new TokenizerRegistry();
        $r->add($t1);
        $r->add($t2);
        $this->assertEquals('input', $r->tokenize('input'));

    }


}

class TestTokenizer implements \Org\Heigl\Hyphenator\Tokenizer\Tokenizer
{
    public function run($input)
    {
        return $input;
    }
}
class Test1Tokenizer extends TestTokenizer{}
