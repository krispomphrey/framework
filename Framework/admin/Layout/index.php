<!DOCTYPE html>
<html>
	<head>
		<title>Framework</title>
		<?php $this->flush_queue(); ?>
		<meta name="viewport" content="width=device-width; initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
	</head>
	<body>
	<?php $this->layout('header', $this->admin); ?>
	<?php $this->view($this->view, $this->admin); ?>
	<?php $this->layout('footer', $this->admin); ?>

	</body>
</html>