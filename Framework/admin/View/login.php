<div class="login-wrapper">
	<div class="container">
		<div class="col-md-3 login-box">
		<h1 class="login-title">FRAMEWORK</h1>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						Login
					</h4>
				</div>
				<div class="panel-body">
					<?php if(!empty($this->messages)): ?>
					<div class="alerts">
						<?php foreach($this->messages as $message): ?>
						<div class="alert alert-<?php echo $message['type']; ?>">
							<?php echo $message['notice']; ?>
						</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>
					<form name="login-form" method="POST">
            <input type="text" name="fw[username]" placeholder="Enter Username" value="<?php echo $this->router->request['fw']['username']; ?>" />
            <input type="password" name="fw[password]" placeholder="Enter Password" value="<?php echo $this->router->request['fw']['password']; ?>" />
            <button class="btn btn-primary">Login</button>
          </form>
				</div>
			</div>
		</div>
	</div>
</div>
