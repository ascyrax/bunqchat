import React from "react";
import reactLogo from "/bunq.svg";
import "../styles/Landing.css";
import { NavLink } from "react-router-dom";

export default function UserLanding({ handleUserCreate, handleUserJoin }) {
  return (
    <div className="landing UserLanding">
      <div className="header">
        <a href="https://www.bunq.com/" target="_blank">
          <img src={reactLogo} className="logo spin" alt="React logo" />
        </a>
        <h1>bunqchat</h1>
      </div>
      <div className="card">
        <NavLink to="/register">
          <button onClick={handleUserCreate}>Register</button>
        </NavLink>
        <NavLink to="/login">
          <button onClick={handleUserJoin}>Login</button>
        </NavLink>
      </div>
    </div>
  );
}
