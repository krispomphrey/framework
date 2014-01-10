	<header class="admin-header clearfix">
		<div class="col-md-2">
			<span class="framework-logo">FW</span>
		</div>
		<div class="col-md-8">
		</div>
		<div class="col-md-2 welcome-back">
			<p>Welcome back <?php echo $this->user->name; ?></p>
			<p><a href="/logout">Logout</a></p>
		</div>
		<?php if(!empty($this->messages)): ?>
		<div class="alerts">
			<?php foreach($this->messages as $message): ?>
			<div class="alert alert-<?php echo $message['type']; ?>">
				<?php echo $message['notice']; ?>
			</div>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</header>