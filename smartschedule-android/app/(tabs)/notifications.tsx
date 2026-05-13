import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, FlatList, StyleSheet, TouchableOpacity, RefreshControl, SafeAreaView, ActivityIndicator } from 'react-native';
import api from '../../api/axios';
import { Ionicons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';

export default function NotificationsScreen() {
    const [notifications, setNotifications] = useState([]);
    const [refreshing, setRefreshing] = useState(false);
    const [loading, setLoading] = useState(true);
    const router = useRouter();

    const fetchNotifications = async () => {
        try {
            const response = await api.get('/notifications');
            setNotifications(response.data);
        } catch (error) {
            console.error('Error fetching notifications:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchNotifications();
    }, []);

    const onRefresh = useCallback(() => {
        setRefreshing(true);
        fetchNotifications().then(() => setRefreshing(false));
    }, []);

    const markAllRead = async () => {
        try {
            await api.post('/notifications/mark-read');
            // Update local state
            setNotifications(notifications.map(n => ({ ...n, read_at: new Date().toISOString() })));
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    };

    const handlePress = async (notification) => {
        // Mark as read locally and remotely if not already read
        if (!notification.read_at) {
            try {
                await api.post(`/notifications/${notification.id}/read`);
                setNotifications(notifications.map(n => 
                    n.id === notification.id ? { ...n, read_at: new Date().toISOString() } : n
                ));
            } catch (error) {
                console.error('Error marking notification as read:', error);
            }
        }
        
        // Navigate
        const data = typeof notification.data === 'string' ? JSON.parse(notification.data) : notification.data;
        if (data && data.action) {
            // For now, redirect to tasks
            router.push('/(tabs)');
        }
    };

    const timeAgo = (dateString) => {
        if (!dateString) return '';
        const now = new Date();
        const d = new Date(dateString);
        const diffS = Math.floor((now.getTime() - d.getTime()) / 1000);
        
        if (diffS < 60) return "à l'instant";
        if (diffS < 3600) return Math.floor(diffS / 60) + ' min';
        if (diffS < 86400) return Math.floor(diffS / 3600) + ' h';
        
        const days = Math.floor(diffS / 86400);
        if (days === 1) return 'hier';
        if (days < 7) return days + ' j';
        
        return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
    };

    const getTypeConfig = (type) => {
        switch (type) {
            case 'success': return { icon: 'checkmark-circle', color: '#16A34A', bg: '#DCFCE7' };
            case 'error':   return { icon: 'alert-circle', color: '#E11D48', bg: '#FFE4E6' };
            case 'warning': return { icon: 'warning', color: '#D97706', bg: '#FEF3C7' };
            case 'info':    return { icon: 'information-circle', color: '#3B82F6', bg: '#DBEAFE' };
            default:        return { icon: 'notifications', color: '#64748B', bg: '#F1F5F9' };
        }
    };

    const renderItem = ({ item }) => {
        const data = typeof item.data === 'string' ? JSON.parse(item.data) : (item.data || {});
        const config = getTypeConfig(data.type);
        const isUnread = !item.read_at;
        
        return (
            <TouchableOpacity 
                style={[styles.notificationItem, isUnread && styles.notificationItemUnread]} 
                onPress={() => handlePress(item)}
                activeOpacity={0.7}
            >
                <View style={[styles.iconContainer, { backgroundColor: config.bg }]}>
                    <Ionicons name={config.icon as any} size={20} color={config.color} />
                </View>
                <View style={styles.content}>
                    <Text style={[styles.message, isUnread && styles.messageUnread]}>
                        {data.message || 'Nouvelle notification'}
                    </Text>
                    <View style={styles.timeRow}>
                        <Text style={styles.time}>{timeAgo(item.created_at)}</Text>
                        {data.action && (
                            <Text style={styles.actionText}>→ Voir</Text>
                        )}
                    </View>
                </View>
                {isUnread && <View style={styles.unreadDot} />}
            </TouchableOpacity>
        );
    };

    const unreadCount = notifications.filter(n => !n.read_at).length;

    return (
        <SafeAreaView style={styles.container}>
            <View style={styles.header}>
                <View style={styles.headerTitleRow}>
                    <Text style={styles.title}>Notifications</Text>
                    {unreadCount > 0 && (
                        <View style={styles.badge}>
                            <Text style={styles.badgeText}>{unreadCount}</Text>
                        </View>
                    )}
                </View>
                {unreadCount > 0 && (
                    <TouchableOpacity onPress={markAllRead}>
                        <Text style={styles.markReadText}>Tout lire</Text>
                    </TouchableOpacity>
                )}
            </View>
            
            {loading ? (
                <View style={styles.centerContainer}>
                    <ActivityIndicator size="large" color="#0F0F10" />
                </View>
            ) : (
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
                            <View style={styles.emptyIconBg}>
                                <Ionicons name="notifications-off" size={32} color="#D1D1D6" />
                            </View>
                            <Text style={styles.emptyTitle}>Tout est à jour !</Text>
                            <Text style={styles.emptyText}>Vous n'avez aucune notification.</Text>
                        </View>
                    }
                />
            )}
        </SafeAreaView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F7F7F8' },
    centerContainer: { flex: 1, justifyContent: 'center', alignItems: 'center' },
    header: { 
        padding: 20, 
        backgroundColor: '#FFFFFF', 
        borderBottomWidth: 1, 
        borderBottomColor: '#E5E5E7',
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center'
    },
    headerTitleRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
    title: { fontSize: 24, fontWeight: '800', color: '#0F0F10', letterSpacing: -0.5 },
    badge: { backgroundColor: '#FEE2E2', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 12 },
    badgeText: { color: '#E11D48', fontSize: 12, fontWeight: '800' },
    markReadText: { color: '#3B82F6', fontSize: 14, fontWeight: '700' },
    list: { padding: 16 },
    notificationItem: { 
        flexDirection: 'row', 
        backgroundColor: '#FFFFFF', 
        padding: 16, 
        borderRadius: 16, 
        marginBottom: 12,
        borderWidth: 1,
        borderColor: '#E5E5E7',
        alignItems: 'flex-start'
    },
    notificationItemUnread: {
        backgroundColor: '#F8FAFC',
        borderColor: '#E2E8F0',
    },
    iconContainer: { width: 40, height: 40, borderRadius: 12, justifyContent: 'center', alignItems: 'center', marginRight: 12 },
    content: { flex: 1 },
    message: { fontSize: 14, fontWeight: '500', color: '#333333', marginBottom: 6, lineHeight: 20 },
    messageUnread: { fontWeight: '700', color: '#0F0F10' },
    timeRow: { flexDirection: 'row', alignItems: 'center', gap: 8 },
    time: { fontSize: 12, color: '#8E8E93', fontWeight: '500' },
    actionText: { fontSize: 11, fontWeight: '700', color: '#3B82F6' },
    unreadDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: '#3B82F6', marginTop: 6, marginLeft: 8 },
    emptyContainer: { alignItems: 'center', justifyContent: 'center', marginTop: 100 },
    emptyIconBg: { width: 64, height: 64, borderRadius: 32, backgroundColor: '#F3F3F4', justifyContent: 'center', alignItems: 'center', marginBottom: 16 },
    emptyTitle: { color: '#0F0F10', fontSize: 18, fontWeight: '700', marginBottom: 4 },
    emptyText: { color: '#8E8E93', fontSize: 14, fontWeight: '500', textAlign: 'center' }
});
