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
 * @category  Hyphenator
 * @package   Org\Heigl\Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @link      http://github.com/heiglandreas/Hyphenator
 * @since     02.11.2011
 */

namespace Org\Heigl\Hyphenator;

use Org\Heigl\Hyphenator\Dictionary\Dictionary;
use Org\Heigl\Hyphenator\Dictionary\DictionaryRegistry;
use Org\Heigl\Hyphenator\Exception\PathNotDirException;
use Org\Heigl\Hyphenator\Exception\PathNotFoundException;
use Org\Heigl\Hyphenator\Filter\Filter;
use Org\Heigl\Hyphenator\Filter\FilterRegistry;
use Org\Heigl\Hyphenator\Tokenizer\Token;
use Org\Heigl\Hyphenator\Tokenizer\Tokenizer;
use Org\Heigl\Hyphenator\Tokenizer\TokenizerRegistry;
use Org\Heigl\Hyphenator\Tokenizer\TokenRegistry;
use Org\Heigl\Hyphenator\Tokenizer\WordToken;

/**
 * This class implements word-hyphenation
 *
 * Word-hyphenation is implemented on the basis of the algorithms developed by
 * Franklin Mark Liang for LaTeX as described in his dissertation at the department
 * of computer science at stanford university.
 *
 * The idea to this package came from Mathias Nater <mnater@mac.com> who
 * implemented this word-hyphenation-algorithm for javascript.
 *
 * After Implementing that algorithm for the first Hyphenator-Version I stumbled
 * over the Informations of LÁSZLÓ NÉMETH from OpenOffice.org.
 *
 * That brought me to change three things for the next Version of the
 * Hyphenator.
 * <ol>
 * <li>Use the Dictionary files from OpenOffice.org instead of the ones directly
 * from Tex because the OOo-Files are already stripped of the unnecessary
 * Informations</li>
 * <li>Add the possibility to use non-standard hyphenations</li>
 * <li>Add the possibility to add better word-tokenising</li>
 * </ol>
 *
 * Beside those changes there are some other changes between the first and the
 * second version of the Hyphenator.
 *
 * So Version 2 of the Hyphenator<ul>
 * <li>requires PHP5.3 as it uses namespaces.</li>
 * <li>aims to 100% Code-Coverage via Unit-Tests</li>
 * <li>removes some unnecessary options</li>
 * <li>is completely rewritten from scratch</li>
 * </ul>
 *
 * So here is the smalest example for the usage of the class:
 * <code>
 * &lt;?php
 * use \Org\Heigl\Hyphenator as h;
 * // Create a hyphenator-instance based on a given config-file
 * $hyphenator = h\Hyphenator::factory('/path/to/the/config/file.properties');
 *
 * // And hyphenate a given string
 * $hyphenatedText = $hyphenator->hyphenate($string);
 * </code>
 * Registering the autoloader is essential before the first call to the
 * Hyphenator
 * <code language="php">
 * &lt;?php
 * require_once '/path/to/Org/Heigl/Hyphenator/Hyphenator.php';
 * spl_autoload_register('\Org\Heigl\Hyphenator\Hyphenator::__autoload');
 * </code>
 * Of course the Hyphenator can be adapted to the most requirements via an
 * Options-Object. And the tokenisation in this small example uses the simple
 * WhiteSpace-Tokenizer. Other more complex Tokenizers are available.
 *
 * Examples for those can be found at http://github.com/heiglandreas/Hyphenator
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <a.heigl@wdv.de>
 * @copyright 2008-2011 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   2.0.1
 * @link      http://code.google.com/p/hyphenator
 * @link      http://www.tug.org/docs/liang/liang-thesis.pdf
 * @link      http://hunspell.sourceforge.net/tb87nemeth.pdf
 * @link      http://github.com/heiglandreas/Hyphenator
 * @since     04.11.2011
 */
final class Hyphenator
{

    /**
     * The highest possible hyphernation quality
     *
     * @const int QUALITY_HIGHEST
     */
    const QUALITY_HIGHEST = 9;

    /**
     * A high hyphernation quality
     *
     * @const int QUALITY_HIGH
     */
    const QUALITY_HIGH    = 7;

    /**
     * A medium hyphernation quality
     *
     * @const int QUALITY_NORMAL
     */
    const QUALITY_NORMAL  = 5;

    /**
     * A low hyphernation quality
     *
     * @const int QUALITY_LOW
     */
    const QUALITY_LOW     = 3;

    /**
     * The lowest possible hyphernation quality
     *
     * @const int QUALITY_LOWEST
     */
    const QUALITY_LOWEST  = 1;

    /**
     * Storage for the Home-path.
     *
     * The hyphenation-files iare searched in different places.
     * <ol><li>Location given via the constant HYPHENATOR_HOME</li>
     * <li>Location set via \Org\Heigl\Hyphenator\Hyphenator::setDefaultHome()</li>
     * <li>Location set via \Org\Heigl\Hyphenator\Hyphenator::setHome()</li>
     * <li>The 'share'-Folder inside the Hyphenator-Package</li>
     * </ol>
     *
     * The configuration-object can also be obtained using the
     * \Org\Heigl\Hyphenator::getConfig()-Method and can then be adapted
     * according to ones needs.
     *
     * @var string $homePath
     */
    private $homePath = '';

