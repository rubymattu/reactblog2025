import React from 'react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';

function CreatePost() {

  const [title, setTitle] = React.useState('');
  const [content, setContent] = React.useState('');
  const [author, setAuthor] = React.useState('');
  const [loading, setIsLoading] = React.useState(false);
  const [error, setError] = React.useState('');

  const navigate = useNavigate();

  // Function to handle form validation
  const validateForm = () => {
    if (!title.trim || !content.trim || !author.trim) {
      setError('All fields are required.');
      return false;
    }
    setError('');
    return true;
  }

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError(''); // Clear errors from previous submissions

    if (!validateForm()) {
      return;
    }

    setIsLoading(true);

    try {

    } catch (error) {

    }
  } 

  return (
    <div className="container mt-4">
      <h2>Create a New Post</h2>
    </div>
  );
}

export default CreatePost;
