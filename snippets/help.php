<?php

if ( strlen($_GET['topic']) > 0)
	$topic = urldecode($_GET['topic']);
else 
	$topic = "index";

if ( file_exists("../docs/{$topic}.html") ):
	echo @file_get_contents("../docs/{$topic}.html");
else: ?>
    <h3 style="font-family: Tahoma, Arial, Helvetica, san-serif; font-size: 130%; text-align: center;">
        Subiectul <em><?php echo $topic; ?></em> nu a fost g&#259;sit.
    </h3>
<?php endif;