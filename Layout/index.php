<!DOCTYPE html>
<html>
	<head>
		<title>Framework</title>
		<?php $this->flush_assets(); ?>
		<meta name="viewport" content="width=device-width; initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
	</head>
	<body>
  	<?php $this->layout('header'); ?>
  	<?php $this->view($this->view); ?>
  	<?php $this->layout('footer'); ?>
	</body>
</html>
