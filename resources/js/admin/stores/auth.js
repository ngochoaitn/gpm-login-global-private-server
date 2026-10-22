import { defineStore } from 'pinia';
import { http } from '../api/http';

const config = window.__APP_CONFIG__ || {};

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: config.initialUser || null,
        serverVersion: config.serverVersion || '',
        phpVersion: config.phpVersion || '',
    }),
    getters: {
        isAdmin: (state) => state.user?.system_role === 'ADMIN',
        displayName: (state) =>
            state.user?.display_name || state.user?.email || 'Admin',
    },
    actions: {
        async fetchMe() {
            const { data } = await http.get('/me');
            if (data?.success) {
                this.user = data.data.user;
                this.serverVersion = data.data.server_version;
            }
        },
        async logout() {
            try {
                await http.post('/logout');
            } finally {
                window.location.href = '/admin/auth';
            }
        },
    },
});
