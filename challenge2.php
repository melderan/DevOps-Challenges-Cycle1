<?php
    // require libraries
    require 'vendor/autoload.php';
 
    // Namespaces
    use OpenCloud\Rackspace;
    use OpenCloud\Compute\Constants\Network;
    use OpenCloud\Compute\Constants\ServerState;

    $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, parse_ini_file('api-credentials.ini', true)['Rax-Personal']);

    