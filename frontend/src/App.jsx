import "./styles/App.css";
import JoinGroup from "./components/JoinGroup";
import CreateGroup from "./components/CreateGroup";
import { useState } from "react";
import Landing from "./components/Landing";

function App() {
  const [groupStatus, setGroupStatus] = useState(0);
  // 0 = neutral, 1 = create, 2 = join

  const handleGroupCreate = () => {
    setGroupStatus(1);
  };
  const handleGroupJoin = () => {
    setGroupStatus(2);
  };

  switch (groupStatus) {
    case 0:
      return <Landing
        handleGroupCreate={handleGroupCreate}
        handleGroupJoin={handleGroupJoin}
      />;
      break;
    case 1:
      return <CreateGroup />;
      break;
    case 2:
      return <JoinGroup />;
      break;
    default:
      <div>Group Status Error</div>;
  }
}

export default App;
