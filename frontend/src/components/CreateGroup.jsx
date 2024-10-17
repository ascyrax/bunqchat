import React, { useEffect, useState } from "react";
import "../styles/CreateGroup.css";

export default function CreateGroup() {
  const [groupAlreadyExists, setGroupAlreadyExists] = useState(false);
  const [value, setValue] = useState("");

  useEffect(() => {
    let timeoutId = setTimeout(handleDebouncedInput, 1000);

    return () => clearTimeout(timeoutId);
  }, [value]);

  const handleDebouncedInput = () => {
    console.log(value);
    // send this value to backend to check if such a group exists
    // receive the response from the server
    let result = false; // or false
    setGroupAlreadyExists(result);
  };

  const handleInput = (e) => {
    setValue(e.target.value);
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
      >
        Create
      </button>
    </div>
  );
}
