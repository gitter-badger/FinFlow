<?php

$topic      = ( strlen($_GET['topic']) > 0 ) ? urldecode($_GET['topic']) :  "index";
$subject   = ( strlen($_GET['subject']) > 0 ) ? urldecode($_GET['subject']) :  "";

if ( file_exists("../docs/{$topic}.html") ):
	echo @file_get_contents("../docs/{$topic}.html#" . $subject);
else: ?>
    <h3 style="font-family: Tahoma, Arial, Helvetica, san-serif; font-size: 130%; text-align: center;">
        Subiectul <em><?php echo $topic; ?></em> nu a fost g&#259;sit.
    </h3>
<?php endif;