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
 * @version    2.0.beta
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      04.11.2011
 */

namespace Org\Heigl\Hyphenator\Tokenizer;

/**
 * This Class describes a Token representing a word
 *
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Tokenizer
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.beta
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      04.11.2011
 */
class WordToken extends Token
{
    /**
     * The hyphenation patterns for this token.
     *
     * @var \array $_pattern
     */
    protected $_pattern = array();

    /**
     * Add a substring=>pattern array to the already existing ones
     *
     * @param \array $pattern The new patterns to add
     *
     * @return Token
     */
    public function addPattern(array $pattern)
    {
        $this->_pattern = array_merge($this->_pattern, $pattern);
        return $this;
    }

    /**
     * Get the content for the hyphenator.
     *
     * THis will prepend and append a dot to the content for better hyphenation
     *
     * @return \string
     */
    public function getHyphenateContent()
    {
        return '.' . $this->_content . '.';
    }

    /**
     * Merge the given Hyphenation patterns to one pattern for the given token
     *
     * This is done using the given quality value.
     *
     * @param \int $quality The hyphenation quality to use
     *
     * @return Token
     */
    public function getMergedPattern($quality = \Org\Heigl\Hyphenator\Hyphenator::QUALITY_HIGHEST )
    {
        $content = $this->getHyphenateContent();
        $endPattern = str_repeat('0', mb_strlen($content)+1);
        foreach ( $this->_pattern as $string => $pattern ) {
            $strStart = -1;
            while ( false !== $strStart = @mb_strpos($content, $string, $strStart + 1) ) {
                $strLen   = mb_strlen($string);
                for ( $i=0; $i <= $strLen; $i++ ) {
                    $start = $i+$strStart;
                    $currentQuality = substr($endPattern, $start, 1);
                    $patternQuality = substr($pattern, $i, 1);
                    if ( $currentQuality >= $patternQuality ) {
                        continue;
                    }
                    if ( $quality < $patternQuality ) {
                        continue;
                    }
                    $endPattern = substr($endPattern, 0, $start) . $patternQuality . substr($endPattern, $start+1);
                }
            }
        }
        return substr($endPattern, 1, strlen($endPattern)-2);
    }
}
