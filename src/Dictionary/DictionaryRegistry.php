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
 * This class provides a registry for storing multiple dictionaries
 *
 * @category   Hyphenation
 * @package    Org\Heigl\Hyphenator
 * @subpackage Dictionary
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      01.11.2011
 */
class DictionaryRegistry implements \Iterator, \Countable
{
    /**
     * Storage for the Dictionaries.
     *
     * @var Dictionary[] $registry
     */
    protected $registry = array();

    /**
     * Add an item to the registry.
     *
     * @param Dictionary $dict The Dictionary to add to the registry
     *
     * @return DictionaryRegistry
     */
    public function add(Dictionary $dict)
    {
        if (! in_array($dict, $this->registry)) {
            $this->registry[] = $dict;
        }

        return $this;
    }

    /**
     * Get a dictionary entry by it's key
     *
     * @param mixed $key The key to retrieve the Dictionary for
     *
     * @return \Org\Heigl\Hyphenator\Dictionary\Dictionary
     */
    public function getDictionaryWithKey($key)
    {
        if (array_key_exists($key, $this->registry)) {
            return $this->registry[$key];
        }

        return null;
    }

    /**
     * Get an array of hyphenation-patterns for a given word.
     *
     * @param string $word The word to get the patterns for.
     *
     * @return array
     */
    public function getHyphenationPattterns($word)
    {
        $pattern = array();
        foreach ($this as $dictionary) {
            $pattern = array_merge($pattern, $dictionary->getPatternsForWord($word));
        }

        return $pattern;
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
        reset($this->registry);
    }

    /**
     * Get the current object
     *
     * @see \Iterator::current()
     *
     * @return \Org\Heigl\Hyphenator\Dictionary\Dictionary
     */
    public function current()
    {
        return current($this->registry);
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
        return key($this->registry);
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
        return count($this->registry);
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
        next($this->registry);
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
        if (false === current($this->registry)) {
            return false;
        }

        return true;
    }
}
