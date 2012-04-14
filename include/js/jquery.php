<?php
 // ob_start( 'ob_gzhandler' );
 // echo join('',file('jquery.js'));
 // ob_end_flush();

ob_start( 'ob_gzhandler' );
// optional: header( "Expires: Fri, 27 Aug 2010 12:12:12 GMT" );
foreach( array(
                'jquery.js',
                'interface.js',
                'jquery.form.js',
                'lib.js',
                'highslide/highslide.js'
                )
        as $fn ) {
        echo file_get_contents($fn);
        echo ";\n"; // accommodate scripts which are missing a trailing semicolon
}

ob_end_flush();
?> 
