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
 * @link      http://github.com/heiglandreas/Hyphenator
 * @since     02.11.2011
 */

namespace Org\Heigl\Hyphenator;

/**
 * This class implements word-hyphenation
 *
 * Word-hyphenation is implemented on the basis of the algorithms developed by
 * Franklin Mark Liang for LaTeX as described in his dissertation at the department
 * of computer science at stanford university.
 *
 * The idea to this package came from Mathias Nater <mnater@mac.com> who
 * implemented this word-hyphenation-algorithm for javascript.
 *
 * After Implementing that algorithm for the first Hyphenator-Version I stumbled
 * over the Informations of LÁSZLÓ NÉMETH from OpenOffice.org.
 *
 * That brought me to change three things for the next Version of the
 * Hyphenator.
 * <ol>
 * <li>Use the Dictionary files from OpenOffice.org instead of the ones directly
 * from Tex because the OOo-Files are already stripped of the unnecessary
 * Informations</li>
 * <li>Add the possibility to use non-standard hyphenations</li>
 * <li>Add the possibility to add better word-tokenising</li>
 * </ol>
 *
 * Beside those changes there are some other changes between the first and the
 * second version of the Hyphenator.
 *
 * So Version 2 of the Hyphenator<ul>
 * <li>requires PHP5.3 as it uses namespaces.</li>
 * <li>aims to 100% Code-Coverage via Unit-Tests</li>
 * <li>removes some unnecessary options</li>
 * <li>is completely rewritten from scratch</li>
 * </ul>
 *
 * So here is the smalest example for the usage of the class:
 * <code>
 * &lt;?php
 * use \Org\Heigl\Hyphenator as h;
 * // First set the path to the configuration file
 * h\Hyphenator::setConfigFile('/path/to/the/config/file.properties');
 *
 * // Then create a hyphenator-instance for a given locale
 * $hyphenator = h\Hyphenator::factory('de_DE');
 *
 * // And finaly Hyphenate a given string
 * $hyphenatedText = $hyphenator->hyphenate($string);
 * </code>
 * Registering the autoloader is essential before the first call to the
 * Hyphenator
 * <code language="php">
 * &lt;?php
 * require_once '/path/to/Org/Heigl/Hyphenator/Hyphenator.php';
 * spl_autoload_register('\Org\Heigl\Hyphenator\Hyphenator::__autoload');
 * </code>
 * Of course the Hyphenator can be adapted to the most requirements via an
 * Options-Object. And the tokenisation in this small example uses the simple
 * WhiteSpace-Tokenizer. Other more complex Tokenizers are available.
 *
 * Examples for those can be found at http://github.com/heiglandreas/Hyphenator
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <a.heigl@wdv.de>
 * @copyright 2008-2011 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.alpha
 * @link      http://code.google.com/p/hyphenator
 * @link      http://www.tug.org/docs/liang/liang-thesis.pdf
 * @link      http://hunspell.sourceforge.net/tb87nemeth.pdf
 * @link      http://github.com/heiglandreas/Hyphenator
 * @since     04.11.2011
 */
final class Hyphenator
{

    const QUALITY_HIGHEST = 9;
    const QUALITY_HIGH    = 7;
    const QUALITY_NORMAL  = 5;
    const QUALITY_LOW     = 3;
    const QUALITY_LOWEST  = 1;

    /**
     * Storage for the Home-path.
     *
     * The configuration file is searched in different places.
     * <ol><li>Location given via the constant HYPHENATOR_HOME</li>
     * <li>Location set via \Org\Heigl\Hyphenator\Hyphenator::setDefaultHome()</li>
     * <li>Location set via \Org\Heigl\Hyphenator\Hyphenator::setHome()</li>
     * <li>The 'share'-Folder inside the Hyphenator-Package</li>
     * </ol>
     *
     * The configoration-object can also be obtained using the
     * \Org\Heigl\hypghenator::getConfig()-Method and can then be adapted
     * according to ones needs.
     *
     * @var string $_homePath
     */
    private $_homePath = null;

