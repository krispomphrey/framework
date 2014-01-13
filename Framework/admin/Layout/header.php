	<header class="admin-header clearfix">
		<div class="col-md-2">
			<span class="framework-logo">FW</span>
		</div>
		<div class="col-md-8">
		</div>
		<div class="col-md-2">
			<div class="welcome-back clearfix">
				<span class="user-image pull-right">
					<?php if($this->user->image): ?>
						<img class="responsive admin-bar-img" src="/Uploads/<?php echo $this->user->image; ?>" />
					<?php else: ?>
						<img class="responsive admin-bar-img" src="/Framework/admin/assets/img/no-pic.png" />
					<?php endif; ?>
				</span>
				<span class="user-name"><?php echo $this->user->name; ?></span>
			</div>
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
	<section class="admin-dashboard">
	<div class="col-md-2">
		<div class="list-group">
		  <a href="/admin" class="list-group-item">Dashboard</a>
		  <a href="/admin/content" class="list-group-item">Content</a>
		  <a href="/admin/pages" class="list-group-item">Pages</a>
		  <a href="/admin/users" class="list-group-item">Users</a>
		  <a href="/admin/settings" class="list-group-item">Settings</a>
		</div>
	</div>
	<div class="col-md-10">