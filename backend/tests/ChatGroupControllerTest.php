<?php

// tests/ChatGroupControllerTest.php

use PHPUnit\Framework\TestCase;

class ChatGroupControllerTest extends TestCase {
    private $client;

    protected function setUp(): void {
        $this->client = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:8000']); // Adjust the base URI if necessary
    }

    public function testCreateGroup() {
        $response = $this->client->post('/groups', [
            'json' => ['group_name' => 'Test Group']
        ]);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testGetAllGroups() {
        $response = $this->client->get('/groups');
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testSendMessage() {
        $response = $this->client->post('/messages', [
            'json' => ['group_id' => 1, 'username' => 'User1', 'message' => 'Hello World']
        ]);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testGetMessages() {
        $response = $this->client->get('/messages/1');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
