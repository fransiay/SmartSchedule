import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, StyleSheet, FlatList, TouchableOpacity, RefreshControl } from 'react-native';
import api from '../../api/axios';

const DAYS = ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];

export default function AvailabilitiesScreen() {
    const [availabilities, setAvailabilities] = useState([]);
    const [refreshing, setRefreshing] = useState(false);

    const fetchAvailabilities = async () => {
        try {
            const { data } = await api.get('/availabilities');
            setAvailabilities(data);
        } catch (e) {
            console.error(e);
        }
    };

    const onRefresh = useCallback(async () => {
        setRefreshing(true);
        await fetchAvailabilities();
        setRefreshing(false);
    }, []);

    useEffect(() => {
        fetchAvailabilities();
    }, []);

    // Grouping
    const grouped = DAYS.map((dayName, index) => {
        return {
            title: dayName,
            data: availabilities.filter((a: any) => a.day_of_week === index)
        };
    });

    return (
        <View style={styles.container}>
            <View style={styles.header}>
                <Text style={styles.title}>Disponibilités</Text>
            </View>

            <FlatList
                data={grouped}
                keyExtractor={(item) => item.title}
                refreshControl={<RefreshControl refreshing={refreshing} onRefresh={onRefresh} />}
                renderItem={({ item }) => (
                    <View style={styles.dayGroup}>
                        <Text style={styles.dayTitle}>{item.title}</Text>
                        {item.data.length === 0 ? (
                            <Text style={styles.emptyText}>Aucune plage (Repos)</Text>
                        ) : (
                            item.data.map((block: any, i: number) => (
                                <View key={i} style={styles.block}>
                                    <Text style={styles.blockText}>
                                        {block.start_time.substring(0,5)} - {block.end_time.substring(0,5)}
                                    </Text>
                                </View>
                            ))
                        )}
                    </View>
                )}
            />
        </View>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, backgroundColor: '#f9fafb', padding: 15 },
    header: { marginBottom: 20 },
    title: { fontSize: 24, fontWeight: 'bold', color: '#111827' },
    dayGroup: { backgroundColor: '#fff', padding: 15, borderRadius: 10, marginBottom: 10, shadowColor: '#000', shadowOpacity: 0.05, shadowRadius: 5, elevation: 2 },
    dayTitle: { fontSize: 16, fontWeight: 'bold', color: '#4f46e5', marginBottom: 10 },
    block: { backgroundColor: '#f3f4f6', padding: 10, borderRadius: 6, marginBottom: 5 },
    blockText: { fontSize: 14, color: '#374151' },
    emptyText: { fontSize: 14, color: '#9ca3af', fontStyle: 'italic' }
});
