import React from "react";
import reactLogo from "/bunq.svg";
import "../styles/Landing.css";
import { NavLink } from "react-router-dom";

export default function GroupLanding({
  handleGroupCreate,
  handleGroupJoin,
  username,
}) {
  return (
    <div className="landing GroupLanding">
      <div className="header">
        <a href="https://www.bunq.com/" target="_blank">
          <img src={reactLogo} className="logo spin" alt="React logo" />
        </a>
        <h1>bunqchat</h1>
        <h2>
          {`Welcome`} <span style={{ color: "cyan" }}>{username}</span>
        </h2>
      </div>
      <div className="card">
        <NavLink to="/groups">
          <button>Create a Group</button>
        </NavLink>
        <NavLink to="/join">
          <button>Join a Group</button>
        </NavLink>
      </div>
    </div>
  );
}
