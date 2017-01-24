<?php
/**
 * Copyright (c) Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @since     17.10.2016
 * @link      http://github.com/heiglandreas/Hyphenator
 */

namespace Org\Heigl\HyphenatorTest\Tokenizer;

use Org\Heigl\Hyphenator\Hyphenator;
use Org\Heigl\Hyphenator\Options;
use Org\Heigl\Hyphenator\Tokenizer\PunctuationTokenizer;
use Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry;
use Org\Heigl\Hyphenator\Tokenizer\WhitespaceTokenizer;

class TokenizerRegistryIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenizerSpeed()
    {
        $tokenizer = new TokenizerRegistry();
        $tokenizer->add(new WhitespaceTokenizer());
        $tokenizer->add(new PunctuationTokenizer());

        $time = microtime(true);
        $tokenizer->tokenize(file_get_contents(__DIR__ . '/_assets/tokenizer.text'));
        $time = microtime(true) - $time;
        //echo round($time, 5);

    }

    public function testHyphenatorSpeed()
    {
        $o = new Options();
        $o->setDefaultLocale('de_DE')
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(4)
          ->setTokenizers('Whitespace', 'Punctuation');

        $hyphenator = new Hyphenator();
        $hyphenator->setOptions($o);
        $hyphenator->addFilter('Simple');
        $time = microtime(true);
        $hyphenator->hyphenate(file_get_contents(__DIR__ . '/_assets/tokenizer.text'));
        $time = microtime(true) - $time;
        //echo round($time, 5);
    }
}
