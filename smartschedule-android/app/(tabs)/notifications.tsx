import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, FlatList, StyleSheet, TouchableOpacity, RefreshControl, SafeAreaView } from 'react-native';
import api from '../../api/axios';
import { Ionicons } from '@expo/vector-icons';

export default function NotificationsScreen() {
    const [notifications, setNotifications] = useState([]);
    const [refreshing, setRefreshing] = useState(false);

    const fetchNotifications = async () => {
        try {
            const response = await api.get('/notifications');
            setNotifications(response.data);
            
            // Marquer comme lu après ouverture
            await api.post('/notifications/mark-read');
        } catch (error) {
            console.error('Error fetching notifications:', error);
        }
    };

    useEffect(() => {
        fetchNotifications();
    }, []);

    const onRefresh = useCallback(() => {
        setRefreshing(true);
        fetchNotifications().then(() => setRefreshing(false));
    }, []);

    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', { 
            day: '2-digit', 
            month: '2-digit', 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    };

    const renderItem = ({ item }) => {
        const data = typeof item.data === 'string' ? JSON.parse(item.data) : item.data;
        
        return (
            <View style={styles.notificationItem}>
                <View style={[styles.iconContainer, { backgroundColor: data.type === 'success' ? '#DCFCE7' : '#F1F5F9' }]}>
                    <Ionicons 
                        name={data.type === 'success' ? "checkmark-circle" : "notifications"} 
                        size={20} 
                        color={data.type === 'success' ? "#16A34A" : "#64748B"} 
                    />
                </View>
                <View style={styles.content}>
                    <Text style={styles.message}>{data.message}</Text>
                    <Text style={styles.time}>{formatDate(item.created_at)}</Text>
                </View>
            </View>
        );
    };

    return (
        <SafeAreaView style={styles.container}>
            <View style={styles.header}>
                <Text style={styles.title}>Notifications</Text>
            </View>
            
            <FlatList
                data={notifications}
                renderItem={renderItem}
                keyExtractor={(item) => item.id.toString()}
                contentContainerStyle={styles.list}
                refreshControl={
                    <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#0F0F10" />
                }
                ListEmptyComponent={
                    <View style={styles.emptyContainer}>
                        <Ionicons name="notifications-off-outline" size={48} color="#D1D1D6" />
                        <Text style={styles.emptyText}>Aucune notification pour le moment</Text>
                    </View>
                }
            />
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F7F7F8' },
    header: { padding: 20, backgroundColor: '#FFFFFF', borderBottomWidth: 1, borderBottomColor: '#E5E5E7' },
    title: { fontSize: 24, fontWeight: '800', color: '#0F0F10', letterSpacing: -0.5 },
    list: { padding: 16 },
    notificationItem: { 
        flexDirection: 'row', 
        backgroundColor: '#FFFFFF', 
        padding: 16, 
        borderRadius: 16, 
        marginBottom: 12,
        borderWidth: 1,
        borderColor: '#E5E5E7',
        alignItems: 'center'
    },
    iconContainer: { width: 40, height: 40, borderRadius: 12, justifyContent: 'center', alignItems: 'center', marginRight: 12 },
    content: { flex: 1 },
    message: { fontSize: 14, fontWeight: '600', color: '#0F0F10', marginBottom: 4 },
    time: { fontSize: 12, color: '#8E8E93' },
    emptyContainer: { alignItems: 'center', justifyContent: 'center', marginTop: 100, gap: 12 },
    emptyText: { color: '#8E8E93', fontSize: 15, fontWeight: '500' }
});
