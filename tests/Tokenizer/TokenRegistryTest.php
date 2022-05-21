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

namespace Org\Heigl\HyphenatorTest\Tokenizer;

use Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry;
use Org\Heigl\Hyphenator\Tokenizer\TokenRegistry;
use Org\Heigl\Hyphenator\Tokenizer\Token;
use Org\Heigl\Hyphenator\Tokenizer\WordToken;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

/**
 * This class tests the functionality of the class Token
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */
class TokenRegistryTest extends TestCase
{
    public function testAddingToken()
    {
        $t = new WordToken('a');
        $t1 = new WordToken('a');
        $r = new TokenRegistry();
        self::assertEquals(0, $r->count());
        $this->assertSame($r, $r->add($t));
        self::assertEquals(1, $r->count());
        self::assertSame($t, $r->getTokenWithKey(0));
        $this->assertSame($r, $r->add($t1));
        self::assertEquals(2, $r->count());
        self::assertSame($t, $r->getTokenWithKey(0));
        self::assertSame($t1, $r->getTokenWithKey(1));
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

    public function testAccessingNonexistingObjectThrowsException(): void
    {
        $r = new TokenRegistry();
        $this->expectException(OutOfBoundsException::class);
        $r->current();
    }

    public function testAccessingNonexistingKeyThrowsException(): void
    {
        $r = new TokenRegistry();
        $this->expectException(OutOfBoundsException::class);
        $r->key();
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
        self::assertEquals(3, $r->count());
        self::assertSame($wt1, $r->getTokenWithKey(0));
        self::assertSame($wt2, $r->getTokenWithKey(1));
        self::assertSame($wt3, $r->getTokenWithKey(2));
        $r->replace($wt4, array());
        self::assertEquals(3, $r->count());
        self::assertSame($wt1, $r->getTokenWithKey(0));
        self::assertSame($wt2, $r->getTokenWithKey(1));
        self::assertSame($wt3, $r->getTokenWithKey(2));
        $r->replace($wt2, array( $wt4, 'foo', $wt5));
        self::assertEquals(4, $r->count());
        self::assertSame($wt1, $r->getTokenWithKey(0));
        self::assertSame($wt4, $r->getTokenWithKey(1));
        self::assertSame($wt5, $r->getTokenWithKey(2));
        self::assertSame($wt3, $r->getTokenWithKey(3));
    }
}
