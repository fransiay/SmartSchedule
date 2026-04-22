import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, StyleSheet, TouchableOpacity, ScrollView, ActivityIndicator } from 'react-native';
import { useRouter } from 'expo-router';
import api from '../api/axios';

interface TaskFormProps {
    task?: any;
    onSuccess: () => void;
}

export default function TaskForm({ task, onSuccess }: TaskFormProps) {
    const router = useRouter();
    const [title, setTitle] = useState(task?.title || '');
    const [description, setDescription] = useState(task?.description || '');
    const [duration, setDuration] = useState(task?.duration_minutes?.toString() || '60');
    const [priority, setPriority] = useState(task?.priority || 3);
    const [deadline, setDeadline] = useState(task?.deadline ? task.deadline.split('T')[0] : new Date().toISOString().split('T')[0]);
    const [categories, setCategories] = useState([]);
    const [categoryId, setCategoryId] = useState(task?.category_id || '');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        fetchCategories();
    }, []);

    const fetchCategories = async () => {
        try {
            const { data } = await api.get('/categories');
            setCategories(data);
        } catch (e) { console.error(e); }
    };

    const handleSave = async () => {
        if (!title) { setError('Le titre est obligatoire'); return; }
        setLoading(true);
        setError('');

        const payload = {
            title,
            description,
            duration_minutes: parseInt(duration),
            priority,
            deadline: deadline + ' 00:00:00',
            category_id: categoryId || null,
            status: task?.status || 'todo'
        };

        try {
            if (task?.id) {
                await api.put(`/tasks/${task.id}`, payload);
            } else {
                await api.post('/tasks', payload);
            }
            onSuccess();
        } catch (e: any) {
            setError(e.response?.data?.message || 'Une erreur est survenue');
        } finally {
            setLoading(false);
        }
    };

    return (
        <ScrollView style={styles.container}>
            <Text style={styles.label}>Titre *</Text>
            <TextInput style={styles.input} value={title} onChangeText={setTitle} placeholder="Ex: Réviser l'examen" placeholderTextColor="#9ca3af" />

            <Text style={styles.label}>Description</Text>
            <TextInput style={[styles.input, styles.textArea]} value={description} onChangeText={setDescription} placeholder="Détails de la tâche..." placeholderTextColor="#9ca3af" multiline numberOfLines={3} />

            <View style={styles.row}>
                <View style={styles.flex1}>
                    <Text style={styles.label}>Durée (min) *</Text>
                    <TextInput style={styles.input} value={duration} onChangeText={setDuration} keyboardType="numeric" />
                </View>
                <View style={styles.flex1}>
                    <Text style={styles.label}>Priorité (1-5)</Text>
                    <View style={styles.prioritySelector}>
                        {[1, 2, 3, 4, 5].map(p => (
                            <TouchableOpacity key={p} onPress={() => setPriority(p)} style={[styles.priorityBtn, priority === p && styles.priorityBtnActive]}>
                                <Text style={[styles.priorityBtnText, priority === p && styles.priorityBtnTextActive]}>{p}</Text>
                            </TouchableOpacity>
                        ))}
                    </View>
                </View>
            </View>

            <Text style={styles.label}>Deadline (AAAA-MM-JJ) *</Text>
            <TextInput style={styles.input} value={deadline} onChangeText={setDeadline} placeholder="YYYY-MM-DD" placeholderTextColor="#9ca3af" />

            <Text style={styles.label}>Catégorie</Text>
            <View style={styles.categoryList}>
                <TouchableOpacity onPress={() => setCategoryId('')} style={[styles.categoryBtn, !categoryId && styles.categoryBtnActive]}>
                    <Text style={[styles.categoryBtnText, !categoryId && styles.categoryBtnTextActive]}>Aucune</Text>
                </TouchableOpacity>
                {categories.map((cat: any) => (
                    <TouchableOpacity key={cat.id} onPress={() => setCategoryId(cat.id)} style={[styles.categoryBtn, categoryId === cat.id && { backgroundColor: cat.color || '#4f46e5', borderColor: 'transparent' }]}>
                        <Text style={[styles.categoryBtnText, categoryId === cat.id && { color: '#fff' }]}>{cat.name}</Text>
                    </TouchableOpacity>
                ))}
            </View>

            {error ? <Text style={styles.errorText}>{error}</Text> : null}

            <TouchableOpacity style={styles.submitBtn} onPress={handleSave} disabled={loading}>
                {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>{task?.id ? 'Modifier' : 'Créer'} la tâche</Text>}
            </TouchableOpacity>

            <TouchableOpacity style={styles.cancelBtn} onPress={() => onSuccess()}>
                <Text style={styles.cancelBtnText}>Annuler</Text>
            </TouchableOpacity>
            <View style={{ height: 40 }} />
        </ScrollView>
    );
}

const styles = StyleSheet.create({
    container: { flex: 1, padding: 20 },
    label: { fontSize: 13, fontWeight: '700', color: '#9ca3af', marginBottom: 8, textTransform: 'uppercase', letterSpacing: 1 },
    input: { backgroundColor: '#1f2937', color: '#fff', borderRadius: 10, padding: 12, marginBottom: 20, borderWidth: 1, borderColor: '#374151' },
    textArea: { height: 80, textAlignVertical: 'top' },
    row: { flexDirection: 'row', gap: 15, marginBottom: 5 },
    flex1: { flex: 1 },
    prioritySelector: { flexDirection: 'row', gap: 5 },
    priorityBtn: { flex: 1, height: 44, borderRadius: 8, backgroundColor: '#1f2937', justifyContent: 'center', alignItems: 'center', borderWidth: 1, borderColor: '#374151' },
    priorityBtnActive: { backgroundColor: '#4f46e5', borderColor: '#6366f1' },
    priorityBtnText: { color: '#9ca3af', fontWeight: 'bold' },
    priorityBtnTextActive: { color: '#fff' },
    categoryList: { flexDirection: 'row', flexWrap: 'wrap', gap: 8, marginBottom: 25 },
    categoryBtn: { paddingHorizontal: 12, paddingVertical: 6, borderRadius: 20, borderWidth: 1, borderColor: '#374151', backgroundColor: '#1f2937' },
    categoryBtnActive: { backgroundColor: '#4f46e5', borderColor: '#6366f1' },
    categoryBtnText: { color: '#9ca3af', fontSize: 12, fontWeight: '600' },
    categoryBtnTextActive: { color: '#fff' },
    submitBtn: { backgroundColor: '#4f46e5', padding: 16, borderRadius: 12, alignItems: 'center', marginTop: 10 },
    submitBtnText: { color: '#fff', fontWeight: '800', fontSize: 16 },
    cancelBtn: { padding: 16, alignItems: 'center', marginTop: 10 },
    cancelBtnText: { color: '#9ca3af', fontWeight: '600' },
    errorText: { color: '#f87171', marginBottom: 15, textAlign: 'center' }
});
