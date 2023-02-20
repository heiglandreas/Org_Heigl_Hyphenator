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

class HyphenatorUserTest extends TestCase
{
    public function testUsingTwoHyphenationPatterns()
    {
        Dictionary::setFileLocation(__DIR__ . '/../src/share/files/dictionaries');
        $hyphenator = Hyphenator::factory();

        $hyphenator->getOptions()->setHyphen('-');

        $de_DE = Dictionary::fromLocale('de_DE');
        $de_DE->addPattern('spender', '08000000');
        $hyphenator->addDictionary($de_DE);

        $this->assertEquals('Hand-tuch-spen-der', $hyphenator->hyphenate('Handtuchspender'));
    }
}
