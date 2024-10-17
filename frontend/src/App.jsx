import JoinGroup from "./components/JoinGroup";
import CreateGroup from "./components/CreateGroup";
import { useState } from "react";
import Landing from "./components/Landing";
import { Routes, Route } from "react-router-dom";

function App() {
  const [groupStatus, setGroupStatus] = useState(0);
  // 0 = neutral, 1 = create, 2 = join

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
      <Route path="gcreate" element={<CreateGroup />} />
      <Route path="gjoin" element={<JoinGroup />} />
    </Routes>
  );
}

export default App;
