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
 * Use Punktuation to split any input into tokens
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
class PunctuationTokenizer implements Tokenizer
{

    /**
     * The tokens to be handled by this tokenizer as an array.
     *
     * @var string $_tokens
     */
    protected $_tokens = array (
        '.',
        '?',
        '!',
        ':',
        ';',
        ',',
        '#',
        '"',
        '$',
        '§',
        '%',
        '&',
        '/',
        '(',
        ')',
        '=',
        '[',
        ']',
        '|',
        '{',
        '}',
        '\\',
        '<',
        '>',
        '«',
        '»',
        '“',
        '”',
        '^',
        '°',
        '≤',
        '≥',
        '¥',
        '©',
        '€',
        "'",
        '-',
        '_',
    );

    /**
     * Split the given input into tokens using punktuation marks as splitter
     *
     * The input can be a string or a tokenRegistry. If the input is a
     * TokenRegistry, each item will be tokenized.
     *
     * @param string|\Org\Heigl\Hyphenator\Tokenizer\TokenRegistry $input The
     * input to be tokenized
     *
     * @return \Org\Heigl\Hyphenator\Tokenizer\TokenRegistry
     */
    public function run($input)
    {
        if ($input instanceof TokenRegistry) {
            // Tokenize a TokenRegistry
            $f = clone($input);
            foreach ($input as $token) {
                if ($token instanceof WhitespaceToken) {
                    continue;
                }
                if ($token instanceof NonWordToken) {
                    continue;
                }
                $newTokens = $this->_tokenize($token->get());
                if ( $newTokens == array($token) ) {
                    continue;
                }
                $f->replace($token, $newTokens);
            }

            return $f ;
        }

        // Tokenize a simple string.
        $array =  $this->_tokenize($input);
        $registry = new TokenRegistry();
        foreach ($array as $item) {
            $registry->add($item);
        }

        return $registry;
    }

    /**
     * Split the given string into tokens using whitespace.
     *
     * Each whitespace is placed in a WhitespaceToken and everything else is
     * placed in a WordToken-Object
     *
     * @param \string $input The String to tokenize
     *
     * @return Token
     */
    protected function _tokenize($input)
    {
        $tokens = array();
        $signs = '\\' . implode('\\', $this->_tokens);
        $splits = preg_split('/([' . $signs . ']+)/u', $input, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($splits as $split) {
            if ('' == $split) {
                continue;
            }
            if ( in_array(mb_substr($split, 0, 1), $this->_tokens) ) {
                $tokens[] = new NonWordToken($split);
                continue;
            }
            $tokens[] = new WordToken($split);
        }

        return $tokens;
    }
}
