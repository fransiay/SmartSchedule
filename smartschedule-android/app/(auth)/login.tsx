import React, { useState, useContext } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ActivityIndicator, KeyboardAvoidingView, Platform, SafeAreaView } from 'react-native';
import { AuthContext } from '../../context/AuthContext';
import { useRouter } from 'expo-router';
import api from '../../api/axios';
import { Ionicons } from '@expo/vector-icons';

export default function LoginScreen() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const { login } = useContext(AuthContext);
    const router = useRouter();

    const handleLogin = async () => {
        if (!email || !password) return;
        setLoading(true);
        try {
            const response = await api.post('/login', { email, password });
            await login(response.data.access_token, response.data.user);
        } catch (error) {
            alert('Erreur de connexion, vérifiez vos identifiants.');
        } finally {
            setLoading(false);
        }
    };

    return (
        <SafeAreaView style={styles.container}>
            <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.flex}>
                <View style={styles.inner}>
                    <View style={styles.logoContainer}>
                        <View style={styles.logoIcon}>
                            <Ionicons name="calendar" size={24} color="white" />
                        </View>
                        <Text style={styles.logoText}>SmartSchedule</Text>
                    </View>
                    
                    <Text style={styles.title}>Bon retour</Text>
                    <Text style={styles.subtitle}>Connectez-vous pour accéder à votre planning intelligent.</Text>
                    
                    <View style={styles.form}>
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>ADRESSE EMAIL</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="vous@exemple.com"
                                placeholderTextColor="#AEAeb2"
                                value={email}
                                onChangeText={setEmail}
                                keyboardType="email-address"
                                autoCapitalize="none"
                            />
                        </View>

                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>MOT DE PASSE</Text>
                            <TextInput
                                style={styles.input}
                                placeholder="••••••••"
                                placeholderTextColor="#AEAeb2"
                                value={password}
                                onChangeText={setPassword}
                                secureTextEntry
                            />
                        </View>

                        <TouchableOpacity style={styles.button} onPress={handleLogin} disabled={loading}>
                            {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.buttonText}>Se connecter →</Text>}
                        </TouchableOpacity>
                    </View>

                    <TouchableOpacity onPress={() => router.push('/(auth)/register')} style={styles.link}>
                        <Text style={styles.linkText}>Pas encore de compte ? <Text style={styles.linkBold}>S'inscrire</Text></Text>
                    </TouchableOpacity>
                </View>
            </KeyboardAvoidingView>
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F7F7F8' },
    flex: { flex: 1 },
    inner: { flex: 1, justifyContent: 'center', padding: 24 },
    logoContainer: { flexDirection: 'row', alignItems: 'center', justifyContent: 'center', gap: 12, marginBottom: 40 },
    logoIcon: { width: 44, height: 44, backgroundColor: '#0F0F10', borderRadius: 12, justifyContent: 'center', alignItems: 'center' },
    logoText: { fontSize: 20, fontWeight: '800', color: '#0F0F10', letterSpacing: -0.5 },
    title: { fontSize: 28, fontWeight: '800', color: '#0F0F10', marginBottom: 8, textAlign: 'center', letterSpacing: -0.5 },
    subtitle: { fontSize: 15, color: '#6C6C70', marginBottom: 32, textAlign: 'center', lineHeight: 22 },
    form: { gap: 20 },
    inputGroup: { gap: 8 },
    label: { fontSize: 11, fontWeight: '700', color: '#6C6C70', letterSpacing: 1 },
    input: { 
        height: 54, 
        backgroundColor: '#FFFFFF', 
        borderRadius: 12, 
        paddingHorizontal: 16, 
        color: '#0F0F10', 
        fontSize: 16,
        borderWidth: 1,
        borderColor: '#E5E5E7'
    },
    button: { 
        backgroundColor: '#0F0F10', 
        padding: 16, 
        borderRadius: 12, 
        alignItems: 'center', 
        marginTop: 12,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.1,
        shadowRadius: 8,
        elevation: 3
    },
    buttonText: { color: '#fff', fontSize: 16, fontWeight: '700' },
    link: { marginTop: 28, alignItems: 'center' },
    linkText: { color: '#6C6C70', fontSize: 14 },
    linkBold: { color: '#0F0F10', fontWeight: '700', textDecorationLine: 'underline' }
});
