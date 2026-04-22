import React, { createContext, useState, useEffect, ReactNode } from 'react';
import * as SecureStore from 'expo-secure-store';
import api from '../api/axios';

interface AuthContextType {
    user: any;
    isLoading: boolean;
    login: (token: string, user: any) => Promise<void>;
    logout: () => Promise<void>;
    setUser: (user: any) => void;
}

export const AuthContext = createContext<AuthContextType>({} as AuthContextType);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
    const [user, setUser] = useState<any>(null);
    const [isLoading, setIsLoading] = useState(true);

    const checkToken = async () => {
        try {
            const token = await SecureStore.getItemAsync('userToken');
            if (token) {
                const response = await api.get('/user');
                setUser(response.data);
            }
        } catch (e) {
            console.log('Needs login or token expired');
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        checkToken();
    }, []);

    const login = async (token: string, userData: any) => {
        await SecureStore.setItemAsync('userToken', token);
        setUser(userData);
    };

    const logout = async () => {
        try {
            await api.post('/logout'); // Invalidate token on server
        } catch (e) {
            console.error(e);
        }
        await SecureStore.deleteItemAsync('userToken');
        setUser(null);
    };

    return (
        <AuthContext.Provider value={{ user, isLoading, login, logout, setUser }}>
            {children}
        </AuthContext.Provider>
    );
};
