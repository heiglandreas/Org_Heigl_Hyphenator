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
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      02.11.2011
 * @todo       Implement!
 */

namespace Org\Heigl\Hyphenator\Filter;

use Countable;
use Iterator;
use Org\Heigl\Hyphenator\Tokenizer\TokenRegistry;
use OutOfBoundsException;

/**
 * This class provides a registry for storing multiple filters
 *
 * @category   Hyphenation
 * @package    Org\Heigl\Hyphenator
 * @subpackage Filter
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      02.11.2011s
 * @template-implements Iterator<Filter>
 */
class FilterRegistry implements Iterator, Countable
{
    /**
     * Storage for the Tokenizers.
     *
     * @var Filter[]
     */
    protected $registry = array();

    /**
     * Add an item to the registry
     *
     * @param Filter $filter The Filter
     * to be added
     */
    public function add(Filter $filter): self
    {
        if (! in_array($filter, $this->registry)) {
            $this->registry[] = $filter;
        }

        return $this;
    }

    /**
     * Get a Filters entry by its key
     *
     * @param mixed $key The key to get the Filter for.
     */
    public function getFilterWithKey($key): ?Filter
    {
        if (array_key_exists($key, $this->registry)) {
            return $this->registry[$key];
        }

        return null;
    }

    /**
     * Cleanup the registry
     */
    public function cleanup(): self
    {
        $this->registry = array();

        return $this;
    }

    /**
     * Pass the given string through the given Filter
     *
     * @param TokenRegistry $tokens The
     * Registry to filter
     */
    public function filter(TokenRegistry $tokens): TokenRegistry
    {
        foreach ($this as $filter) {
            $tokens = $filter->run($tokens);
        }

        return $tokens;
    }

    /**
     * Concatenate the content of the given TokenRegistry
     *
     * @param TokenRegistry $tokens The
     * Registry to filter
     *
     * @return mixed
     */
    public function concatenate(TokenRegistry $tokens)
    {
        foreach ($this as $filter) {
            $tokens = $filter->concatenate($tokens);
        }

        return $tokens;
    }
    /**
     * Implementation of Iterator
     *
     * @see Iterator::rewind()
     */
    public function rewind(): void
    {
        reset($this->registry);
    }

    /**
     * Get the current object
     *
     * @see Iterator::current()
     */
    public function current(): Filter
    {
        $current = current($this->registry);
        if (false === $current) {
            throw new OutOfBoundsException('You requested a non-existend entry');
        }

        return $current;
    }

    /**
     * Get the current key
     *
     * @see Iterator::key()
     *
     * @return mixed
     */
    public function key(): int
    {
        $key = key($this->registry);

        if (null === $key) {
            throw new OutOfBoundsException('You requested a non-existend entry');
        }

        return $key;
    }

    /**
     * Get the number of items in the registry
     *
     * @see Countable::count()
     */
    public function count(): int
    {
        return count($this->registry);
    }

    /**
     * Push the internal pointer forward one step
     *
     * @see Iterator::next()
     */
    public function next(): void
    {
        next($this->registry);
    }

    /**
     * Check whether the current pointer is in a valid place
     *
     * @see Iterator::valid()
     */
    public function valid(): bool
    {
        return false !== current($this->registry);
    }
}
