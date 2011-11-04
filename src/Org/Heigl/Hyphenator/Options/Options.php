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
 * @package    Org\Heigl\Hyphenator
 * @subpackage Options
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.alpha
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      07.09.2011
 */

namespace Org\Heigl\Hyphenator\Options;

use \Org\Heigl\Hyphenator\Exception;

/** \Org\Heigl\Hyphenator\Exception\InvalidArgumentException */
require_once 'Org/Heigl/Hyphenator/Exception/InvalidArgumentException.php';

/**
 * This class provides Options for the Hyphenator.
 *
 * @category   Hyphenation
 * @package    Org\Heigl\Hyphenator
 * @subpackage Options
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.alpha
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      07.09.2011
 */
class Options
{
    /**
     * The String that marks a word not to be hyphenated.
     *
     * This string has to be prepend to the word in question
     *
     * @var string _noHyphenateString
     */
    protected $_noHyphenateString = null;

    /**
     * This property defines the default hyphenation-character.
     *
     * By default this is the soft-hyphen character U+00AD
     *
     * @var string $_hyphen
     */
    protected $_hyphen = "\xAD";

    /**
     * How many chars to stay to the left of the first hyphenation of a word.
     *
     * By default this is 2 characters
     *
     * @var int $_leftmin
     */
    protected $_leftMin = 2;

    /**
     * How many chars to stay to the right of the last hyphenation of a word.
     *
     * By default this is 2 characters
     *
     * @var int $_rightmin
     */
    protected $_rightMin = 2;

    /**
     * Whether to mark User-Hyphenations or not.
     *
     * User-Hyphenations will result in a word not being hyphenated automaticaly
     *
     * When this is set to true, the string defined in $_customizedMarker will
     * be prepend to the word in question.
     *
     * This is turned off by default!
     *
     * @var boolean $_markCustomized
     */
    protected $_markCustomized = false;

    /**
     * Customize-Marker.
     *
     * When customizations shall be used, what string shall be prepend to the
     * word that contains customizations.
     *
     * @var string|null $_customizedMarker
     */
    protected $_customizedMarker = '<!--cm-->';

    /**
     * The shortest pattern length to use for Hyphenating.
     *
     * @var int $_shortestPattern
     */
    protected $_shortestPattern = 2;

    /**
     * The longest pattern length to use for hyphenating.
     *
     * Using a high number (like '10') almost every pattern should be used
     *
     * @var int $_longestPattern
     */
    protected $_longestPattern = 10;

    /**
     * Special Character to use.
     *
     * This property defines some spechial Characters for a language that need
     * to be taken into account for the definition of a word.
     *
     * These Characters will be taken into account when splitting a text into
     * words as word-characters
     *
     * @var string $_specialChars
     */
    protected $_specialChars = '';

    /**
     * Minimum Word length for Hyphenation.
     *
     * This defaults to 6 Characters.
     *
     * @var int $_wordMin
     */
    protected $_wordMin = 6;

    /**
     * The currently set quality for hyphenation.
     *
     * The higher the number, the better the hyphenation is
     *
     * @var int $_quality
     */
    protected $_quality = 9;

    /**
     * The String that shall be searched for as a customHyphen.
     *
     * @var string $_customHyphen
     */
    protected $_customHyphen = '--';

    /**
     * The special strings to parse as hyphenations.
     *
     * @var array $_specialStrings
     */
    protected $_specialStrings = array ( '&shy;', '&#173;', '-/-', '-' );

    /**
     * Set the String that marks a word as not to be hyphenated
     *
     * @param string $noHyphenateString The string that marks a word not to be
     * hyphenated
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function setNoHyphenateString($noHyphenateString)
    {
        $this->_noHyphenateString = $noHyphenateString;
        return $this;
    }

    /**
     * Get the String that marks a word as not to be hyphenated
     *
     * @return string
     */
    public function getNoHyphenateString()
    {
        return $this->_noHyphenateString;
    }

    /**
     * Set the hyphen-string
     *
     * @param string $hyphen The hyphen to use
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function setHyphen($hyphen)
    {
        $this->_hyphen = $hyphen;
        return $this;
    }

    /**
     * Get the hyphen-string
     *
     * @return string
     */
    public function getHyphen()
    {
        return $this->_hyphen;
    }

