<?php

// /app/tests/ChatAppTest.php

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

require_once __DIR__ . '/../Controllers/GroupController.php';
require_once __DIR__ . '/../Controllers/MessageController.php';
require_once __DIR__ . '/../Controllers/UserController.php';
require_once __DIR__ . '/../Controllers/AuthController.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Middleware/JsonBodyParserMiddleware.php';


class ChatAppTest extends TestCase
{
    protected $pdo;
    protected $app;
    protected $userToken;
    protected $userId;
    protected $groupId;

    protected function setUp(): void
    {
        // Initialize the in-memory database
        $this->pdo = createDatabase('sqlite::memory:');

        // Set up the app
        $this->app = AppFactory::create();

        // Exception Handling Middleware
        // $this->app->addErrorMiddleware(true, true, true);

        // Instantiate controllers and middleware with $pdo
        $AuthController = new AuthController($this->pdo);
        $GroupController = new GroupController($this->pdo);
        $MessageController = new MessageController($this->pdo);
        $AuthMiddleware = new AuthMiddleware();

        // Routes
        $this->app->post('/register', [$AuthController, 'register']);
        $this->app->post('/login', [$AuthController, 'login']);

        // Protected Routes
        $this->app->group('', function ($group) use ($GroupController, $MessageController) {
            $group->post('/groups', [$GroupController, 'createGroup']);
            $group->post('/groups/{id}/join', [$GroupController, 'joinGroup']);
            $group->post('/groups/{id}/messages', [$MessageController, 'sendMessage']);
            $group->get('/groups/{id}/messages', [$GroupController, 'listMessages']);
        })->add($AuthMiddleware);

        $this->app->addBodyParsingMiddleware();
    }


    private function runApp($request)
    {
        return $this->app->handle($request);
    }

    /*** TESTS FOR USER REGISTRATION ***/

