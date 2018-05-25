<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Swoole\Websocket\Frame;
use Swoole\Websocket\Server;
use Swoole\Http\Request;

/** create a WebSocket server */
class WebsocketCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('websocket:start')
            ->setDescription('Start WebSocket server.')
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'Host for server', '0.0.0.0')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port for server', 9501);
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $server = new Server($input->getOption('host'), $input->getOption('port'));

        $server->on('handshake', function (\swoole_http_request $request, \swoole_http_response $response) {

            echo $request->header['sec-websocket-key'];

            $key = base64_encode(sha1($request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));

            $headers = [
                'Upgrade' => 'websocket',
                'Connection' => 'Upgrade',
                'Sec-WebSocket-Accept' => $key,
                'Sec-WebSocket-Version' => '13',
            ];

            // failed: Error during WebSocket handshake:
            // Response must not include 'Sec-WebSocket-Protocol' header if not present in request: websocket
            if (isset($request->header['sec-websocket-protocol'])) {
                $headers['Sec-WebSocket-Protocol'] = $request->header['sec-websocket-protocol'];
            }

            foreach ($headers as $key => $val) {
                $response->header($key, $val);
            }

            $response->status(101);
            $response->end();
            return true;
        });

        $server->on('open', function (Server $server, Request $request) {
            echo "server: handshake success with fd{$request->fd}\n";
        });

        $server->on('connect', function (Server $server, int $a, int $b) {
            echo "server: connect\n";
        });

        $server->on('message', function (Server $server, Frame $frame) {
            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";

            $keyWord = "UPDATE";
            $checkCompatibility = strpos($frame->data, $keyWord);
            if ($checkCompatibility === false) {
                return false;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://127.0.0.1/api/pipelines");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $output = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if (200 === $httpCode) {
                foreach ($server->getClientList() as $client) {
                    if ($client !== $frame->fd)
                        $server->push($client, $output);
                }
            }
            $server->push($frame->fd, 'message received');
        });

        $server->on('close', function ($ser, $fd) {
            echo "client {$fd} closed\n";
        });

        $server->start();
    }
}






























//        $server = new Server($input->getOption('host'), $input->getOption('port'));
//
//        $server->on('open', function (Server $server, Request $request) {
//            echo "server: handshake success with fd{$request->fd}\n";
//        });
//
//        $server->on('message', function (Server $server, Frame $frame) {
//            echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
//            $server->push($frame->fd, json_encode("data refreshed"));
//
//        });
//
//        $server->on('close', function ($ser, $fd) {
//            echo "client {$fd} closed\n";
//        });
//
//        $server->start();
