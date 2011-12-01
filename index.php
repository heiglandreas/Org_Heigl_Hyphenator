<?php
/**
 * $Id$
 *
 *
 */

ini_set ( 'display_errors', true );
ini_set ( 'include_path', './src' . PATH_SEPARATOR . ini_get ( 'include_path'));
require_once 'Org/Heigl/Hyphenator/Hyphenator.php';
\Org\Heigl\Hyphenator\Hyphenator::registerAutoload();

if ( ! isset ( $_REQUEST ['language'] ) ) {
    $_REQUEST ['language'] = 'de_DE';
}
if ( ! isset ( $_REQUEST ['text'] ) ) {
    $_REQUEST ['text'] = '';
}
if ( ! isset ( $_REQUEST ['quality'] ) ) {
    $_REQUEST ['quality'] = '9';
}
if ( ! isset ( $_REQUEST ['leftMin'] ) ) {
    $_REQUEST ['leftMin'] = '2';
}
if ( ! isset ( $_REQUEST ['rightMin'] ) ) {
    $_REQUEST ['rightMin'] = '2';
}
if ( ! isset ( $_REQUEST ['wordMin'] ) ) {
    $_REQUEST ['wordMin'] = '5';
}
if ( ! isset ( $_REQUEST ['customHyphen'] ) ) {
    $_REQUEST ['customHyphen'] = '-';
}
if ( ! isset ( $_REQUEST ['noHyphenateMarker'] ) ) {
    $_REQUEST ['noHyphenateMarker'] = 'nbr:';
}
if ( ! isset ( $_REQUEST ['usePdflib'] ) ) {
    $_REQUEST ['usePdflib'] = false;
}
if ( ! isset ( $_REQUEST ['fontsize'] ) ) {
    $_REQUEST ['fontsize'] = 9;
}
if ( ! isset ( $_REQUEST ['adjustmethod'] ) ) {
    $_REQUEST ['adjustmethod'] = 'auto';
}
if ( ! isset ( $_REQUEST ['alignment'] ) ) {
    $_REQUEST['alignment'] = 'left';
}
if ( ! isset ( $_REQUEST ['maxspacing'] ) ) {
    $_REQUEST['maxspacing'] = '500';
}
if ( ! isset ( $_REQUEST ['minspacing'] ) ) {
    $_REQUEST['minspacing'] = '50';
}
if ( ! isset ( $_REQUEST ['nofitlimit'] ) ) {
    $_REQUEST['nofitlimit'] = '50';
}
if ( ! isset ( $_REQUEST ['shrinklimit'] ) ) {
    $_REQUEST['shrinklimit'] = '85';
}
if ( ! isset ( $_REQUEST ['spreadlimit'] ) ) {
    $_REQUEST['spreadlimit'] = '0';
}
if ( ! isset ( $_REQUEST ['charspacing'] ) ) {
    $_REQUEST['charspacing'] = '100';
}


