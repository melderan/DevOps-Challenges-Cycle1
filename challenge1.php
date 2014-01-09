<?php
	// require libraries
    require 'vendor/autoload.php';
 
    // Namespaces
    use OpenCloud\Rackspace;
    use OpenCloud\Compute\Constants\Network;
    use OpenCloud\Compute\Constants\ServerState;

    $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, parse_ini_file('api-credentials.ini', true)['Rax-Personal']);

    $compute = $client->computeService('cloudServersOpenStack', 'IAD');
    
    $img = $compute->image('f70ed7c7-b42e-4d77-83d8-40fa29825b85');
    $flv = $compute->flavor('2');
    $name = 'ch1-php';

    $server = $compute->server();

    try {
    	$server->create(array(
    		'name'  => $name,
    		'image' => $img,
    		'flavor' => $flv,
    		'networks' => array (
    			$compute->network(Network::RAX_PUBLIC),
    			$compute->network(Network::RAX_PRIVATE)
    		)
    	));
    } catch (\Guzzle\Http\Exception\BadResponseException $e) {
    	$resp = (string) $e->getResponse()->getBody();
    	$status = $e->getResponse()->getStatusCode();
    	$headers = $e->getResponse()->getHeaderLines();

    	echo sprintf("Status: %s\nBody: %s\nHeaders: %s", $status, $resp, implode(', ', $headers));
    }

    echo "Building Server Now\n";

    $server->waitFor(ServerState::ACTIVE, 300);

    if($server->status() == "ACTIVE") {
    	echo sprintf(
    		"Server Name: %s\nRoot Password: %s\nPublic IP: %s\n",
    		$server->name(),
    		$server->adminPass,
    		$server->accessIPv4
    	);
    } else {
    	echo "The server failed to build correctly.\n";
    	$server->delete();
    	echo "I have deleted the failed server. Please try creating a new one.";
    }