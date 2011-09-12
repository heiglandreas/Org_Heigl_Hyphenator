<?php
/**
 * $Id: Hyphenator.php 1114 2009-07-10 08:48:44Z heiglandreas $
 *
 * Copyright (c) 2008-2009 Andreas Heigl<andreas@heigl.org>
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
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   SVN: $Revision: 1114 $
 * @since     12.06.2008
 */

/**
 * This class implements word-hyphenation
 *
 * Word-hyphenation is implemented on the basis of the algorithms developed by
 * Franklin Mark Liang for LaTeX as described in his dissertation at the department
 * of computer science at stanford university.
 *
 * This package is based on an idea of Mathias Nater<mnater@mac.com> who
 * implemented this word-hyphenation-algorithm for javascript.
 *
 * Hyphenating means in this case, that all possible hypheantions in a word are
 * marked using the soft-hyphen character (ASCII-Caracter 173) or any other
 * character set via the setHyphen() method.
 *
 * A complete text will first be divided into words via a regular expression
 * that takes all characters that the \w-Special-Character specifies as well as
 * the '@'-Character and possible other - language-specific - characters that
 * can be set via the setSpecialChars() method.
 *
 * Hyphenation is done using a set of files taken from a current TeX-Distribution
 * that are matched using the method getTexFile().
 *
 * So here is an example for the usage of the class:
 * <code>
 * <?php
 * // Place all parsed files in the given folder instead of the default one
 * Org_Heigl_Hyphenator::setDefaultParsedFileDir('/tmp/hyphenator');
 * $hyphenator = Org_Heigl_Hyphenator::getInstance ( 'de' );
 * $hyphenator -> setHyphen ( '-' )
 *             // Minimum 5 characters before the first hyphenation
 *             -> setLeftMin ( 5 )
 *             // Hyphenate only words with more than 4 characters
 *             -> setWordMin ( 5 )
 *             // Set some special characters
 *             -> setSpecialChars ( 'äöüß' )
 *             // Only Hyphenate with the best quality
 *             -> setQuality ( Org_Heigl_Hyphenate::QUALITY_HIGHEST )
 *             // Words that shall not be hyphenated have to start with this string
 *             -> setNoHyphenateMarker ( 'nbr:' )
 *             // Words that contain this string are custom hyphenated
 *             -> setCustomHyphen ( '--' );
 *
 * // Hyphenate the string $string
 * $hyphenated = $hyphenator -> hyphenate ( $text );
 * ?>
 * </code>
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <a.heigl@wdv.de>
 * @copyright 2008-2010 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   SVN: $Revision: 1114 $
 * @see       http://code.google.com/p/hyphenator
 * @see       http://www.tug.org/docs/liang/liang-thesis.pdf
 * @since     12.06.2008
 */
final class Org_Heigl_Hyphenator
{

    const QUALITY_HIGHEST = 9;
    const QUALITY_HIGH    = 7;
    const QUALITY_NORMAL  = 5;
    const QUALITY_LOW     = 3;
    const QUALITY_LOWEST  = 1;

    /**
     * This is the default language to use.
     *
     * @var string $_defaultLanguage
     */
    private static $_defaultLanguage = 'en';

    /**
     * This property stores an instance of the hyphenator for each language
     *
     * @var array $_store
     */
    private static $_store = array ();

    /**
     * Store the caching-Object
     *
     * @var Zend_Cache $_cache
     */
    private static $_cache = null;

    /**
     * Store whether caching is enabled or not
     *
     * Caching is turned off by default
     *
     * @var boolean $_cachingEnabled
     */
    private $_cachingEnabled = false;

    /**
     * The String that marks a word not to hyphenate
     *
     * @var string _noHyphenateString
     */
    private $_noHyphenateString = null;

    /**
     * This property defines the default hyphenation-character.
     *
     * This is set during instantiation to the Soft-Hyphen-Character (ASCII 173)
     * but can be overwritten using the setHyphen()-Method
     *
     * @var string $_hyphen
     */
    private $_hyphen = null;

