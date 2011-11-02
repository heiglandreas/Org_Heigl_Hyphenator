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

/** Org_Heigl_Hyphenator_Algorithm_Abstract */
require_once 'Org/Heigl/Hyphenator/Algorithm/Abstract.php';


/**
 * This class provides the actual Word-Hyphenation.
 *
 * Word-hyphenation is implemented on the basis of the algorithms developed by
 * Franklin Mark Liang for LaTeX as described in his dissertation at the
 * department of computer science at stanford university.
 *
 * The algorithm has been adapted to support UTF-8 Multibyte characters.
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
class Org_Heigl_Hyphenator_Algorithm_Texutf8
extends Org_Heigl_Hyphenator_Algorithm_Abstract
{
    /**
     * Creating the instance of the hyphenator sets required configuration
     * parameters
     *
     * @return void
     */
    public function __construct ()
    {
        if (!extension_loaded('iconv')) {
            include_once 'Org/Heigl/Hyphenator/Exception/RequirementsNotMetException.php';
            throw new Org_Heigl_Hyphenator_Exception_RequirementsNotMetException( 'ICONV-Extension is required');
        }
        if (!extension_loaded('mbstring')) {
            include_once 'Org/Heigl/Hyphenator/Exception/RequirementsNotMetException.php';
            throw new Org_Heigl_Hyphenator_Exception_RequirementsNotMetException( 'MBSTRING-Extension is required');
        }
        mb_internal_encoding('UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');
    }

    /**
     * Hyphenate one single word.
     *
     * @param string $word The word to hyphenate
     *
     * @return array
     */
    protected function _hyphenate($word)
    {
        $pattern = $this->_getNumericalPattern($word);
        $hyphenated = $this->_splitWordsByPattern($word, $pattern);
        return $hyphenated;
    }

    /**
     * Create the numerical hyphenation pattern for the given word according to
     * the values taken from the set hyphenation-table
     *
     * @var string $word
     *
     * @return string
     */
    protected function _getNumericalPattern($word)
    {
        $charLength = array ();
        $word = '_' . $word . '_';
        $matches = str_repeat('0', 1+strlen(utf8_decode($word)));
        foreach (preg_split('/(?<!^)(?!$)/u', $word) as $char) {
            $charLength[] = mb_strwidth($char);
        }
        $charLengthLength=count($charLength);
        foreach ($this->getTable()->getPatternsForWord($word)
            as $pattern => $hyphens) {
            $start = mb_strpos($word, $pattern);
            $f=0;
            for ( $k=0; $k<$charLengthLength; $k++) {
                $f=$f+$charLength[$k];
                if ($f>$start) break;
            }
            for ($i=0; $i<strlen($hyphens); $i++) {
                $curpos = substr($matches, $i+$k, 1);
                $compos = substr($hyphens, $i, 1);
                if ($compos>$curpos) {
                    $matches = substr($matches, 0, $i+$k)
                             . $compos
                             . substr($matches, $i+$k+1);
                }
            }

        }
        return substr($matches, 1, strlen($matches)-2);
    }

    /**
     * Replace odd numbers off the pattern with the given hyphen-character and
     * merge them with the word
     *
     *  @param string $word
     *  @param string $pattern
     *
     *  @return string
     */
    protected function _splitWordsByPattern($word, $pattern)
    {
        for ($i=strlen($pattern); 0<$i; $i--) {
            if (1==(substr($pattern, $i, 1)%2)) {
                $word = preg_replace(
                    '/(.{' . $i . '})(.*)/u', '\1&shy;\2', $word
                );
            }
        }

        return str_Replace(
            '&shy;',
            $this->getOptions()->getHyphen(),
            preg_replace('/(^&shy;|&shy;?$)/u', '', $word)
           );
    }

}