import React, { useEffect, useState } from "react";
import "../styles/GroupChat.css";

export default function GroupChat({ currentGroup, currentUser }) {
  const [value, setValue] = useState("");
  const groupMembers = ["das", "ganja", "modi", "shashank", "You"];
  // todo. remove this with actual data sent by server :)
  const messages = [
    { sender: "suraj", content: "yo niggas", timestamp: "" },
    { sender: "das", content: "yo yo", timestamp: "" },
    { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
    { sender: "shashank", content: "mera to whi wfh chal rha", timestamp: "" },
    { sender: "ganja", content: "hum end sem ke liye padh rhe", timestamp: "" },
  ];
  // todo. same as above

  const handleInput = (e) => {
    setValue(e.target.value);
  };

  const handleSubmit = (e) => {
    // client side show the text in the body  ie text sent
    // send the data to backend to be stored for later
  };

  return (
    <div className="groupChat">
      <div className="header">
        <h2>{currentGroup}</h2>
        <h3>{groupMembers.join(", ")}</h3>
      </div>
      <div className="body">
        <ul>
          {messages.map((message) => {
            return (
              <div
                className={`messageBlock ${
                  currentUser == message.sender ? "self" : "other"
                }`}
                key={message.sender + message.timestamp}
              >
                <span className="sender">{message.sender}</span>
                <span className="content">{message.content}</span>
                <span className="timestamp">{message.timestamp}</span>
              </div>
            );
          })}
        </ul>
      </div>
      <div className="footer">
        <input
          type="text"
          className="input"
          placeholder="Type a message"
          value={value}
          onInput={handleInput}
        />
        <button type="submit" onClick={handleSubmit}>
          💬
        </button>
      </div>
    </div>
  );
}
