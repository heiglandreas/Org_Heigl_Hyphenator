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
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     02.11.2011
 */

namespace Org\Heigl\HyphenatorTest;

use Org\Heigl\Hyphenator as h;
use PHPUnit\Framework\TestCase;

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @since     20.04.2009
 */
class HyphenatorFeatureTest extends TestCase
{
    
    /**
     * @dataProvider hyphenationOfSingleWordWithArrayOutputProvider
     */
    public function testHyphenationOfSingleWordWithArrayOutput($word, $language, $expected)
    {
        $o = new h\Options();
        $o->setHyphen('-')
          ->setDefaultLocale($language)
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(4)
          ->setFilters('NonStandard')
          ->setTokenizers('Whitespace, Punctuation');
        
        $h = new h\Hyphenator();
        $h->setOptions($o);
        $this->assertEquals($expected, $h->hyphenate($word));
    }

    public function hyphenationOfSingleWordWithArrayOutputProvider()
    {
        return [
            [
                'donaudampfschifffahrt',
                'de_DE',
                [
                    'do-naudampfschifffahrt',
                    'donau-dampfschifffahrt',
                    'donaudampf-schifffahrt',
                    'donaudampfschiff-fahrt'
                ]
            ],
//            ['altbaucharme', 'de_DE', array['alt-baucharme', 'altbau-charme']],
            ['otto', 'de_DE', ['ot-to']],

        ];
    }


    /**
     * @dataProvider hyphenationOfSingleWordWithDefaultOutputProvider
     */
    public function testHyphenationOfSingleWordWithDefaultOutput($word, $language, $expected, $quality = 9)
    {
        $o = new h\Options();
        $o->setHyphen('^')
          ->setDefaultLocale($language)
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(4)
          ->setFilters('Simple')
          ->setQuality($quality)
          ->setTokenizers('Whitespace, Punctuation');

        $h = new h\Hyphenator();
        $h->setOptions($o);

        $this->assertEquals($expected, $h->hyphenate($word));
    }

    public function hyphenationOfSingleWordWithDefaultOutputProvider()
    {
        return [
            ['donaudampfschifffahrt ', 'de_DE', 'do^nau^dampf^schiff^fahrt '],
//            ['altbaucharme', 'de_DE', 'alt-bau-charme'],
            ['otto ', 'de_DE', 'ot^to '],
            ['daniel ', 'de_DE', 'da^niel '],
            // Sturm will not be hyphenated…
            ['aussichtsturm ', 'de_DE', 'aus^sichtsturm '],
            // Sturm will be hyphenated…
            ['aussichtsturm ', 'de_DE', 'aus^sicht^sturm ', h\Hyphenator::QUALITY_NORMAL],
            ['urinstinkt ', 'de_DE', 'ur^instinkt ', h\Hyphenator::QUALITY_HIGHEST],
            ['Brücke ', 'de_DE', 'Brü^cke ', h\Hyphenator::QUALITY_NORMAL],
            ['Röcke ', 'de_DE', 'Rö^cke '],
        ];
    }

    /**
     * @dataProvider hyphenationOfHtmlWithDefaultOutputProvider
     */
    public function testHyphenationOfHtmlWithDefaultOutput($html, $language, $expected, $quality = 9)
    {
        $o = new h\Options();
        $o->setHyphen('^')
          ->setDefaultLocale($language)
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(4)
          ->setFilters('Simple')
          ->setQuality($quality)
          ->setTokenizers('Xml, Whitespace, Punctuation');

        $h = new h\Hyphenator();
        $h->setOptions($o);

        $this->assertEquals($expected, $h->hyphenate($html));
    }

    public function hyphenationOfHtmlWithDefaultOutputProvider()
    {
        return [
            [
                '<xml>Otto<br/>Aussichtsturm</html>',
                'de_DE',
                '<xml>Ot^to<br/>Aus^sicht^sturm</html>',
                h\Hyphenator::QUALITY_NORMAL
            ],
        ];
    }

    public function testBaseExample()
    {
        $hyphenator = h\Hyphenator::factory('/path/to/the/config/file.properties');
        $hyphenator->getOptions()->setHyphen('-');

        $this->assertEquals('Hy-phe-na-tion', $hyphenator->hyphenate('Hyphenation'));
    }
}
