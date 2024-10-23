import React, { useState } from "react";
import "../styles/Login.css";
import { NavLink, useNavigate } from "react-router-dom";

const Login = ({ setLoginStatus, setUsername, setUserId }) => {
  const navigate = useNavigate();
  // State for form fields
  const [formData, setFormData] = useState({
    username: "",
    password: "",
  });

  // State for error messages
  const [errors, setErrors] = useState({});

  // State for success message
  const [success, setSuccess] = useState("");

  // state for error in api calls
  const [apiError, setApiError] = useState("");

  const SERVER_URL = import.meta.env.VITE_SERVER_URL;

  // Handle input change
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  // Validate form data
  const validate = () => {
    let newErrors = {};
    if (!formData.username.trim()) newErrors.username = "Username is required";
    if (!formData.password) newErrors.password = "Password is required";

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // Handle form submit
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (validate()) {
      // Perform login logic (e.g., API call)
      try {
        // Perform API call for login
        const response = await fetch(`${SERVER_URL}/login`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            username: formData.username,
            password: formData.password,
          }),
        });

        const data = await response.json();

        console.log(data);

        // Handle response based on status
        if (response.ok) {
          setSuccess("Login successful!");
          setFormData({ username: "", password: "" });
          setErrors({});

          // You can handle the token and redirect logic here
          localStorage.setItem("token", data.token);
          setLoginStatus(true);
          setUsername(data.username);
          setUserId(data.userId);
          navigate("/"); // Redirect to homepage
        } else {
          // Handle errors from the server
          setApiError(data.message || "Failed to login. Please try again.");
        }
      } catch (error) {
        // Handle network or unexpected errors
        setApiError("An unexpected error occurred. Please try again later.");
        console.error("Login Error:", error);
      }
      // Simulating a successful login
      setSuccess("Login successful!");
      setFormData({ username: "", password: "" });
      setErrors({});
    }
  };

  return (
    <div className="login-container">
      <h2 className="login-title">Login</h2>
      {success && <p className="success-message">{success}</p>}
      <form className="login-form" onSubmit={handleSubmit}>
        <div className="form-group">
          <label htmlFor="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            value={formData.username}
            onChange={handleChange}
            className={errors.username ? "input-error" : ""}
          />
          {errors.username && <p className="error-text">{errors.username}</p>}
        </div>
        <div className="form-group">
          <label htmlFor="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            value={formData.password}
            onChange={handleChange}
            className={errors.password ? "input-error" : ""}
          />
          {errors.password && <p className="error-text">{errors.password}</p>}
        </div>
        <button type="submit" className="login-button">
          Login
        </button>
        {apiError && <p className="error-text">{apiError}</p>}
      </form>
      <div className="logRegNav">
        New User?
        <NavLink to="/register">
          <span>Register</span>
        </NavLink>
      </div>
    </div>
  );
};

export default Login;
