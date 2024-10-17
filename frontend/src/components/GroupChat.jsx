import React, { useEffect, useState, useRef } from "react";
import "../styles/GroupChat.css";

export default function GroupChat({ currentGroup, currentUser }) {
  const [value, setValue] = useState("");
  const groupMembers = [
    "das",
    "modi",
    "shashank",
    "ganja",
    "suraj",
    "das1",
    "modi1",
    "shashank1",
    "ganja1",
    "suraj1",
    "das2",
    "modi2",
    "shashank2",
    "ganja2",
    "suraj2",
  ];
  // todo. remove this with actual data sent by server :)
  const [messages, setMessages] = useState([
    { sender: "suraj", content: "yo niggas", timestamp: "" },
    { sender: "das", content: "yo yo", timestamp: "" },
    { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
    { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
    { sender: "shashank", content: "mera to whi wfh chal rha", timestamp: "" },
    { sender: "ganja", content: "hum end sem ke liye padh rhe", timestamp: "" },
    { sender: "suraj1", content: "yo niggas", timestamp: "" },
    { sender: "das1", content: "yo yo", timestamp: "" },
    { sender: "modi1", content: "kidhar hai sab aaj kal", timestamp: "" },
    { sender: "suraj1", content: "same chal rha mera to", timestamp: "" },
    { sender: "shashank1", content: "mera to whi wfh chal rha", timestamp: "" },
    {
      sender: "ganja1",
      content: "hum end sem ke liye padh rhe",
      timestamp: "",
    },
    { sender: "suraj2", content: "yo niggas", timestamp: "" },
    { sender: "das2", content: "yo yo", timestamp: "" },
    { sender: "modi2", content: "kidhar hai sab aaj kal", timestamp: "" },
    { sender: "suraj2", content: "same chal rha mera to", timestamp: "" },
    { sender: "shashank2", content: "mera to whi wfh chal rha", timestamp: "" },
    {
      sender: "ganja2",
      content: "hum end sem ke liye padh rhe",
      timestamp: "",
    },
  ]);
  // todo. same as above

  const colors = [
    "Red",
    "Lime",
    "Yellow",
    "Cyan",
    "Magenta",
    "Maroon",
    "Olive",
    "Green",
    "Purple",
    "Teal",
    "Navy",
    "Orange",
    "Gold",
    "Pink",
    "Indigo",
    "Brown",
    "Coral",
    "Turquoise",
    "Violet",
    "Crimson",
    "DarkBlue",
    "DarkGreen",
    "DarkRed",
    "DarkOrange",
    "DeepPink",
    "DodgerBlue",
    "ForestGreen",
    "Fuchsia",
    "GoldenRod",
    "HotPink",
    "Khaki",
    "Lavender",
    "LightBlue",
    "LawnGreen",
    "MediumOrchid",
    "MidnightBlue",
    "Moccasin",
    "Orchid",
    "PaleGreen",
    "PeachPuff",
    "Peru",
    "Plum",
    "RoyalBlue",
    "Salmon",
    "SeaGreen",
  ];

  const userColorMap = new Map([]);

  let memLen = groupMembers.length;
  let colLen = colors.length;
  for (let i = 0; i < memLen; i++) {
    userColorMap.set(groupMembers[i], colors[i % colLen]);
  }

  const messagesEndRef = useRef(null);

  // Scroll to bottom whenever the messages array is updated
  useEffect(() => {
    messagesEndRef.current.scrollTop = messagesEndRef.current.scrollHeight; // exact value will be scrollHeight - clientHeight, but this works too :)
  }, [messages]);

  const handleInput = (e) => {
    setValue(e.target.value);
  };

  const handleSubmit = (e) => {
    // client side show the text in the body  ie text sent
    // todo do this correctly
    setMessages((prevMessage) => [
      ...prevMessage,
      { sender: currentUser, content: value, timestamp: "" },
    ]);

    setValue("");
    // send the data to backend to be stored for later
  };

  const handleEnterPress = (e) => {
    if (e.key == "Enter") {
      handleSubmit();
    }
  };

  return (
    <div className="groupChat">
      <div className="header">
        <h2>{currentGroup}</h2>
        <h3>{groupMembers.join(", ")}</h3>
      </div>
      <div className="body" ref={messagesEndRef}>
        <ul>
          {messages.map((message) => {
            return (
              <div
                className={`messageBlock ${
                  currentUser == message.sender ? "self" : "other"
                }`}
                key={
                  message.sender +
                  message.timestamp +
                  message.content.substring(0, 5)
                }
              >
                <span
                  className="sender"
                  style={{ color: `${userColorMap.get(message.sender)}` }}
                >
                  {message.sender}
                </span>
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
          onKeyDown={handleEnterPress}
        />
        <button type="submit" onClick={handleSubmit}>
          💬
        </button>
      </div>
    </div>
  );
}
