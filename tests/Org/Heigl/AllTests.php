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
 * @copyright 2008-2009 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   SVN: $Revision$
 * @since     20.04.2009
 */

/** PHPUnit_Framework_TestSuite */
require_once 'PHPUnit/Framework/TestSuite.php';

/** PHPUnit_TextUI_TestRunner */
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * This class provides all tests of the Org_Heigl Package
 *
 * @category  Org_Heigl
 * @package   Org_Heigl_Hyphenator
 * @author    Andreas Heigl <andreas@heigl.org>
 * @copyright 2008-2009 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   SVN: $Revision$
 * @since     20.04.2009
 */
class Org_Heigl_AllTests
{
    public static function main () {
        PHPUnit_TextUI_TestRunner::run ( self::suite () );
    }

    public static function suite () {
        $suite = new PHPUnit_Framework_TestSuite ( 'Org_Heigl'  );

        include_once 'Org/Heigl/HyphenatorTest.php';

        $suite -> addTestSuite ( 'Org_Heigl_HyphenatorTest' );

        return $suite;

    }
}