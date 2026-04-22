import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, RefreshControl, Alert } from 'react-native';
import { useRouter } from 'expo-router';
import api from '../../api/axios';

export default function TasksScreen() {
    const router = useRouter();
    const [tasks, setTasks] = useState([]);
    const [refreshing, setRefreshing] = useState(false);

    const fetchTasks = async () => {
        try {
            const { data } = await api.get('/tasks');
            setTasks(Array.isArray(data) ? data : []);
        } catch (e) {
            console.error(e);
        }
    };

    const onDelete = (id: number) => {
        Alert.alert('Supprimer', 'Voulez-vous supprimer cette tâche ?', [
            { text: 'Annuler', style: 'cancel' },
            { text: 'Supprimer', style: 'destructive', onPress: async () => {
                await api.delete(`/tasks/${id}`);
                fetchTasks();
            }}
        ]);
    };

    const onRefresh = useCallback(async () => {
        setRefreshing(true);
        await fetchTasks();
        setRefreshing(false);
    }, []);

    useEffect(() => {
        fetchTasks();
    }, []);

    return (
        <View style={styles.container}>
            <View style={styles.header}>
                <Text style={styles.title}>Mes Tâches</Text>
            </View>

            <FlatList
                data={tasks}
                keyExtractor={(item: any) => item.id.toString()}
                refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#6366f1" />}
                ListEmptyComponent={<Text style={styles.emptyText}>Aucune tâche pour le moment.</Text>}
                contentContainerStyle={{ paddingBottom: 100 }}
                renderItem={({ item }: { item: any }) => (
                    <View style={styles.card}>
                        <View style={styles.cardHeader}>
                            <View style={styles.titleRow}>
                                <View style={[styles.priorityDot, { backgroundColor: item.priority >= 4 ? '#ef4444' : (item.priority === 3 ? '#fbbf24' : '#10b981') }]} />
                                <Text style={styles.taskTitle} numberOfLines={1}>{item.title}</Text>
                            </View>
                            <TouchableOpacity onPress={() => onDelete(item.id)}>
                                <Text style={styles.deleteBtn}>✕</Text>
                            </TouchableOpacity>
                        </View>
                        
                        <View style={styles.detailsRow}>
                            <View style={styles.detailItem}>
                                <Text style={styles.detailLabel}>DURÉE</Text>
                                <Text style={styles.detailValue}>{item.duration_minutes} min</Text>
                            </View>
                            <View style={styles.detailItem}>
                                <Text style={styles.detailLabel}>DEADLINE</Text>
                                <Text style={styles.detailValue}>{new Date(item.deadline).toLocaleDateString()}</Text>
                            </View>
                            <View style={styles.detailItem}>
                                <Text style={styles.detailLabel}>STATUT</Text>
                                <Text style={[styles.detailValue, { color: item.status === 'done' ? '#10b981' : '#818cf8' }]}>
                                    {item.status.toUpperCase()}
                                </Text>
                            </View>
                        </View>
                        
                        {item.category && (
                            <View style={styles.categoryBadge}>
                                <View style={[styles.catDot, { backgroundColor: item.category.color || '#6366f1' }]} />
                                <Text style={styles.catName}>{item.category.name}</Text>
                            </View>
                        )}
                    </View>
                )}
            />

            <TouchableOpacity style={styles.fab} onPress={() => router.push('/modal')}>
                <Text style={styles.fabText}>+</Text>
            </TouchableOpacity>
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F7F7F8' },
    header: { paddingHorizontal: 20, paddingTop: 20, marginBottom: 20 },
    title: { fontSize: 28, fontWeight: '900', color: '#0F0F10', letterSpacing: -0.8 },
    emptyText: { textAlign: 'center', marginTop: 100, color: '#AEAeb2', fontSize: 16 },
    card: { 
        backgroundColor: '#FFFFFF', 
        borderRadius: 20, 
        padding: 20, 
        marginHorizontal: 20,
        marginBottom: 16, 
        borderWidth: 1, 
        borderColor: '#E5E5E7',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.03,
        shadowRadius: 4,
        elevation: 2
    },
    cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 15 },
    titleRow: { flexDirection: 'row', alignItems: 'center', flex: 1, gap: 10 },
    priorityDot: { width: 8, height: 8, borderRadius: 4 },
    taskTitle: { fontSize: 18, fontWeight: '800', color: '#0F0F10', letterSpacing: -0.4 },
    deleteBtn: { color: '#AEAeb2', fontSize: 18, fontWeight: '600', padding: 5 },
    detailsRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15 },
    detailItem: { gap: 4 },
    detailLabel: { fontSize: 10, fontWeight: '800', color: '#AEAeb2', letterSpacing: 1 },
    detailValue: { fontSize: 13, color: '#3A3A3C', fontWeight: '700' },
    categoryBadge: { 
        flexDirection: 'row', 
        alignItems: 'center', 
        alignSelf: 'flex-start',
        backgroundColor: '#F7F7F8', 
        paddingHorizontal: 10, 
        paddingVertical: 4, 
        borderRadius: 20,
        gap: 6,
        borderWidth: 1,
        borderColor: '#E5E5E7'
    },
    catDot: { width: 6, height: 6, borderRadius: 3 },
    catName: { fontSize: 11, fontWeight: '700', color: '#6C6C70' },
    fab: { 
        position: 'absolute', 
        bottom: 30, 
        right: 30, 
        width: 60, 
        height: 60, 
        borderRadius: 30, 
        backgroundColor: '#0F0F10', 
        justifyContent: 'center', 
        alignItems: 'center',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.2,
        shadowRadius: 10,
        elevation: 5,
    },
    fabText: { color: '#fff', fontSize: 32, fontWeight: '300' }
});
