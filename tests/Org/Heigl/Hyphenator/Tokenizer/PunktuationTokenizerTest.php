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

namespace Org\Heigl\HyphenatorTest\Tokenizer;

use \Org\Heigl\Hyphenator\Tokenizer as t;

/**
 * This class tests the functionality of the class PunktuationTokenizer
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.alpha
 * @since     02.11.2011
 */
class PunktuationTokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenizingString()
    {
        new t\Token('test');
        $tokenizer = new t\PunktuationTokenizer();
        $tReg = new t\TokenRegistry();
        $tReg->add(new t\WordToken('Das'))
             ->add(new t\NonWordToken('('))
             ->add(new t\WordToken('ist'))
             ->add(new t\NonWordToken( '/'))
             ->add(new t\WordToken('ein'))
             ->add(new t\NonWordToken('>'))
             ->add(new t\WordToken('Test oder	so'))
             ->add(new t\NonWordToken(')'));
        $registry = $tokenizer->run('Das(ist/ein>Test oder	so)' );
        $this->assertEquals($tReg, $registry);
    }



    public function testTokenizingRegistry()
    {
        new t\Token('test');
        $tokenizer = new t\PunktuationTokenizer();
        $tReg = new t\TokenRegistry();
        $tReg->add(new t\WordToken('Das(ist'))
             ->add(new t\WhitespaceToken(' '))
             ->add(new t\WordToken('ein>Test)'))
             ->add(new t\WhitespaceToken(' '))
             ->add(new t\WordToken('vieleicht'))
             ->add(new t\NonWordToken('/'))
             ->add(new t\WordToken('oder{so!'));
        $tReg1 = new t\TokenRegistry();
        $tReg1->add(new t\WordToken('Das'))
              ->add(new t\NonWordToken('('))
              ->add(new t\WordToken('ist'))
              ->add(new t\WhitespaceToken(' '))
              ->add(new t\WordToken('ein'))
              ->add(new t\NonWordToken('>'))
              ->add(new t\WordToken('Test'))
              ->add(new t\NonWordToken(')'))
              ->add(new t\WhitespaceToken(' '))
              ->add(new t\WordToken('vieleicht'))
              ->add(new t\NonWordToken('/'))
              ->add(new t\WordToken('oder'))
              ->add(new t\NonWordToken('{'))
              ->add(new t\WordToken('so'))
              ->add(new t\NonWordToken('!'));
        $registry = $tokenizer->run($tReg);
        $this->assertEquals($tReg1, $tokenizer->run($tReg));
    }

}
