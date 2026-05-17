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
    container: { flex: 1, backgroundColor: '#F7F7F8' },
    header: { paddingHorizontal: 20, paddingTop: 20, marginBottom: 20 },
    title: { fontSize: 28, fontWeight: '900', color: '#0F0F10', letterSpacing: -0.8 },
    dayGroup: { 
        backgroundColor: '#FFFFFF', 
        padding: 20, 
        borderRadius: 20, 
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
    dayTitle: { fontSize: 16, fontWeight: '800', color: '#0F0F10', marginBottom: 12, letterSpacing: -0.4 },
    block: { 
        backgroundColor: '#F9FAFB', 
        paddingVertical: 10, 
        paddingHorizontal: 12, 
        borderRadius: 12, 
        marginBottom: 8,
        borderWidth: 1,
        borderColor: '#F1F1F2',
        flexDirection: 'row',
        alignItems: 'center'
    },
    blockText: { fontSize: 14, color: '#3A3A3C', fontWeight: '700' },
    emptyText: { fontSize: 13, color: '#AEAeb2', fontStyle: 'italic', fontWeight: '500' }
});
