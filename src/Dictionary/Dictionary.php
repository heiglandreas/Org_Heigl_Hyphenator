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
 * @subpackage Dictionary
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      01.11.2011
 */

namespace Org\Heigl\Hyphenator\Dictionary;

/**
 * This class provides a generic dictionary contianing hyphenation-patterns
 *
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Dictionary
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      01.11.2011
 */
class Dictionary
{
    /**
     * The internal storage for the dictionary.
     *
     * @var array $_dictionary
     */
    protected $_dictionary = array ();

    /**
     * Where to look for the basic files.
     *
     * @var string $_fileLocation
     */
    protected static $_fileLocation = '';

    /**
     * Set the file-location.
     *
     * @param string $fileLocation THe default file-location for ini-files
     *
     * @return void
     */
    public static function setFileLocation($fileLocation)
    {
        self::$_fileLocation = $fileLocation;
    }

    /**
     * Create a new Instance of the Dictionary
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Create an instance for a given file
     *
     * @param string $locale The locale to be set for this Dictionary
     *
     * @return Dictionary
     */
    public static function factory($locale)
    {
        $dict = new Dictionary();
        $dict->load($locale);

        return $dict;
    }

    /**
     * Load a given locale-file as base for the dictionary
     *
     * @param string $locale Load the file for the given locale
     *
     * @return Dictionary
     */
    public function load($locale)
    {
        $locale = $this->_unifyLocale($locale);
        $file = self::$_fileLocation . DIRECTORY_SEPARATOR . $locale . '.ini';
        $this->_dictionary = array();
        if ( ! file_exists(realpath($file)) ) {
            return $this;
        }
        foreach ( parse_ini_file($file) as $key => $val ) {
            $this->_dictionary[str_replace('@:', '', $key)] = $val;
        }

        return $this;
    }

    /**
     * parse a dictionary-file to create an ini-file from it.
     *
     * @param string $locale Parse the file for the given locale
     *
     * @throws \Org\Heigl\Hyphenator\Exception\PathNotFoundException
     * @return string
     */
    public static function parseFile($locale)
    {
        $path = self::$_fileLocation . DIRECTORY_SEPARATOR;
        $file = $path . 'hyph_' . $locale . '.dic';
        if ( ! file_Exists($file) ) {
            throw new \Org\Heigl\Hyphenator\Exception\PathNotFoundException('The given Path does not exist');
        }

        $items = file($file);
        $source = trim($items[0]);
        if (0===strpos($source, 'ISO8859')) {
            $source = str_Replace('ISO8859', 'ISO-8859', $source);
        }
        unset ($items[0]);
        $fh = fopen($path . $locale . '.ini', 'w+');
        foreach ($items as $item) {
            // Remove comment-lines starting with '#' or '%'.
            if ( in_array(mb_substr($item, 0, 1), array('#', '%')) ) {
                continue;
            }
            // Ignore empty lines.
            if ( '' == trim($item) ) {
                continue;
            }
            // Remove all Upper-case items as they are OOo-specific
            if ($item === mb_strtoupper($item) ) {
                continue;
            }
            // Ignore lines containing an '=' sign as these are specific
            // instructions for non-standard-hyphenations. These will be
            // implemented later.
            if ( false !== mb_strpos($item, '=') ) {
                continue;
            }
            $item = mb_convert_Encoding($item, 'UTF-8', $source);
            $result = Pattern::factory($item);
            $string = '@:' . $result->getText() . ' = "' . $result->getPattern() . '"' . "\n";
            fwrite($fh, $string);
        }
        fclose($fh);

        return $path . $locale . '.ini';
    }

    /**
     * Get all patterns for a given word.
     *
     * @param string $word The word to get the patterns for.
     *
     * @return array
     */
    public function getPatternsForWord($word)
    {
        $return = array ();
        $word = '.' . $word . '.';
        $strlen = mb_strlen($word);
        for ($i = 0; $i <= $strlen; $i ++) {
            for ( $j = 2; $j <= ($strlen-$i); $j++ ) {
                $substr = mb_substr($word, $i, $j);
                if ( ! isset($this->_dictionary[$substr]) ) {
                    continue;
                }
                $return[$substr] = $this->_dictionary[$substr];
            }
        }

        return $return;
    }

    /**
     * Manually add or overwrite a pattern
     *
     * @param string $string  String to be maatched
     * @param string $pattern Numerical hyphenation-pattern
     *
     * @return \Org\Heigl\Hyphenator\Dictionary\Dictionary
     */
    public function addPAttern( $string, $pattern)
    {
        $this->_dictionary[$string] = $pattern;

        return $this;
    }

    /**
     * Unify the given locale to a defsult format.
     *
     * For that in a 2 by 2 format the whole string is splited, the first part
     * lowercased, the sewcond part upercased and concatenated with n under-
     * score.
     *
     * a 2letter locae will simply be lowercased.
     *
     * everything else will be returned AS IS
     *
     * @param string $locale The locale to unify
     *
     * @return string
     */
    protected function _unifyLocale($locale)
    {
        if ( 2 == strlen($locale) ) {
            return strtolower($locale);
        }
        if ( preg_match('/([a-zA-Z]{2})[^a-zA-Z]+([a-zA-Z]{2})/i', $locale, $result) ) {
            return strtolower($result[1]) . '_' . strtoupper($result[2]);
        }

        return (string) $locale;
    }
}
