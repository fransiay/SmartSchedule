import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, RefreshControl, ActivityIndicator, TouchableOpacity } from 'react-native';
import api from '../../api/axios';

export default function ScheduleScreen() {
    const [schedules, setSchedules] = useState([]);
    const [refreshing, setRefreshing] = useState(false);
    const [generating, setGenerating] = useState(false);

    const fetchSchedules = async () => {
        try {
            const { data } = await api.get('/schedule');
            setSchedules(Array.isArray(data) ? data : []);
        } catch (e) {
            console.error(e);
        }
    };

    const onRefresh = useCallback(async () => {
        setRefreshing(true);
        await fetchSchedules();
        setRefreshing(false);
    }, []);

    useEffect(() => {
        fetchSchedules();
    }, []);

    const generateSchedule = async () => {
        setGenerating(true);
        try {
            const { data } = await api.post('/schedule/generate');
            setSchedules(data.schedules || []);
            alert('Planning généré avec succès !');
        } catch (e) {
            alert('Erreur lors de la génération du planning');
        } finally {
            setGenerating(false);
        }
    };

    const formatTime = (isoString: string) => {
        if (!isoString) return '';
        const d = new Date(isoString);
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    };

    const formatDate = (isoString: string) => {
        if (!isoString) return '';
        const d = new Date(isoString);
        return d.toLocaleDateString([], { weekday: 'long', day: 'numeric', month: 'long' });
    };

    return (
        <View style={styles.container}>
            <View style={styles.header}>
                <View>
                    <Text style={styles.title}>Planning</Text>
                    <Text style={styles.subtitle}>Votre emploi du temps optimisé</Text>
                </View>
                <TouchableOpacity style={styles.generateButton} onPress={generateSchedule} disabled={generating}>
                    {generating ? <ActivityIndicator color="#fff" size="small" /> : <Text style={styles.buttonText}>Optimiser</Text>}
                </TouchableOpacity>
            </View>

            <FlatList
                data={schedules}
                keyExtractor={(item: any) => item.id.toString()}
                refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor="#6366f1" />}
                ListEmptyComponent={<Text style={styles.emptyText}>Rien de prévu pour le moment. Cliquez sur "Optimiser".</Text>}
                contentContainerStyle={{ paddingBottom: 20 }}
                renderItem={({ item }) => (
                    <View style={styles.card}>
                        <View style={styles.timeBlock}>
                            <Text style={styles.timeText}>{formatTime(item.start_time)}</Text>
                            <View style={styles.timeDivider} />
                            <Text style={styles.timeTextEnd}>{formatTime(item.end_time)}</Text>
                        </View>
                        <View style={styles.taskContent}>
                            <Text style={styles.dateLabel}>{formatDate(item.start_time)}</Text>
                            <Text style={styles.taskTitle}>{item.task?.title || 'Tâche'}</Text>
                            <View style={styles.metaRow}>
                                <View style={[styles.priorityBadge, { backgroundColor: item.task?.priority >= 4 ? 'rgba(239, 68, 68, 0.1)' : 'rgba(16, 185, 129, 0.1)' }]}>
                                    <Text style={[styles.priorityBadgeText, { color: item.task?.priority >= 4 ? '#f87171' : '#34d399' }]}>P{item.task?.priority}</Text>
                                </View>
                                {item.task?.category && (
                                    <Text style={styles.catName}>• {item.task.category.name}</Text>
                                )}
                            </View>
                        </View>
                    </View>
                )}
            />
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#F7F7F8' },
    header: { paddingHorizontal: 20, paddingTop: 20, paddingBottom: 10, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 20 },
    title: { fontSize: 28, fontWeight: '900', color: '#0F0F10', letterSpacing: -0.8 },
    subtitle: { fontSize: 13, color: '#6C6C70', marginTop: 2, fontWeight: '500' },
    generateButton: { 
        backgroundColor: '#0F0F10', 
        paddingHorizontal: 16, 
        paddingVertical: 10, 
        borderRadius: 10,
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 2 },
        shadowOpacity: 0.1,
        shadowRadius: 4,
        elevation: 2
    },
    buttonText: { color: '#fff', fontWeight: '700', fontSize: 13 },
    emptyText: { textAlign: 'center', marginTop: 100, color: '#AEAeb2', fontSize: 15, paddingHorizontal: 40, lineHeight: 22 },
    card: { 
        flexDirection: 'row', 
        backgroundColor: '#FFFFFF', 
        borderRadius: 16, 
        marginHorizontal: 20,
        marginBottom: 12, 
        borderWidth: 1, 
        borderColor: '#E5E5E7',
        overflow: 'hidden',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 1 },
        shadowOpacity: 0.03,
        shadowRadius: 2,
    },
    timeBlock: { 
        width: 80, 
        backgroundColor: '#F7F7F8', 
        justifyContent: 'center', 
        alignItems: 'center',
        paddingVertical: 15,
        borderRightWidth: 1,
        borderRightColor: '#E5E5E7'
    },
    timeText: { color: '#0F0F10', fontSize: 14, fontWeight: '800' },
    timeTextEnd: { color: '#AEAeb2', fontSize: 12, fontWeight: '600' },
    timeDivider: { width: 1, height: 10, backgroundColor: '#E5E5E7', marginVertical: 4 },
    taskContent: { flex: 1, padding: 15, justifyContent: 'center' },
    dateLabel: { fontSize: 10, color: '#AEAeb2', fontWeight: '800', textTransform: 'uppercase', marginBottom: 4 },
    taskTitle: { fontSize: 16, fontWeight: '700', color: '#0F0F10' },
    metaRow: { flexDirection: 'row', alignItems: 'center', marginTop: 6, gap: 8 },
    priorityBadge: { paddingHorizontal: 6, paddingVertical: 2, borderRadius: 6 },
    priorityBadgeText: { fontSize: 10, fontWeight: '900' },
    catName: { fontSize: 12, color: '#6C6C70', fontWeight: '600' }
});
