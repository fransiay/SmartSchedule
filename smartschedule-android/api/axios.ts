import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';
import Constants from 'expo-constants';

// Détection automatique de l'IP du serveur Expo
const debuggerHost = Constants.expoConfig?.hostUri;
let ipAddress = '10.188.114.101'; // IP détectée de votre machine

if (debuggerHost) {
  ipAddress = debuggerHost.split(':')[0];
}

// L'URL de l'API
const baseURL = Platform.OS === 'android' 
    ? `http://${ipAddress}:8000/api` 
    : 'http://localhost:8000/api';

console.log('Attempting to connect to API at:', baseURL);

const api = axios.create({
    baseURL,
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

api.interceptors.request.use(async (config) => {
    const token = await SecureStore.getItemAsync('userToken');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default api;
