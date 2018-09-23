<?php

namespace Org\Heigl\Hyphenator\Tokenizer;

abstract class AbstractTokenizer implements Tokenizer
{
    /**
     * Split the given input into tokens.
     *
     * The input can be a string or a tokenRegistry. If the input is a
     * TokenRegistry, each item will be tokenized.
     *
     * @param string|\Org\Heigl\Hyphenator\Tokenizer\TokenRegistry $input The
     * input to be tokenized
     *
     * @return \Org\Heigl\Hyphenator\Tokenizer\TokenRegistry
     */
    public function run($input)
    {
        if (! $input instanceof TokenRegistry) {
            $wt = new WordToken($input);
            $input = new TokenRegistry();
            $input->add($wt);
        }

        // Clone the TokenRegistry to prevent iterating over newly inserted tokens
        $registry = clone $input;

        foreach ($input as $token) {
            if (! $token instanceof WordToken) {
                continue;
            }
            $newTokens = $this->tokenize($token->get());
            if ($newTokens == array($token)) {
                continue;
            }
            $registry->replace($token, $newTokens);
        }

        return $registry;
    }

    /**
     * Split the given string into tokens.
     *
     * @param string $input The String to tokenize
     *
     * @return Token[]
     */
    abstract protected function tokenize($input);
}
