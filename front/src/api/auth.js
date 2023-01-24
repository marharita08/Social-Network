import apiClient from '../config/axios';

export const googleAuth = async (data) => {
    return apiClient.post('/auth/google', data);
}

export const facebookAuth = async (data) => {
    return apiClient.post('/auth/facebook', data);
}

export const auth = async (data) => {
    return apiClient.post('users/login', data);
}

export const apiLogout = async (data) => {
    return apiClient.post('/users/logout', data);
}

