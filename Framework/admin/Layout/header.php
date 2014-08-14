	<header class="admin-header clearfix">
		<div class="col-md-2">
			<span class="framework-logo">FW</span>
		</div>
		<div class="col-md-10">
      <div class="welcome-back pull-right col-md-2 clearfix">
        <span class="user-image pull-right">
          <img class="responsive admin-bar-img" src="/Framework/admin/assets/img/no-pic.png" />
        </span>
        <span class="user-name"><?php echo $this->user->session['fw']['name']; ?></span> <span><a href="/admin/logout">Logout</a>
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
