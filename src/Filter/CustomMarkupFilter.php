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
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      02.11.2011
 * @todo       Implement!
 */

namespace Org\Heigl\Hyphenator\Filter;

use Org\Heigl\Hyphenator\Tokenizer\TokenRegistry;

/**
 * This class provides a filter for non-standard hyphenation-patterns
 *
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Filter
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    2.0.1
 * @link       http://github.com/heiglandreas/Hyphenator
 * @since      02.11.2011s
 */
class CustomMarkupFilter extends Filter
{
    /**
     * Extends the abstract Filter::run()
     *
     * @param TokenRegistry $tokens The registry
     * to act upon
     *
     * @see Filter::run()
     * @return TokenRegistry
     */
    public function run(TokenRegistry $tokens)
    {
        return $tokens;
    }

    /**
     * Implements interface Filter
     *
     * @param TokenRegistry $tokens The registry
     * to act upon
     *
     * @see Filter::run()
     * @return mixed
     */
    protected function doConcatenate(TokenRegistry $tokens)
    {
        $string = '';
        foreach ($tokens as $token) {
            $string .= $token->getFilteredContent();
        }

        return $string;
    }
}
