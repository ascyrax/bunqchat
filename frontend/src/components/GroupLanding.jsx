import React from "react";
import reactLogo from "/bunq.svg";
import "../styles/Landing.css";
import { NavLink } from "react-router-dom";

export default function GroupLanding({ handleGroupCreate, handleGroupJoin }) {
  return (
    <div className="landing GroupLanding">
      <div className="header">
        <a href="https://www.bunq.com/" target="_blank">
          <img src={reactLogo} className="logo spin" alt="React logo" />
        </a>
        <h1>bunqchat</h1>
      </div>
      <div className="card">
        <NavLink to="/gcreate">
          <button onClick={handleGroupCreate}>Create a Group</button>
        </NavLink>
        <NavLink to="gjoin">
          <button onClick={handleGroupJoin}>Join a Group</button>
        </NavLink>
      </div>
    </div>
  );
}
