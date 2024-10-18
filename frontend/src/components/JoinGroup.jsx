import React, { useState, useEffect } from "react";
import "../styles/JoinGroup.css";
import { Navigate, useNavigate } from "react-router-dom";

export default function JoinGroup({ setCurrentGroup }) {
  let [value, setValue] = useState("");
  let [groups, setGroups] = useState([
    {
      groupId: "team1",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team2",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team3",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team4",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team5",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team6",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team7",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team8",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team9",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
    {
      groupId: "team10",
      members: ["das", "modi", "shashank", "ganja", "suraj"],
      messages: [
        { sender: "suraj", content: "yo niggas", timestamp: "" },
        { sender: "das", content: "yo yo", timestamp: "" },
        { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
        { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
        {
          sender: "shashank",
          content: "mera to whi wfh chal rha",
          timestamp: "",
        },
        {
          sender: "ganja",
          content: "hum end sem ke liye padh rhe",
          timestamp: "",
        },
      ],
    },
  ]);
  let navigate = useNavigate();

  useEffect(() => {
    let timeoutId = setTimeout(handleDebouncedInput, 1000);

    return () => clearTimeout(timeoutId);
  }, [value]);

  const handleDebouncedInput = () => {
    // send this value to backend to check if such a group or similar ones exists
    // receive the response from the server
    let result = false; // or false
  };

  const handleInput = (e) => {
    setValue(e.target.value);
  };

  const handleClick = (e) => {
    // console.log(e.target.innerText)
    setCurrentGroup(e.target.innerText);
    navigate(`/groups/${e.target.innerText}`);
  };

  return (
    <div className="joinGroup">
      <div className="search">
        <input
          type="search"
          placeholder="search a group"
          value={value}
          onInput={handleInput}
        ></input>
      </div>
      <div className="list">
        <ul>
          {groups.map((group) => {
            return (
              <li key={group.groupId} onClick={handleClick}>
                {group.groupId}
              </li>
            );
          })}
        </ul>
      </div>
    </div>
  );
}
