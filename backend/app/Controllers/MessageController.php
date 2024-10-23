<?php
// app/Controllers/MessageController.php

require_once __DIR__ . '/../Models/Message.php';
require_once __DIR__ . '/../Models/Group.php';

class MessageController
{
    private $pdo, $MessageModel, $GroupModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->MessageModel = new Message($pdo);
        $this->GroupModel = new Group($pdo);
    }

    public function sendMessage($request, $response)
    {
        $user = $request->getAttribute('user');
        $username = $user['username'] ?? '';
        $userId = $user['userId'] ?? '';

        $params = (array)$request->getParsedBody();
        $groupName = $params['groupName'] ?? '';

        $content = $params['message'] ?? '';

        // error_log(var_export($groupName . $username . $content, true));

        if (empty($groupName) || empty($username) || empty($content)) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Group Name, username, and message are required.']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        list($result, $groupId) = $this->groupContainsUser($groupName, $userId);

        if (empty($result)) { // => no such groupMemberss exist
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'User is not a member of the group']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        if ($this->MessageModel->sendMessage($groupId, $userId, $content)) {
            $response->getBody()->write(json_encode(['flag' => 'success', 'message' => 'Message sent successfully.']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Failed to send message.']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getMessages($request, $response, $args)
    {
        $groupName = $args['groupName'] ?? ''; // since we are using a GET request with groupName as a parameter, for this
        try {
            $groupId = $this->getGroupId($groupName);
            if ($groupId) {
                $messages = $this->MessageModel->getMessagesByGroup($groupId);
                $response->getBody()->write(json_encode(['flag' => 'success', 'message' => $messages]));
                return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'invalid group']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
        } catch (\Exception $e) {
            error_log('error: could not get the group messages: ' . $e->getMessage());
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'error retrieving messages'],));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }


    public function groupContainsUser($groupName, $userId)
    {
        $groupId = $this->getGroupId($groupName);

        $stmt = $this->pdo->prepare('SELECT * FROM groupMembers WHERE groupId = :groupId AND userId = :userId');
        $stmt->bindParam(':groupId', $groupId);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        $result =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [$result, $groupId];
    }

    function getGroupId($groupName)
    {
        try {
            $group = $this->GroupModel->getGroupByName($groupName);
            return $group['id'];
        } catch (\Exception $e) {
            error_log("Could not find group: " . $e->getMessage());
            throw new Exception("Group could not be found in the database.");
        }
    }
}
