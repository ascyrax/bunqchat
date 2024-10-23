import React, { useEffect, useState, useRef } from "react";
import "../styles/GroupChat.css";

export default function GroupChat({ currentGroup, username, userId }) {
  const [value, setValue] = useState("");
  const [messages, setMessages] = useState([]);
  const [error, setError] = useState("");

  const messagesEndRef = useRef(null);

  // Scroll to bottom whenever the messages array is updated
  useEffect(() => {
    if (messagesEndRef.current) {
      messagesEndRef.current.scrollTop = messagesEndRef.current.scrollHeight;
    }
  }, [messages]);

  // Fetch messages for the current group
  useEffect(() => {
    const fetchMessages = async () => {
      try {
        const token = localStorage.getItem("token");

        if (!token) {
          setError("You must be logged in to view messages.");
          return;
        }

        const response = await fetch(
          `http://localhost:8000/messages/${currentGroup}`,
          {
            method: "GET",
            headers: {
              "Content-Type": "application/json",
              Authorization: `Bearer ${token}`,
            },
          }
        );

        const data = await response.json();

        console.log(data);

        if (response.ok) {
          setMessages(data.message);
          // console.log(username);
        } else {
          setError(data.message || "Failed to load messages.");
        }
      } catch (err) {
        setError("An unexpected error occurred. Please try again later.");
        console.error("Fetch Messages Error:", err);
      }
    };

    if (currentGroup) {
      fetchMessages(); // Fetch messages when the currentGroup changes
    }
  }, [currentGroup]);

  const handleInput = (e) => {
    setValue(e.target.value);
  };

  const handleSubmit = async (e) => {
    console.log(messages);
    e.preventDefault();
    if (value.trim() === "") return;
    const newMessage = {
      groupName: currentGroup,
      message: value,
      createdAt: new Date().toLocaleTimeString(),
      createdBy: username,
    };

    console.log(newMessage);
    // Optimistically update the UI
    setMessages((prevMessages) => [...prevMessages, newMessage]);
    setValue("");

    try {
      const token = localStorage.getItem("token");

      if (!token) {
        setError("You must be logged in to send messages.");
        return;
      }

      // Send the new message to the backend
      const response = await fetch(`http://localhost:8000/messages`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify(newMessage),
      });

      if (!response.ok) {
        const data = await response.json();
        console.log(data);
        setError(data.message || "Failed to send the message.");
      }
    } catch (err) {
      setError("An unexpected error occurred. Please try again later.");
      console.error("Send Message Error:", err);
    }
  };

  const handleEnterPress = (e) => {
    if (e.key === "Enter") {
      handleSubmit(e);
    }
  };

  return (
    <div className="groupChat">
      <div className="header">
        <h2>{currentGroup}</h2>
      </div>
      <div className="body" ref={messagesEndRef}>
        <ul>
          {messages.map((message, index) => (
            <div
              className={`messageBlock ${
                username === message.createdBy ? "self" : "other"
              }`}
              key={index}
            >
              <span className="sender">{message.createdBy}</span>
              <span className="content">{message.message}</span>
              <span className="timestamp">{message.createdAt}</span>
            </div>
          ))}
        </ul>
      </div>
      <div className="footer">
        <input
          type="text"
          className="input"
          placeholder="Type a message"
          value={value}
          onChange={handleInput}
          onKeyDown={handleEnterPress}
        />
        <button type="submit" onClick={handleSubmit}>
          💬
        </button>
      </div>
      {error && <p className="error">{error}</p>}
    </div>
  );
}
