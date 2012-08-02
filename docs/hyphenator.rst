Introduction
############

``Org\Heigl\Hyphenator`` is a package to enable word-hyphenation in PHP. It uses
the algorithms described by Marc Liang in his thesis `Word Hyphenation by 
computer <http://www.tug.org/docs/liang/liang-thesis.pdf>`_ and the extensions 
described by László Németh in his work `Automatic non-standard hyphenation in 
OpenOffice.org <http://www.tug.org/TUGboat/tb27-1/tb86nemeth.pdf>`_.

These algorithms are based on matching words against certain patterns that 
describe places inside a word where hyphenation is possible or must not occur. 
This Hyphenator uses the pattern-files from OpenOffice which are based on the 
pattern-files created for TeX.

Theory of operation
===================

Only words can be hyphenated and the beginning and the end of a word
are special boundaries that have to be considered for hyphenation. Therefore
the first part of the hyphenation-process is to split up any string into
words that can be hyphenated and other stuff. In this ``Hyphenator``-package
that ist done by using special ``Tokenizers``. These split the given
string according to their special Task. So the ``WhitespaceTokenizer``
uses whitespace-characters as split-point whereas the ``PunctuationTokenizer``
uses common punktuation.characters.

The next step in the hyphenation process is to determin the possible 
hyphenation-places using special hyphenation-pattern. These patterns have been 
used in the TeX-language  for a long time now and are widely used in other 
OpenSource-Projects. The pattern files used for this ``Hyphenator``-package are 
from the OpenOffice.org-project. These are also based on the TeX-pattern, but 
are more easy to parse than the original TeX-files. They are also in some cases 
enriched with additional information. These patterns are locale-dependend and 
are provided using a ``Dictionary``

After the patterns have been retrieved for a word, the possible hyphenation 
positions can be defined. The word is then filtered using a ``Filter`` that 
handles the actual hyphenation. According to the selected filter it is for 
instance possible to mark every possible hyphenation-position with the given 
Hyphen-string (``SimpleFilter``). Other Filters are possible.

The last step is to merge all the bits and pieces the tokenizers left over so we
can ge a final hyphenation result. This too is handled by the Filters as the 
result might be different according to the used token-filter.

