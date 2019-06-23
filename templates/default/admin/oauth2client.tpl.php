<div class="row">

    <div class="col-md-10 col-md-offset-1">
	            <?=$this->draw('admin/menu')?>
        <h1><?= \Idno\Core\Idno::site()->language()->_('OAuth2 Client'); ?></h1>

    </div>

</div>
<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <form action="<?=\Idno\Core\site()->config()->getDisplayURL()?>admin/oauth2client/" class="form-horizontal" method="post" enctype="multipart/form-data">
            <div class="controls-group">
                <div class="controls-config">
                    <p>
			<?= \Idno\Core\Idno::site()->language()->_(''); ?>
		    </p>
                    
                </div>
            </div>
            
            <div class="controls-group">
		
		<div class="row">
		    <div class="col-md-10">
			<h3><?= \Idno\Core\Idno::site()->language()->_('Configure client details'); ?></h3>
		    </div>
		</div>
		
		<div class="controls-group">
		<?php
		if ($clients = \IdnoPlugins\OAuth2Client\Entities\Client::get()) {
		
		    foreach (\Idno\Core\Idno::site()->config()->oauth2client as $client) {

			echo $this->__(['client' => $client])->draw('admin/oauth2client/form');

		    }
		    
		}
		
		?>
		</div>
		
		<hr>
		
		<div class="well">
		<?php
		
		echo $this->__(['client' => ''])->draw('admin/oauth2client/form');
		
		?>
		</div>
		
                    
            </div>
            
                        
            <div>

                <div class="controls-save">
                    <button type="submit" class="btn btn-primary"><?= \Idno\Core\Idno::site()->language()->_('Save settings'); ?></button>
                </div>
            </div>

            <?= \Idno\Core\site()->actions()->signForm('/admin/oauth2client/')?>
        </form>
    </div>
</div>