<?php		
	$userId = 1;	// Hardcode user id for now
	
	require_once 'vendor/autoload.php';
	require_once 'config.php';
	require_once 'qm-config.php';
	
	require_once 'PHPConnect/PHPConvert.php';

	$app = new \Slim\Slim(array(
		'templates.path' => './Templates'
	));
		
	$phpConvert = new phpConnectClient(new QuantimodoMessaging());

	$app->get('/list', function () use ($app, $userId, $phpConnectClient)
	{
		$message = new FrameworkRequestMessage($userId, 'listConnectors', $app->request->params());
		
		$responseMessage = $phpConnectClient->messaging->sendMessage($message, true, false);

		$clientResponse = new StdClass();
		$responseMessageType = get_class($responseMessage);
		switch($responseMessageType)
		{
			case 'ErrorResponseMessage':
				$clientResponse->error = $responseMessage;
				break;
			case 'RedirectResponseMessage':
				$clientResponse->redirect = $responseMessage;
				break;
			case 'JsonResponseMessage':
				$clientResponse->json = json_decode($responseMessage->json);
				break;
			case 'ResponseMessage':
				$clientResponse->success = $responseMessage;
				break;
			default:
				echo $response;
		}
		echo json_encode($clientResponse);

		$app->response->headers->set('Content-Type', 'application/json');
	});

	$app->map('/:connector/:method', function ($connector, $method) use ($app, $userId, $phpConnectClient)
	{
		$message = new ConnectorRequestMessage($userId, $connector, $method, $app->request->params());

		$responseMessage = $phpConnectClient->messaging->sendMessage($message, true);

		$responseMessageType = get_class($responseMessage);
		switch($responseMessageType)
		{
			case 'ErrorResponseMessage':
				$app->response->headers->set('Content-Type', 'application/json');
				$clientResponse = new StdClass();
				$clientResponse->error = $responseMessage;
				echo json_encode($clientResponse);
				break;
			case 'RedirectResponseMessage':
				$app->redirect($responseMessage->location);
				break;
			case 'JsonResponseMessage':
				$app->response->headers->set('Content-Type', 'application/json');
				echo $responseMessage->json;
				break;
			case 'ResponseMessage':
				echo "Success!";
				break;
			default:
				echo $response;
		}
	})->via('GET', 'POST', 'DELETE');

	$app->run();
	
	$phpConnectClient->stop();
?>
