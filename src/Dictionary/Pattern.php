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
 * @since      24.11.2011
 */

namespace Org\Heigl\Hyphenator\Dictionary;

/**
 * This class provides a pattern for hyphenation
 *
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Dictionary
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      24.11.2011
 */
class Pattern
{
    /**
     * The internal storage of the text.
     *
     * @var string $text
     */
    protected $text = '';

    /**
     * The internal storage for the pattern.
     *
     * @var string $pattern
     */
    protected $pattern = '';

    /**
     * Set the pattern.
     *
     * @param string $pattern The UTF-8 encoded Pattern to store
     *
     * @return Pattern
     */
    public function setPattern($pattern)
    {
        $item = trim($pattern);
        $strlen = mb_strlen($item);
        for ($i = 0; $i < $strlen; $i++) {
            if ((($i) <= $strlen) && preg_match('/[0-9]/u', mb_substr($item, $i, 1))) {
                $this->pattern .= mb_substr($item, $i, 1);
                $i++;
            } else {
                $this->pattern .= '0';
            }
        }
        $this->text = preg_replace(array('/[0-9]/u','/\'/u'), array('','\\’'), $item);
        if (strlen($this->pattern) == mb_strlen($this->text)) {
            $this->pattern .= '0';
        }

        return $this;
    }

    /**
     * Create a new Instance of the Pattern
     *
     * @return void
     */
    public function __construct()
    {
        // Nothing to do on instantiation
    }

    /**
     * Creates Pattern-Instance and provide it with the given Pattern
     *
     * @param string $pattern The pattern to store
     *
     * @return Pattern
     */
    public static function factory($pattern)
    {
        $p = new Pattern();
        $p->setPattern($pattern);

        return $p;
    }

    /**
     * Get the text of the pattern
     *
     * @throws \Org\Heigl\Hyphenator\Exception\NoPatternSetException
     * @return string
     */
    public function getText()
    {
        if (! $this->text) {
            throw new \Org\Heigl\Hyphenator\Exception\NoPatternSetException('No Pattern set');
        }

        return $this->text;
    }

    /**
     * Get the pattern of this instance
     *
     * @throws \Org\Heigl\Hyphenator\Exception\NoPatternSetException
     * @return string
     */
    public function getPattern()
    {
        if (! $this->pattern) {
            throw new \Org\Heigl\Hyphenator\Exception\NoPatternSetException('No Pattern set');
        }

        return $this->pattern;
    }
}
