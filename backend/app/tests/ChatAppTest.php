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
require_once __DIR__ . '/../db.php';  // Include your database setup
require_once __DIR__ . '/../Models/Group.php';


class ChatAppTest extends TestCase
{
    protected $pdo;
    protected $app;
    protected $userToken;
    protected $userId;
    protected $groupId;
    protected $GroupModel;
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
        $UserController = new UserController($this->pdo);
        $GroupController = new GroupController($this->pdo);
        $MessageController = new MessageController($this->pdo);
        $AuthMiddleware = new AuthMiddleware();

        $this->GroupModel = new Group($this->pdo);

        // Routes
        $this->app->post('/register', [$AuthController, 'register']);
        $this->app->post('/login', [$AuthController, 'login']);

        // Protected Routes
        $this->app->group('', function ($group) use ($UserController, $GroupController, $MessageController) {
            $group->post('/groups', [$GroupController, 'createGroup']);
            $group->post('/join', [$UserController, 'joinGroup']);
            $group->post('/messages', [$MessageController, 'sendMessage']);
            $group->get('/messages/{groupName}', [$MessageController, 'getMessages']);
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
        $this->assertEquals('success', $body['flag']);
        $this->assertEquals('registration successful.', $body['message']);
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
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('user already registered.', $body['message']);
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
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('username and password are required.', $body['message']);
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
        if ($body) {
            if ($body['flag'])
                $this->assertEquals('success', $body['flag']);
            if ($body['message'])
                $this->assertEquals('Login successful.', $body['message']);
        }
        $this->assertArrayHasKey('token', $body);

        $this->userToken = $body['token'];

        // Optionally, decode the JWT to get the user ID
        $payload = $this->decodeJwt($this->userToken);
        $this->userId = $payload->data->userId;
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
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Invalid username or password.', $body['message']);
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
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('both username and password are required.', $body['message']);
    }

    /*** TESTS FOR GROUP CREATION ***/

    public function testCreateGroupSuccess()
    {
        $this->testUserLoginSuccess();
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody(['groupName' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());


        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('success', $body['flag']);
        $this->assertEquals('group created + user joined the group successfully.', $body['message']);
    }

    public function testCreateGroupWithoutName()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody([]);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Group name is required.', $body['message']);
    }

    public function testCreateGroupWithExistingName()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody(['groupName' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('group already exists.', $body['message']);
    }

    public function testCreateGroupWithoutAuth()
    {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/groups')
            ->withParsedBody(['name' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Unauthorized. Login required.', $body['message']);
    }

    /*** TESTS FOR JOINING A GROUP ***/

    public function testJoinGroupSuccess()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/join')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody(['groupName' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('success', $body['flag']);
        $this->assertEquals('User joined the group successfully.', $body['message']);
    }

    public function testJoinNonExistentGroup()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/join')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody(['groupName' => '999']);

        $response = $this->runApp($request);

        $this->assertEquals(404, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Group not found.', $body['message']);
    }

    public function testJoinGroupWithoutAuth()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/join')
            ->withParsedBody(['groupName' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Unauthorized. Login required.', $body['message']);
    }

    /*** TESTS FOR SENDING MESSAGES ***/

    public function testSendMessageSuccess()
    {
        $this->testCreateGroupSuccess();

        // User is already in the group since they created it

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/messages')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody(['message' => 'Hello, World!', 'groupName' => 'Test Group']);

        $response = $this->runApp($request);

        $this->assertEquals(201, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('success', $body['flag']);
        $this->assertEquals('Message sent successfully.', $body['message']);
    }

    public function testSendMessageWithoutContent()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/messages')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody([]);

        $response = $this->runApp($request);

        $this->assertEquals(400, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('GroupName, username and message are required.', $body['message']);
    }

    public function testSendMessageToNonExistentGroup()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/messages')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody(['message' => 'Hello, World!', 'groupName' => '999 Group']);


        $response = $this->runApp($request);

        $this->assertEquals(404, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Group not found.', $body['message']);
    }

    public function testSendMessageWithoutAuth()
    {
        $this->testCreateGroupSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/messages')
            ->withParsedBody(['message' => 'Hello, World!', 'groupName' => '999 Group']);


        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Unauthorized. Login required.', $body['message']);
    }

    public function testSendMessageToGroupNotJoined()
    {
        $this->testUserLoginSuccess();

        // Create a new group without joining it
        $stmt = $this->pdo->prepare("INSERT INTO groups (name, createdBy) VALUES (:name, :createdBy)");
        $stmt->execute([':name' => 'Another Group', ':createdBy' => $this->userId]);
        $groupId = $this->pdo->lastInsertId();

        // Remove user from groupMembers if added automatically
        $stmt = $this->pdo->prepare("DELETE FROM groupMembers WHERE userId = :userId AND groupId = :groupId");
        $stmt->execute([':userId' => $this->userId, ':groupId' => $groupId]);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/messages')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken)
            ->withParsedBody(['message' => 'Hello', 'groupName' => 'Another Group']);

        $response = $this->runApp($request);

        $this->assertEquals(403, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('User is not a member of the group.', $body['message']);
    }

    /*** TESTS FOR LISTING MESSAGES ***/

    public function testListMessagesSuccess()
    {
        $this->testSendMessageSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET',   '/messages' . '/' . 'Test Group')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken);

        $response = $this->runApp($request);

        $this->assertEquals(200, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('success', $body['flag']);
        $this->assertIsArray($body['message']);
        $this->assertEquals('Hello, World!', $body['message'][0]['content']);
    }

    public function testListMessagesFromNonExistentGroup()
    {
        $this->testUserLoginSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/messages/9999')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken);

        $response = $this->runApp($request);

        $this->assertEquals(404, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Group not found.', $body['message']);
    }

    public function testListMessagesWithoutAuth()
    {
        $this->testSendMessageSuccess();

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/messages' . '/Test Group');

        $response = $this->runApp($request);

        $this->assertEquals(401, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('Unauthorized. Login required.', $body['message']);
    }

    public function testListMessagesFromGroupNotJoined()
    {
        $this->testUserLoginSuccess();

        // Create a new group without joining it
        $stmt = $this->pdo->prepare("INSERT INTO groups (name, createdBy) VALUES (:name, :createdBy)");
        $stmt->execute([':name' => 'Not Joined Group', ':createdBy' => $this->userId]);
        $groupId = $this->pdo->lastInsertId();

        // Ensure user is not in groupMembers
        $stmt = $this->pdo->prepare("DELETE FROM groupMembers WHERE userId = :userId AND groupId = :groupId");
        $stmt->execute([':userId' => $this->userId, ':groupId' => $groupId]);

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/messages'.'/Not Joined Group')
            ->withHeader('Authorization', 'Bearer ' . $this->userToken);

        $response = $this->runApp($request);

        $this->assertEquals(403, $response->getStatusCode());

        $body = json_decode((string)$response->getBody(), true);
        $this->assertEquals('error', $body['flag']);
        $this->assertEquals('User is not a member of the group.', $body['message']);
    }

    /*** HELPER METHODS ***/

    // Decode JWT Token
    private function decodeJwt($token)
    {
        $secretKey = 'awesomeANDsecretKEY';  // Replace with your actual secret key
        $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($secretKey, 'HS256'));
        return $decoded;
    }

    private function getGroupId($groupName)
    {
        try {
            $group = $this->GroupModel->getGroupByName($groupName);
            return $group['id'];
        } catch (\Exception $e) {
            error_log("Could not find group: " . $e->getMessage());
            return throw new Exception("Group not found.");
        }
    }
}
