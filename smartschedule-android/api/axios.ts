import axios from 'axios';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';
import Constants from 'expo-constants';

/*
 * Configuration de l'instance Axios pour l'application mobile SmartSchedule.
 * 
 * Axios est utilisé pour toutes les requêtes HTTP vers l'API Laravel.
 * Un intercepteur injecte automatiquement le token Bearer dans chaque requête.
 */

const baseURL = 'https://neo-solution.fr/api';

console.log('Attempting to connect to API at:', baseURL);

// ────────────────────────────────────────────────
// Création de l'instance Axios avec configuration par défaut
// ────────────────────────────────────────────────
const api = axios.create({
    baseURL,
    headers: {
        'Content-Type': 'application/json', // Toutes les requêtes sont en JSON
        'Accept':        'application/json'  // On attend du JSON en retour
    }
});

// ────────────────────────────────────────────────
// Intercepteur de requêtes : injection du token d'authentification
// ────────────────────────────────────────────────
// Avant chaque requête, on lit le token Bearer stocké dans SecureStore
// et on l'ajoute dans le header "Authorization".
// SecureStore est l'équivalent sécurisé d'AsyncStorage (Keychain iOS / Keystore Android).
api.interceptors.request.use(async (config) => {
    const token = await SecureStore.getItemAsync('userToken');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`; // Format attendu par Laravel Sanctum
    }
    return config;
});

export default api;
