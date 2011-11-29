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
 * @subpackage Filter
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.alpha
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      02.11.2011
 * @todo       Implement!
 */

namespace Org\Heigl\Hyphenator\Filter;

/**
 * This class provides a registry for storing multiple filters
 *
 * @category   Hyphenation
 * @package    Org\Heigl\Hyphenator
 * @subpackage Filter
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.alpha
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      02.11.2011s
 */
class FilterRegistry implements \Iterator, \Countable
{
    /**
     * Storage for the Tokenizers.
     *
     * @var \Org\Heigl\Hyphenator\Filter\Filter[] $_registry
     */
    protected $_registry = array ();

    /**
     * Add an item to the registry
     *
     * @param \Org\Heigl\Hyphenator\Filter\Filter $filter The Filter
     * to be added
     *
     * @return \Org\Heigl\Hyphenator\Filter\FilterRegistry
     */
    public function add(Filter $filter)
    {
        if ( ! in_array($filter, $this->_registry)) {
            $this->_registry[] = $filter;
        }
        return $this;
    }

    /**
     * Get a Filters entry by it's key
     *
     * @param mixed $key The key to get the Filter for.
     *
     * @return \Org\Heigl\Hyphenator\Filter\Filter
     */
    public function getFilterWithKey($key)
    {
        if ( array_key_exists($key, $this->_registry)) {
            return $this->_registry[$key];
        }
        return null;
    }

    /**
     * Cleanup the registry
     *
     * @return Filter\FilterRegistry
     */
    public function cleanup()
    {
        $this->_registry = array ();
        return $this;
    }

    /**
     * Pass the given string through the given Filter
     *
     * @param \Org\Heigl\Hyphenator\Tokenizer\TokenRegistry $tokens The
     * Registry to filter
     *
     * @return \Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry
     */
    public function filter(\Org\Heigl\Hyphenator\Tokenizer\TokenRegistry $tokens)
    {
        foreach ( $this as $filter ) {
            $tokens = $filter->run($tokens);
        }
        return $tokens;
    }

    /**
     * Implementation of \Iterator
     *
     * @see \Iterator::rewind()
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->_registry);
    }

    /**
     * Get the current object
     *
     * @see \Iterator::current()
     *
     * @return \Org\Heigl\Hyphenator\Filter\Filter
     */
    public function current()
    {
        return current($this->_registry);
    }

    /**
     * Get the current key
     *
     * @see \Iterator::key()
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_registry);
    }

    /**
     * Get the number of items in the registry
     *
     * @see \Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->_registry);
    }

    /**
     * Push the internal pointer forward one step
     *
     * @see \Iterator::next()
     *
     * @return void
     */
    public function next()
    {
        next($this->_registry);
    }

    /**
     * Check whether the current pointer is in a valid place
     *
     * @see \Iterator::valid()
     *
     * @return boolean
     */
    public function valid()
    {
        if ( false === current($this->_registry)) {
            return false;
        }
        return true;
    }

}
