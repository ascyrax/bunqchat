import React, { useState, useEffect } from "react";
import "../styles/JoinGroup.css";
import { useNavigate } from "react-router-dom";

export default function JoinGroup({ setCurrentGroup }) {
  const [value, setValue] = useState("");
  const [groups, setGroups] = useState([]);
  const [error, setError] = useState("");
  const navigate = useNavigate();

  const SERVER_URL = import.meta.env.VITE_SERVER_URL;

  // Fetch groups from the backend when the component mounts
  useEffect(() => {
    const fetchGroups = async () => {
      try {
        const token = localStorage.getItem("token");

        if (!token) {
          setError("You must be logged in to view groups.");
          return;
        }

        const response = await fetch(`${SERVER_URL}/groups`, {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });

        const data = await response.json();
        // console.log(data);

        if (response.ok) {
          setGroups(data.message); // Set the groups from the API response
        } else {
          setError(data.message || "Failed to load groups.");
        }
      } catch (err) {
        setError("An unexpected error occurred. Please try again later.");
        console.error("Fetch Groups Error:", err);
      }
    };

    fetchGroups();
  }, []);

  // Handle debounced search input
  useEffect(() => {
    const timeoutId = setTimeout(() => handleDebouncedInput(), 1000);
    return () => clearTimeout(timeoutId);
  }, [value]);

  const handleDebouncedInput = async () => {
    if (value.trim() === "") return;

    try {
      const token = localStorage.getItem("token");

      if (!token) {
        setError("You must be logged in to search groups.");
        return;
      }

      const response = await fetch(
        `${SERVER_URL}/groups/search?query=${value}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        }
      );

      const data = await response.json();

      if (response.ok) {
        setGroups(data.groups); // Set the filtered groups based on the search input
      } else {
        setError(data.message || "Failed to search groups.");
      }
    } catch (err) {
      setError("An unexpected error occurred. Please try again later.");
      console.error("Search Groups Error:", err);
    }
  };

  const handleInput = (e) => {
    setValue(e.target.value);
  };

  const handleClick = async (e) => {
    const groupName = e.target.innerText;
    setCurrentGroup(groupName);

    try {
      const token = localStorage.getItem("token");

      if (!token) {
        setError("You must be logged in to search groups.");
        return;
      }

      const response = await fetch(`${SERVER_URL}/join`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({ groupName: groupName }),
      });

      const data = await response.json();
      console.log(data);

      if (response.ok) {
        setGroups(data.groups); // Set the filtered groups based on the search input
        navigate(`/groups/${groupName}`);
      } else {
        setError(data.message || "Failed to search groups.");
      }
    } catch (err) {
      setError("An unexpected error occurred. Please try again later.");
      console.error("Search Groups Error:", err);
    }
  };

  return (
    <div className="joinGroup">
      <div className="search">
        <input
          type="search"
          // placeholder="Search a group"
          placeholder="TODO: WORK IN PROGRESS"
          value={value}
          onChange={handleInput}
        />
      </div>
      {error && <p className="error">{error}</p>}
      <div className="list">
        <ul>
          {groups.map((group) => (
            <li key={group.id} onClick={handleClick}>
              {group.name}
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}
