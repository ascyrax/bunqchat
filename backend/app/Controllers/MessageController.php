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
            $response->withStatus(400)->getBody()->write(var_export(['flag' => 'error', 'message' => 'Group Name, username, and message are required.'], true));
            return $response;
        }

        list($result, $groupId) = $this->groupContainsUser($groupName, $userId);

        if (empty($result)) { // => no such groupMemberss exist
            $response->withStatus(401)->getBody()->write(var_export(['flag' => 'error', 'message' => 'User is not a member of the group'], true));
            return $response;
        }

        if ($this->MessageModel->sendMessage($groupId, $userId, $content)) {
            $response->withStatus(201)->getBody()->write(var_export(['flag' => 'success', 'message' => 'Message sent successfully.'], true));
        } else {
            $response->withStatus(500)->getBody()->write(var_export(['flag' => 'error', 'message' => 'Failed to send message.'], true));
        }
        return $response;
    }

    public function getMessages($request, $response, $args)
    {
        $groupName = $args['groupName'] ?? ''; // since we are using a GET request with groupName as a parameter, for this
        try {
            $groupId = $this->getGroupId($groupName);
            if ($groupId) {
                $messages = $this->MessageModel->getMessagesByGroup($groupId);
                $response->withStatus(200)->getBody()->write(var_export(['flag' => 'success', 'message' => $messages], true));
            } else {
                $response->withStatus(404)->getBody()->write(var_export(['flag' => 'error', 'message' => 'invalid group'], true));
            }
        } catch (\Exception $e) {
            error_log('error: could not get the group messages: ' . $e->getMessage());
            $response->withStatus(500)->getBody()->write(var_export(['flag' => 'error', 'message' => 'error retrieving messages'],   true));
        }
        return $response;
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
