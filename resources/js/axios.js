import axios from 'axios';

axios.defaults.withCredentials = true;
axios.defaults.baseURL = '/'; // Laravel backend URL

export default axios;