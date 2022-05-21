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

use Iterator;
use Countable;
use OutOfBoundsException;

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
 * @template-implements Iterator<Dictionary>
 */
class DictionaryRegistry implements Iterator, Countable
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
     */
    public function add(Dictionary $dict): self
    {
        if (! in_array($dict, $this->registry)) {
            $this->registry[] = $dict;
        }

        return $this;
    }

    /**
     * Get a dictionary entry by its key
     *
     * @param mixed $key The key to retrieve the Dictionary for
     */
    public function getDictionaryWithKey($key): ?Dictionary
    {
        if (array_key_exists($key, $this->registry)) {
            return $this->registry[$key];
        }

        return null;
    }

    /**
     * Get an array of hyphenation-patterns for a given word.
     *
     * @deprecated use getHyphenationPatterns() instead
     *
     * @param string $word The word to get the patterns for.
     *
     * @return array
     */
    public function getHyphenationPattterns($word)
    {
        return $this->getHyphenationPatterns($word);
    }

    /**
     * Get an array of hyphenation-patterns for a given word.
     *
     * @param string $word The word to get the patterns for.
     *
     * @return array<string>
     */
    public function getHyphenationPatterns($word): array
    {
        $pattern = array(array());
        foreach ($this as $dictionary) {
            $pattern[] = $dictionary->getPatternsForWord($word);
        }

        return array_merge(...$pattern);
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
     *
     * @return \Org\Heigl\Hyphenator\Dictionary\Dictionary
     */
    public function current(): Dictionary
    {
        $current = current($this->registry);
        if (false === $current) {
            throw new OutOfBoundsException('You requested a non-existent entry');
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
            throw new OutOfBoundsException('You requested a non-existend key');
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
