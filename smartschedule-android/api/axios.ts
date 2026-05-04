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

// ────────────────────────────────────────────────
// Détection automatique de l'adresse IP du serveur
// ────────────────────────────────────────────────
// Expo expose l'IP de la machine de développement via expoConfig.hostUri.
// On extrait l'IP pour construire l'URL de l'API dynamiquement.
const debuggerHost = Constants.expoConfig?.hostUri;
let ipAddress = '192.168.1.225'; // IP de la machine hôte (mise à jour automatiquement)

if (debuggerHost) {
  const host = debuggerHost.split(':')[0]; // Exemple : "192.168.1.10:8081" → "192.168.1.10"
  // Si l'hôte est localhost ou 127.0.0.1, on garde l'IP de secours car Android ne peut pas atteindre l'hôte via localhost
  if (host !== 'localhost' && host !== '127.0.0.1') {
    ipAddress = host;
  } else if (Platform.OS === 'android') {
    // Sur émulateur Android, 10.0.2.2 pointe vers le localhost de l'ordinateur
    ipAddress = '10.0.2.2';
  }
}

// On utilise l'IP de la machine pour toutes les plateformes (Android, iOS, Web)
// car 'localhost' ne fonctionne pas sur les appareils physiques.
const baseURL = `http://${ipAddress}:8000/api`;

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
