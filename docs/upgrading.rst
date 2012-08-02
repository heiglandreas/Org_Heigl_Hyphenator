Upgrading from the previous version of the ``Hyphenator``
#########################################################

Removed features
================

Due to the UTF-8-capability of the new ``Hyphenator``-package
``setSpecialChars()`` has been removed, as that simply was
a workaround for non-ASCII-characters.

Currently the feature to disable already hyphenated words and to replace
custom-hyphenation-characters with the default-hyphen-character as disabled.
This feature will come again in a later version, but has currently not been implemented
in the new version. So, if you NEED that feature DO NOT UPGRADE!

Different approach to tokenizing strings
========================================

In the previous version of the ``Hyphenator`` a string has been tokenized into
words by using whitespace and - to a certain extend - some special punktuation marks.
This has been done right inside the hyphenation-method which was a rather dirty trick
and has proven extremely inflexible an inacurate

Therefore this tokenizing has been completely rewritten for the new version. Now you can add
``\Org\Heigl\Hyphenator\Tokenizer``-Objects to your Hyphenato-Instance that will
split the given string into Word-Tokens (those will be hyphenated later), Whitespace-Tokens and Non-Word-Tokens.

Currently there are two tokenizers delivered with the package.

``WhitespaceTokenizer``
    Split the given string at every whitespace occurence creating ``WhitespaceTokens``
    and ``WordTokens``.

``PunctuationTokenizer``
    Split the given string at every occurence of a common set of punctuation-characters creating
    ``NonWordTokens`` for the punctuation-characters and ``WordTokens``
    for everything else
    
But more Tokenizers will come and feel free to write your own ones.

The above mentioned feature to disable hyphenation of already hyphenated words will be implemented
using such a new tokenizer.
