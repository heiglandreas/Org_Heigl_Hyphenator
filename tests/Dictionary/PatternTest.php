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

namespace Org\Heigl\HyphenatorTest\Dictionary;

use Org\Heigl\Hyphenator\Dictionary\Pattern;
use Org\Heigl\Hyphenator\Exception\NoPatternSetException;
use PHPUnit\Framework\TestCase;

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */
class PatternTest extends TestCase
{
    public function testSettingPattern()
    {
        $p = new Pattern();

        try {
            $p->getText();
            $this->fail('No Exception raised');
        } catch (NoPatternSetException $e) {
            $this->assertTrue(true);
        }
        try {
            $p->getPattern();
            $this->fail('No Exception raised');
        } catch (NoPatternSetException $e) {
            $this->assertTrue(true);
        }
        $this->assertSame($p, $p->setPattern('te8st'));
        TestCase::assertSame('test', $p->getText());
        TestCase::assertSame('00800', $p->getPattern());
    }

    /**
     * @dataProvider patternCreationProvider
     */
    public function testPatternCreation($input, $text, $pattern)
    {
        $p = Pattern::factory($input);
        $this->assertEquals($text, $p->getText());
        $this->assertEquals($pattern, $p->getPattern());
    }

    public function patternCreationProvider()
    {
        return array(
            array('te8st', 'test','00800'),
            array('øre5sœnd', 'øresœnd', '00050000'),
        );
    }
}
