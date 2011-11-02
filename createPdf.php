<?php

if ( ! isset ( $_REQUEST ['text'] ) ) {
    $_REQUEST['text'] = '';
}
if ( ! isset ( $_REQUEST ['fontsize'] ) ) {
    $_REQUEST['fontsize'] = 9;
}
if ( ! isset ( $_REQUEST ['adjustmethod'] ) ) {
    $_REQUEST['adjustmethod'] = 'auto';
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
if ( ! isset ( $_REQUEST ['nowrap'] ) ) {
    $_REQUEST['nowrap'] = false;
}

$_REQUEST['maxspacing']  = (int) $_REQUEST['maxspacing'] . '%';
$_REQUEST['minspacing']  = (int) $_REQUEST['minspacing'] . '%';
$_REQUEST['nofitlimit']  = (int) $_REQUEST['nofitlimit'] . '%';
$_REQUEST['shrinklimit'] = (int) $_REQUEST['shrinklimit'] . '%';
$_REQUEST['charspacing'] = (int) $_REQUEST['charspacing'] . '%';
$_REQUEST['text']        = str_replace ( '-', chr ( 173 ), $_REQUEST['text'] );


try {
    $p = new PDFlib ();
    /* open new PDF file; insert a file name to create the PDF on disk */
    if ( $p -> begin_document ( '', '' ) == 0 ) {
        die("Error: " . $p -> get_errmsg () );
    }
    $p -> begin_page_ext ( 1100, 600, '' );

    $tfcreate_options = array ( 'fontname=Helvetica', 'fontsize=' . $_REQUEST ['fontsize'], 'encoding=winansi' );
    $tfcreate_options [] = 'alignment=' . $_REQUEST['alignment'];
    $tfcreate_options [] = 'adjustmethod=' . $_REQUEST['adjustmethod'];
    $tfcreate_options [] = 'maxspacing=' . $_REQUEST['maxspacing'];
    $tfcreate_options [] = 'minspacing=' . $_REQUEST['minspacing'];
    $tfcreate_options [] = 'nofitlimit=' . $_REQUEST['nofitlimit'];
    $tfcreate_options [] = 'shrinklimit=' . $_REQUEST['shrinklimit'];
    $tfcreate_options [] = 'spreadlimit=' . $_REQUEST['spreadlimit'];
    $tfcreate_options [] = 'charspacing=' . $_REQUEST['charspacing'];

    $tffit_options = array ( 'showborder=true' );

    $tf1 = $p -> create_textflow ( $_REQUEST ['text'], implode ( ' ', $tfcreate_options ) );
    $tf2 = $p -> create_textflow ( $_REQUEST ['text'], implode ( ' ', $tfcreate_options ) );
    $tf3 = $p -> create_textflow ( $_REQUEST ['text'], implode ( ' ', $tfcreate_options ) );
    $tf4 = $p -> create_textflow ( $_REQUEST ['text'], implode ( ' ', $tfcreate_options ) );
    $p -> fit_textflow ( $tf1, 20, 20, 400, 580,implode ( ' ', $tffit_options ) );
    $p -> fit_textflow ( $tf2, 420, 20, 720, 580,implode ( ' ', $tffit_options ) );
    $p -> fit_textflow ( $tf3, 740, 20, 960, 580,implode ( ' ', $tffit_options ) );
    $p -> fit_textflow ( $tf4, 980, 20, 1080, 580,implode ( ' ', $tffit_options ) );
    $p -> delete_textflow ( $tf1 );
    $p -> delete_textflow ( $tf2 );
    $p -> delete_textflow ( $tf3 );
    $p -> delete_textflow ( $tf4 );

    $tf = $p -> create_textflow ( implode ( ' ', $tfcreate_options ), 'fontname=Helvetica fontsize=9 encoding=winansi' );
    $p -> fit_textflow ( $tf, 5, 5, 1095, 18,implode ( ' ', $tffit_options ) );
    $p -> delete_textflow ( $tf );
    $p -> end_page_ext ( '' );
    $p -> end_document ( '' );
    $buf = $p -> get_buffer ();
    $len = strlen ( $buf );
    header("Content-type: application/pdf");
    header("Content-Length: $len");
    header("Content-Disposition: inline; filename=hello.pdf");
    print $buf;
} catch ( PDFlibException $e ) {
    die("<h1>PDFlib exception occurred</h1>" .
    "<p><strong>" . $e->get_errnum() . "</strong> " . $e->get_apiname() . ": " .
    $e->get_errmsg() . "</p><p>" . $e -> getFile () . ': ' . $e -> getLine () . '</p>');
}
?>