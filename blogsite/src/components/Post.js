import React, { useState } from "react";
import { useParams } from "react-router-dom";
import axios from "axios";

const Post = () => {

    const { id } = useParams();
    const [post, setPost] = useState(null);

    const fetchPost = async () => {
        try {
          const response = await axios.get(`${process.env.REACT_APP_API_BASE_URL}/show-post.php/post/${id}`);
            const post = response.data.data;
            setPost(post);
        }
        catch (error) {
            console.log(error);
        }
    };

    React.useEffect(() => {
        fetchPost();
    }, []);

    if (!post) {
        return <div>Loading...</div>;
    }

    return (
        <div className="container my-5">
            <h1 >{post.title}</h1>
            <div className="d-flex justify-content-center">
                <small className="text-muted">
                    Posted by {post.author} on {post.date}
                </small>
            </div>
            <hr />
            <p className="mt-5">{post.content}</p>
            
        </div>
    );
};

export default Post;
