<?php

/**
 * Twig extension for Org_Heigl_Hyphenator (https://github.com/heiglandreas/Org_Heigl_Hyphenator)
 *
 * @package TwigHyphenate
 * @author Martin Tournoij <martin@arp242.net>
 * @license Free to use for any purpose. There are no restrictions.
 *
 *
 * This adds both a filter:
 *     {{ 'hyphenate this precariously long string'|hyphenate }}
 *
 * And a tag:
 *     {% hyphenate %}
 *        hyphenate this precariously long string
 *    {% endhyphenate %}
 *
 * Usage:
 *     include('twig/lib/Twig/Autoloader.php');
 *     Twig_Autoloader::register();
 *     include('/TwigHyphenate.php');
 *
 *     $loader = new Twig_Loader_Filesystem();
 *     $twig = new Twig_Environment($loader, array());
 *     $twig->addExtension(new TwigHyphenate());
 *
 *
 * TODO:
 *   - Add language selection to tag
 *   - Add easier method for finding Hyphenator.php
 *   - Add parameter for $html parameter to Org_Heigl_Hyphenator->hyphenate();
 */


/**
 * This actually hyphenates the text
 *
 * @param string $text Text to hyphenate
 * @param string $language Language to use
 */
function Hyphenate($text, $language='en')
{
	if (!class_exists('Org_Heigl_Hyphenator'))
		include_once('hyphenator/Hyphenator.php');

	$hyphenator = Org_Heigl_Hyphenator::getInstance($language);
	$hyphenator->setHyphen('&shy;')
			->setQuality(Org_Heigl_Hyphenator::QUALITY_HIGHEST)
			->setNoHyphenateMarker('nbr:')
	;

	return $hyphenator->hyphenate($text, True);
}

/**
 * Add filter
 */
class TwigHyphenate extends Twig_Extension
{
	public function getName()
	{
		return 'TwigHyphenate';
	}

	public function getFilters()
	{
		return array(
			'hyphenate' => new Twig_Filter_Function('Hyphenate', array('pre_escape' => 'html', 'is_safe' => array('html'))),
		);
	}

	public function getTokenParsers()
	{
		return array(
			new TwigHyphenateTokenParser()
		);
	}

}

/**
 * Add tag
 */
class TwigHyphenateTokenParser extends Twig_TokenParser
{
	public function getTag()
	{
		return 'hyphenate';
	}

	public function decideEnd($token)
	{
		return $token->test('endhyphenate');
	}

	public function parse(Twig_Token $token)
	{
		$lineno = $token->getLine();

		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decideEnd'), true);
		$this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

		return new TwigHyphenateNode($body, $lineno, $this->getTag());
	}
}

class TwigHyphenateNode extends Twig_Node
{
	public function __construct(Twig_NodeInterface $body, $lineno, $tag = 'hyphenate')
	{
			parent::__construct(array('body' => $body), array(), $lineno, $tag);
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(Twig_Compiler $compiler)
	{
			$compiler
					->addDebugInfo($this)
					->write("ob_start();\n")
					->subcompile($this->getNode('body'))
					->write("echo Hyphenate(ob_get_clean());\n")
			;
	}
}
?>
