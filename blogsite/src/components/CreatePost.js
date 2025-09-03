import React from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

function CreatePost() {
  const [title, setTitle] = React.useState('');
  const [content, setContent] = React.useState('');
  const [author, setAuthor] = React.useState('');
  const [image, setImage] = React.useState(null); // ✅ Added state for image
  const [isLoading, setIsLoading] = React.useState(false);
  const [error, setError] = React.useState('');

  const navigate = useNavigate();

  // Function to handle form validation
  const validateForm = () => {
    if (!title.trim() || !content.trim() || !author.trim()) {
      setError('All fields are required.');
      return false;
    }
    setError('');
    return true;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');

    if (!validateForm()) {
      return;
    }

    const formData = new FormData();
    formData.append("title", title);
    formData.append("content", content);
    formData.append("author", author);
    if (image) {
      formData.append("image", image);
    }

    setIsLoading(true);

    try {
      const response = await axios.post(
        `${process.env.REACT_APP_API_BASE_URL}/create-post.php`,
        formData,
        { withCredentials: true,
          headers: { "Content-Type": "multipart/form-data" } }
      );

      console.log(response.data);
      navigate('/');
    } catch (error) {
      console.error('There was an error creating the post!', error);
      setError('There was an error creating the post. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="container mt-4 p-4 bg-light rounded shadow-lg mt-5 border-0">
      <h2 className="mb-5">Create a New Post</h2>
      {error && <div className="alert alert-danger">{error}</div>}
      <form onSubmit={handleSubmit}>
        {/* Title */}
        <div className="row mb-3 align-items-center">
          <label htmlFor="title" className="col-sm-2 col-form-label fw-semibold">
            Title
          </label>
          <div className="col-sm-10">
            <input
              type="text"
              className="form-control w-50"
              id="title"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="Enter post title"
              required
            />
          </div>
        </div>

        {/* Content */}
        <div className="row mb-3 align-items-center">
          <label htmlFor="content" className="col-sm-2 col-form-label fw-semibold">
            Content
          </label>
          <div className="col-sm-10">
            <textarea
              className="form-control w-50"
              id="content"
              value={content}
              onChange={(e) => setContent(e.target.value)}
              placeholder="Enter post content"
              required
            />
          </div>
        </div>

        {/* Author */}
        <div className="row mb-3 align-items-center">
          <label htmlFor="author" className="col-sm-2 col-form-label fw-semibold">
            Author
          </label>
          <div className="col-sm-10">
            <input
              type="text"
              className="form-control w-50"
              id="author"
              value={author}
              onChange={(e) => setAuthor(e.target.value)}
              placeholder="Enter author name"
              required
            />
          </div>
        </div>

        {/* Image Upload */}
        <div className="row mb-3 align-items-center">
          <label htmlFor="image" className="col-sm-2 col-form-label fw-semibold">
            Author Image
          </label>
          <div className="col-sm-10">
            <input
              type="file"
              className="form-control w-50"
              id="image"
              accept="image/*"
              onChange={(e) => setImage(e.target.files[0])} // ✅ Save uploaded file
            />
            {image && (
              <img
                src={URL.createObjectURL(image)} // ✅ Preview uploaded file
                alt="Preview"
                className="img-thumbnail mt-2"
                style={{ maxWidth: "150px" }}
              />
            )}
          </div>
        </div>

        {/* Submit Button */}
        <div className="text-end">
          <button type="submit" className="btn btn-dark" disabled={isLoading}>
            {isLoading ? (
              <span>
                <span
                  className="spinner-border spinner-border-sm me-2"
                  role="status"
                  aria-hidden="true"
                ></span>
                Creating post...
              </span>
            ) : (
              "Create Post"
            )}
          </button>
        </div>
      </form>
    </div>
  );
}

export default CreatePost;
