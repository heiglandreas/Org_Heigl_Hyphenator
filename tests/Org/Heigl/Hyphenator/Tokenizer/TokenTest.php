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
 * @version   2.0.alpha
 * @since     02.11.2011
 */
class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenReturnsCorrectClass()
    {
        new Token('test');
        $tokenA = new WordToken('a');
        $this->assertEquals('Org\Heigl\Hyphenator\Tokenizer\WordToken',$tokenA->getType());
        $tokenB = new NonWordToken('a');
        $this->assertEquals('Org\Heigl\Hyphenator\Tokenizer\NonWordToken',$tokenB->getType());
        $tokenC = new WhitespaceToken('a');
        $this->assertEquals('Org\Heigl\Hyphenator\Tokenizer\WhitespaceToken',$tokenC->getType());
    }

    public function testTokenReturnsCorrectValues()
    {
        $tokenA = new Token('test');
        $this->assertAttributeEquals('test','_content', $tokenA);
        $this->assertEquals('test', $tokenA->get());
        $this->assertAttributeEquals(array('test'), '_hyphenatedContent', $tokenA);
        $this->assertEquals(array('test'),$tokenA->getHyphenatedContent());
        $tokenA->setHyphenatedContent(array('a','B'));
        $this->assertAttributeEquals(array('a','B'), '_hyphenatedContent', $tokenA);
        $this->assertEquals(array('a','B'),$tokenA->getHyphenatedContent());
    }
}
