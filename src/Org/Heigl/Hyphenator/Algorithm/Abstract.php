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

/**
 * Base class for the Hyphenation-Algorithms
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
abstract class Org_Heigl_Hyphenator_Algorithm_Abstract
{
    /**
     * Store the Options to be used during the processing
     *
     * @var Org_Heigl_Hyphenation_Options $_options
     */
    protected $_options = null;

	/**
     * Store the Hyphenation-Table
     *
     * @var array $_table
     */
    protected $_table = null;


    /**
     * Provide one interface to the Public
     *
     * @param string $word The word to hyphenate
     *
     * @return array
     */
    public function hyphenate($word)
    {
        if ( '' === trim ( $word ) ) {
            // Hyphenating an empty string does not make sense ;-)
            return '';
        }
        if($this->getOptions()->getMinWordLength() >= strlen(utf8_decode($word))){
            // Word does not meet the minimum length requirement
            return $word;
        }
        if (false!==mb_strpos($word,$this->getOptions()->getHyphen())){
            // Word seems already to be hyphenated, so simply return 'AS IS'.
            return $word;
        }
        if(0===mb_strpos($word,$this->getOptions()->getNoHyphenateString())){
            // Word starts with a no-hyphenate-string
            return mb_substr($word,mb_strlen($this->getOptions()->getNoHyphenateString()));
        }
        foreach($this->getOptions()->getCustomHyphens() as $hyphen){
            if(false!==mb_strpos($word,$hyphen)){
                return str_replace($hyphen,$this->getOptions()->getHyphen(),$word);
            }
        }
        return $this->_hyphenate($word);
    }

    /**
     * Invoke the actual hyphenation algorithm for the given word
     *
     * @param string $word
     *
     * @return string
     */
    protected abstract function _hyphenate($word);


    /**
     * Set the Options
     *
     * @param Org_Heigl_Hyphenator_Options $options
     *
     * @return Org_Heigl_Hyphenator_Algoritm_Abstract
     */
    public function setOptions(Org_Heigl_Hyphenator_Options $options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Get the Options
     *
     * @return Org_Heigl_Hyphenator_Options
     */
    public function getOptions()
    {
        if(null==$this->_options){
            $this->_options = new Org_Heigl_Hyphenator_Options();
        }
        return $this->_options;
    }

    /**
     * Set the Hyphenation table
     *
     * @param Org_Heigl_Hyphenator_Algorithm_Textutf8_Table $table
     *
     * @return Org_Heigl_Hyphenator_Algoritm_Abstract
     */
    public function setTable(Org_Heigl_Hyphenator_Algorithm_Texutf8_Table $table)
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


}