<?php
// Routes

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("index '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/getrest', function ($request, $response, $args) {
    
   die('jjj');
    // Render index view
    return $this->renderer->render($response, 'get_all.phtml', $args);
});

$app->group('/api', function () use ($app) {
 
    // Version group
    $app->group('/v1', function () use ($app) {
		$app->get('/customers', 'getCustomers');
		$app->get('/customer/{id}', 'getCustomer');
		$app->post('/create', 'addCustomer');
		$app->put('/update/{id}', 'updateCustomer');
		$app->delete('/delete/{id}', 'deleteCustomer');
	});
});
