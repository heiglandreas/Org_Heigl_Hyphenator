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

use Org\Heigl\Hyphenator\Options;
use Org\Heigl\Hyphenator\Tokenizer as t;
use Mockery as M;
use PHPUnit\Framework\TestCase;

/**
 * This class tests the functionality of the class PunctuationTokenizer
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */
class CustomHyphenationTokenizerTest extends TestCase
{
    public function testTokenizingString()
    {
        $options = M::mock(Options::class);
        $options->shouldReceive('getNoHyphenateString')->andReturn('==');
        $options->shouldReceive('getCustomHyphen')->andReturn('--');
        $options->shouldReceive('getHyphen')->andReturn('^^');


        $tReg = new t\TokenRegistry();
        $tReg->add(new t\WordToken('Das ist '))
             ->add(new t\ExcludedWordToken('nicht'))
             ->add(new t\WordToken(' getrennt und das hat eine '))
             ->add(new t\ExcludedWordToken('kunden^^spezifische'))
             ->add(new t\WordToken(' Trennung!'));

        $tokenizer = new t\CustomHyphenationTokenizer($options);
        $registry = $tokenizer->run('Das ist ==nicht getrennt und das hat eine kunden--spezifische Trennung!');
        $this->assertEquals($tReg, $registry);
    }
}
