import React, { createContext, useState, useEffect, ReactNode } from 'react';
import * as SecureStore from 'expo-secure-store';
import * as Notifications from 'expo-notifications';
import { Platform } from 'react-native';
import api from '../api/axios';

/*
 * Contexte d'authentification global — AuthContext
 * 
 * Ce contexte React permet de partager l'état d'authentification
 * (utilisateur connecté, token) avec tous les composants de l'application,
 * sans avoir à passer des props manuellement à chaque niveau.
 * 
 * Fonctionnement :
 *  1. Au démarrage, on vérifie si un token existe dans SecureStore.
 *  2. Si oui, on récupère le profil utilisateur via l'API.
 *  3. On expose les fonctions login() et logout() aux composants enfants.
 */

// ────────────────────────────────────────────────
// Définition du type TypeScript du contexte
// ────────────────────────────────────────────────
interface AuthContextType {
    user: any;                                  // Données de l'utilisateur connecté (ou null)
    isLoading: boolean;                         // true pendant la vérification du token au démarrage
    login: (token: string, user: any) => Promise<void>; // Sauvegarde le token et l'utilisateur
    logout: () => Promise<void>;                // Supprime le token et déconnecte
    setUser: (user: any) => void;               // Met à jour les données utilisateur
}

// Création du contexte avec des valeurs par défaut vides
export const AuthContext = createContext<AuthContextType>({} as AuthContextType);

// ────────────────────────────────────────────────
// Fournisseur du contexte (Provider)
// Enveloppe toute l'application dans _layout.tsx
// ────────────────────────────────────────────────
export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [user, setUser]         = useState<any>(null);    // Utilisateur connecté
    const [isLoading, setIsLoading] = useState(true);       // Chargement initial

    /**
     * Vérifie si un token est stocké dans SecureStore au démarrage de l'app.
     * Si un token valide existe, récupère le profil utilisateur depuis l'API.
     */
    const checkToken = async () => {
        try {
            const token = await SecureStore.getItemAsync('userToken');
            if (token) {
                // Token trouvé : récupération du profil pour valider le token
                const response = await api.get('/user');
                setUser(response.data);
            }
        } catch (e) {
            // Token expiré ou invalide → l'utilisateur devra se reconnecter
            console.log('Needs login or token expired');
        } finally {
            setIsLoading(false); // Fin du chargement initial
        }
    };

    /**
     * Demande la permission et récupère le token de notification Expo.
     */
    const registerForPushNotificationsAsync = async () => {
        let token;
        
        if (Platform.OS === 'android') {
            await Notifications.setNotificationChannelAsync('default', {
                name: 'default',
                importance: Notifications.AndroidImportance.MAX,
                vibrationPattern: [0, 250, 250, 250],
                lightColor: '#FF231F7C',
            });
        }

        const { status: existingStatus } = await Notifications.getPermissionsAsync();
        let finalStatus = existingStatus;
        if (existingStatus !== 'granted') {
            const { status } = await Notifications.requestPermissionsAsync();
            finalStatus = status;
        }
        
        if (finalStatus !== 'granted') {
            console.log('Failed to get push token for push notification!');
            return;
        }

        token = (await Notifications.getExpoPushTokenAsync({
            projectId: 'your-project-id' // À remplacer par le vrai ID ou laisser vide si configuré dans app.json
        })).data;

        return token;
    };

    // Vérification du token au premier chargement de l'application
    useEffect(() => {
        checkToken();
    }, []);

    /**
     * Connexion : sauvegarde le token Bearer dans SecureStore et l'utilisateur en mémoire.
     * Appelé après un POST /api/login ou /api/register réussi.
     */
    const login = async (token: string, userData: any) => {
        await SecureStore.setItemAsync('userToken', token); // Stockage sécurisé du token
        setUser(userData);

        // Enregistrement du token de notification après la connexion
        try {
            const pushToken = await registerForPushNotificationsAsync();
            if (pushToken) {
                await api.post('/user/push-token', { token: pushToken });
            }
        } catch (error) {
            console.error('Error registering push token:', error);
        }
    };

    /**
     * Déconnexion : invalide le token sur le serveur, puis le supprime localement.
     */
    const logout = async () => {
        try {
            await api.post('/logout'); // Suppression du token côté serveur (Laravel Sanctum)
        } catch (e) {
            console.error(e);
        }
        await SecureStore.deleteItemAsync('userToken'); // Suppression du token local
        setUser(null); // Réinitialisation de l'état utilisateur
    };

    return (
        // Mise à disposition du contexte pour tous les composants enfants
        <AuthContext.Provider value={{ user, isLoading, login, logout, setUser }}>
            {children}
        </AuthContext.Provider>
    );
};
