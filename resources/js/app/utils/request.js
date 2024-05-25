//util/request.js
import axios from 'axios';
import { isLogged, getToken, removeToken } from '@/app/utils/auth';

const service = axios.create({
  baseURL: '/api',
  timeout: 10000,
});

service.interceptors.request.use(
  config => {
    const token = isLogged();
    if (token) {
      config.headers['Authorization'] = 'Bearer ' + getToken();
    }
    return config;
  },
  error => {
    console.log(error);
    Promise.reject(error);
  }
);

service.interceptors.response.use(
  response => {
    return response.data;
  },
  error => {
    removeToken();
    let message = error.message;
    if (error.response.data && error.response.data.message) {
      message = error.response.data.message;
    }
    return Promise.reject(error);
  }
);

export default service;
