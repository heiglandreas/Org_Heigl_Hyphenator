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
 * @since      04.11.2011
 */

namespace Org\Heigl\Hyphenator\Tokenizer;

/**
 * This Class describes a default Token
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
class Token
{
    /**
     * The content of the token.
     *
     * @var \string $_content
     */
    protected $_content = '';

    /**
     * The filtered content of the token.
     *
     * @var \string $_filteredContent
     */
    protected $_filteredContent = '';

    /**
     * The hyphenated token.
     *
     * @var \array $_hyphenatedContent
     */
    protected $_hyphenatedContent = array();

    /**
     * The hyphenation patterns for this token.
     *
     * @var \array $_pattern
     */
    protected $_pattern = array();

    /**
     * Create the Token
     *
     * @param \string $content The content to be stored in the token.
     *
     * @return \void
     */
    public function __construct($content)
    {
        $this->_content = $content;
        $this->_hyphenatedContent = array ( $content );
    }

    /**
     * Get the tokens content
     *
     * @return \string
     */
    public function get()
    {
        return $this->_content;
    }

    /**
     * Set the tokens hyphenated content
     *
     * @param \array $hyphenatedContent all possible hyphenations
     *
     * @return Token
     */
    public function setHyphenatedContent (array $hyphenatedContent)
    {
        $this->_hyphenatedContent = $hyphenatedContent;
        return $this;
    }

    /**
     * Get the hyphenated content
     *
     * @return \array
     */
    public function getHyphenatedContent()
    {
        return $this->_hyphenatedContent;
    }

    /**
     * Get the type of this token
     *
     * @return \string
     */
    public function getType()
    {
        return get_class($this);
    }

    /**
     * Get the already filtered content of the token.
     *
     * @return \string
     */
    public function getFilteredContent()
    {
        if ( ! $this->_filteredContent ) {
            return $this->_content;
        }
        return $this->_filteredContent;
    }

    /**
     * Set the filtered content
     *
     * @param \string $content The Filtered Content
     *
     * @return Token
     */
    public function setFilteredContent($content)
    {
        $this->_filteredContent = $content;
        return $this;
    }

    /**
     * Get the length of the contained (unhyphenated) token
     *
     * @return \int
     */
    public function length()
    {
        return mb_strlen($this->_content);
    }

}

/**
 * This Class describes a Token represeonting something that is not a word.
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
class NonWordToken extends Token
{
    //
}
/**
 * This Class describes a  Token containing whitespace
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
class WhitespaceToken extends Token
{
    //
}
