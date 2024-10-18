import JoinGroup from "./components/JoinGroup";
import CreateGroup from "./components/CreateGroup";
import GroupChat from "./components/GroupChat";
import { useEffect, useState } from "react";
import Landing from "./components/Landing";
import { Routes, Route } from "react-router-dom";

function App() {
  const [groupStatus, setGroupStatus] = useState(0);
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

  return (
    <Routes>
      <Route
        path="/"
        element={
          <Landing
            handleGroupCreate={handleGroupCreate}
            handleGroupJoin={handleGroupJoin}
          />
        }
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
