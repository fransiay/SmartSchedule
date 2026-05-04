import { Tabs } from 'expo-router';
import React from 'react';
import { Platform } from 'react-native';

import { Ionicons } from '@expo/vector-icons';

export default function TabLayout() {

  return (
    <Tabs
      screenOptions={{
        tabBarActiveTintColor: '#0F0F10',
        tabBarInactiveTintColor: '#AEAeb2',
        headerShown: true,
        headerStyle: {
          backgroundColor: '#F7F7F8',
          elevation: 0,
          shadowOpacity: 0,
          borderBottomWidth: 1,
          borderBottomColor: '#E5E5E7',
        },
        headerTitleStyle: {
          fontWeight: '800',
          color: '#0F0F10',
          fontSize: 18,
        },
        tabBarStyle: Platform.select({
          ios: {
            position: 'absolute',
            borderTopColor: '#E5E5E7',
            backgroundColor: '#FFFFFF',
          },
          default: {
            backgroundColor: '#FFFFFF',
            borderTopColor: '#E5E5E7',
            height: 60,
            paddingBottom: 8,
          },
        }),
      }}>
      <Tabs.Screen
        name="index"
        options={{
          title: 'Planning',
          tabBarIcon: ({ color }) => <Ionicons size={28} name="calendar" color={color} />,
        }}
      />
      <Tabs.Screen
        name="tasks"
        options={{
          title: 'Tâches',
          tabBarIcon: ({ color }) => <Ionicons size={28} name="list" color={color} />,
        }}
      />
      <Tabs.Screen
        name="notifications"
        options={{
          title: 'Alertes',
          tabBarIcon: ({ color }) => <Ionicons size={28} name="notifications" color={color} />,
        }}
      />
      <Tabs.Screen
        name="availabilities"
        options={{
          title: 'Dispo',
          tabBarIcon: ({ color }) => <Ionicons size={28} name="time" color={color} />,
        }}
      />
      <Tabs.Screen
        name="profile"
        options={{
          title: 'Profil',
          tabBarIcon: ({ color }) => <Ionicons size={28} name="person" color={color} />,
        }}
      />
    </Tabs>
  );
}
