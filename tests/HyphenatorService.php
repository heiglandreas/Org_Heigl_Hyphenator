<?php

declare(strict_types=1);

/**
 * Copyright Andreas Heigl <andreas@heigl.org>
 *
 * Licensed under the MIT-license. For details see the included file LICENSE.md
 */

namespace Org\Heigl\HyphenatorTest;

use Org\Heigl\Hyphenator\Hyphenator;
use Org\Heigl\Hyphenator\Options;

final class HyphenatorService
{
    private static $instance = null;

    public function __construct(Hyphenator $hyphenator, array $customPattern)
    {
        $o = new Options();
        $o->setHyphen('-')
            ->setDefaultLocale('de_DE')
            ->setRightMin(2)
            ->setLeftMin(2)
            ->setWordMin(4)
            ->setFilters('NonStandard')
            ->setTokenizers('Whitespace, Punctuation');

        $hyphenator->setOptions($o);

        $dictionary = $hyphenator->getDictionaries()->current();
        foreach ($customPattern as $string => $pattern) {
            $dictionary->addPattern($string, $pattern);
        }

        $this->hyphenator = $hyphenator;
    }

    public static function singleton(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(new Hyphenator(), [
                'spender' => '08000000',
            ]);
        }

        return self::$instance;
    }

    public function hyphenate(string $word): array
    {
        return $this->hyphenator->hyphenate($word);
    }
}
