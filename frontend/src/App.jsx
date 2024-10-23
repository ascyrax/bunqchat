import JoinGroup from "./components/JoinGroup";
import CreateGroup from "./components/CreateGroup";
import GroupChat from "./components/GroupChat";
import { useEffect, useState } from "react";
import UserLanding from "./components/UserLanding";
import GroupLanding from "./components/GroupLanding";
import { Routes, Route } from "react-router-dom";
import Register from "./components/Register";
import Login from "./components/Login";

function App() {
  const [loginStatus, setLoginStatus] = useState(false);
  const [username, setUsername] = useState("");
  const [userId, setUserId] = useState("");
  const [currentGroup, setCurrentGroup] = useState("");

  const handleGroupCreate = () => {
    setGroupStatus(1);
  };
  const handleGroupJoin = () => {
    setGroupStatus(2);
  };

  const handleUserCreate = () => {
    setUserStatus(1);
  };
  const handleUserJoin = () => {
    setUserStatus(2);
  };

  return (
    <Routes>
      <Route
        path="/"
        element={
          loginStatus ? (
            <GroupLanding
              handleGroupCreate={handleGroupCreate}
              handleGroupJoin={handleGroupJoin}
              username={username}
            />
          ) : (
            <UserLanding
              handleUserCreate={handleUserCreate}
              handleUserJoin={handleUserJoin}
            />
          )
        }
      />
      <Route path="/register" element={<Register />} />
      <Route
        path="/login"
        element={
          <Login
            setLoginStatus={setLoginStatus}
            setUsername={setUsername}
            setUserId={setUserId}
          />
        }
      />
      <Route
        path="/groups"
        element={<CreateGroup setCurrentGroup={setCurrentGroup} />}
      />
      <Route
        path="/join"
        element={<JoinGroup setCurrentGroup={setCurrentGroup} />}
      />
      <Route
        path="groups/*"
        element={
          <GroupChat
            currentGroup={currentGroup}
            username={username}
            userId={userId}
          />
        }
      />
    </Routes>
  );
}

export default App;