    /**
     * This property defines how many characters need to stay to the left side
     * of a hyphenation.
     *
     * This defaults to 2 characters, but it can be overwritten using the
     * setLeftMin()-Method
     *
     * @var int $_leftmin
     */
    private $_leftMin = 2;

    /**
     * This property defines how many characters need to stay to the right side
     * of a hyphenation.
     *
     * This defaults to 2 characters, but it can be overwritten using the
     * setRightMin()-Method
     *
     * @var int $_rightmin
     */
    private $_rightMin = 2;

    /**
     * Whether to mark Customized Hyphenations or not.
     *
     * @var boolean $_markCustomized
     */
    private $_markCustomized = false;

    /**
     * When customizations shall be used, what string shall be prepend to the
     * word that contains customizations.
     *
     * @var string|null $_customizedMarker
     */
    private $_customizedMarker = '<!--cm-->';

    /**
     * The shortest pattern length to use for Hyphenating
     *
     * @var int $_shortestPattern
     */
    private $_shortestPattern = 2;

    /**
     * The longest pattern length to use for hyphenating.
     *
     * Using a high number (like '10') almost every pattern should be used
     *
     * @var int $_longestPattern
     */
    private $_longestPattern = 10;

    /**
     * This property defines some spechial Characters for a language that need
     * to be taken into account for the definition of a word.
     *
     * @var string $_specialChars
     */
    private $_specialChars = '';

    /**
     * This property defines, how long a word that can be hyphenated needs to be.
     *
     * This defaults to 6 Characters, but it can be overridden using
     * setWordMin()
     *
     * @var int $_wordMin
     */
    private $_wordMin = 6;

    /**
     * This property contains the pattern-array for a specific language
     *
     * @var array|null $_pattern
     */
    private $_pattern = null;

    /**
     * The currently set quality for hyphenation
     *
     * The higher the number, the better the hyphenation is
     *
     * @var int $_quality
     */
    private $_quality = 9;

    /**
     * The String that shall be searched for as a customHyphen
     * @var string $_customHyphen
     */
    private $_customHyphen = '--';

    /**
     * The special strings to parse as hyphenations
     *
     * @var array $_specialStrings
     */
    private $_specialStrings = array ( '-/-', '-' );

    /**
     * Tags to completly skip when treating text as HTML
     *
     * @var array $_skipTags
     */
    private $_skipTags = array( 'head', 'script', 'style', 'code', 'pre' );

    /**
     * This is the static way of hyphenating a string.
     *
     * This method gets the appropriate Hyphenator-object and calls the method
     * hyphenate() on it.
     *
     * @param string $string  The String to hyphenate
     * @param string $options The Options to use for Hyphenation
     *
     * @return string The hyphenated string
     */
    public static function parse ( $string, $options = null ) {

        if ( null === $options ) {
            $options = array ();
        }
        if ( ! isset ( $options [ 'language' ] ) ) {
            $options [ 'language' ] = Org_Heigl_Hyphenator::getDefaultLanguage ();
        }
        // Get the instance for the language.
        $hyphenator = Org_Heigl_Hyphenator::getInstance ( $options ['language'] );

        unset ( $options['language'] );
        foreach ( $options as $key => $val ) {
            call_user_func ( array ( $hyphenator, 'set' . $key ), $val );
        }

        // Hyphenate the string using the Hyphenator instance.
        $string = $hyphenator -> hyphenate ( $string );

        // Return the hyphenated string.
        return $string;
    }

    /**
     * Set the default Language
     *
     * @param string $language The Lanfuage to set.
     *
     * @return void
     */
    public static function setDefaultLanguage ( $language ) {
        Org_Heigl_Hyphenator::$_defaultLanguage = $language;
    }

