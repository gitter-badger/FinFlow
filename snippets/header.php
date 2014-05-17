<?php if ( !defined('FNPATH') ) exit(); $active=array(); if ( isset($_GET['p']) ): $active[urldecode($_GET['p'])] = 'active'; else: $active['index']='active'; endif; ?>
<div class="row header">
	<div class="span12">
		<div class="navbar">
			<div class="navbar-inner">
				<a class="brand <?php echo $active['index']; ?>" href="index.php">&nbsp;</a>
				<ul class="nav">
					<li class="<?php echo $active['transactions']; ?>"><a href="<?php fn_UI::page_url('transactions'); ?>">Tranzac&#355;ii</a></li>
					<li class="<?php echo $active['performance']; ?>"><a href="<?php fn_UI::page_url('performance'); ?>">Performan&#355;&#259;</a></li>
					<li class="<?php echo $active['labels']; ?>"><a href="<?php fn_UI::page_url('labels'); ?>">Etichete</a></li>
                    <li class="<?php echo $active['accounts']; ?>"><a href="<?php fn_UI::page_url('accounts'); ?>">Conturi</a></li>
                    <li class="<?php echo $active['contacts']; ?>"><a href="<?php fn_UI::page_url('contacts'); ?>">Contacte</a></li>
					<li class="<?php echo $active['currencies']; ?>"><a href="<?php fn_UI::page_url('currencies'); ?>">Monede</a></li>
                    <li class="<?php echo $active['tools']; ?>"><a href="<?php fn_UI::page_url('tools'); ?>">Unelte</a></li>
					<li class="<?php echo $active['settings']; ?>"><a href="<?php fn_UI::page_url('settings'); ?>">Set&#259;ri</a></li>
				</ul>
				
				<a class="btn pull-right logout" href="<?php fn_UI::page_url('logout'); ?>" title="de-autentificare"><span class="icon-off"></span></a>
				<a class="btn pull-right default" onclick="fn_popup('<?php echo FN_URL; ?>/snippets/help.php?topic=<?php echo $_GET['p']; ?>');" href="#help" title="ajutor"><span class="icon-question-sign"></span></a>
                <a class="btn pull-right default" href="<?php echo fn_UI::get_file_url('/setup/upgrade.php', false); ?>" title="verifica actualizari"><span class="icon-cloud-download"></span></a>
			</div>
		</div>
	</div>
</div>
