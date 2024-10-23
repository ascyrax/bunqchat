import React, { useState } from "react";
import "../styles/Register.css";
import { NavLink, useNavigate } from "react-router-dom";

const Register = () => {
  const navigate = useNavigate();

  // State for form fields
  const [formData, setFormData] = useState({
    username: "",
    password: "",
    confirmPassword: "",
  });

  //   console.log(process.env.SERVER_URL);

  // State for error messages
  const [errors, setErrors] = useState({});

  // State for success message
  const [success, setSuccess] = useState("");

  // state for error in api calls
  const [apiError, setApiError] = useState("");

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
    else if (formData.password.length < 6)
      newErrors.password = "Password must be at least 6 characters";
    if (formData.password !== formData.confirmPassword)
      newErrors.confirmPassword = "Passwords do not match";

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // Handle form submit
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (validate()) {
      // Perform registration logic (e.g., API call)
      try {
        // Perform API call for registration
        const response = await fetch(`http://localhost:8000/register`, {
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
          // Simulating a successful registration
          setSuccess("Registration successful!");
          setFormData({ username: "", password: "", confirmPassword: "" });
          setErrors({});
          navigate("/login");
        } else {
          // Handle errors from the server
          setApiError(data.message || "Failed to register. Please try again.");
        }
      } catch (error) {
        // Handle network or unexpected errors
        setApiError("An unexpected error occurred. Please try again later.");
        console.error("Registration Error:", error);
      }
    }
  };

  return (
    <div className="register-container">
      <h2 className="register-title">Register</h2>
      {success && <p className="success-message">{success}</p>}
      <form className="register-form" onSubmit={handleSubmit}>
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
        <div className="form-group">
          <label htmlFor="confirmPassword">Confirm Password</label>
          <input
            type="password"
            id="confirmPassword"
            name="confirmPassword"
            value={formData.confirmPassword}
            onChange={handleChange}
            className={errors.confirmPassword ? "input-error" : ""}
          />
          {errors.confirmPassword && (
            <p className="error-text">{errors.confirmPassword}</p>
          )}
        </div>
        <button type="submit" className="register-button">
          Register
        </button>
        {apiError && <p className="error-text">{apiError}</p>}
      </form>
      <div className="logRegNav">
        Already Registered?{" "}
        <NavLink to="/login">
          <span>Login</span>
        </NavLink>
      </div>
    </div>
  );
};

export default Register;