    /**
     * Get the default language
     *
     * @return string
     */
    public static function getDefaultLanguage () {
        return Org_Heigl_Hyphenator::$_defaultLanguage;
    }
    /**
     * This method gets the hyphenator-instance for the language <var>$language</var>
     *
     * If no instance exists, it is created and stored.
     *
     * @param string $language The language to use for hyphenating
     *
     * @return Org_Heigl_Hyphenator A Hyphenator-Object
     * @throws InvalidArgumentException
     */
    public static function getInstance ( $language = 'en' ) {
        $file       = dirname ( __FILE__ )
                    . DIRECTORY_SEPARATOR
                    . 'Hyphenator'
                    . DIRECTORY_SEPARATOR
                    . 'files'
                    . DIRECTORY_SEPARATOR
                    . Org_Heigl_Hyphenator::getTexFile ( $language );
        $parsedFile = self::getDefaultParsedFileDir()
                    . DIRECTORY_SEPARATOR
                    . $language
                    . '.php';
        if ( ! file_exists ( $parsedFile ) ) {
            Org_Heigl_Hyphenator::parseTexFile ( $file, $parsedFile, $language );
        }
        if ( ! file_exists ( $parsedFile ) ) {
            throw new InvalidArgumentException( 'file ' . $language . '.php does not exist' );
            return false;
        }
        if ( ( count ( Org_Heigl_Hyphenator::$_store ) <= 0 ) ||
             ( ! array_key_exists ( $language, Org_Heigl_Hyphenator::$_store ) ) ||
             ( ! is_object ( Org_Heigl_Hyphenator::$_store[$language] ) )||
             ( ! Org_Heigl_Hyphenator::$_store[$language] instanceof Org_Heigl_Hyphenator ) ) {
            // Begin IF.
            Org_Heigl_Hyphenator::$_store[$language] = new Org_Heigl_Hyphenator($language);
        }
        return Org_Heigl_Hyphenator::$_store[$language];
    }

    /**
     * This method parses a TEX-Hyphenation file and creates the appropriate
     * PHP-Hyphenation file
     *
     * @param string $file       The original TEX-File
     * @param string $parsedFile The PHP-File to be created
     *
     * @return boolean
     */
    public static function parseTexFile ( $file, $parsedFile ) {
        $fc    = file_get_contents ( $file );
        $array = array ();
        if ( ! preg_match ( '/[\\n\\r]\\\\patterns\\{(.*)\\}\\s*\\\\/sim', $fc, $array ) ) {
            return false;
        }
        $fc         = preg_replace ( '/%.*/', '', $array[1] );
        $fc         = preg_replace ( '/\\\\n\\{(.+?)\\}/', '\1', $fc );
        $fc         = preg_replace ( array('/"a/', '/"o/', '/"u/', '/\\./' ), array ( 'ä', 'ö', 'ü', '_' ), $fc );
        $array      = preg_split ( '/\\s+/', $fc );
        $fh         = fopen ( $parsedFile, 'w+' );
        if ( ! $fh ) {
            throw new Exception ( 'Unable to open file for writing: ' . $parsedFile );
        }
        $fileheader = '<?php
/**
 * $'.'Id'.'$
 *
 * Copyright (c) 2008-2010 Andreas Heigl<andreas@heigl.org>
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
 * This file has been automaticly created from the file ' . basename ( $file ) . '
 * via the method Org_Heigl_Hyphenator::parseTexFile().
 *
 * DO NOT EDIT THIS FILE EXCEPT YOU KNOW WHAT YOU DO!!
 *
 * @category   Org_Heigl
 * @package    Org_Heigl_Hyphenator
 * @subpackage HyphenationFiles
 * @author     Org_Heigl_Hyphenator
 * @copyright  2008-2010 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    1.0
 * @since      ' . date ( 'd.m.Y' ) . '
 */
';
        fwrite ( $fh, $fileheader );
        foreach ( $array as $pattern ) {
            if ( strpos ( $pattern, '\\' ) !== false ) {
                continue;
            }
            $patternstring = '';
            $patternint    = '';
            $strlen        = strlen ( $pattern );
            for ( $i = 0; $i < $strlen; $i++ ) {
                if ( ( ( $i ) <= $strlen ) && preg_match ( '/[0-9]/', substr ( $pattern, $i, 1 ) ) ) {
                    $patternint .= substr ( $pattern, $i, 1 );
                } else {
                    $patternint .= '0';
                }
            }
            $patternstring = preg_replace ( array('/[0-9]/','/\'/'), array('','\\’'), $pattern );
            if ( $patternstring != '' ) {
                fwrite ( $fh, '$pattern[\'' . $patternstring . '\'] = \'' . $patternint . '\';' . "\n" );
            }
        }
        fwrite ( $fh, '?>' );
        fclose ( $fh );
        return true;
    }

