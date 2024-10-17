import React from "react";
import reactLogo from "/bunq.svg";

export default function Landing({ handleGroupCreate, handleGroupJoin }) {
  return (
    <div className="landing">
      <div className="header">
        <a href="https://www.bunq.com/" target="_blank">
          <img src={reactLogo} className="logo spin" alt="React logo" />
        </a>
        <h1>bunqchat</h1>
      </div>
      <div className="card">
        <button onClick={handleGroupCreate}>Create a Group</button>
        <button onClick={handleGroupJoin}>Join a Group</button>
      </div>
    </div>
  );
}
