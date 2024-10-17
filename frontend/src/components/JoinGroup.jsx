import React, { useState, useEffect } from "react";
import "../styles/JoinGroup.css";

export default function JoinGroup() {
  let [value, setValue] = useState("");

  useEffect(() => {
    let timeoutId = setTimeout(handleDebouncedInput, 1000);

    return () => clearTimeout(timeoutId);
  }, [value]);

  const handleDebouncedInput = () => {
    console.log(value);
    // send this value to backend to check if such a group or similar ones exists
    // receive the response from the server
    let result = false; // or false
  };

  const handleInput = (e) => {
    setValue(e.target.value);
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
          <li>suraj</li>
          <li>suraj</li>
          <li>suraj3</li>
          <li>suraj4</li>
          <li>suraj5</li>
          <li>suraj6</li>
          <li>suraj7</li>
          <li>suraj8</li>
          <li>suraj9</li>
          <li>suraj10</li>
          <li>suraj11</li>
          <li>suraj12</li>
        </ul>
      </div>
    </div>
  );
}