    /**
     * Set the Minimum left characters
     *
     * @param int $leftMin Left minimum Chars
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function setLeftMin($leftMin)
    {
        $this->_leftMin = (int) $leftMin;
        return $this;
    }

    /**
     * Get the minimum left characters
     *
     * @return int
     */
    public function getLeftMin()
    {
        return (int) $this->_leftMin;
    }

    /**
     * Set the Minimum right characters
     *
     * @param int $rightMin Right minimum Characters
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function setRightMin($rightMin)
    {
        $this->_rightMin = (int) $rightMin;
        return $this;
    }

    /**
     * Get the minimum right characters
     *
     * @return int
     */
    public function getRightMin()
    {
        return (int) $this->_rightMin;
    }

    /**
     * Set whether to mark customized words
     *
     * @param boolean $markCustomized Shall we mark customized words
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function markCustomized($markCustomized)
    {
        $this->_markCustomized = (bool) $markCustomized;
        return $this;
    }

    /**
     * Check whehter customized words shall be marked
     *
     * @return bool
     */
    public function isMarkCustomized()
    {
        return (bool) $this->_markCustomized;
    }

    /**
     * Set the mark for pre-hyphenated words
     *
     * @param string $customizedMark Mark for customizations
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function setCustomizedMark($customizedMark)
    {
        $this->_customizedMarker = $customizedMark;
        return $this;
    }

    /**
     * Get the customized mark
     *
     * @return string
     */
    public function getCustomizedMark()
    {

        if (!$this->isMarkCustomized()) {
            return '';
        }
        return $this->_customizedMarker;
    }

    /**
     * Set the size of the shortest pattern
     *
     * @param int $minPatternSize set a minimum pattern size.
     *
     * @todo remove this  - This is not needed!
     * @return Org_Heigl_Hyphenator_Option
     */
    public function setMinPatternSize($minPatternSize)
    {
        $this->_shortestPattern = (int) $minPatternSize;
        return $this;
    }

    /**
     * Get the size of the shortest possible pattern
     *
     * @todo Remove this - this is not needed!
     * @return int
     */
    public function getMinPatternSize()
    {
        return (int) $this->_shortestPattern;
    }

    /**
     * Set the size of the longest pattern
     *
     * @param int $maxPatternSize Set a maximum Pattern-Size
     *
     * @todo Remove this - this is not needed!
     * @return Org_Heigl_Hyphenator_Options
     */
    public function setMaxPatternSize($maxPatternSize)
    {
        $this->_longestPattern = (int) $maxPatternSize;
        return $this;
    }

    /**
     * Get the size of the longest possible pattern
     *
     * @todo Remove this - this is not needed!
     * @return int
     */
    public function getMaxPatternSize()
    {
        return (int) $this->_longestPattern;
    }

    /**
     * Set the minimum size of a word to be hyphenated
     *
     * Words with less characters (not byte!) are not to be hyphenated
     *
     * @param int $minLength Minimum Word-Length
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function setMinWordLength($minLength)
    {
        $this->_wordMin = (int) $minLength;
        return $this;
    }

    /**
     * Get the minimum Length of a word to be hyphenated
     *
     * @return int
     */
    public function getMinWordLength()
    {
        return (int) $this->_wordMin;
    }

    /**
     * Set strings that are treated as Custom Hyphenations
     *
     * These will be replaced by the set hyphenation character
     *
     * @param array $customHyphens The custom hyphenation-characters
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function setCustomHyphens(array $customHyphens)
    {
        $this->_specialStrings = array ();
        foreach ($customHyphens as $customHyphen) {
            $this->addCustomHyphen($customHyphen);
        }
        return $this;
    }

    /**
     * Add a string to the list of custom Hyphenations
     *
     * @param string $customHyphen A custom hyphenation-character
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function addCustomHyphen($customHyphen)
    {
        $this->_specialStrings[] = (string) $customHyphen;
        return $this;
    }

    /**
     * Get the custom Hyphens
     *
     * @return array
     */
    public function getCustomHyphens()
    {
        return $this->_specialStrings;
    }
}