    public function testUserRegistrationSuccess()
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/register')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([
                'username' => 'testuser',
                'password' => 'testpassword'
            ]);
        $parsedReqBody = $request->getParsedBody();

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        if ($body) {
            if ($body['flag'])
                $this->assertEquals('success', $body['flag']);
            if ($body['message'])
                $this->assertEquals('registration successful.', $body['message']);
        }
    }

    public function testUserRegistrationWithExistingUsername()
    {
        // Register the user first
        $this->testUserRegistrationSuccess();

        // Attempt to register again with the same username
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/register')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([
                'username' => 'testuser',
                'password' => 'testpassword'
            ]);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());
        $body = json_decode((string)$response->getBody(), true);
        // $this->assertEquals('Username already exists', $body['error']);
        if ($body) {
            if ($body['flag'])
                $this->assertEquals('error', $body['flag']);
            if ($body['message'])
                $this->assertEquals('user already registered.', $body['message']);
        }
    }

    public function testUserRegistrationWithoutCredentials()
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/register')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([]);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());
        $body = json_decode((string)$response->getBody(), true);
        if ($body) {
            if ($body['flag'])
                $this->assertEquals('error', $body['flag']);
            if ($body['message'])
                $this->assertEquals('username and password are required.', $body['message']);
        }
    }

    /*** TESTS FOR USER LOGIN ***/

    public function testUserLoginSuccess()
    {
        // Register the user first
        $this->testUserRegistrationSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/login')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([
                'username' => 'testuser',
                'password' => 'testpassword'
            ]);

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Login successful', $body['message']);
        $this->assertArrayHasKey('token', $body);

        $this->userToken = $body['token'];

        // Retrieve user ID for future use
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE token = :token");
        $stmt->execute([':token' => $this->userToken]);
        $this->userId = $stmt->fetchColumn();
    }

    public function testUserLoginWithInvalidCredentials()
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/login')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([
                'username' => 'nonexistentuser',
                'password' => 'wrongpassword'
            ]);

        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());
        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Invalid username or password', $body['error']);
    }

    public function testUserLoginWithoutCredentials()
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/login')
            ->withHeader('Content-Type', 'application/json')
            ->withParsedBody([]);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());
        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Username and password are required', $body['error']);
    }

    /*** TESTS FOR GROUP CREATION ***/

    public function testCreateGroupSuccess()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups')
            ->withHeader('Authorization', $this->userToken)
            ->withParsedBody(['name' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Group created', $body['message']);
        $this->assertArrayHasKey('groupId', $body);

        $this->groupId = $body['groupId'];
    }

    public function testCreateGroupWithoutName()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups')
            ->withHeader('Authorization', $this->userToken)
            ->withParsedBody([]);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Group name is required', $body['error']);
    }

    public function testCreateGroupWithExistingName()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups')
            ->withHeader('Authorization', $this->userToken)
            ->withParsedBody(['name' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Group name already exists', $body['error']);
    }

    public function testCreateGroupWithoutAuth()
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups')
            ->withParsedBody(['name' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Unauthorized', $body['error']);
    }

    /*** TESTS FOR JOINING A GROUP ***/

    public function testJoinGroupSuccess()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups/' . $this->groupId . '/join')
            ->withHeader('Authorization', $this->userToken);

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Joined group', $body['message']);
    }

    public function testJoinNonExistentGroup()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups/9999/join')
            ->withHeader('Authorization', $this->userToken);

        $response = $this->runApp($request);

        $this->assertEquals(404, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Group not found', $body['error']);
    }

    public function testJoinGroupWithoutAuth()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups/' . $this->groupId . '/join');

        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Unauthorized', $body['error']);
    }

    /*** TESTS FOR SENDING MESSAGES ***/

    public function testSendMessageSuccess()
    {
        $this->testCreateGroupSuccess();

        // User is already in the group since they created it

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups/' . $this->groupId . '/messages')
            ->withHeader('Authorization', $this->userToken)
            ->withParsedBody(['content' => 'Hello, World!']);

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Message sent', $body['message']);
    }

    public function testSendMessageWithoutContent()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups/' . $this->groupId . '/messages')
            ->withHeader('Authorization', $this->userToken)
            ->withParsedBody([]);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Message content is required', $body['error']);
    }

    public function testSendMessageToNonExistentGroup()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups/9999/messages')
            ->withHeader('Authorization', $this->userToken)
            ->withParsedBody(['content' => 'Hello']);

        $response = $this->runApp($request);

        $this->assertEquals(404, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Group not found', $body['error']);
    }

    public function testSendMessageWithoutAuth()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups/' . $this->groupId . '/messages')
            ->withParsedBody(['content' => 'Hello']);

        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Unauthorized', $body['error']);
    }

    public function testSendMessageToGroupNotJoined()
    {
        $this->testUserLoginSuccess();

        // Create a new group without joining it
        $stmt = $this->pdo->prepare("INSERT INTO groups (name, created_by) VALUES (:name, :created_by)");
        $stmt->execute([':name' => 'Another Group', ':created_by' => $this->userId]);
        $groupId = $this->pdo->lastInsertId();

        // Remove user from group
        $stmt = $this->pdo->prepare("DELETE FROM groupMembers WHERE userId = :userId AND groupId = :groupId");
        $stmt->execute([':userId' => $this->userId, ':groupId' => $groupId]);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups/' . $groupId . '/messages')
            ->withHeader('Authorization', $this->userToken)
            ->withParsedBody(['content' => 'Hello']);

        $response = $this->runApp($request);

        $this->assertEquals(403, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('User not in group', $body['error']);
    }

    /*** TESTS FOR LISTING MESSAGES ***/

    public function testListMessagesSuccess()
    {
        $this->testSendMessageSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/groups/' . $this->groupId . '/messages')
            ->withHeader('Authorization', $this->userToken);

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);

        $this->assertIsArray($body);
        $this->assertCount(1, $body);
        $this->assertEquals('Hello, World!', $body[0]['content']);
        $this->assertEquals('testuser', $body[0]['user']);
    }

    public function testListMessagesFromNonExistentGroup()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/groups/9999/messages')
            ->withHeader('Authorization', $this->userToken);

        $response = $this->runApp($request);

        $this->assertEquals(404, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Group not found', $body['error']);
    }

    public function testListMessagesWithoutAuth()
    {
        $this->testSendMessageSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/groups/' . $this->groupId . '/messages');

        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('Unauthorized', $body['error']);
    }

    public function testListMessagesFromGroupNotJoined()
    {
        $this->testUserLoginSuccess();

        // Create a new group without joining it
        $stmt = $this->pdo->prepare("INSERT INTO groups (name, created_by) VALUES (:name, :created_by)");
        $stmt->execute([':name' => 'Not Joined Group', ':created_by' => $this->userId]);
        $groupId = $this->pdo->lastInsertId();

        // Ensure user is not in groupMembers
        $stmt = $this->pdo->prepare("DELETE FROM groupMembers WHERE userId = :userId AND groupId = :groupId");
        $stmt->execute([':userId' => $this->userId, ':groupId' => $groupId]);

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/groups/' . $groupId . '/messages')
            ->withHeader('Authorization', $this->userToken);

        $response = $this->runApp($request);

        $this->assertEquals(403, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('User not in group', $body['error']);
    }
}