?><?xml version="1.0" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Hyphenation-Tests</title>
        <link rel="stylesheet" href="style.css" type="text/css" />
        <meta http-equiv="Content-type" value="text/html; charset=UTF-8"/>
    </head>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <fieldset>
                <legend>Text to hyphenate</legend>
                <textarea name="text"><?php echo $_REQUEST['text']; ?></textarea>
            </fieldset>
            <fieldset>
                <legend>Hyphenator-Options</legend>
                <label for="language">Language</label>
                <select size="1" name="language">
                    <option <?php echo ('de_DE' === $_REQUEST['language'])?'selected="selected"':'';?>value="de_DE">German</option>
                    <option <?php echo ('en_GB' === $_REQUEST['language'])?'selected="selected"':'';?>value="en_GB">British English</option>
                    <option <?php echo ('en_US' === $_REQUEST['language'])?'selected="selected"':'';?>value="en_US">American English</option>
                </select>
                <label for="quality">Quality</label>
                <select size="1" name="quality">
                    <option <?php echo ('1' === $_REQUEST['quality'])?'selected="selected"':'';?>value="1">Best</option>
                    <option <?php echo ('3' === $_REQUEST['quality'])?'selected="selected"':'';?>value="3">Better</option>
                    <option <?php echo ('5' === $_REQUEST['quality'])?'selected="selected"':'';?>value="5">Normal</option>
                    <option <?php echo ('7' === $_REQUEST['quality'])?'selected="selected"':'';?>value="7">Poorer</option>
                    <option <?php echo ('9' === $_REQUEST['quality'])?'selected="selected"':'';?>value="9">Poorest</option>
                </select>
                <label for="leftMin">Minimum Characters on the left</label>
                <select size="1" name="leftMin">
                    <option <?php echo ('2' === $_REQUEST['leftMin'])?'selected="selected"':'';?>value="2">2</option>
                    <option <?php echo ('3' === $_REQUEST['leftMin'])?'selected="selected"':'';?>value="3">3</option>
                    <option <?php echo ('4' === $_REQUEST['leftMin'])?'selected="selected"':'';?>value="4">4</option>
                    <option <?php echo ('5' === $_REQUEST['leftMin'])?'selected="selected"':'';?>value="5">5</option>
                    <option <?php echo ('6' === $_REQUEST['leftMin'])?'selected="selected"':'';?>value="6">6</option>
                    <option <?php echo ('7' === $_REQUEST['leftMin'])?'selected="selected"':'';?>value="7">7</option>
                    <option <?php echo ('8' === $_REQUEST['leftMin'])?'selected="selected"':'';?>value="8">8</option>

                </select>
                <label for="rightMin">Minimum Characters on the right</label>
                <select size="1" name="rightMin">
                    <option <?php echo ('2' === $_REQUEST['rightMin'])?'selected="selected"':'';?>value="2">2</option>
                    <option <?php echo ('3' === $_REQUEST['rightMin'])?'selected="selected"':'';?>value="3">3</option>
                    <option <?php echo ('4' === $_REQUEST['rightMin'])?'selected="selected"':'';?>value="4">4</option>
                    <option <?php echo ('5' === $_REQUEST['rightMin'])?'selected="selected"':'';?>value="5">5</option>
                    <option <?php echo ('6' === $_REQUEST['rightMin'])?'selected="selected"':'';?>value="6">6</option>
                    <option <?php echo ('7' === $_REQUEST['rightMin'])?'selected="selected"':'';?>value="7">7</option>
                    <option <?php echo ('8' === $_REQUEST['rightMin'])?'selected="selected"':'';?>value="8">8</option>

                </select>
                <label for="wordMin">Minimum length of a word</label>
                <select size="1" name="wordMin">
                    <option <?php echo ('2' === $_REQUEST['wordMin'])?'selected="selected"':'';?>value="2">2</option>
                    <option <?php echo ('3' === $_REQUEST['wordMin'])?'selected="selected"':'';?>value="3">3</option>
                    <option <?php echo ('4' === $_REQUEST['wordMin'])?'selected="selected"':'';?>value="4">4</option>
                    <option <?php echo ('5' === $_REQUEST['wordMin'])?'selected="selected"':'';?>value="5">5</option>
                    <option <?php echo ('6' === $_REQUEST['wordMin'])?'selected="selected"':'';?>value="6">6</option>
                    <option <?php echo ('7' === $_REQUEST['wordMin'])?'selected="selected"':'';?>value="7">7</option>
                    <option <?php echo ('8' === $_REQUEST['wordMin'])?'selected="selected"':'';?>value="8">8</option>

                </select>
                <label for="customHyphen">Custom Hyphen-String</label>
                <input type="text" name="customHyphen" value="<?php echo $_REQUEST['customHyphen']; ?>" />
            </fieldset>
            <fieldset class="buttons">
                <input type="submit" name="submit" value="Submit" />
                <input type="reset" name="reset" value="Reset" />
            </fieldset>
        </form>
        <div class="result">
            <?php
                $hyphenator = \Org\Heigl\Hyphenator\Hyphenator::factory(null,$_REQUEST['language']);
                $hyphenator->getOptions()
                           ->setHyphen($_REQUEST['customHyphen'])
                           ->setLeftMin ( $_REQUEST ['leftMin'])
                           ->setRightMin ( $_REQUEST ['rightMin'])
                           ->setWordMin ( $_REQUEST ['wordMin'])
                           ->setQuality ( $_REQUEST ['quality']);
                $hyphenated = $hyphenator->hyphenate($_REQUEST['text']);
                echo '<span>' . $hyphenated . '</span>';
            ?>
        </div>
    </body>
</html>