import React, { useContext, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, TextInput, ActivityIndicator, Alert, ScrollView } from 'react-native';
import { AuthContext } from '../../context/AuthContext';
import api from '../../api/axios';

export default function ProfileScreen() {
    const { user, logout, setUser } = useContext(AuthContext);
    const [breakDuration, setBreakDuration] = useState(user?.break_duration?.toString() || '15');
    const [updating, setUpdating] = useState(false);

    const handleUpdate = async () => {
        setUpdating(true);
        try {
            const { data } = await api.put('/user', {
                break_duration: parseInt(breakDuration)
            });
            setUser(data.user);
            Alert.alert('Succès', 'Profil mis à jour !');
        } catch (e) {
            Alert.alert('Erreur', 'Impossible de mettre à jour le profil');
        } finally {
            setUpdating(false);
        }
    };

    return (
        <ScrollView style={styles.container}>
            <View style={styles.profileHeader}>
                <View style={styles.avatar}>
                    <Text style={styles.avatarText}>{user?.name?.charAt(0).toUpperCase()}</Text>
                    <View style={styles.activeDot} />
                </View>
                <Text style={styles.name}>{user?.name}</Text>
                <Text style={styles.email}>{user?.email}</Text>
            </View>

            <View style={styles.section}>
                <Text style={styles.sectionTitle}>PARAMÈTRES DE PLANIFICATION</Text>
                <View style={styles.card}>
                    <Text style={styles.label}>Temps de pause (minutes)</Text>
                    <View style={styles.inputRow}>
                        <TextInput 
                            style={styles.input} 
                            value={breakDuration} 
                            onChangeText={setBreakDuration} 
                            keyboardType="numeric"
                            placeholder="15"
                            placeholderTextColor="rgba(255,255,255,0.2)"
                        />
                        <TouchableOpacity style={styles.saveBtn} onPress={handleUpdate} disabled={updating}>
                            {updating ? <ActivityIndicator size="small" color="#fff" /> : <Text style={styles.saveBtnText}>Enregistrer</Text>}
                        </TouchableOpacity>
                    </View>
                    <Text style={styles.infoText}>Ce temps sera automatiquement inséré entre vos tâches lors de l'optimisation.</Text>
                </View>
            </View>

            <View style={styles.section}>
                <Text style={styles.sectionTitle}>COMPTE</Text>
                <TouchableOpacity style={styles.logoutButton} onPress={logout}>
                    <Text style={styles.logoutText}>Se déconnecter</Text>
                </TouchableOpacity>
            </View>
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#0f0f1a', padding: 20 },
    profileHeader: { alignItems: 'center', marginTop: 40, marginBottom: 40 },
    avatar: { 
        width: 100, 
        height: 100, 
        borderRadius: 50, 
        backgroundColor: '#6366f1', 
        justifyContent: 'center', 
        alignItems: 'center', 
        marginBottom: 15,
        shadowColor: '#6366f1',
        shadowOffset: { width: 0, height: 10 },
        shadowOpacity: 0.3,
        shadowRadius: 15,
    },
    avatarText: { fontSize: 40, color: '#fff', fontWeight: '900' },
    activeDot: { position: 'absolute', bottom: 5, right: 5, width: 22, height: 22, borderRadius: 11, backgroundColor: '#10b981', borderWidth: 4, borderColor: '#0f0f1a' },
    name: { fontSize: 24, fontWeight: '900', color: '#fff' },
    email: { fontSize: 14, color: 'rgba(255,255,255,0.4)', marginTop: 4 },
    section: { marginBottom: 30 },
    sectionTitle: { fontSize: 11, fontWeight: '800', color: 'rgba(255,255,255,0.3)', letterSpacing: 2, marginBottom: 15, paddingLeft: 5 },
    card: { 
        backgroundColor: 'rgba(255,255,255,0.03)', 
        borderRadius: 20, 
        padding: 20, 
        borderWidth: 1, 
        borderColor: 'rgba(255,255,255,0.06)' 
    },
    label: { fontSize: 13, fontWeight: '700', color: '#fff', marginBottom: 12 },
    inputRow: { flexDirection: 'row', gap: 10 },
    input: { flex: 1, backgroundColor: 'rgba(255,255,255,0.05)', borderRadius: 12, padding: 12, color: '#fff', fontWeight: 'bold' },
    saveBtn: { backgroundColor: '#6366f1', paddingHorizontal: 20, justifyContent: 'center', borderRadius: 12 },
    saveBtnText: { color: '#fff', fontWeight: '800', fontSize: 13 },
    infoText: { fontSize: 12, color: 'rgba(255,255,255,0.3)', marginTop: 12, lineHeight: 18 },
    logoutButton: { 
        backgroundColor: 'rgba(239, 68, 68, 0.1)', 
        padding: 18, 
        borderRadius: 20, 
        alignItems: 'center',
        borderWidth: 1,
        borderColor: 'rgba(239, 68, 68, 0.2)'
    },
    logoutText: { color: '#ef4444', fontSize: 14, fontWeight: '800' }
});