    /**
     * This method returns the name of a TeX-Hyphenation file to a language code
     *
     * @param string $language The language code to get the to use
     *
     * @return string
     */
    public static function getTexFile ( $language ) {
        $files = array (
            ' '  => 'zerohyph.tex',
            'ba' => 'bahyph.tex',
            'ca' => 'cahyph.tex',
            'de' => 'dehyphn.tex',
            'de_OLD' => 'dehypht.tex',
            'dk' => 'dkcommon.tex',
            'ee' => 'eehyph.tex',
            'en' => 'hyphen.tex',
            'eo' => 'eohyph.tex',
            'es' => 'eshyph.tex',
            'fi' => 'fihyph.tex',
            'fr' => 'frhyph.tex',
            'ga' => 'gahyph.tex',
            'gr' => 'grhyph.tex',
            'hr' => 'hrhyph.tex',
            'hu' => 'huhyph.tex',
            'ic' => 'icehyph.tex',
            'in' => 'inhyph.tex',
            'it' => 'ithyph.tex',
            'la' => 'lahyph.tex',
            'mn' => 'mnhyphen.tex',
            'nl' => 'nehyph.tex',
            'no' => 'nohyph.tex',
            'pl' => 'plhyph.tex',
            'pt' => 'pt8hyph.tex',
            'ro' => 'rohyphen.tex',
            'se' => 'sehyph.tex',
            'si' => 'sihyph23.tex',
            'sk' => 'skhyph2e.tex',
            'sr' => 'srhyphc.tex',
            'tr' => 'trhyph.tex',
        );
        if ( array_key_exists ( $language, $files ) ) {
            return $files[$language];
        }
        return $files['en'];
    }

    /**
     * Set an instance of Zend_Cache as Caching-Backend.
     *
     * @param Zend_Cache $cache The caching Backend
     *
     * @uses Zend_Cache
     * @link http://framework.zend.com/zend.cache.html
     * @return boolean
     */
    public static function setCache ( Zend_Cache $cache ) {

        Org_Heigl_Hyphenator::$_cache = $cache;
        return true;
    }

    /**
     * Get the cache-Object
     *
     * @return Zend_Cache
     */
    public static function getCache () {
        return Org_Heigl_Hyphenator::$_cache;
    }

