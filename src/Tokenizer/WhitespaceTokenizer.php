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
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Tokenizer
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      11.11.2011
 */

namespace Org\Heigl\Hyphenator\Tokenizer;

/**
 * Use Whitespace to split any input into tokens
 *
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Tokenizer
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      04.11.2011
 */
class WhitespaceTokenizer extends AbstractTokenizer
{
    protected $whitespaces = array(
      '\s',           // white space
      "\xE2\x80\xAF", // non-breaking thin white space
      "\xC2\xA0",     // non-breaking space
    );

    /**
     * Split the given string into tokens using whitespace as splitter.
     *
     * Each whitespace is placed in a WhitespaceToken and everything else is
     * placed in a WordToken-Object
     *
     * @param string $input The String to tokenize
     *
     * @return Token[]
     */
    protected function tokenize($input)
    {
        $tokens = array();
        $splits = preg_split("/([".implode("", $this->whitespaces)."]+)/u", $input, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach ($splits as $split) {
            if ($split === '') {
                $tokens[] = new EmptyToken($split);
                continue;
            }
            if (preg_match("/^[".implode("", $this->whitespaces)."]+$/um", $split)) {
                $tokens[] = new WhitespaceToken($split);
                continue;
            }
            $tokens[] = new WordToken($split);
        }

        return $tokens;
    }
}
