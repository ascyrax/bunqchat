import React, { useEffect, useState } from "react";
import "../styles/CreateGroup.css";
import { useNavigate } from "react-router-dom";

export default function CreateGroup({ setCurrentGroup }) {
  const navigate = useNavigate();

  // const [groupAlreadyExists, setGroupAlreadyExists] = useState(true);
  const [groupName, setGroupName] = useState("");

  // State for success message
  const [success, setSuccess] = useState("");
  // State for error message
  const [error, setError] = useState("");

  // useEffect(() => {
  //   let timeoutId = setTimeout(handleDebouncedInput, 1000);

  //   return () => clearTimeout(timeoutId);
  // }, [groupName]);

  // const handleDebouncedInput = () => {
  //   if (groupName.trim() == "") return;
  //   // send this groupName to backend to check if such a group exists
  //   // receive the response from the server
  //   let result = false; // or false
  //   setGroupAlreadyExists(result);
  // };

  const handleInput = (e) => {
    setGroupName(e.target.value);
  };

  const handleClick = async (e) => {
    e.preventDefault();
    setSuccess("");
    setError("");

    const token = localStorage.getItem("token");

    if (!token) {
      setError("You must be logged in to create a group.");
      return;
    }
    // send server query to create a new grp
    try {
      // Perform API call to create a group
      const response = await fetch("http://localhost:8000/groups", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`, // Add the token in Authorization header
        },
        body: JSON.stringify({ groupName: groupName }),
      });

      const data = await response.json();

      // Handle response based on status
      if (response.ok) {
        setSuccess("Group created successfully!");
        setGroupName("");
        // if group created successfully, take the user to that group
        // http://localhost:5173/groups/groupId
        navigate(`/groups/${groupName}`);
        setCurrentGroup(groupName);
      } else {
        setError(
          data.message || "Failed to create the group. Please try again."
        );
      }
    } catch (err) {
      setError("An unexpected error occurred. Please try again later.");
      console.error("Create Group Error:", err);
    }
  };

  return (
    <div className="createGroup">
      <div className="search">
        {success && <p className="success-message">{success}</p>}
        {error && <p className="error-text">{error}</p>}
        <input
          type="search"
          placeholder="Create New Group"
          onInput={handleInput}
          id="groupName"
          name="groupName"
          value={groupName}
          required
        ></input>
      </div>
      {error ? (
        <p className="error">Error!</p>
      ) : (
        <p className="success">go ahead :)</p>
      )}
      <button
        className={`btn-create ${error ? "error" : "success"}`}
        onClick={handleClick}
      >
        Create
      </button>
    </div>
  );
}
