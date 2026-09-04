<?php

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licensed under the MIT-license. For details see the included file LICENSE.md
 */
declare(strict_types=1);

namespace Org\Heigl\HyphenatorTest;

use Org\Heigl\Hyphenator\Dictionary\Dictionary;
use Org\Heigl\Hyphenator\Hyphenator;
use PHPUnit\Framework\TestCase;

final class HyphenatorUserTest extends TestCase
{
    public function testUsingTwoHyphenationPatterns(): void
    {
        Dictionary::setFileLocation(__DIR__ . '/../src/share/files/dictionaries');
        $hyphenator = Hyphenator::factory();

        $hyphenator->getOptions()->setHyphen('-');

        $de_DE = Dictionary::fromLocale('de_DE');
        // The dictionary no longer hyphenates "spender" on its own, so the
        // added pattern asks for the point instead of suppressing a wrong one.
        $de_DE->addPattern('spender', '00001000');
        $hyphenator->addDictionary($de_DE);

        $this->assertEquals('Hand-tuch-spen-der', $hyphenator->hyphenate('Handtuchspender'));
    }

    public function testMultipleHyphenationCallsResultInSameHyphenation(): void
    {
        $service = HyphenatorService::singleton();

        $this->assertEquals([
            'Hand-tuchspender',
            'Handtuch-spender',
            'Handtuchspen-der'
        ], $service->hyphenate('Handtuchspender'));
        $this->assertEquals([
            'Hand-tuchspender',
            'Handtuch-spender',
            'Handtuchspen-der'
        ], $service->hyphenate('Handtuchspender'));

        $service2 = HyphenatorService::singleton();
        $this->assertEquals([
            'Hand-tuchspender',
            'Handtuch-spender',
            'Handtuchspen-der'
        ], $service->hyphenate('Handtuchspender'));

        $this->assertSame($service, $service2);
    }

    public function testSingleLetterPatternProducesHyphenationPoint(): void
    {
        $hyphenator = Hyphenator::factory();
        $options = $hyphenator->getOptions();
        $options->setHyphen('-');
        $options->setLeftMin(2);
        $options->setRightMin(2);
        $options->setWordMin(6);
        $options->setQuality(Hyphenator::QUALITY_HIGHEST);

        $dictionary = new Dictionary();
        $dictionary->addPattern('b', '10');
        $hyphenator->addDictionary($dictionary);

        $this->assertSame('Sil-ben', $hyphenator->hyphenate('Silben'));
    }
}
