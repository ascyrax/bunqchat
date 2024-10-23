// import React, { useState, useEffect } from "react";
// import "../styles/JoinGroup.css";
// import { Navigate, useNavigate } from "react-router-dom";

// export default function JoinGroup({ setCurrentGroup }) {
//   let [value, setValue] = useState("");
//   let [groups, setGroups] = useState([
//     {
//       groupId: "team1",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team2",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team3",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team4",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team5",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team6",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team7",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team8",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team9",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//     {
//       groupId: "team10",
//       members: ["das", "modi", "shashank", "ganja", "suraj"],
//       messages: [
//         { sender: "suraj", content: "yo niggas", timestamp: "" },
//         { sender: "das", content: "yo yo", timestamp: "" },
//         { sender: "modi", content: "kidhar hai sab aaj kal", timestamp: "" },
//         { sender: "suraj", content: "same chal rha mera to", timestamp: "" },
//         {
//           sender: "shashank",
//           content: "mera to whi wfh chal rha",
//           timestamp: "",
//         },
//         {
//           sender: "ganja",
//           content: "hum end sem ke liye padh rhe",
//           timestamp: "",
//         },
//       ],
//     },
//   ]);
//   let navigate = useNavigate();

//   useEffect(() => {
//     let timeoutId = setTimeout(handleDebouncedInput, 1000);

//     return () => clearTimeout(timeoutId);
//   }, [value]);

//   const handleDebouncedInput = () => {
//     // send this value to backend to check if such a group or similar ones exists
//     // receive the response from the server
//     let result = false; // or false
//   };

//   const handleInput = (e) => {
//     setValue(e.target.value);
//   };

//   const handleClick = (e) => {
//     // console.log(e.target.innerText)
//     setCurrentGroup(e.target.innerText);
//     navigate(`/groups/${e.target.innerText}`);
//   };

//   return (
//     <div className="joinGroup">
//       <div className="search">
//         <input
//           type="search"
//           placeholder="search a group"
//           value={value}
//           onInput={handleInput}
//         ></input>
//       </div>
//       <div className="list">
//         <ul>
//           {groups.map((group) => {
//             return (
//               <li key={group.groupId} onClick={handleClick}>
//                 {group.groupId}
//               </li>
//             );
//           })}
//         </ul>
//       </div>
//     </div>
//   );
// }

import React, { useState, useEffect } from "react";
import "../styles/JoinGroup.css";
import { useNavigate } from "react-router-dom";

export default function JoinGroup({ setCurrentGroup }) {
  const [value, setValue] = useState("");
  const [groups, setGroups] = useState([]);
  const [error, setError] = useState("");
  const navigate = useNavigate();

  // Fetch groups from the backend when the component mounts
  useEffect(() => {
    const fetchGroups = async () => {
      try {
        const token = localStorage.getItem("token");

        if (!token) {
          setError("You must be logged in to view groups.");
          return;
        }

        const response = await fetch("http://localhost:8000/groups", {
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
        `http://localhost:8000/groups/search?query=${value}`,
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

      const response = await fetch(`http://localhost:8000/join`, {
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
          placeholder="Search a group"
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