    /**
     * Storage of the default Home-Path.
     *
     * @var string $defaultHomePath
     */
    private static $defaultHomePath = '';

    /**
     * Storage for the Options-Object.
     *
     * @var Options $options
     */
    private $options;

    /**
     * Storage for the Dictionaries.
     *
     * @var DictionaryRegistry $dicts
     */
    private $dicts;

    /**
     * Storage for the Filters.
     *
     * @var FilterRegistry $filters
     */
    private $filters;

    /**
     * Storage for the tokenizers.
     *
     * @var TokenizerRegistry $tokenizers
     */
    private $tokenizers;

    /**
     * Set the Options
     *
     * @param Options $options The options to set
     *
     * @return Hyphenator
     */
    public function setOptions(Options $options)
    {
        $this->options = $options;
        $this->tokenizers->cleanup();
        foreach ($this->options->getTokenizers() as $tokenizer) {
            $this->addTokenizer($tokenizer);
        }

        return $this;
    }

    /**
     * Get the Options
     *
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add a Dictionary to the Hyphenator
     *
     * @param Dictionary|string $dictionary The
     * Dictionary wit hyphenation-Patterns to add to this Hyphenator
     *
     * @return Hyphenator
     */
    public function addDictionary($dictionary)
    {
        if (! $dictionary instanceof Dictionary) {
            Dictionary::setFileLocation($this->getHomePath() . '/files/dictionaries');
            $dictionary = Dictionary::factory($dictionary);
        }
        $this->dicts->add($dictionary);

        return $this;
    }

    /**
     * Add a Filter to the Hyphenator
     *
     * @param Filter|string $filter The Filter with
     * non-standard-hyphenation-patterns
     *
     * @link http://hunspell.sourceforge.net/tb87nemeth.pdf
     * @return Hyphenator
     */
    public function addFilter($filter)
    {
        if (! $filter instanceof Filter) {
            $filter = '\\Org\\Heigl\\Hyphenator\\Filter\\' . ucfirst($filter) . 'Filter';
            /** @var Filter $filter */
            $filter = new $filter();
        }
        $filter->setOptions($this->getOptions());
        $this->filters->add($filter);

        return $this;
    }

    /**
     * Add a tokenizer to the tokenizer-registry
     *
     * @param Tokenizer|string $tokenizer The tokenizer to add
     *
     * @return Hyphenator
     */
    public function addTokenizer($tokenizer)
    {
        if (! $tokenizer instanceof Tokenizer) {
            $tokenizer = '\\Org\\Heigl\Hyphenator\\Tokenizer\\' . ucfirst($tokenizer) . 'Tokenizer';
            /** @var Tokenizer $tokenizer */
            $tokenizer = new $tokenizer();
        }
        $this->tokenizers->add($tokenizer);

        return $this;
    }

    /**
     * Get the tokenizers
     *
     * @return TokenizerRegistry
     */
    public function getTokenizers()
    {
        if (0 == $this->tokenizers->count()) {
            foreach ($this->getOptions()->getTokenizers() as $tokenizer) {
                $this->addTokenizer($tokenizer);
            }
        }

        return $this->tokenizers;
    }

    /**
     * Get the dictionaries
     *
     * @return DictionaryRegistry
     */
    public function getDictionaries()
    {
        if (0 == $this->dicts->count()) {
            $this->addDictionary($this->getOptions()->getDefaultLocale());
        }

        return $this->dicts;
    }

    /**
     * Get the filters
     *
     * @return FilterRegistry
     */
    public function getFilters()
    {
        if (0 == $this->filters->count()) {
            foreach ($this->getOptions()->getFilters() as $filter) {
                $this->addFilter($filter);
            }
        }

        return $this->filters;
    }

    public function __construct()
    {
        $this->dicts      = new DictionaryRegistry();
        $this->filters    = new FilterRegistry();
        $this->tokenizers = new TokenizerRegistry();

        $optFile = $this->getHomePath() . DIRECTORY_SEPARATOR . 'Hyphenator.properties';
        $this->setOptions(Options::factory($optFile));
    }

    /**
     * This method does the actual hyphenation.
     *
     * The given <var>$string</var> is splitted into chunks (i.e. Words) at
     * every blank.
     *
     * After that every chunk is hyphenated and the array of chunks is merged
     * into a single string using blanks again.
     *
     * This method does not take into account other word-delimiters than blanks
     * (eg. returns or tabstops) and it will fail with texts containing markup
     * in any way.
     *
     * @param string $string The string to hyphenate
     *
     * @return string|array<array-key, mixed> The hyphenated string
     */
    public function hyphenate($string)
    {
        $tokens = $this->tokenizers->tokenize($string);
        $tokens = $this->getHyphenationPattern($tokens);
        $tokens = $this->filter($tokens);
        if (1 === count($tokens) && 1 === $this->getFilters()->count()) {
            $tokens->rewind();
            return $tokens->current()->getHyphenatedContent();
        }

        return $this->getFilters()->concatenate($tokens);
    }

