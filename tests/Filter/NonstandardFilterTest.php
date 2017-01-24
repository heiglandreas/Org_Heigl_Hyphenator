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
 * @subpackage Filter
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.12.2011
 */

namespace Org\Heigl\HyphenatorTest\Filter;

use \Org\Heigl\Hyphenator\Filter\NonStandardFilter;
use \Org\Heigl\Hyphenator\Options;
use \Org\Heigl\Hyphenator\Tokenizer as t;
use \Mockery as M;

/**
 * This class tests the functionality of the class NonStandardFilter
 *
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @subpackage Filter
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.12.2011
 */
class NonStandardFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider filterProvider
     */
    public function testFilter($input, $pattern, $result)
    {
        $f = new NonStandardFilter();
        $o = new Options();
        $t = new t\WordToken($input);
        $t->addPattern($pattern);
        $r = new t\TokenRegistry();
        $r->add($t);
        $o->setHyphen('-');
        $f->setOptions($o);
        $this->assertSame($r, $f->run($r));
        $this->assertEquals($result, $r->getTokenWithKey(0)->getHyphenatedContent());
    }

    public function filterProvider()
    {
        return array(
            array('Donaudampfschifffahrt', array('auda' => '00100', 'pfsch'=>'001000', 'fff'=>'0010'), array( 'Donau-dampfschifffahrt','Donaudampf-schifffahrt','Donaudampfschiff-fahrt')),
        );
    }

    public function testFilterWithNonWordToken()
    {
        $tokenList = M::mock('\Org\Heigl\Hyphenator\Tokenizer\TokenRegistry');
        $tokenList->shouldReceive('rewind')->once();
        $tokenList->shouldReceive('valid')->twice()->andReturnValues(array(true, false));
        $tokenList->shouldReceive('current')->once()->andReturn('foo');
        $tokenList->shouldReceive('next')->once();
        $tokenList->shouldReceive('key')->andReturn(0);

        $filter = new NonStandardFilter();
        $result = $filter->run($tokenList);

        $this->assertSame($tokenList, $result);
    }

    public function testConcatenation()
    {
        $obj = new NonStandardFilter();

        $token1 = M::mock('\Org\Heigl\Hyphenator\Tokenizer\Token');
        $token1->shouldReceive('getFilteredContent')->once()->andReturn('a');

        $token2 = M::mock('\Org\Heigl\Hyphenator\Tokenizer\Token');
        $token2->shouldReceive('getFilteredContent')->once()->andReturn('b');

        $tokenList = M::mock('\Org\Heigl\Hyphenator\Tokenizer\TokenRegistry');
        $tokenList->shouldReceive('rewind')->once();
        $tokenList->shouldReceive('valid')->times(3)->andReturnValues(array(true, true, false));
        $tokenList->shouldReceive('current')->twice()->andReturnValues(array($token1, $token2));
        $tokenList->shouldReceive('next')->twice();
        $tokenList->shouldReceive('key')->andReturnValues(array(0, 1));

        $method = \UnitTestHelper::getMethod($obj, '_concatenate');
        $result = $method->invoke($obj, $tokenList);

        $this->assertEquals('ab', $result);
    }
}
