<?php
/**
 * $Id$
 *
 * Copyright (c) 2008-2009 Andreas Heigl<andreas@heigl.org>
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
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   SVN: $Revision$
 * @since     20.04.2009
 */

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Org_Heigl_Hyphenator */
require_once 'Org/Heigl/Hyphenator.php';

/**
 * This class tests the functionality of the class Org_Heigl_Hyphenator
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   SVN: $Revision$
 * @since     20.04.2009
 */
class Org_Heigl_HyphenatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test whether the Hyphenator class can not be created via a normal
     * 'new' Statement
     *
     * @throws Exception
     * @todo   This Test currently crashes due to a Fatal Error that is
     * triggered by PHP
     * @return void
     */
    public function testHyphenatorCanOnlyBeCalledStatic () {
        $this -> setExpectedException ( 'Exception' );
        $this -> markTestSkipped ( 'Calling the new Operator crashes' );
    }

    /**
     * Test that the Object returned by the singleton is an
     * Org_Heigl_Hyphenator-Object
     *
     * @return void
     */
    public function testHyphenatorSingletonReturnsHyphenatorObject () {
        $hyphen = Org_Heigl_Hyphenator::getInstance ( 'en_EN' );
        $this -> assertTrue ( $hyphen instanceof Org_Heigl_Hyphenator );
    }

    /**
     * Test that the singleton returns different Objects for different Languages
     *
     * @return void
     */
    public function testHyphenatorSingeltonReturnsDifferentObjectsForDifferentLanguages () {
        $hyphenA = Org_Heigl_Hyphenator::getInstance ( 'de_DE' );
        $hyphenB = Org_Heigl_Hyphenator::getInstance ( 'en_EN' );
        $this -> assertTrue ( $hyphenA !== $hyphenB );
    }

    /**
     * Test that Hyphenating the English word supercalifragilisticexpialidocious works
     * as expected by Liang
     *
     * @return void
     */
    public function testHyphenateWordEnglish () {
        $hyphen = Org_Heigl_Hyphenator::getInstance ( 'en_EN' );
        $hyphen -> setHyphen ( '-' );
        $result = $hyphen -> hyphenateWord ( 'supercalifragilisticexpialidocious' );
        $this -> assertEquals ( $result, 'su-per-cal-ifrag-ilis-tic-ex-pi-ali-do-cious' );
    }

    /**
     * Test that Hyphenating the german word 'Donaudampfschifffahrtskapitänsmütze'
     * works as expected
     *
     * @return void
     */
    public function testHyphenateWordGerman () {
        $hyphen = Org_Heigl_Hyphenator::getInstance ( 'de_DE' );
        $hyphen -> setHyphen ( '-' );
        $result = $hyphen -> hyphenateWord ( 'Donaudampfschifffahrtskapitänsmütze' );
        $this->markTestIncomplete('Doesn\' work');
        $this -> assertEquals ('Do-nau-dampf-schiff-fahrts-ka-pitäns-müt-ze',$result );
    }

    /**
     * Test that sentences are hyphenated as expected
     *
     * @return void
     */
    public function testHyphenateSentence () {
        Org_Heigl_Hyphenator::setDefaultLanguage ( 'de_DE' );
        $hyphenated = Org_Heigl_Hyphenator::parse ( '(Nützliches aus dem Netz: Neue Plattform aok4you' );
        $this->markTestIncomplete('Doesn\' work');
        $this -> assertEquals ('(Nütz-li-ches aus dem Netz: Neue Platt-form ao-k4y-ou', $hyphenated );
    }

    /**
     * Test that already hyphenated words are not hyphenated by the algorithm
     *
     * @return void
     */
    public function testHyphenateTrenngebot () {
        Org_Heigl_Hyphenator::setDefaultLanguage ( 'de_DE' );
        $hyphenated = Org_Heigl_Hyphenator::parse ( '(Themen werden noch festge&shy;legt.)' );
        $this -> assertEquals ( '(The-men wer-den noch festge-legt.)', $hyphenated );
    }

    /**
     * Test that setting a hyphen works
     *
     * @return void
     */
    public function testHyphenateStringForMinus () {
        $hyphen = Org_Heigl_Hyphenator::getInstance ( 'de_DE' );
        $hyphen -> setHyphen ( '-' );
        $result = $hyphen -> hyphenate ( 'Das ist der Nachtrag des Nikolaus von Smyrna unter dem Supermarkt des Hinterlandes' );
        $this -> assertTrue ( strpos ( $result, '-' ) !== false );
    }


    public function testSettingLeftMin () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $this -> assertEquals ( 2, $this -> readAttribute($hyph, '_leftMin' ) );
        $hyph -> setLeftMin ( 5 );
        $this -> assertEquals ( 5, $this -> readAttribute($hyph, '_leftMin' ) );
    }

    public function testSettingRightMin () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $this -> assertEquals ( 2, $this -> readAttribute($hyph, '_rightMin' ) );
        $hyph -> setRightMin ( 5 );
        $this -> assertEquals ( 5, $this -> readAttribute($hyph, '_rightMin' ) );
    }

    public function testSettingWordMin () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $this -> assertEquals ( 6, $this -> readAttribute($hyph, '_wordMin' ) );
        $hyph -> setWordMin ( 8 );
        $this -> assertEquals ( 8, $this -> readAttribute($hyph, '_wordMin' ) );
    }

    public function testParsingTexFile() {
        $origFile = dirname ( __FILE__ )
                  . DIRECTORY_SEPARATOR
                  . '..'
                  . DIRECTORY_SEPARATOR
                  . '..'
                  . DIRECTORY_SEPARATOR
                  . '..'
                  . DIRECTORY_SEPARATOR
                  . 'src'
                  . DIRECTORY_SEPARATOR
                  . 'Org'
                  . DIRECTORY_SEPARATOR
                  . 'Heigl'
                  . DIRECTORY_SEPARATOR
                  . 'Hyphenator'
                  . DIRECTORY_SEPARATOR
                  . 'files'
                  . DIRECTORY_SEPARATOR
                  . 'dehyphn.tex';
        $parsedFile = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'de.php';
        $hyp = Org_Heigl_Hyphenator::parseTexFile ( $origFile, $parsedFile, 'de' );
        $this -> assertTrue ( $hyp );
        $this -> assertFileExists ( $parsedFile );
        unlink ( $parsedFile );
    }

    public function testSettingCustomHyphen () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $this -> assertEquals ( '--', $this -> readAttribute($hyph, '_customHyphen' ) );
        $hyph -> setHyphen ( '-' );
        $hyph -> setCustomHyphen ( '==' );
        $this -> assertEquals ( '==', $this -> readAttribute($hyph, '_customHyphen' ) );
        $this -> assertEquals ( 'Ka-pitän', $hyph -> hyphenate ( 'Ka==pitän') );
    }

    public function testSettingNoHyphen () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $this -> assertEquals ( null, $this -> readAttribute($hyph, '_noHyphenateString' ) );
        $hyph -> setNoHyphenateMarker ( '==' );
        $this -> assertEquals ( '==', $this -> readAttribute($hyph, '_noHyphenateString' ) );
        $this -> assertEquals ( 'Kapitän', $hyph -> hyphenate ( '==Kapitän') );
    }

    public function testSettingNoHyphenWithCustomHyphen () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $hyph -> setNoHyphenateMarker ( '--' );
        $hyph -> setHyphen ( '-' );
        $hyph -> setCustomHyphen ( '==' );
        $this -> assertEquals ( 'Kapitän', $hyph -> hyphenate ( '--Ka==pitän') );
    }

    public function testSettingQuality () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $this -> assertEquals ( 9, $this -> readAttribute($hyph, '_quality' ) );
        $hyph -> setQuality ( 5);
        $this -> assertEquals ( 5, $this -> readAttribute($hyph, '_quality' ) );
    }

    public function testHyphenationQuality() {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $hyph -> setQuality ( Org_Heigl_Hyphenator::QUALITY_HIGHEST );
        $poorest = $hyph -> hyphenate ( 'wortgestenreich');
        $this -> assertEquals ( 9, $this -> readAttribute($hyph, '_quality' ) );
        $hyph -> setQuality ( Org_Heigl_Hyphenator::QUALITY_LOWEST );
        $best = $hyph -> hyphenate ( 'wortgestenreich');
        $this -> assertTrue ( $poorest !== $best );
        $this -> assertEquals ( 1, $this -> readAttribute($hyph, '_quality' ) );

    }

    public function testHyphenatingEmptyString () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $this -> assertEquals ( '', $hyph -> hyphenateWord ( '' ) );
        $this -> assertEquals ( '', $hyph -> hyphenateWord ( ' ' ) );

    }

    public function testHyphenateStringTooSmall () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $hyph -> setWordMin ( 12 )
              -> setLeftMin ( 2)
              -> setRightMin ( 2 )
              -> setQuality (9)
              -> setHyphen ( '-' );
        $this -> assertEquals ( 'Butzbach', $hyph -> hyphenateWord ( 'Butzbach' ) );
        $hyph -> setWordMin ( 5 );
        $this -> assertEquals ( 'Toll-wut', $hyph -> hyphenateWord ( 'Tollwut' ) );

    }

    public function testHyphenateStringForShyEntity () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $hyph -> setWordMin ( 5 )
              -> setLeftMin ( 2)
              -> setRightMin ( 2 )
              -> setQuality (9)
              -> setHyphen ( '&shy;' );
        $this -> assertEquals ( 'Butz&shy;bach', $hyph -> hyphenateWord ( 'Butzbach' ) );

    }
    public function testHyphenateStringWithShyEntity () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $hyph -> setWordMin ( 5 )
              -> setLeftMin ( 2)
              -> setRightMin ( 2 )
              -> setQuality (9)
              -> setHyphen ( '-' );
        $this -> assertEquals ( 'Butz-bach', $hyph -> hyphenateWord ( 'Butz&shy;bach' ) );
    }

    public function testSettingParsedFileDir ()
    {
        $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'org_heigl_hyphenator';
        $this->assertAttributeEquals($tmp,'_defaultParsedFileDir','Org_Heigl_Hyphenator');
        $this->assertEquals($tmp, Org_Heigl_Hyphenator::getDefaultParsedFileDir());
        Org_Heigl_Hyphenator::setDefaultParsedFileDir('/test');
        $this->assertAttributeEquals($tmp,'_defaultParsedFileDir','Org_Heigl_Hyphenator');
        $this->assertEquals($tmp, Org_Heigl_Hyphenator::getDefaultParsedFileDir());
        $cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test';
        Org_Heigl_Hyphenator::setDefaultParsedFileDir($cacheDir);
        $this->assertAttributeEquals($cacheDir,'_defaultParsedFileDir','Org_Heigl_Hyphenator');
        $this->assertEquals($cacheDir, Org_Heigl_Hyphenator::getDefaultParsedFileDir());
    }

    public function testHyphenationSpeed () {
        $hyph = Org_Heigl_Hyphenator::getInstance ( 'de' );
        $hyph -> setWordMin ( 5 )
              -> setLeftMin ( 2)
              -> setRightMin ( 2 )
              -> setQuality (9);
        $string = file_get_contents ( 'files/bible.txt' );
        $time = microtime ( true );
        $hyph -> hyphenate ( $string );
        $time = microtime ( true ) - $time;
        $words = explode ( ' ', $string );
        $words = count ( $words );
        $ratio = $time / $words;
        $this -> assertTrue ( ( $words / $time ) > 800 );
        $string = file_get_contents ( 'files/wortberge.txt' );
        $time = microtime ( true );
        $hyph -> hyphenate ( $string );
        $time = microtime ( true ) - $time;
        $words = explode ( ' ', $string );
        $words = count ( $words );
        $ratio = $words / $time;
        $this -> assertTrue ( ( $words / $time ) > 700 );

    }
    public function setup () {
        $file = dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'org_heigl_hyphenator_de_DE_cache';
        if ( file_exists ( $file ) ) {
            unlink ( $file );
        }
    }
}