<?php

$client = $vars['client'];


?>


<div class="row">
    <div class="col-md-2">
	<p>
	    <label class="control-label" for="label[<?= $client ? $client->_id : 'new'; ?>]"><?= \Idno\Core\Idno::site()->language()->_('Client Label'); ?></label>
	</p>
    </div>
    <div class="col-md-4">
	<input type="text" class="form-control" name="label[<?= $client ? $client->_id : 'new'; ?>]" value="<?= htmlspecialchars($client->label)?>" >
    </div>
    <div class="col-md-6">
	<p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Label for OAuth 2 client'); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
	<p>
	    <label class="control-label" for="signin_button[<?= $client ? $client->_id : 'new'; ?>]"><?= \Idno\Core\Idno::site()->language()->_('"Sign in with" button'); ?></label>
	</p>
    </div>
    <div class="col-md-4">
	<input type="text" class="form-control" name="signin_button[<?= $client ? $client->_id : 'new'; ?>]">
    </div>
    <div class="col-md-6">
	<p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Graphic to use for the sign in with function'); ?></p>
    </div>
</div>


<div class="row">
    <div class="col-md-2">
	<p>
	    <label class="control-label" for="client_id[<?= $client ? $client->_id : 'new'; ?>]"><?= \Idno\Core\Idno::site()->language()->_('Client ID'); ?></label>
	</p>
    </div>
    <div class="col-md-4">
	<input type="text" class="form-control" name="client_id[<?= $client ? $client->_id : 'new'; ?>]" value="<?= htmlspecialchars($client->client_id)?>" >
    </div>
    <div class="col-md-6">
	<p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Public key from your OAuth server'); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
	<p>
	    <label class="control-label" for="client_secret[<?= $client ? $client->_id : 'new'; ?>]"><?= \Idno\Core\Idno::site()->language()->_('Secret key'); ?></label>
	</p>
    </div>
    <div class="col-md-4">
	<input type="text" class="form-control" name="client_secret[<?= $client ? $client->_id : 'new'; ?>]" value="<?= htmlspecialchars($client->client_secret)?>" >
    </div>
    <div class="col-md-6">
	<p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Secret key from your OAuth server'); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
	<p>
	    <label class="control-label" for="redirect_uri[<?= $client ? $client->_id : 'new'; ?>]"><?= \Idno\Core\Idno::site()->language()->_('Redirect URI'); ?></label>
	</p>
    </div>
    <div class="col-md-4">
	<input type="url" class="form-control" name="redirect_uri[<?= $client ? $client->_id : 'new'; ?>]" value="<?= htmlspecialchars($client->redirect_uri)?>" >
    </div>
    <div class="col-md-6">
	<p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Where should we send the visitor after the OAuth2 handshake?'); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
	<p>
	    <label class="control-label" for="url_authorise[<?= $client ? $client->_id : 'new'; ?>]"><?= \Idno\Core\Idno::site()->language()->_('Authorise URL'); ?></label>
	</p>
    </div>
    <div class="col-md-4">
	<input type="url" class="form-control" name="url_authorise[<?= $client ? $client->_id : 'new'; ?>]" value="<?= htmlspecialchars($client->url_authorise)?>" >
    </div>
    <div class="col-md-6">
	<p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Where should we send the auth request.'); ?></p>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
	<p>
	    <label class="control-label" for="url_access_token[<?= $client ? $client->_id : 'new'; ?>]"><?= \Idno\Core\Idno::site()->language()->_('Access Token URL'); ?></label>
	</p>
    </div>
    <div class="col-md-4">
	<input type="url" class="form-control" name="url_access_token[<?= $client ? $client->_id : 'new'; ?>]" value="<?= htmlspecialchars($client->url_access_token)?>" >
    </div>
    <div class="col-md-6">
	<p class="config-desc"><?= \Idno\Core\Idno::site()->language()->_('Access token URL.'); ?></p>
    </div>
</div>

