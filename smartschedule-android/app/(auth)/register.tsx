import React, { useState, useContext } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ActivityIndicator, KeyboardAvoidingView, Platform, ScrollView, SafeAreaView } from 'react-native';
import { AuthContext } from '../../context/AuthContext';
import { useRouter } from 'expo-router';
import api from '../../api/axios';
import { Ionicons } from '@expo/vector-icons';

export default function RegisterScreen() {
    const [name, setName] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [loading, setLoading] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const { login } = useContext(AuthContext);
    const router = useRouter();

    const handleRegister = async () => {
        if (!name || !email || !password) return;
        setLoading(true);
        setErrorMessage('');
        try {
            const response = await api.post('/register', { name, email, password });
            await login(response.data.access_token, response.data.user);
        } catch (error: any) {
            if (error.response && error.response.data) {
                const data = error.response.data;
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0] as string[];
                    setErrorMessage(firstError[0]);
                } else if (data.message) {
                    setErrorMessage(data.message);
                } else {
                    setErrorMessage('Erreur lors de l\'inscription.');
                }
            } else {
                setErrorMessage('Impossible de contacter le serveur.');
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <SafeAreaView style={styles.container}>
            <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.flex}>
                <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
                    <View style={styles.inner}>
                        <View style={styles.logoContainer}>
                            <View style={styles.logoIcon}>
                                <Ionicons name="calendar-outline" size={24} color="white" />
                            </View>
                            <Text style={styles.logoText}>SmartSchedule</Text>
                        </View>
                        
                        <Text style={styles.title}>Créer un compte</Text>
                        <Text style={styles.subtitle}>Rejoignez SmartSchedule pour automatiser votre planification quotidienne.</Text>
                        
                        {errorMessage ? (
                            <View style={styles.errorBox}>
                                <Ionicons name="alert-circle" size={18} color="#E11D48" />
                                <Text style={styles.errorText}>{errorMessage}</Text>
                            </View>
                        ) : null}

                        <View style={styles.form}>
                            <View style={styles.inputGroup}>
                                <Text style={styles.label}>NOM COMPLET</Text>
                                <TextInput
                                    style={styles.input}
                                    placeholder="Jean Dupont"
                                    placeholderTextColor="#AEAeb2"
                                    value={name}
                                    onChangeText={setName}
                                />
                            </View>

                            <View style={styles.inputGroup}>
                                <Text style={styles.label}>ADRESSE EMAIL</Text>
                                <TextInput
                                    style={styles.input}
                                    placeholder="jean@exemple.com"
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
                                    placeholder="Min. 8 caractères"
                                    placeholderTextColor="#AEAeb2"
                                    value={password}
                                    onChangeText={setPassword}
                                    secureTextEntry
                                />
                            </View>

                            <TouchableOpacity style={styles.button} onPress={handleRegister} disabled={loading}>
                                {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.buttonText}>S'inscrire →</Text>}
                            </TouchableOpacity>
                        </View>

                        <TouchableOpacity onPress={() => router.back()} style={styles.link}>
                            <Text style={styles.linkText}>Déjà un compte ? <Text style={styles.linkBold}>Se connecter</Text></Text>
                        </TouchableOpacity>
                    </View>
                </ScrollView>
            </KeyboardAvoidingView>
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F7F7F8' },
    flex: { flex: 1 },
    scroll: { flexGrow: 1 },
    inner: { padding: 24, paddingVertical: 40 },
    logoContainer: { flexDirection: 'row', alignItems: 'center', gap: 12, marginBottom: 32 },
    logoIcon: { width: 44, height: 44, backgroundColor: '#0F0F10', borderRadius: 12, justifyContent: 'center', alignItems: 'center' },
    logoText: { fontSize: 18, fontWeight: '800', color: '#0F0F10', letterSpacing: -0.5 },
    title: { fontSize: 28, fontWeight: '800', color: '#0F0F10', marginBottom: 8, letterSpacing: -0.5 },
    subtitle: { fontSize: 15, color: '#6C6C70', marginBottom: 32, lineHeight: 22 },
    errorBox: { 
        flexDirection: 'row', 
        alignItems: 'center', 
        gap: 8, 
        backgroundColor: '#FFF1F2', 
        padding: 12, 
        borderRadius: 10, 
        marginBottom: 24,
        borderWidth: 1,
        borderColor: '#FECDD3'
    },
    errorText: { color: '#E11D48', fontSize: 14, fontWeight: '600' },
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
