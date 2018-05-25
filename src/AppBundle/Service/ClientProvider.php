<?php

namespace AppBundle\Service;

use WebSocket\Client;

class ClientProvider
{
    /**
     * @throws \Exception
     * @param $data
     */
    public function createClient($data)
    {

        $client = new Client("ws://localhost:9501");
        $client->send($data);
        echo $client->receive();
    }
}