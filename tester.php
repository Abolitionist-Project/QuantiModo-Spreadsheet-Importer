<?php
	require_once 'vendor/autoload.php';
	
	require_once('PHPConvert/PHPConvert.php');
	require_once('Model/TesterStorage.php');

	$app = new \Slim\Slim();

	$app->post('/upload', function () use ($app)
	{
		$app->response->headers->set('Content-Type', 'text/plain ');

		$phpConvert = new phpConvert(0, new TesterStorage());

		$phpConvert->handleFile($_FILES['file']);
	});

	$app->get('/upload', function () use ($app)
	{
		echo '
			<html>
			<body>
				<form action="upload" method="post" enctype="multipart/form-data">
					<label for="file">File:</label>
					<input type="file" name="file" id="file"><br>
					<input type="submit" name="submit" value="Submit">
				</form>
			</body>
			</html>
		';
	});

	$app->run();
?>