    /**
     * Get the hyphenation pattern for the contained tokens
     *
     * Use the dictionaties and options of the given Hyphenator-Object
     *
     * @param TokenRegistry $registry The Hyphenator object containing the
     * dictionaries and options
     *
     * @return TokenRegistry
     */
    public function getHyphenationPattern(TokenRegistry $registry)
    {
        $minWordLength = $this->getOptions()->getMinWordLength();
        foreach ($registry as $token) {
            if (! $token instanceof WordToken) {
                continue;
            }
            if ($minWordLength > $token->length()) {
                continue;
            }
            $this->getPatternForToken($token);
        }

        return $registry;
    }

    /**
     * Filter the content of the given TokenRegistry
     *
     * @param TokenRegistry $registry The tokens
     * to filter
     *
     * @return TokenRegistry
     */
    public function filter(TokenRegistry $registry)
    {
        return $this->getFilters()->filter($registry);
    }

    /**
     * Hyphenate a Token-Object
     *
     * @param WordToken $token The token to hyphenate
     *
     * @return Token
     */
    public function getPatternForToken(WordToken $token)
    {
        foreach ($this->getDictionaries() as $dictionary) {
            $token->addPattern($dictionary->getPatternsForWord($token->get()));
        }

        return $token;
    }

    /**
     * Set the default home-Path
     *
     * @param string $homePath The default Hyphenator Home-path.
     *
     * @throws PathNotFoundException
     * @throws PathNotDirException
     * @return void
     */
    public static function setDefaultHomePath($homePath)
    {
        if (! file_exists($homePath)) {
            throw new PathNotFoundException($homePath . ' does not exist');
        }
        if (! is_Dir($homePath)) {
            throw new PathNotDirException($homePath . ' is not a directory');
        }

        self::$defaultHomePath = realpath($homePath);
    }

    /**
     * Get the default Home-Path
     *
     * @return string
     */
    public static function getDefaultHomePath()
    {
        if (is_Dir(self::$defaultHomePath)) {
            return self::$defaultHomePath;
        }
        if (defined('HYPHENATOR_HOME') && is_Dir(HYPHENATOR_HOME)) {
            return realpath(HYPHENATOR_HOME);
        }
        if ($home = getenv('HYPHENATOR_HOME')) {
            if (is_Dir($home)) {
                return $home;
            }
        }

        return __DIR__ . '/share';
    }

    /**
     * Set the instance-home-Path
     *
     * @param string $homePath This instances home-path.
     *
     * @throws PathNotFoundException
     * @throws PathNotDirException
     * @return Hyphenator
     */
    public function setHomePath($homePath)
    {
        if (! file_exists($homePath)) {
            throw new PathNotFoundException($homePath . ' does not exist');
        }
        if (! is_Dir($homePath)) {
            throw new PathNotDirException($homePath . ' is not a directory');
        }

        $this->homePath = realpath($homePath);

        return $this;
    }

    /**
     * Get this instances Home-Path.
     *
     * If no homePath is set for this instance this method will return the
     * result of the \Org\Heigl\Hyphenator\Hyphenator::getdefaultHomePath()
     * Method
     *
     * @return string
     */
    public function getHomePath()
    {
        if (! is_dir($this->homePath)) {
            return self::getDefaultHomePath();
        }

        return $this->homePath;
    }

    /**
     * Create a new Hyphenator-Object for a certain locale
     *
     * To determine the storage of the dictionaries we either use the set
     * default configuration-file or we take the provided file and set the
     * home-path from the information within that file.
     *
     * @param string $path   The path to the configuration-file to use
     * @param string $locale The locale to be used
     *
     * @return Hyphenator
     */
    public static function factory($path = null, $locale = null)
    {
        $hyphenator = new Hyphenator();
        if (null !== $path && file_Exists($path)) {
            $hyphenator->setHomePath($path);
        }
        if (null !== $locale) {
            $hyphenator->getOptions()->setDefaultLocale($locale);
        }

        return $hyphenator;
    }

    /**
     * autoload classes.
     *
     * @param string $className the name of the class to load
     *
     * @return bool
     */
    public static function __autoload($className)
    {
        if (0 !== strpos($className, 'Org\\Heigl\\Hyphenator')) {
            return false;
        }
        $className = substr($className, strlen('Org\\Heigl\\Hyphenator\\'));
        $file = str_replace('\\', '/', $className) . '.php';
        $fileName = __DIR__ . DIRECTORY_SEPARATOR . $file;
        if (! file_exists(realpath($fileName))) {
            return false;
        }
        if (! @include_once $fileName) {
            return false;
        }

        return true;
    }

    /**
     * Register this packages autoloader with the autoload-stack
     *
     * @return void
     */
    public static function registerAutoload()
    {
        spl_autoload_register(array(Hyphenator::class, '__autoload'));
    }
}

/*
 * Check for requirements and if these are not met throw an exception
 */
if (! extension_loaded('mbstring')) {
    throw new \Exception('\Org\Heigl\Hyphenator requires the mbstring-extension to be loaded');
}
mb_internal_encoding('UTF-8');
