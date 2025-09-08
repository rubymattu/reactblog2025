import React, { useState, useEffect } from "react";
import { NavLink, useLocation, useNavigate } from "react-router-dom";
import axios from "axios";

const Navbar = () => {
  const [username, setUsername] = useState("");
  const location = useLocation();
  const navigate = useNavigate();


  // Hide links if we are on login or register page
  const hideLinks = location.pathname === "/login" || location.pathname === "/register";

  // Fetch logged-in user info
  useEffect(() => {
    const fetchUser = async () => {
      try {
        const response = await axios.get(
          `${process.env.REACT_APP_API_BASE_URL}/checkAuth.php`,
          { withCredentials: true }
        );

        if (response.data && response.data.authenticated) {
          setUsername(response.data.user.userName);
        } else {
          navigate("/login");
        }
      } catch (error) {
        console.error("Auth check failed", error);
        navigate("/login");
      }
    };

    fetchUser();
  }, [navigate]);

  // Logout handler
  const handleLogout = async () => {
    try {
      await axios.post(
        `${process.env.REACT_APP_API_BASE_URL}/logout.php`,
        {},
        { withCredentials: true }
      );
    } catch (error) {
      console.error("Logout failed", error);
    } finally {
      navigate("/login");
    }
  };


  return (
    <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
      <div className="container">
        <NavLink className="navbar-brand" to="/">
          Blog Application
        </NavLink>
        {!hideLinks && (
          <>
            <button
              className="navbar-toggler"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#navbarNav"
              aria-controls="navbarNav"
              aria-expanded="false"
              aria-label="Toggle navigation"
            >
              <span className="navbar-toggler-icon"></span>
            </button>
            <div className="collapse navbar-collapse" id="navbarNav">
              <ul className="navbar-nav ms-auto mb-2 mb-lg-0 fs-5">
                <li className="nav-item">
                  <NavLink
                    to="/"
                    className={({ isActive }) =>
                      "nav-link" + (isActive ? " active text-light fw-semibold" : "")
                    }
                  >
                    Home
                  </NavLink>
                </li>
                <li className="nav-item">
                  <NavLink
                    to="/create-post"
                    className={({ isActive }) =>
                      "nav-link" + (isActive ? " active text-light fw-semibold" : "")
                    }
                  >
                    Create Post
                  </NavLink>
                </li>
                              {username && (
            <li className="nav-item">
              <span className="navbar-text text-primary me-4">
                Hii {username}!
              </span>
              <button className="btn btn-outline-light" onClick={handleLogout}>
                Logout
              </button>
            </li>
          )}
              </ul>

            </div>
          </>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
