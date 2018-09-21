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
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      07.09.2011
 */

namespace Org\Heigl\Hyphenator;

use \Org\Heigl\Hyphenator\Tokenizer\Tokenizer;
use \Org\Heigl\Hyphenator\Filter\Filter;

/**
 * This class provides Options for the Hyphenator.
 *
 * @category   Hyphenation
 * @package    Org\Heigl\Hyphenator
 * @subpackage Options
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
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
     * @var string noHyphenateString
     */
    private $noHyphenateString = null;

    /**
     * This property defines the default hyphenation-character.
     *
     * By default this is the soft-hyphen character U+00AD
     *
     * @var string $hyphen
     */
    private $hyphen = "\xAD";

    /**
     * How many chars to stay to the left of the first hyphenation of a word.
     *
     * By default this is 2 characters
     *
     * @var int $leftmin
     */
    private $leftMin = 2;

    /**
     * How many chars to stay to the right of the last hyphenation of a word.
     *
     * By default this is 2 characters
     *
     * @var int $rightmin
     */
    private $rightMin = 2;

    /**
     * Minimum Word length for Hyphenation.
     *
     * This defaults to 6 Characters.
     *
     * @var int $wordMin
     */
    private $wordMin = 6;

    /**
     * The currently set quality for hyphenation.
     *
     * The higher the number, the better the hyphenation is
     *
     * @var int $quality
     */
    private $quality = 9;

    /**
     * The String that shall be searched for as a customHyphen.
     *
     * @var string $customHyphen
     */
    private $customHyphen = '--';

    /**
     * The filters to be used to postprocess the hyphenations.
     *
     * @var array $filters
     */
    private $filters = array();

    /**
     * The tokenizers to use.
     *
     * @var array $tokenizers
     */
    private $tokenizers = array();

    /**
     * THe locale to be used.
     *
     * @var string $_locale
     */
    private $defaultLocale = 'en_EN';

    /**
     * Set the String that marks a word as not to be hyphenated
     *
     * @param string $noHyphenateString The string that marks a word not to be
     * hyphenated
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setNoHyphenateString($noHyphenateString)
    {
        $this->noHyphenateString = $noHyphenateString;

        return $this;
    }

    /**
     * Get the String that marks a word as not to be hyphenated
     *
     * @return string
     */
    public function getNoHyphenateString()
    {
        return $this->noHyphenateString;
    }

    /**
     * Set the hyphen-string
     *
     * @param string $hyphen The hyphen to use
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setHyphen($hyphen)
    {
        $this->hyphen = $hyphen;

        return $this;
    }

    /**
     * Get the hyphen-string
     *
     * @return string
     */
    public function getHyphen()
    {
        return $this->hyphen;
    }

    /**
     * Set the Minimum left characters
     *
     * @param int $leftMin Left minimum Chars
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setLeftMin($leftMin)
    {
        $this->leftMin = (int) $leftMin;

        return $this;
    }

    /**
     * Get the minimum left characters
     *
     * @return int
     */
    public function getLeftMin()
    {
        return $this->leftMin;
    }

    /**
     * Set the Minimum right characters
     *
     * @param int $rightMin Right minimum Characters
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setRightMin($rightMin)
    {
        $this->rightMin = (int) $rightMin;

        return $this;
    }

    /**
     * Get the minimum right characters
     *
     * @return int
     */
    public function getRightMin()
    {
        return $this->rightMin;
    }

    /**
     * Set the minimum size of a word to be hyphenated
     *
     * Words with less characters (not byte!) are not to be hyphenated
     *
     * @param int $minLength Minimum Word-Length
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setMinWordLength($minLength)
    {
        $this->wordMin = (int) $minLength;

        return $this;
    }

    /**
     * This is a wrapper for setMinWordLength
     *
     * @param int $wordLength The minimum word Length
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setWordMin($wordLength)
    {
        return $this->setMinWordLength($wordLength);
    }

    /**
     * Set the hyphenation quality
     *
     * @param int $quality The new Hyphenation Quality
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setQuality($quality)
    {
        $this->quality = (int) $quality;

        return $this;
    }

    /**
     * Get the hyphenation-quality
     *
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }

    /**
     * Get the minimum Length of a word to be hyphenated
     *
     * @return int
     */
    public function getMinWordLength()
    {
        return $this->wordMin;
    }

    /**
     * Set the string that is treated as a custom Hyphenation
     *
     * These will be replaced by the set hyphenation character
     *
     * @param array $customHyphen The custom hyphenation-character
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setCustomHyphen($customHyphen)
    {
        $this->customHyphen = (string) $customHyphen;

        return $this;
    }

    /**
     * Get the custom Hyphen
     *
     * @return string
     */
    public function getCustomHyphen()
    {
        return $this->customHyphen;
    }

    /**
     * Set the filters
     *
     * @param string|array $filters The filters to use as comma separated list
     *                              or array
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setFilters($filters)
    {
        $this->filters = array();
        if (! is_array($filters)) {
            $filters = explode(',', $filters);
        }
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    /**
     * Add a filter to the options-array
     *
     * @param string|Filter $filter The filter to be added
     *
     * @throws \UnexpectedValueException
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function addFilter($filter)
    {
        if (is_string($filter)) {
            $filter = trim($filter);
        } elseif (! $filter instanceof Filter) {
            throw new \UnexpectedValueException('Expceted instanceof Org\Heigl\Hyphenator\Filter\Filter or string');
        }
        if (! $filter) {
            return $this;
        }
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Get all the filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set the tokenizers to use
     *
     * @param string|array $tokenizers The Tokenizers to use
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setTokenizers($tokenizers)
    {
        $this->tokenizers = array();
        if (! is_array($tokenizers)) {
            $tokenizers = explode(',', $tokenizers);
        }
        foreach ($tokenizers as $tokenizer) {
            $this->addTokenizer($tokenizer);
        }

        return $this;
    }

    /**
     * Add a tokenizer to the tomeizer-list
     *
     * @param string|Tokenizer $tokenizer The tokenizer to add
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function addTokenizer($tokenizer)
    {
        if (is_string($tokenizer)) {
            $tokenizer = trim($tokenizer);
        } elseif (! $tokenizer instanceof Tokenizer) {
            throw new \UnexpectedValueException(
                'Expceted instanceof Org\Heigl\Hyphenator\Tokenizer\Tokenizer or string'
            );
        }

        if (! $tokenizer) {
            return $this;
        }
        $this->tokenizers[] = $tokenizer;

        return $this;
    }

    /**
     * Get all the tokenizers
     *
     * @return array
     */
    public function getTokenizers()
    {
        return $this->tokenizers;
    }

    /**
     * Create an Option-Object by parsing a given file.
     *
     * @param string $file The config-file to be parsed
     *
     * @throws \Org\Heigl\Hyphenator\Exception\PathNotFoundException
     * @throws \Org\Heigl\Hyphenator\Exception\InvalidArgumentException
     * @return \Org\Heigl\Hyphenator\Options
     */
    public static function factory($file)
    {
        if (! file_Exists($file)) {
            $file = $file . '.dist';
            if (! file_exists($file)) {
                throw new \Org\Heigl\Hyphenator\Exception\PathNotFoundException($file);
            }
        }
        $params = parse_ini_file($file);
        if (! is_array($params) || 1 > count($params)) {
            throw new \Org\Heigl\Hyphenator\Exception\InvalidArgumentException($file . ' is not a parseable file');
        }

        $option = new Options();
        foreach ($params as $key => $val) {
            if (! method_Exists($option, 'set' . $key)) {
                continue;
            }
            call_user_Func(array($option,'set' . $key), $val);
        }

        return $option;
    }

    /**
     * Set the default locale for this instance
     *
     * @param string $locale The locale to be set
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function setDefaultLocale($locale)
    {
        $this->defaultLocale = (string) $locale;

        return $this;
    }

    /**
     * Get the default locale for this instance
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }
}
