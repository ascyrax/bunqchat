import React, { useEffect, useState } from "react";
import "../styles/CreateGroup.css";
import { useNavigate } from "react-router-dom";

export default function CreateGroup({ setCurrentGroup }) {
  const [groupAlreadyExists, setGroupAlreadyExists] = useState(true);
  const [value, setValue] = useState("");
  const navigate = useNavigate();

  useEffect(() => {
    let timeoutId = setTimeout(handleDebouncedInput, 1000);

    return () => clearTimeout(timeoutId);
  }, [value]);

  const handleDebouncedInput = () => {
    if (value.trim() == "") return;
    // send this value to backend to check if such a group exists
    // receive the response from the server
    let result = false; // or false
    setGroupAlreadyExists(result);
  };

  const handleInput = (e) => {
    setValue(e.target.value);
  };

  const handleClick = (e) => {
    // send server query to create a new grp

    // get server response

    // if group created successfully, take the user to that group
    // http://localhost:5173/groups/groupId
    setCurrentGroup(value);
    navigate(`/groups/${value}`);

    // else show an error message and say to retry
  };

  return (
    <div className="createGroup">
      <div className="search">
        <input
          type="search"
          placeholder="Create New Group"
          onInput={handleInput}
          value={value}
        ></input>
      </div>
      {groupAlreadyExists ? (
        <p className="error">group already exists!</p>
      ) : (
        <p className="success">go ahead :)</p>
      )}
      <button
        className={`btn-create ${groupAlreadyExists ? "error" : "success"}`}
        onClick={handleClick}
      >
        Create
      </button>
    </div>
  );
}