    /**
     * This is the constructor, that initialises the hyphenator for the given
     * language <var>$language</var>
     *
     * This constructor is  declared private to ensure, that it is only called
     * via the getInstance() method, so we only initialize the stuff only once
     * for each language.
     *
     * @param string $language The language to use for hyphenating
     *
     * @throws Exception
     */
    public function __construct ( $language = 'en' ) {

        $lang = array ( $language );
        $pos  = strpos ( '_', $language );
        if ( false !== $pos ) {
            $lang [] = substr ( $language, 0, $pos );
        }
        foreach ( $lang as $language ) {
            $parsedFile = self::getDefaultParsedFileDir()
                        . DIRECTORY_SEPARATOR
                        . $language
                        . '.php';

            $this -> _language = $language;
            try {
                include_once $parsedFile;
            } catch ( Exception $e ) {
                throw new Exception ( 'File \'' . $parsedFile . '\' could not be found' );
            }
        }
        $this -> _pattern = $pattern;

        if ( null === $this -> _hyphen ) {
            $this -> _hyphen = chr ( 173 );
        }
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
    public function hyphenate ( $string, $html=False ) {

        $this -> _rawWord = array ();
        // If caching is enabled and the string is already cached, return the
        // cached version.
        if ( $this -> isCachingEnabled () ) {
            $result = $this -> cacheRead ( $string );
            if ( false !== $result ) {
                return $result;
            }
        }

        if ( $html )
            $array = preg_split ( '/([\s<>])/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
        else
            $array = preg_split ( '/([\s])/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE );
        $size  = count ( $array );

        // HTML
        if ( $html ) {
            $inTag = False;
            $inSkip = False;
            $skip = $this->getSkipTags();
            $skipEnd = $this->getSkipTagsEnd();
            for ( $i = 0; $i < $size; $i++ ) {
                if ( ! $inTag and substr( $array[$i], 0, 1 ) == '<' )
                    $inTag = True;
                # XXX This isn't perfect (But kinda works for now)
                if ( ! $inSkip and $i+2 < $size and in_array($array[$i] . $array[$i+1], $skip ) )
                    $inSkip = True;

                if ( ! $inTag and ! $inSkip
                    and ! ( ( substr( $array[$i], 0, 1 ) == '&' and substr( $array[$i], -1, 1 ) == ';' ) )
                ) {
                    $array[$i] = $this -> hyphenateWord ( $array[$i] );
                }

                if ( substr( $array[$i], -1, 1 ) == '>' )
                    $inTag = False;
                if ( $i+2 < $size and in_array($array[$i] . $array[$i+1] . $array[$i+2] , $skipEnd ) )
                    $inSkip = False;
            }
        }
        // Plain text
        else
        {
            for ( $i = 0; $i < $size; $i++ ) {
               $array[$i] = $this -> hyphenateWord ( $array[$i] );
            }
        }
        $hyphenatedString = implode ( '', $array );

        // If caching is enabled, write the hyphenated string to the cache.
        if ( $this -> isCachingEnabled () ) {
            $this -> cacheWrite ( $string, $hyphenatedString );
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
    public function hyphenateWord ( $word ) {

        // If the Word is empty, return an empty string.
        if ( '' === trim ( $word ) ) {
            return $word;
        }

        // Check whether the word shall be hyphenated.
        $result = $this -> _isNotToBeHyphenated ( $word );
        if ( false !== $result ) {
            return $result;
        }

        // If the length of the word is smaller than the minimum word-size,
        // return the word.
        if ( $this -> _wordMin > strlen ( $word ) ) {
            return $word;
        }

        // Character 173 is the unicode char 'Soft Hyphen' wich may  not be
        // visible in some editors!
        // HTML-Entity for soft hyphenation is &shy;!
        if ( false !== strpos ( $word, '&shy;' ) ) {
            return str_replace ( '&shy;', $this -> _hyphen, $word );
        }

        // Replace a custom hyphenate-string with the hyphen.
        $result = $this -> _replaceCustomHyphen ( $word );
        if ( false !== $result ) {
            return $result;
        }

        // If the word already contains a hyphen-character, we assume it is
        // already hyphenated and return the word 'as is'.
        if ( false !== strpos ( $word, $this -> _hyphen ) ) {
            return $word;
        }

        // Hyphenate words containing special strings for further processing, so
        // put a zerowidthspace after it and hyphenate the parts separated by
        // the special string.
        $result = $this -> _handleSpecialStrings ( $word );
        if ( false !== $result ) {
            return $result;
        }

        return $this -> _hyphenateWord ( $word );
    }

    /**
     * Hyphenate a single word
     *
     * @param string $word The word to hyphenate
     *
     * @return string The hyphenated word
     */
    private function _hyphenateWord ( $word ) {

        $prepend = '';
        $word    = $word;
        $append  = '';

        $specials = '\.\:\-\,\;\!\?\/\\\(\)\[\]\{\}\"\'\+\*\#\§\$\%\&\=\@';
        // If a special character occurs in the middle of the word, simply
        // return the word AS IS as the word can not really be hyphenated
        // automaticaly.
        if ( preg_match ( '/[^' . $specials . ']['.$specials.'][^'.$specials.']/', $word ) ) {
            return $word;
        }
        // If one ore more special characters appear before or after a word
        // we take the word in between and hyphenate that asn append and prepend
        // the special characters later on.
        if ( preg_match ( '/(['.$specials.']*)([^' . $specials . ']+)(['.$specials.']*)/', $word, $result ) ) {
            $prepend = $result [1];
            $word    = $result [2];
            $append  = $result [3];
        }

        $result = array ();

        $positions = $this -> _getHyphenationPositions ( $word );
        $wl      = strlen ( $word );
        $lastOne = 0;

        for ( $i = 1; $i < $wl; $i++ ) {
            // If the integer on position $i is higher than 0 and is odd,
            // we can hyphenate at that position if the integer is lower or
            // equal than the set quality-level.
            // Additionaly we check whether the left and right margins are met.
            if ( ( 0 !== $positions[$i] ) &&
                 ( 1 === ( $positions[$i] % 2 ) ) &&
                 ( $positions[$i] <= $this -> _quality ) &&
                 ( $i >= $this -> _leftMin ) &&
                 ( $i <= ( strlen ( $word ) - $this -> _rightMin ) ) ) {
                // Begin IF.
                $sylable = substr ( $word, $lastOne, $i - $lastOne );

                $lastOne  = $i;
                $result[] = $sylable;
            }
        }
        $result [] = substr ( $word, $lastOne );
        $return = $prepend . trim ( implode ( $this -> _hyphen, $result ) ) . $append;
        return $return;
    }

    /**
     * Get the positions, where a hyphenation might occur and where not.
     *
     * @param string $word The word to hyphenate
     *
     * @return array The numerical positions-array
     */
    private function _getHyphenationPositions ( $word ) {

        $positions = array();
        $w         = '_' . strtolower ( $word ) . '_';
        $wl        = strlen ( $w );
        // Initialize an array of length of the word with 0-values.
        for ( $i = 0; $i < $wl; $i++ ) {
            $positions[$i] = 0;
        }
        for ( $s = 0; $s < $wl -1; $s++ ) {
            $maxl   = $wl - $s;
            $window = substr ( $w, $s );
            for ( $l = $this -> _shortestPattern; $l <= $maxl && $l <= $this -> _longestPattern; $l++ ) {
                $part   = substr ( $window, 0, $l );
                $values = null;
                if ( isset ( $this -> _pattern[$part] ) ) {
                    // We found a pattern for this part.
                    $values    = (string) $this -> _pattern [$part];
                    $i         = $s;
                    $v         = null;
                    $m         = strlen ( $values );
                    $corrector = 1;
                    for ( $p = 0; $p < $m; $p++ ) {
                        $v        = substr ( $values, $p, 1 );
                        $arrayKey = $i + $p - $corrector;
                        if ( array_key_exists ( $arrayKey, $positions) && ( ( (int) $v > $positions[$arrayKey] ) ) ) {
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
    private function _isNotToBeHyphenated ( $word ) {
        if ( ( null === $this -> _noHyphenateString ) || ( 0 !== strpos ( $word, $this -> _noHyphenateString ) ) ) {
            return false;
        }
        $string = str_replace ( $this -> _noHyphenateString, '', $word );
        $string = str_replace ( $this -> _customHyphen, '', $string );
        if ( null !== $this -> _customizedMarker && true === $this -> _markCustomized ) {
            $string = $this -> getCustomizationMarker () . $string;
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
    private function _replaceCustomHyphen ( $word ) {
        if ( ( null === $this -> _customHyphen ) || ( false === strpos ( $word, $this -> _customHyphen ) ) ) {
            return false;
        }
        $string = str_replace ( $this -> _customHyphen, $this -> _hyphen, $word );
        if ( null !== $this -> _customizedMarker && true === $this -> _markCustomized ) {
            $string = $this -> getCustomizationMarker () . $string;
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
    public function _handleSpecialStrings ( $word ) {

        foreach ( $this -> _specialStrings as $specialString ) {
            if ( false === strpos ( $word, $specialString ) ) {
                continue;
            }
            // Word contains a special string so put a zerowidthspace after
            // it and hyphenate the parts separated with the special string.
            $parts   = explode ( $specialString, $word );
            $counter = count ( $parts );
            for ( $i = 0; $i < $counter; $i++ ) {
                $parts[$i] = $this -> hyphenateWord ( $parts[$i] );
            }
            return implode ( $specialString, $parts );
        }
        return false;
    }

    /**
     * Set the special strings
     *
     * These are strings that can be used for further parsing of the text.
     *
     * For instance a string to be replaced with a soft return or any other
     * symbol your application needs.
     *
     * @param array $specialStrings An array of special strings.
     *
     * @return Org_Heigl_Hyphenator
     */
    public function setSpecialStrings ( $specialStrings = array () ) {

        $this -> _specialStrings = (array) $specialStrings;
        return $this;
    }

    /**
     * This method sets the Hyphenation-Character.
     *
     * @param string $char The Hyphenation Character
     *
     * @return Org_Heigl_Hyphenator Provides fluent Interface
     */
    public function setHyphen ( $char ) {
        $this -> _hyphen = (string) $char;
        return $this;
    }

    /**
     * Get the hyphenation character
     *
     * @return string
     */
    public function getHyphen () {
        return $this -> _hyphen;
    }

    /**
     * This method sets the minimum Characters, that have to stay to the left of
     * a hyphenation
     *
     * @param int $count The left minimum
     *
     * @return Org_Heigl_Hyphenator Provides fluent Interface
     */
    public function setLeftMin ( $count ) {
        $this -> _leftMin = (int) $count;
        return $this;
    }

    /**
     * This method sets the minimum Characters, that have to stay to the right of
     * a hyphenation
     *
     * @param int $count The minimmum characters
     *
     * @return Org_Heigl_Hyphenator Provides fluent Interface
     */
    public function setRightMin ( $count) {
        $this -> _rightMin = (int) $count;
        return $this;
    }

    /**
     * This method sets the minimum Characters a word has to have before being
     * hyphenated
     *
     * @param int $count The minimmum characters
     *
     * @return Org_Heigl_Hyphenator Provides fluent Interface
     */
    public function setWordMin ( $count) {
        $this -> _wordMin = (int) $count;
        return $this;
    }

    /**
     * This method sets the special Characters for a specified language
     *
     * @param string $chars The spechail characters
     *
     * @return Org_Heigl_Hyphenator Provides fluent Interface
     */
    public function setSpecialChars ( $chars ) {
        $this -> specialChars = $chars;
        return $this;
    }

    /**
     * Enable or disable caching of hyphenated texts
     *
     * @param boolean $caching Whether to enable caching or not. Defaults to
     * <var>true</var>
     *
     * @return Org_Heigl_Hyphenator
     */
    public function enableCaching ( $caching = true ) {
        $this -> _cachingEnabled = (bool) $caching;

        return $this;
    }

    /**
     * Check whether caching is enabled or not
     *
     * @return boolean
     */
    public function isCachingEnabled () {
        return (bool) $this -> _cachingEnabled;
    }

    /**
     * Write <var>string</var> to the cache.
     *
     * <var>string</var> can be retrieved using <var>key</var>
     *
     * @param string $key    The key under which the string can be found in the cache
     * @param string $string The string to cache
     *
     * @return Org_Heigl_Hyphenator
     */
    public function cacheWrite ( $key, $string ) {

        $cache = Org_Heigl_Hyphenator::getCache ();

        if ( false === $this -> cacheRead ( $key ) ) {

            $cache -> save ( $string, $key );
        }

        return $this;
    }

    /**
     * Get the cached string to a key
     *
     * @param string $key The key to return a string to
     *
     * @return string
     */
    public function cacheRead ( $key ) {

        $cache = Org_Heigl_Hyphenator::getCache ();

        $result = $cache -> load ( $key );

        if ( ! $result ) {
            return false;

        }
        return $result;
    }

    /**
     * Set the quality that the Hyphenation needs to have minimum
     *
     * The lower the number, the better is the quality
     *
     * @param int $quality The quality-level to set
     *
     * @return Org_Heigl_Hyphenator
     */
    public function setQuality ( $quality = 5 ) {
        $this -> _quality = (int) $quality;
        return $this;
    }

    /**
     * Set a string that will be replaced with the soft-hyphen before
     * Hyphenation actualy starts.
     *
     * If this string is found in a word no hyphenation will be done except for
     * the place where the custom hyphen has been found
     *
     * @param string $customHyphen The Custom Hyphen to set
     *
     * @return Org_Heigl_Hyphenator
     */
    public function setCustomHyphen ( $customHyphen = null ) {
        $this -> _customHyphen = $customHyphen;

        return $this;
    }

    /**
     * Set a string that marks a words not to hyphenate
     *
     * @param string $marker THe Marker that marks a word
     *
     * @return Org_Heigl_Hyphenator
     */
    public function setNoHyphenateMarker ( $marker = null ) {
        $this -> _noHyphenateString = $marker;

        return $this;
    }

    /**
     * Get the marker for custom hyphenations
     *
     * @return string
     */
    public function getCustomMarker () {
        return (string) $this -> _customHyphen;
    }

    /**
     * Get the marker for Words not to hyphenate
     *
     * @return string
     */
    public function getNoHyphenMarker () {
        return (string) $this -> _noHyphenateString;
    }

    /**
     * Set and retrieve whether or not to mark custom hyphenations
     *
     * This method always returns the current setting, so you can set AND
     * retrieve the value with this method.
     *
     * @param null|booelan $mark Whether or not to mark
     *
     * @return boolean
     */
    public function markCustomization ( $mark = null ) {
        if ( null !== $mark ) {
            $this -> _markCustomized = (bool) $mark;
        }
        return (bool) $this -> _markCustomized;
    }

    /**
     * Set the string that shall be prepend to a customized word.
     *
     * @param string $marker The Marker to set
     *
     * @return Org_Heigl_Hyphenator
     */
    public function setCustomizationMarker ( $marker ) {
        $this -> _customizedMarker = (string) $marker;
        return $this;
    }

    /**
     * Set list of tags to completly skip when treating text as HTML.
     *
     * @param array $tags
     *
     * @return Org_Heigl_Hyphenator
     */
    public function setSkipTags ($tags) {
        $this -> _skipTags = $tags;

        return $this;
    }

    /**
     *
     */
    public function getSkipTags () {
        $array = array();
        foreach ($this -> _skipTags as $t)
        {
            $array[] = '<' . $t . '>';
            $array[] = '<' . $t;
        }

        return $array;
    }

    /**
     *
     */
    public function getSkipTagsEnd () {
        $array = array();
        foreach ($this -> _skipTags as $t)
            $array[] = '</' . $t . '>';

        return $array;
    }


    /**
     * Get the string that shall be prepend to a customized word.
     *
     * @return string
     */
    public function getCustomizationMarker () {
        return (string) $this -> _customizedMarker;
    }

    /**
     * Set the default directory for parsed files
     *
     * This is the place where the precompiled hyphenation-files are stored.
     * The directory will be created, if possible. If the directory does not
     * exist and can not be created, the default temporary directory
     * identified by 'get_sys_temp_dir' will be used.
     *
     * If that directory can not be retrieved, an Exception will be raised.
     *
     * @param string $path
     *
     * @throws InvalidArgumentException
     * @return void
     */
    public static function setDefaultParsedFileDir($path)
    {
        if ( false === realpath($path) ) {
            self::mkdir( $path, 0777 );
        }
        if ( false === realpath( $path ) ) {
            $path = sys_get_temp_dir()
                    . DIRECTORY_SEPARATOR
                    . 'org_heigl_hyphenator_'
                    . get_current_user();
            @mkdir( $path, 0777 );
        }
        if ( false === realpath( $path ) ) {
            throw new InvalidArgumentException( 'The given folder could not be retrieved' );
        }

        self::$_defaultParsedFileDir = $path;
    }

    /**
     * Store the default parsedFileDir
     *
     * @var string $_defaultParsedFileDir
     */
    protected static $_defaultParsedFileDir = null;

    /**
     * Get the default parsedFile directory
     *
     * @return string
     */
    public static function getDefaultParsedFileDir()
    {
        return self::$_defaultParsedFileDir;
    }

    /**
     * Recursively create a directory including all it's parents
     *
     * @param string $folder
     * @param int $right
     *
     * @return
     */
    public static function mkdir( $folder, $right=0777 )
    {
        $parent = dirname( $folder );
        if ( ! $parent ) {
            return;
        }
        $self = basename( $folder );
        if ( ! file_exists( $parent ) ) {
            self::mkdir($parent, $right);
        }
        if ( ! file_exists( $folder ) ) {
            @mkdir( $folder, $right );
        }
        return true;
    }
}

Org_Heigl_Hyphenator::setDefaultParsedFileDir( sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'org_heigl_hyphenator_' . get_current_user());
