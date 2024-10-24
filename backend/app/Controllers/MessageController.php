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
        $message = $params['message'] ?? '';
        $createdAt = $params['createdAt'] ?? date('H:i:s'); // Use provided timestamp or current time
        $createdBy = $params['createdBy'] ?? ''; // Use provided timestamp or current time

        // if (empty($groupName) || empty($username) || empty($message)) {
        if (empty($groupName) || empty($username) || empty($message) || empty($createdAt) || empty($createdBy)) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'GroupName, username, message and creation data are all required.']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            list($result, $groupId) = $this->groupContainsUser($groupName, $userId);
        } catch (Exception $e) {
            error_log(var_export("\n" . $e->getMessage(), true));
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => $e->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        if (empty($result)) { // => no such groupMembers exist
            if ($groupId == "Group not found.") {
                $response->getBody()->write(json_encode(['flag' => 'error', 'message' => "Group not found."]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'User is not a member of the group.']));
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }
        }

        if ($this->MessageModel->sendMessage($groupId, $userId, $message, $createdAt, $createdBy)) {
            $response->getBody()->write(json_encode(['flag' => 'success', 'message' => 'Message sent successfully.']));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Failed to send message.']));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getMessages($request, $response, $args)
    {
        $user = $request->getAttribute('user');
        $username = $user['username'] ?? '';
        $userId = $user['userId'] ?? '';

        $groupName = $args['groupName'] ?? ''; // since we are using a GET request with groupName as a parameter, for this

        if (empty($groupName) || empty($username)) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'GroupName and username are required.']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        try {
            list($result, $groupId) = $this->groupContainsUser($groupName, $userId);
        } catch (Exception $e) {
            error_log(var_export("\n" . $e->getMessage(), true));
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => $e->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        if (empty($result)) { // => no such groupMembers exist
            if ($groupId == "Group not found.") {
                $response->getBody()->write(json_encode(['flag' => 'error', 'message' => "Group not found."]));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'User is not a member of the group.']));
                return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
            }
        }

        try {
            if (empty($groupId)) {
                $response->getBody()->write(json_encode(['flag' => 'error', 'message' => 'Group not found.']));
                return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['flag' => 'error', 'message' => $e->getMessage()]));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        try {
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
        try {
            $groupId = $this->getGroupId($groupName);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        if (empty($groupId)) {
            return [false, "Group not found."];
        }

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
            if ($group) return $group['id'];
            else return null;
        } catch (\Exception $e) {
            error_log("Could not find group: " . $e->getMessage());
            throw new Exception("Group not found.");
        }
    }
}
