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
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage Options
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    0.1
 * @since      07.09.2011
 */

require_once 'Org/Heigl/Hyphenator/File/Parser.php';

/**
 * This class provides A parser for TeX-Hyphenation Files.
 *
 * These files will be parsed and their relevant content is returned as array
 *
 * @category   Hyphenation
 * @package    Org_Heigl_Hyphenator
 * @subpackage File_Parser
 * @author     Andreas Heigl <andreas@heigl.org>
 * @copyright  2008-2011 Andreas Heigl<andreas@heigl.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version    0.1
 * @since      09.09.2011
 */
class Org_Heigl_Hyphenator_File_Parser_Tex extends Org_Heigl_Hyphenator_File_Parser
{
    const TEX_GENERIC    = 0;
    const TEX_INPUT      = 1;
    const TEX_BEGINGROUP = 2;
    const TEX_COMMENT    = 3;
    const TEX_ENDGROUP   = 4;
    const TEX_ENDINPUT   = 5;
    const TEX_LCCODE     = 6;
    const TEX_STRING     = 7;
    const TEX_PATTERNS   = 8;

    /**
     * Storage for the found tokens
     *
     * @var array $_tokens
     */
    protected $_tokens = array ();

    protected $_reserved = array (
        'input',
        'begingroup',
        'def',
        'endgroup',
        'endinput',
        'lccode',
        'uccode',
        'patterns',
    );

    protected $_comments = array (
        '%',
    );

    /**
     * Tokenize the given TeX-File
     *
     * @param string $file
     *
     * @return Org_Heigl_Hyphenator_File_Parser_Tex
     */
    public function tokenize($file)
    {
        $f = file_Get_contents($file);


        print_r(token_get_all($f));
    }


}