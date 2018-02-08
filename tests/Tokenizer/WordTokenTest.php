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
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest\Tokenizer;

use Org\Heigl\Hyphenator\Tokenizer\WordToken;
use PHPUnit\Framework\TestCase;

/**
 * This class tests the functionality of the class Token
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */
class WordTokenTest extends TestCase
{
    public function testWordTokenCanAddPattern()
    {
        $t = new WordToken('test');
        $this->assertAttributeEquals(array(), 'pattern', $t);
        $t->addPattern(array('te'=>'012','es'=>'234'));
        $this->assertAttributeEquals(array('te'=>'012','es'=>'234'), 'pattern', $t);
        $t->addPattern(array('st'=>'234','es'=>'345'));
        $this->assertAttributeEquals(array('te'=>'012','es'=>'345','st'=>'234'), 'pattern', $t);
    }

    /**
     * @dataProvider patternProvider
     */
    public function testWordTokenGetsCorrectPattern($pattern, $word, $result, $quality)
    {
        $t = new WordToken($word);
        $t->addPattern($pattern);
        $this->assertAttributeEquals($pattern, 'pattern', $t);
        $this->assertEquals($result, $t->getMergedPattern($quality));
    }

    public function patternProvider()
    {
        return array(
            array(
                array('.t'=>'012','te'=>'743','es'=>'328','st'=>'070','t.'=>'800'),
                'test',
                '74380',
                \Org\Heigl\Hyphenator\Hyphenator::QUALITY_HIGHEST
            ),
            //  . t e s t .
            // 0 1 2
            //   7 4 3
            //     3 2 8
            //       0 7 0
            //         8 0 0
            // 0 7 4 3 8 0 0
            array(
                array('.t'=>'012','te'=>'743','es'=>'328','st'=>'070','t.'=>'800'),
                'test',
                '14300',
                \Org\Heigl\Hyphenator\Hyphenator::QUALITY_NORMAL
            ),
            //  . t e s t .
            // 0 1 2
            //   0 4 3
            //     3 2 0
            //       0 0 0
            //         0 0 0
            // 0 1 4 3 0 0 0
            array(
                array('.t'=>'012','tä'=>'743','äß'=>'328','är'=>'070','rø'=>'800','øi'=>'345','i.'=>'100'),
                'täßtärøi',
                '743848345',
                \Org\Heigl\Hyphenator\Hyphenator::QUALITY_HIGHEST
            ),
            //  . t ä ß t ä r ø i .
            // 0 1 2
            //   7 4 3
            //     3 2 8
            //         7 4 3
            //           0 7 0
            //             8 0 0
            //               3 4 5
            //                 1 0 0
            // 0 7 4 3 8 4 8 3 4 5 0
        );
    }
}
