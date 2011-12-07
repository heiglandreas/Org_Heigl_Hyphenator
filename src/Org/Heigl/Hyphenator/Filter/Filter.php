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
 * @subpackage Filter
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.beta
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      02.11.2011
 * @todo       Implement!
 */

namespace Org\Heigl\Hyphenator\Filter;

use \Org\Heigl\Hyphenator\Tokenizer as t;

/**
 * This nterface provides a filter for non-standard hyphenation-patterns
 *
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Filter
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.beta
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      02.11.2011s
 */
abstract class Filter
{
    /**
     * Storage of the options-object.
     *
     * @var \Org\Heigl\Hyphenator\Options $_options
     */
    protected $_options = null;

    /**
     * Set the options-object for this filter
     *
     * @param \Org\Heigl\Hyphenator\Options $options The options to set
     *
     * @return Filter
     */
    public function setOptions(\Org\Heigl\Hyphenator\Options $options)
    {
        $this->_options=$options;
        return $this;
    }

    /**
     * Get the currently defined Options
     *
     * @return \Org\Heigl\Hyphenator\Options
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Run the filter over the given Token
     *
     * @param \Org\Heigl\Hyphenator\Tokenizer\TokenRegistry $tokens The registry
     * to apply the filter to
     *
     * @return \Org\Heigl\Hyphenator\Tokenizer\TokenRegistry
     */
    public abstract function run(t\TokenRegistry $tokens);

    /**
     * Concatenate the given TokenRegistry to return one result
     *
     * @param \Org\Heigl\Hyphenator\Tokenizer\TokenRegistry $tokens The registry
     * to apply the filter to
     *
     * @return mixed
     */
    protected abstract function _concatenate(t\TokenRegistry $tokens);

    /**
     * Take any input and eitehr pass it to the concatenate-method or return it.
     *
     * If the input is a TokenRegistry, we process it, otherwise we just return it.
     *
     * @param mixed $tokens The input to process
     *
     * @return mixed
     */
    public function concatenate($tokens)
    {
        if ( $tokens instanceof t\TokenRegistry ) {
            return $this->_concatenate($tokens);
        }
        return $tokens;
    }
}
