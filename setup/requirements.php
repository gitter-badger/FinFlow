<?php

include_once 'header.php';
include_once ( FNPATH . '/inc/class.installer.php' );

?>

        <p align="center" class="logo">
            <img src="<?php echo FN_URL; ?>/images/finflow-logo.png" width="50" align="absmiddle" alt="FinFlow"/>
            <br/>FinFlow
        </p>

		<h3 align="center">Verificare cerin&#355;e de sistem</h3>
				
		<?php
				
			$environement_ready = FALSE;
				
			$required =  array(
                'mbstring'=>'missing',
                'sockets'=>'missing',
                'mysqli'=>'missing',
                'json'=>'missing',
                'calendar'=>'missing',
                'date'=>'missing',
                'session'=>'missing',
                'SimpleXML'=>'missing',
                'zip'=>'missing',
                'curl'=>'missing'
            );

		    $k = 0;
					
		    foreach ($required as $ext=>$status) if ( extension_loaded($ext) ){
				$required[$ext] = 'loaded'; $k++;
			}

			if ( $k == count($required) ) $environement_ready = TRUE;
					
		 ?>
				
		<?php foreach ($required as $ext=>$status): $icon = $status == 'loaded' ? 'ok-sign' : 'remove-circle'; ?>
			<div class="well well-small requirement <?php echo $status; ?>">
				<span class="icon-<?php echo $icon; ?>"></span> Extensia <?php echo ucwords($ext); ?>  <?php echo $status == 'loaded' ? 'este instalat&#259;' : 'lipse&#351;te'; ?>.
			</div>
		<?php endforeach; ?>
				
		<?php if($environement_ready):?>
		    <p class="msg note">Toate extensiile necesare aplica&#355;iei sunt disponibile.</p>

            <p style="text-align: center;">
                <button class="btn" onclick="window.location.href='terms.php';"> <span class="icon-arrow-left"></span> &#206;napoi</button>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php if( fn_Installer::have_cfg_file() ): ?>
                    <button class="btn btn-primary btn-success" onclick="window.location.href='setup-user.php';">
                        Continu&#259; <span class="icon-arrow-right"></span>
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary btn-success" onclick="window.location.href='setup-cfg.php';">
                        Continu&#259; <span class="icon-arrow-right"></span>
                    </button>
                <?php endif; ?>
        </p>
		<?php else: ?>
			<p class="msg warning">
				Unele extensii necesare aplica&#355;iei nu sunt disponibile.
				Mergi la <a href="http://php.net/manual/en/extensions.alphabetical.php" target="_blank">ecranul cu documenta&#355;ie </a>
				pentru extensiile PHP &#351;i instaleaz&#259;-le sau incarc&#259;-le &#238;n php.ini (dac&#259; sunt deja instalate).
			</p>
		<?php endif; ?>

<?php include_once 'footer.php'; ?>