    /**
     * Storage of the default Home-Path
     *
     * @var string $_defaultHomePath
     */
    private static $_defaultHomePath = null;

    /**
     * Storage for the Options-Object.
     *
     * @var Options\Options $_options
     */
    private $_options = null;

    /**
     * Storage for the Dictionaries.
     *
     * @var Dictionary\DictionaryRegistry $_dicts
     */
    private $_dicts = null;

    /**
     * Storage for the Filters.
     *
     * @var Filter\FilterRegistry $_filters
     */
    private $_filters = null;

    /**
     * Set the Options
     *
     * @param \Org\Heigl\Hyphenator\Options\Options $options The options to set
     *
     * @return \Org\Heigl\Hyphenator\Hyphenator
     */
    public function setOptions(Options\Options $options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Get the Options
     *
     * @return \Org\Heigl\Hyphenator\Options\Options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Add a Dictionary to the Hyphenator
     *
     * @param \Org\Heigl\Hyphenator\Dictionary\Dictionary $dictionary The
     * Dictionary wit hyphenation-Patterns to add to this Hyphenator
     *
     * @return Org\Heigl\Hyphenator\Hyphenator
     */
    public function addDictionary(\Org\Heigl\Hyphenator\Dictionary\Dictionary $dictionary)
    {
        $this->_dicts->add($dictionary);
        return $this;
    }

    /**
     * Add a Filter to the Hyphenator
     *
     * @param \Org\Heigl\Hyphenator\Filter\Filter $filter The Filter with
     * non-standard-hyphenation-patterns
     *
     * @link http://hunspell.sourceforge.net/tb87nemeth.pdf
     * @return Org\Heigl\Hyphenator\Hyphenator
     */
    public function addFilter(\Org\Heigl\Hyphenator\Filter\Filter $filter)
    {
        $this->_filters->add($filter);
        return $this;
    }

    /**
     * This method gets the hyphenator-instance for the language <var>$language</var>
     *
     * If no instance exists, it is created and stored.
     *
     * @param string $language The language to use for hyphenating
     *
     * @return Org\Heigl\Hyphenator\Hyphenator A Hyphenator-Object
     * @throws Exception\InvalidArgumentException
     */
    public static function getInstance ( $language = 'en' )
    {
        $file       = dirname(__FILE__)
                    . DIRECTORY_SEPARATOR
                    . 'Hyphenator'
                    . DIRECTORY_SEPARATOR
                    . 'files'
                    . DIRECTORY_SEPARATOR
                    . Hyphenator::getTexFile($language);
        $parsedFile = self::getDefaultParsedFileDir()
                    . DIRECTORY_SEPARATOR
                    . $language
                    . '.php';
        if ( ! file_exists($parsedFile)) {
            Hyphenator::parseTexFile($file, $parsedFile, $language);
        }
        if ( ! file_exists($parsedFile)) {
            throw new Exception\InvalidArgumentException('file ' . $language . '.php does not exist');
            return false;
        }
        if ( ( count(Hyphenator::$_store) <= 0 ) ||
             ( ! array_key_exists($language, Hyphenator::$_store)) ||
             ( ! is_object(Hyphenator::$_store[$language]))||
             ( ! Hyphenator::$_store[$language] instanceof Hyphenator)) {
            // Begin IF.
            Hyphenator::$_store[$language] = new Hyphenator($language);
        }
        return Hyphenator::$_store[$language];
    }

    /**
     * The constructor initializing the Object
     *
     * @return void
     */
    public function __construct ()
    {
        $this->_dicts = new Dictionary\DictionaryRegistry();
        $this->_filters = new Filter\FilterRegistry();
    }

    /**
     * This method does the actual hyphenation.
     *
     * The given <var>$string</var> is splitted into chunks (i.e. Words) at
     * every blank.
     *
     * After that every chunk is hyphenated and the array of chunks is merged
     * into a single string using blanks again.
     *
     * This method does not take into account other word-delimiters than blanks
     * (eg. returns or tabstops) and it will fail with texts containing markup
     * in any way.
     *
     * @param string $string The string to hyphenate
     *
     * @return string The hyphenated string
     */
    public function hyphenate ( $string )
    {

        $this->_rawWord = array ();
        // If caching is enabled and the string is already cached, return the
        // cached version.
        if ( $this->isCachingEnabled()) {
            $result = $this->cacheRead($string);
            if ( false !== $result ) {
                return $result;
            }
        }
        $array = explode(' ', $string);
        $size  = count($array);
        for ( $i = 0; $i < $size; $i++ ) {
            $array[$i] = $this->hyphenateWord($array[$i]);
        }
        $hyphenatedString = implode(' ', $array);

        // If caching is enabled, write the hyphenated string to the cache.
        if ( $this->isCachingEnabled()) {
            $this->cacheWrite($string, $hyphenatedString);
        }

        // Return the hyphenated string.
        return $hyphenatedString;
    }

    /**
     * This method hyphenates a single word
     *
     * @param string $word The Word to hyphenate
     *
     * @return string the hyphenated word
     */
    public function hyphenateWord ( $word )
    {

        // If the Word is empty, return an empty string.
        if ( '' === trim($word) ) {
            return '';
        }

        // Check whether the word shall be hyphenated.
        $result = $this->_isNotToBeHyphenated($word);
        if ( false !== $result ) {
            return $result;
        }

        // If the length of the word is smaller than the minimum word-size,
        // return the word.
        if ( $this->_wordMin > strlen($word)) {
            return $word;
        }

        // Character 173 is the unicode char 'Soft Hyphen' wich may  not be
        // visible in some editors!
        // HTML-Entity for soft hyphenation is &shy;!
        if ( false !== strpos($word, '&shy;')) {
            return str_replace('&shy;', $this->_hyphen, $word);
        }

        // Replace a custom hyphenate-string with the hyphen.
        $result = $this->_replaceCustomHyphen($word);
        if ( false !== $result ) {
            return $result;
        }

        // If the word already contains a hyphen-character, we assume it is
        // already hyphenated and return the word 'as is'.
        if ( false !== strpos($word, $this->_hyphen)) {
            return $word;
        }

        // Hyphenate words containing special strings for further processing, so
        // put a zerowidthspace after it and hyphenate the parts separated by
        // the special string.
        $result = $this->_handleSpecialStrings($word);
        if ( false !== $result ) {
            return $result;
        }

        return $this->_hyphenateWord($word);
    }

    /**
     * Hyphenate a single word
     *
     * @param string $word The word to hyphenate
     *
     * @return string The hyphenated word
     */
    private function _hyphenateWord ( $word )
    {

        $prepend = '';
        $word    = $word;
        $append  = '';

        $specials = '\.\:\-\,\;\!\?\/\\\(\)\[\]\{\}\"\'\+\*\#\§\$\%\&\=\@';
        // If a special character occurs in the middle of the word, simply
        // return the word AS IS as the word can not really be hyphenated
        // automaticaly.
        if ( preg_match('/[^' . $specials . ']['.$specials.'][^'.$specials.']/', $word)) {
            return $word;
        }
        // If one ore more special characters appear before or after a word
        // we take the word in between and hyphenate that asn append and prepend
        // the special characters later on.
        if ( preg_match('/(['.$specials.']*)([^' . $specials . ']+)(['.$specials.']*)/', $word, $result)) {
            $prepend = $result [1];
            $word    = $result [2];
            $append  = $result [3];
        }

        $result = array ();

        $positions = $this->_getHyphenationPositions($word);
        $wl      = strlen($word);
        $lastOne = 0;

        for ( $i = 1; $i < $wl; $i++ ) {
            // If the integer on position $i is higher than 0 and is odd,
            // we can hyphenate at that position if the integer is lower or
            // equal than the set quality-level.
            // Additionaly we check whether the left and right margins are met.
            if ( ( 0 !== $positions[$i] ) &&
                 ( 1 === ( $positions[$i] % 2 ) ) &&
                 ( $positions[$i] <= $this->_quality ) &&
                 ( $i >= $this->_leftMin ) &&
                 ( $i <= ( strlen($word) - $this->_rightMin ) ) ) {
                // Begin IF.
                $sylable = substr($word, $lastOne, $i - $lastOne);

                $lastOne  = $i;
                $result[] = $sylable;
            }
        }
        $result [] = substr($word, $lastOne);
        $return = $prepend . trim(implode($this->_hyphen, $result)) . $append;
        return $return;
    }

    /**
     * Get the positions, where a hyphenation might occur and where not.
     *
     * @param string $word The word to hyphenate
     *
     * @return array The numerical positions-array
     */
    private function _getHyphenationPositions( $word )
    {

        $positions = array();
        $w         = '_' . strtolower($word) . '_';
        $wl        = strlen($w);
        // Initialize an array of length of the word with 0-values.
        for ( $i = 0; $i < $wl; $i++ ) {
            $positions[$i] = 0;
        }
        for ( $s = 0; $s < $wl -1; $s++ ) {
            $maxl   = $wl - $s;
            $window = substr($w, $s);
            for ( $l = $this->_shortestPattern; $l <= $maxl && $l <= $this->_longestPattern; $l++ ) {
                $part   = substr($window, 0, $l);
                $values = null;
                if ( isset($this->_pattern[$part])) {
                    // We found a pattern for this part.
                    $values    = (string) $this->_pattern[$part];
                    $i         = $s;
                    $v         = null;
                    $m         = strlen($values);
                    $corrector = 1;
                    for ( $p = 0; $p < $m; $p++ ) {
                        $v        = substr($values, $p, 1);
                        $arrayKey = $i + $p - $corrector;
                        if ( array_key_exists($arrayKey, $positions) && ( ( (int) $v > $positions[$arrayKey] ))) {
                            $positions[$arrayKey] = (int) $v;
                        }
                        if ( $v > 0 ) {
                            $corrector++;
                        }
                    }
                }
            }
        }
        return $positions;
    }

    /**
     * Check whether this string shall not be hyphenated
     *
     * If so, replace a string that marks strings not to be hyphenated with an
     * empty string. Also replace all custom hyphenations, as the word shall
     * not be hyphenated.
     * Finaly return the word 'as is'.
     *
     * If the word can be hyphenated, return false
     *
     * @param string $word The word to be hyphenated
     *
     * @return string|false
     */
    private function _isNotToBeHyphenated($word)
    {
        if ( ( null === $this->_noHyphenateString ) || ( 0 !== strpos($word, $this->_noHyphenateString))) {
            return false;
        }
        $string = str_replace($this->_noHyphenateString, '', $word);
        $string = str_replace($this->_customHyphen, '', $string);
        if ( null !== $this->_customizedMarker && true === $this->_markCustomized ) {
            $string = $this->getCustomizationMarker() . $string;
        }
        return $string;
    }

    /**
     * Replace a custom hyphen
     *
     * @param string $word The word to parse
     *
     * @return string|false
     */
    private function _replaceCustomHyphen ( $word )
    {
        if ( ( null === $this->_customHyphen ) || ( false === strpos($word, $this->_customHyphen)) ) {
            return false;
        }
        $string = str_replace($this->_customHyphen, $this->_hyphen, $word);
        if ( null !== $this->_customizedMarker && true === $this->_markCustomized) {
            $string = $this->getCustomizationMarker() . $string;
        }
        return $string;
    }

    /**
     * Handle special strings
     *
     * Hyphenate words containing special strings for further processing, so
     * put a zerowidthspace after it and hyphenate the parts separated by
     * the special string.
     *
     * @param string $word The Word to hyphenate
     *
     * @return string|false
     */
    private function _handleSpecialStrings ( $word )
    {

        foreach ( $this->_specialStrings as $specialString ) {
            if ( false === strpos($word, $specialString)) {
                continue;
            }
            // Word contains a special string so put a zerowidthspace after
            // it and hyphenate the parts separated with the special string.
            $parts   = explode($specialString, $word);
            $counter = count($parts);
            for ( $i = 0; $i < $counter; $i++ ) {
                $parts[$i] = $this->hyphenateWord($parts[$i]);
            }
            return implode($specialString, $parts);
        }
        return false;
    }

    /**
     * Set the default home-Path
     *
     * @param string $homePath The defaubnt Hyphenator Home-path.
     *
     * @throws Exception\PathNotFoundException
     * @throws Exception\PathNotDirException
     * @return void
     */
    public static function setDefaultHomePath($homePath)
    {
        if ( ! file_exists($homePath)) {
            throw new Exception\PathNotFoundException($homePath . ' does not exist' );
        }
        if ( ! is_Dir($homePath)) {
            throw new Exception\PathNotDirException($homePath . ' is not a directory' );
        }

        self::$_defaultHomePath = realpath($homePath);
    }

    /**
     * Get the default Home-Path
     *
     * @return string
     */
    public static function getDefaultHomePath()
    {
        if ( is_Dir(self::$_defaultHomePath) )     {
            return self::$_defaultHomePath;
        }
        if ( defined('HYPHENATOR_HOME') && is_Dir(HYPHENATOR_HOME) ) {
            return realpath(HYPHENATOR_HOME);
        }
        if ( $home = getenv('HYPHENATOR_HOME')) {
            if ( is_Dir($home) ) {
                return $home;
            }
        }
        return __DIR__ . '/share';
    }

    /**
     * Set the instance-home-Path
     *
     * @param string $homePath This instances home-path.
     *
     * @throws Exception\PathNotFoundException
     * @throws Exception\PathNotDirException
     * @return \Org\Heigl\Hyphenator\Hyphenator
     */
    public function setHomePath($homePath)
    {
        if ( ! file_exists($homePath)) {
            throw new Exception\PathNotFoundException($homePath . ' does not exist' );
        }
        if ( ! is_Dir($homePath)) {
            throw new Exception\PathNotDirException($homePath . ' is not a directory' );
        }

        $this->_homePath = realpath($homePath);

        return $this;
    }

    /**
     * Get this instances Home-Path.
     *
     * If no homePath is set for this instance this method will return the
     * result of the \Org\HEigl\Hyphenator\Hyphenator::getdefaultzHomePath()
     * Method
     *
     * @return string
     */
    public function getHomePath()
    {
        if ( ! is_dir($this->_homePath) ) {
            return self::getDefaultHomePath();
        }
        return $this->_homePath;
    }

    /**
     * autoload classes.
     *
     * @param string $className the name of the class to load
     *
     * @return void
     */
    public static function __autoload($className)
    {
        if ( 0 !== strpos($className, 'Org\\Heigl\\Hyphenator') ) {
            return false;
        }
        $className = substr($className,strlen('Org\\Heigl\\Hyphenator\\'));
        $file = str_replace('\\', '/', $className) . '.php';
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . $file;
        if ( ! file_exists(realpath($fileName)) ) {
            return false;
        }
        if ( ! @include_once $fileName ) {
            return false;
        }
        return true;
    }

    /**
     * Register this packages autoloader with the autoload-stack
     *
     * @return void
     */
    public static function registerAutoload()
    {
        return spl_autoload_register(array('\Org\Heigl\Hyphenator\Hyphenator', '__autoload'));
    }
}
