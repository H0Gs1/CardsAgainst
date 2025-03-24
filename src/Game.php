<?php

namespace MyGame;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Game implements MessageComponentInterface {
    protected $clients;
    protected $clientIds;

    public function __construct() {
        $this->clients = new \SplObjectStorage; // Stores client connections
        $this->clientIds = []; // Keeps track of assigned client IDs
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        
        // Assign a unique ID to the client
        $clientId = uniqid('client_', true);
        $this->clientIds[$conn->resourceId] = $clientId;

        // Notify the new client of their assigned ID
        $conn->send(json_encode([
            'type' => 'welcome',
            'clientId' => $clientId,
            'message' => "Welcome! Your client ID is {$clientId}."
        ]));

        // Notify all other clients about the new client joining
        $this->broadcast([
            'type' => 'new_client',
            'clientId' => $clientId,
            'message' => "Client {$clientId} has joined the game."
        ], $conn);

        echo "New connection! ({$clientId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $clientId = $this->clientIds[$from->resourceId] ?? 'unknown';

        // Parse the message as JSON
        $data = json_decode($msg, true);

        // Check if a 'greeting' is present
        if (isset($data['greeting']) && $data['greeting'] === 'hello') {
            // Handle the greeting request
            $message = 'Hello there!';
        } 
        // Check if the method is 'create'
        elseif (isset($data['method']) && $data['method'] === 'create') {
            // Handle the 'create' request
            $json = [
                "method" =>"create",
                "Game" => [
                    "gameId" => "game" . $data['Id'],
                    "hostId" => $data['host'],
                    "pointsToWin" => $data['pointsToWin'],
                    "maxPlayers" => $data['maxPlayers'],
                    "players" => [
                        'clients' => array_values($this->clientIds)
                    ],
                    "status" => "waiting"
                ]
            ];
            $message = json_encode($json);
        } else {
            // Broadcast the message to all other clients
            foreach ($this->clients as $client) {
                if ($from !== $client) {
                    $client->send(json_encode([
                        'type' => 'message',
                        'from' => $clientId,   // Ensure $clientId is set before using it
                        'message' => $msg      // Ensure $msg is set before using it
                    ]));
                }
            }
            return; // Exit after broadcasting to avoid sending other responses
        }

        // Send the list of all connected clients if either greeting or create
        $from->send($message);

    }

    public function onClose(ConnectionInterface $conn) {
        $clientId = $this->clientIds[$conn->resourceId] ?? 'unknown';
        
        $this->clients->detach($conn);
        unset($this->clientIds[$conn->resourceId]);
        
        // Notify all clients about the disconnection
        $this->broadcast([
            'type' => 'client_left',
            'clientId' => $clientId,
            'message' => "Client {$clientId} has left the chat."
        ]);

        echo "Connection {$clientId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    private function broadcast(array $data, ConnectionInterface $exclude = null) {
        foreach ($this->clients as $client) {
            if ($client !== $exclude) {
                $client->send(json_encode($data));
            }
        }
    }
}
