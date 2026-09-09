<?php

declare(strict_types=1);

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

use Org\Heigl\Hyphenator\Dictionary\Dictionary;
use Org\Heigl\Hyphenator\Exception\PathNotFoundException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

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
final class DictionaryTest extends TestCase
{
    public function testSettingDefaultFilePath(): void
    {
        $rc = new ReflectionClass(Dictionary::class);
        $rp = $rc->getProperty('fileLocation');
        $rp->setAccessible(true);
        TestCase::assertSame('', $rp->getValue());
        Dictionary::setFileLocation('foo');
        TestCase::assertSame('foo', $rp->getValue());
    }

    public function testParsingOnDictionaryCreationDoesNotWorks(): void
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        @unlink(__DIR__.'/share/de.ini');
        $dict = Dictionary::factory('de');
        $this->assertFalse(file_Exists(__DIR__ . '/share/de.ini'));
    }

    public function testParsingWrongLocaleWorks(): void
    {
        Dictionary::setFileLocation(__DIR__ . '/../share/test3/files/dictionaries');
        $dict = Dictionary::factory('de-de');

        $rc = new ReflectionClass(Dictionary::class);
        $rp = $rc->getProperty('dictionary');
        $rp->setAccessible(true);

        TestCase::assertNotSame([], $rp->getValue($dict));
    }

    public function testGettingPatterns(): void
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        $dict = Dictionary::factory('de-DE');
        $result = $dict->getPatternsForWord('täßterei');
        $this->assertEquals(array('täßt'=>'00020'), $result);
    }

    public function testGettingPatternsForWordIncludesSingleLetterPatterns(): void
    {
        $dictionary = new Dictionary();
        $dictionary->addPattern('b', '10');
        $dictionary->addPattern('ben', '0200');

        $this->assertSame(['b' => '10', 'ben' => '0200'], $dictionary->getPatternsForWord('Silben'));
    }

    public function testSettingPatterns(): void
    {
        $rc = new ReflectionClass(Dictionary::class);
        $rp = $rc->getProperty('dictionary');
        $rp->setAccessible(true);

        $dictionary = new Dictionary();
        $dictionary->addPattern('test', '01234');
        TestCase::assertSame(['test' => '01234'], $rp->getValue($dictionary));
    }

    public function testCreationOfNotExistentLocale(): void
    {
        $rc = new ReflectionClass(Dictionary::class);
        $rp = $rc->getProperty('dictionary');
        $rp->setAccessible(true);

        Dictionary::setFileLocation(__DIR__ . '/share/');
        $dictionary = Dictionary::factory('xx_XX');
        TestCase::assertSame([], $rp->getValue($dictionary));
        $result = $dictionary->getPatternsForWord('Donaudampfschifffahrtskapitänsmütze');
        $this->assertEquals(array(), $result);
    }

    public function testParsingDicFilesWorks(): void
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');
        @unlink(__DIR__.'/share/de_TE.ini');
        $dict = Dictionary::parseFile('de_TE');
        $this->assertTrue(file_Exists($dict));
        $this->assertEquals('UTF-8', mb_detect_encoding(file_get_contents($dict)));
        $this->assertEquals(file_get_contents(__DIR__.'/share/de_TE.default.ini'), file_get_contents($dict));
    }

    public function testPAresingNonExistentDicFilesBlowsUp(): void
    {
        Dictionary::setFileLocation(__DIR__ . '/share/');

        $this->expectException(PathNotFoundException::class);

        Dictionary::parseFile('foobar');
    }

    /**
     * @dataProvider localeUnificationProvider
     */
    #[DataProvider('localeUnificationProvider')]
    public function testLocaleUnification(string $parameter, string $expected): void
    {
        $obj = new \Org\Heigl\Hyphenator\Dictionary\Dictionary();
        $method = \UnitTestHelper::getMethod($obj, 'unifyLocale');
        $result = $method->invoke($obj, $parameter);

        $this->assertEquals($expected, $result);
    }

    public static function localeUnificationProvider(): \Iterator
    {
        yield array('de', 'de');
        yield array('DE', 'de');
        yield array('de de', 'de_DE');
        yield array('fo_BA', 'fo_BA');
        yield array('DE,de', 'de_DE');
        yield array('fooBar', 'fooBar');
    }
}
