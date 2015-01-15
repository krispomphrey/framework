<!DOCTYPE html>
<html>
	<head>
		<title>Framework</title>
		<link rel="stylesheet" type="text/css" href="/Framework/admin/assets/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="/Framework/admin/assets/css/error.css" />
		<meta name="viewport" content="width=device-width; initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no" />
	</head>
	<body>
		<div class="error-wrapper">
    	<div class="container">
    		<div class="col-md-7 error-box">
    		<h1 class="error-title">FRAMEWORK</h1>
    			<div class="panel panel-danger">
    				<div class="panel-heading">
    					<h4 class="panel-title">
    						Whoa, we have a problem...
    					</h4>
    				</div>
    				<div class="panel-body">
    					<h4>Database errors/info:</h4>
              <?php if(is_array($this->client->error) || is_object($this->client->error)): ?>
                <?php foreach($this->client->error as $error): ?>
                  <?php echo $error; ?>
                <?php endforeach; ?>
              <?php else: ?>
                <?php echo $this->client->error; ?>
              <?php endif; ?>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
	</body>
</html>
