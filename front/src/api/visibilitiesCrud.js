import apiClient from '../config/axios';

export const getArticleVisibilities = async () => {
    return apiClient.get('/a-visibilities');
}

export const getFieldVisibilities = async () => {
    return apiClient.get('/f-visibilities');
}
