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

        $h->getDictionaries()->add(h\Dictionary\Dictionary::fromFile(__DIR__ . '/share/de_DE.ini'));

//        $h->getDictionaries()->getDictionaryWithKey(0)->addPattern('strategie', '9800000000');

        $this->assertEquals($expected, $h->hyphenate($word));
    }

    public function hyphenationOfSingleWordWithDefaultOutputProvider()
    {
        return [
            ['donaudampfschifffahrt ', 'de_DE', 'do^nau^dampf^schiff^fahrt '],
            ['Donaudampfschifffahrt ', 'de_DE', 'Do^nau^dampf^schiff^fahrt '],
            //['Altbaucharme ', 'de_DE', 'Alt-bau-charme '],
            ['otto ', 'de_DE', 'ot^to '],
            ['daniel ', 'de_DE', 'da^ni^el '],
            // Sturm will not be hyphenated…
            ['aussichtsturm ', 'de_DE', 'aus^sichts^turm '],
            // Sturm will be hyphenated…
            ['aussichtsturm ', 'de_DE', 'aus^sicht^s^turm ', h\Hyphenator::QUALITY_NORMAL],
            ['urinstinkt ', 'de_DE', 'ur^in^stinkt ', h\Hyphenator::QUALITY_HIGHEST],
            ['Urinstinkt ', 'de_DE', 'Ur^in^stinkt ', h\Hyphenator::QUALITY_HIGHEST],
            ['Brücke ', 'de_DE', 'Brü^cke ', h\Hyphenator::QUALITY_NORMAL],
            ['Röcke ', 'de_DE', 'Rö^cke '],
            ['Produktionsstrategie ', 'de_DE', 'Pro^duk^ti^ons^stra^te^gie '],
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
                '<xml>Ot^to<br/>Aus^sicht^s^turm</html>',
                h\Hyphenator::QUALITY_NORMAL
            ],
        ];
    }

    public function testBaseExample()
    {
        $hyphenator = h\Hyphenator::factory('/path/to/the/config/file.properties');
        $hyphenator->getOptions()->setHyphen('-');

        $this->assertEquals('Hy-phe-na-ti-on', $hyphenator->hyphenate('Hyphenation'));
    }

    public function testUserStory1()
    {
        $source = 'Selten können vergleichbare Arzneimittel eine Kombination von '
         . 'Fieber, raschem Atmen, Schwitzen, Muskelsteifheit und Benommenheit '
         . 'oder Schläfrigkeit hervorrufen. Wenn dies eintritt, setzen Sie sich '
         . 'sofort mit einem Arzt in Verbindung.';

        $expected = 'Selten können ver&shy;gleich&shy;ba&shy;re Arz&shy;nei&shy;'
         . 'mit&shy;tel eine Kom&shy;bi&shy;na&shy;ti&shy;on von Fieber, ras'
         . 'chem Atmen, Schwit&shy;zen, Mus&shy;kel&shy;steif&shy;heit und Be&sh'
         . 'y;nom&shy;men&shy;heit oder Schläf&shy;rig&shy;keit her&shy;vor&shy;'
         . 'ru&shy;fen. Wenn dies ein&shy;tritt, setzen Sie sich sofort mit'
         . ' einem Arzt in Ver&shy;bin&shy;dung.';

        $o = new h\Options();
        $o->setHyphen('&shy;')
          ->setDefaultLocale("de_DE")
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(8)
          ->setFilters('Simple,CustomMarkup')
          ->setTokenizers(['Whitespace', 'Punctuation']);
        $h = new h\Hyphenator();
        $h->setOptions($o);

        self::assertEquals($expected, $h->hyphenate($source));
    }

    public function testIssue40()
    {
        $source = 'Wasserwirtschaft';

        $expected = 'Was-ser-wirt-schaft';

        $o = new h\Options();
        $o->setHyphen('-')
          ->setDefaultLocale("de_DE")
          ->setRightMin(2)
          ->setLeftMin(2)
          ->setWordMin(8)
          ->setFilters('Simple,CustomMarkup')
          ->setTokenizers(['Whitespace', 'Punctuation']);
        $h = new h\Hyphenator();
        $h->setOptions($o);

        self::assertEquals($expected, $h->hyphenate($source));
    }
}
