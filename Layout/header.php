	<header>
    <?php if($this->user->loggedin): ?>
        <div class="welcome-back pull-right col-md-2 clearfix">
          <span class="user-image pull-right">
            <img class="responsive admin-bar-img" src="/Framework/admin/assets/img/no-pic.png" />
          </span>
          <span class="user-name"><?php echo $this->user->session['fw']['name']; ?></span> <span><a href="/admin/logout">Logout</a>
        </div>
      </div>
    <?php endif; ?>
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
