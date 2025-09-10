import axios from "axios";

const API_BASE = "http://localhost:8000/api";

// Helper to get auth token
const getToken = () => localStorage.getItem("token");
const getAuthHeaders = () => ({ Authorization: `Bearer ${getToken()}` });

// -------------------- POSTS -------------------- //
export const fetchPostsApi = (page = 1) => {
  return axios.get(`${API_BASE}/allposts?page=${page}`, { headers: getAuthHeaders() });
};

export const updatePostApi = (postId, description) => {
  return axios.put(`${API_BASE}/posts/${postId}`, { description }, { headers: getAuthHeaders() });
};

export const deletePostApi = (postId) => {
  return axios.delete(`${API_BASE}/posts/${postId}`, { headers: getAuthHeaders() });
};

// -------------------- LIKES -------------------- //
export const likePostApi = (postId) => {
  return axios.post(`${API_BASE}/like`, { post_id: postId }, { headers: getAuthHeaders() });
};

export const unlikePostApi = (likeId) => {
  return axios.delete(`${API_BASE}/like/${likeId}`, { headers: getAuthHeaders() });
};

// -------------------- COMMENTS -------------------- //
export const addCommentApi = (postId, comment_text) => {
  return axios.post(`${API_BASE}/posts/${postId}/comments`, { post_id: postId, comment_text }, { headers: getAuthHeaders() });
};

export const updateCommentApi = (commentId, comment_text) => {
  return axios.put(`${API_BASE}/comments/${commentId}`, { comment_text }, { headers: getAuthHeaders() });
};

export const deleteCommentApi = (commentId) => {
  return axios.delete(`${API_BASE}/comments/${commentId}`, { headers: getAuthHeaders() });
};
