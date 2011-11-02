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
 * @subpackage Algorithm
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    SVN: $Revision: 1114 $
 * @since      05.09.2011
 */

/** Org_Heigl_Hyphenator_Exception_InvalidArgumentException */
require_once 'Org/Heigl/Hyphenator/Exception/InvalidArgumentException.php';

/**
 * This class provides access to the table used for hyphenation.
 *
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Algorithm
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    SVN: $Revision: 1114 $
 * @since      05.09.2011
 */
class Org_Heigl_Hyphenator_Algorithm_Texutf8_Table
{
    /**
     * Store the Hyphenation-Table
     *
     * @var array $_table
     */
    protected $_table = array();

    /**
     * Set the Hyphenation table
     *
     * @param array $table
     *
     * @return Org_Heigl_Hyphenator_Algoritm_Textutf8_Table
     */
    public function setTable(array $table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * Get the Hyphenation table
     *
     * @return Org_Heigl_Hyphenator_Algorithm_Texutf8_Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * add an entry to the table
     *
     * @param string $pattern The pattern to add
     * @param string $marks   The Hyphenation marks for the given pattern
     *
     * @return Org_Heigl_Hyphenator_Algoritm_Textutf8_Table
     */
    public function addPattern($pattern, $marks)
    {
        $pattern = iconv(mb_detect_encoding($pattern),'UTF-8//TRANSLIT',$pattern);
        $this->_table[$pattern] = $marks;
        return $this;
    }

    /**
     * Get all patterns for a given word.
     *
     * @param string $word
     *
     * @return array
     */
    public function getPatternsForWord($word)
    {
        $return = array ();
        foreach ($this->_table as $pattern => $marks){
            if ( false === mb_strpos($word, $pattern)){
                continue;
            }
            $return[$pattern] = $marks;
        }
        return $return;
    }

    /**
     * Parse a given TeX-Hyphenation-File and create an INI File from it.
     *
     * @param string $texFile
     * @param string $iniFile
     *
     * @return string
     */
    public static function parseTeXFile($texFile,$iniFile)
    {
        if ( ! file_exists($texFile)){
            throw new Org_Heigl_Hyphenator_Exception_FileDoesNotExistException($texFile);
        }
        $fc    = file_get_contents ( $texFile );
        $array = array ();
        if ( ! preg_match ( '/[\\n\\r]\\\\patterns\\{(.*)\\}\\s*\\\\/sim', $fc, $array ) ) {
            return false;
        }
        $fc         = preg_replace ( '/%.*/', '', $array[1] );
        $fc         = preg_replace ( '/\\\\n\\{(.+?)\\}/', '\1', $fc );
        $fc         = preg_replace ( array('/"a/', '/"o/', '/"u/', '/\\./' ), array ( 'ä', 'ö', 'ü', '_' ), $fc );
        $array      = preg_split ( '/\\s+/', $fc );
        $fh         = fopen ( $parsedFile, 'w+' );
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
     * Parse a given file.
     *
     * @param string $file
     *
     * @return Org_Heigl_Hyphenator_Algoritm_Textutf8_Table
     */
    public function readFile($file)
    {
        if(!file_exists($file)){
            throw new Org_Heigl_Hyphenator_Exception_InvalidArgumentException(sprintf('The file %1$s does not exist', $file));
        }

        $pattern = @parse_ini_file($file);

        if ( !$pattern){
            throw new Org_Heigl_Hyphenator_Exception_InvalidArgumentException(sprintf('The file %1$s does not contain a $pattern-variable', $file));
        }
        foreach($pattern as $key => $val){
            $this->addPattern($key, $val);
        }
        return $this;
    }
}