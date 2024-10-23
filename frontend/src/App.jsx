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
  const [groupStatus, setGroupStatus] = useState(0);
  const [userStatus, setUserStatus] = useState(0);
  // 0 = neutral, 1 = create, 2 = join
  const [currentGroup, setCurrentGroup] = useState("");
  const [currentUser, setCurrentUser] = useState("suraj");
  //  todo. hardcoded value here

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
        element={<Login setLoginStatus={setLoginStatus} />}
      />
      <Route
        path="gcreate"
        element={<CreateGroup setCurrentGroup={setCurrentGroup} />}
      />
      <Route
        path="gjoin"
        element={<JoinGroup setCurrentGroup={setCurrentGroup} />}
      />
      <Route
        path="groups/*"
        element={
          <GroupChat currentGroup={currentGroup} currentUser={currentUser} />
        }
      />
    </Routes>
  );
}

export default App;
