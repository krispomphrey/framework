<!DOCTYPE html>
<html>
	<head>
		<title>Framework</title>
		<?php $this->flush_queue(); ?>
		<meta name="viewport" content="width=device-width; initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
	</head>
	<body>
	<?php $this->layout('header', $admin); ?>
	<?php $this->view($view, $admin); ?>
	<?php $this->layout('footer', $admin); ?>

	</body>
</html>
