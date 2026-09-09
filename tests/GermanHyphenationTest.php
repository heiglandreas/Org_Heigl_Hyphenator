<?php

declare(strict_types=1);

namespace Org\Heigl\HyphenatorTest;

use Org\Heigl\Hyphenator\Dictionary\Dictionary;
use Org\Heigl\Hyphenator\Hyphenator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Guards the quality of the German hyphenation dictionaries.
 *
 * The corpus are words that the previous, merged two-level dictionary
 * hyphenated wrongly. The check comes in two parts.
 *
 * Words whose hyphenation is complete and correct are compared exactly. For
 * the remaining words only the property this dictionary promises is asserted:
 * every hyphenation point that comes out has to be one of the points German
 * orthography allows. Those words are deliberately not compared exactly,
 * because Dictionary::getPatternsForWord() never looks up single-letter
 * patterns such as `1b` and therefore drops hyphenation points; a separate
 * pull request fixes that. An exact comparison would freeze that incomplete
 * output as the expected one and turn this test red as soon as it is fixed.
 * The subset property stays valid either way.
 */
final class GermanHyphenationTest extends TestCase
{
    /**
     * @dataProvider hyphenatesExactlyProvider
     */
    #[DataProvider('hyphenatesExactlyProvider')]
    public function testHyphenatesExactly(string $word, string $expected): void
    {
        $this->assertSame($expected, $this->hyphenator()->hyphenate($word));
    }

    public static function hyphenatesExactlyProvider(): \Iterator
    {
        yield ['Deutschland', 'Deutsch-land'];
        yield ['Abschiedsspiel', 'Ab-schieds-spiel'];
        yield ['Rutschbahn', 'Rutsch-bahn'];
        yield ['Gutschein', 'Gut-schein'];
        yield ['Wirtschaft', 'Wirt-schaft'];
    }

    /**
     * @dataProvider producesNoWrongBreakProvider
     */
    #[DataProvider('producesNoWrongBreakProvider')]
    public function testProducesNoWrongBreak(string $word, string $allowed): void
    {
        $hyphenated = $this->hyphenator()->hyphenate($word);
        $produced = $this->breakPositions($hyphenated);

        $this->assertNotSame(
            [],
            $produced,
            sprintf('%s was not hyphenated at all', $word)
        );
        $this->assertSame(
            [],
            array_values(array_diff($produced, $this->breakPositions($allowed))),
            sprintf('%s was hyphenated as %s, allowed is %s', $word, $hyphenated, $allowed)
        );
    }

    public static function producesNoWrongBreakProvider(): \Iterator
    {
        yield ['Bewerbungsgespräch', 'Be-wer-bungs-ge-spräch'];
        yield ['anderssprachige', 'an-ders-spra-chi-ge'];
        yield ['Nutzererlebnis', 'Nut-zer-er-leb-nis'];
        yield ['Amperestunde', 'Am-pe-re-stun-de'];
        yield ['Altersprozess', 'Al-ters-pro-zess'];
        yield ['Aktivsaldo', 'Ak-tiv-sal-do'];
        yield ['Zusammenarbeit', 'Zu-sam-men-ar-beit'];
        yield ['Weiterbildung', 'Wei-ter-bil-dung'];
        yield ['Silbentrennung', 'Sil-ben-tren-nung'];
        yield ['Alternativsprache', 'Al-ter-na-tiv-spra-che'];
    }

    private function hyphenator(): Hyphenator
    {
        Dictionary::setFileLocation(__DIR__ . '/../src/share/files/dictionaries');

        $hyphenator = Hyphenator::factory();
        $options = $hyphenator->getOptions();
        $options->setHyphen('-');
        $options->setLeftMin(2);
        $options->setRightMin(2);
        $options->setWordMin(6);
        $options->setQuality(Hyphenator::QUALITY_HIGHEST);
        $hyphenator->addDictionary(Dictionary::fromLocale('de_DE'));

        return $hyphenator;
    }

    /**
     * Character offsets of the hyphens in a hyphenated word.
     *
     * An offset is the number of characters in front of the hyphen, which makes
     * the offsets of two hyphenations of the same word comparable.
     *
     * @return int[]
     */
    private function breakPositions(string $hyphenated): array
    {
        $positions = [];
        $offset = 0;
        $parts = explode('-', $hyphenated);
        $last = count($parts) - 1;
        for ($index = 0; $index < $last; $index++) {
            $offset += mb_strlen($parts[$index]);
            $positions[] = $offset;
        }

        return $positions;
    }
}
