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
    /**
     * @var Hyphenator
     */
    private $hyphenator;

    /** @var self */
    private static $instance = null;

    /**
     * @param Hyphenator $hyphenator
     * @param array<string, string> $customPattern
     */
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
                // Adds the hyphenation point in "spender" that the dictionary
                // does not provide, so the custom pattern stays observable.
                'spender' => '00001000',
            ]);
        }

        return self::$instance;
    }

    /**
     * @param string $word
     * @return mixed[]
     */
    public function hyphenate(string $word): array
    {
        return $this->hyphenator->hyphenate($word);
    }
}
