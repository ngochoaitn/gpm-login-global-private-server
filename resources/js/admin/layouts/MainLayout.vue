<template>
    <el-container class="app-layout">
        <el-aside
            class="app-sidebar app-sidebar--desktop"
            :class="{ 'app-sidebar--collapsed': sidebarCollapsed }"
        >
            <div class="app-brand">
                <div class="app-brand-icon">G</div>
                <div v-if="!sidebarCollapsed" style="flex: 1; min-width: 0">
                    <div>{{ t('app.title') }}</div>
                    <div class="app-version-chip" style="margin-top: 2px; display: inline-block">
                        v{{ authStore.serverVersion }}
                    </div>
                    <div
                        v-if="authStore.phpVersion"
                        class="app-version-chip"
                        style="margin-top: 2px; margin-left: 4px; display: inline-block"
                    >
                        PHP {{ authStore.phpVersion }}
                    </div>
                </div>
            </div>

            <el-menu
                :default-active="activeMenu"
                :collapse="sidebarCollapsed"
                :collapse-transition="false"
                router
                class="app-menu"
                background-color="transparent"
                text-color="rgba(255,255,255,0.85)"
                active-text-color="#ffffff"
            >
                <el-menu-item
                    v-for="item in menuItems"
                    :key="item.path"
                    :index="item.path"
                >
                    <el-icon><component :is="item.icon" /></el-icon>
                    <template #title>{{ t(item.labelKey) }}</template>
                </el-menu-item>
            </el-menu>

            <div style="flex: 1"></div>

            <div class="app-sidebar-footer">
                <div v-if="!sidebarCollapsed" style="font-size: 12px; opacity: 0.75; margin-bottom: 8px">
                    {{ authStore.displayName }}
                </div>
                <el-tooltip
                    :content="t('app.logout')"
                    placement="right"
                    :disabled="!sidebarCollapsed"
                >
                    <el-button size="small" style="width: 100%" @click="handleLogout">
                        <el-icon :style="sidebarCollapsed ? '' : 'margin-right: 4px'">
                            <SwitchButton />
                        </el-icon>
                        <span v-if="!sidebarCollapsed">{{ t('app.logout') }}</span>
                    </el-button>
                </el-tooltip>
            </div>
        </el-aside>

        <el-drawer
            v-model="drawerOpen"
            direction="ltr"
            :with-header="false"
            size="224px"
            class="app-sidebar-drawer"
        >
            <div class="app-sidebar app-sidebar--drawer">
                <div class="app-brand">
                    <div class="app-brand-icon">G</div>
                    <div style="flex: 1">
                        <div>{{ t('app.title') }}</div>
                        <div class="app-version-chip" style="margin-top: 2px; display: inline-block">
                            v{{ authStore.serverVersion }}
                        </div>
                        <div
                            v-if="authStore.phpVersion"
                            class="app-version-chip"
                            style="margin-top: 2px; margin-left: 4px; display: inline-block"
                        >
                            PHP {{ authStore.phpVersion }}
                        </div>
                    </div>
                </div>

                <el-menu
                    :default-active="activeMenu"
                    router
                    class="app-menu"
                    background-color="transparent"
                    text-color="rgba(255,255,255,0.85)"
                    active-text-color="#ffffff"
                    @select="drawerOpen = false"
                >
                    <el-menu-item
                        v-for="item in menuItems"
                        :key="item.path"
                        :index="item.path"
                    >
                        <el-icon><component :is="item.icon" /></el-icon>
                        <span>{{ t(item.labelKey) }}</span>
                    </el-menu-item>
                </el-menu>

                <div style="flex: 1"></div>

                <div style="padding: 12px 16px; border-top: 1px solid rgba(255, 255, 255, 0.08)">
                    <div style="font-size: 12px; opacity: 0.75; margin-bottom: 8px">
                        {{ authStore.displayName }}
                    </div>
                    <el-button size="small" style="width: 100%" @click="handleLogout">
                        <el-icon style="margin-right: 4px"><SwitchButton /></el-icon>
                        {{ t('app.logout') }}
                    </el-button>
                </div>
            </div>
        </el-drawer>

        <el-container>
            <el-header class="app-header">
                <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1">
                    <el-button
                        class="app-header-burger"
                        text
                        size="large"
                        @click="toggleSidebar"
                    >
                        <el-icon :size="20">
                            <Fold v-if="!sidebarCollapsed" />
                            <Expand v-else />
                        </el-icon>
                    </el-button>
                    <div class="app-header-title">
                        {{ currentPageTitle }}
                    </div>
                </div>
                <div class="app-header-actions">
                    <el-dropdown
                        trigger="click"
                        class="app-header-locale"
                        @command="onLocaleChange"
                    >
                        <el-button size="small" plain class="app-header-locale-btn">
                            <el-icon class="app-header-locale-globe">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M2 12h20" />
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                                </svg>
                            </el-icon>
                            <span class="app-header-locale-label">{{ currentLocaleLabel }}</span>
                            <el-icon class="app-header-locale-caret"><ArrowDown /></el-icon>
                        </el-button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item
                                    v-for="opt in localeOptions"
                                    :key="opt.value"
                                    :command="opt.value"
                                    :class="{ 'is-active-locale': locale === opt.value }"
                                >
                                    <span style="min-width: 16px; display: inline-block">
                                        <el-icon v-if="locale === opt.value"><Check /></el-icon>
                                    </span>
                                    {{ opt.label }}
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>

                    <el-button
                        type="primary"
                        plain
                        size="small"
                        class="app-header-switch-ui"
                        @click="switchToOldUi"
                    >
                        <el-icon style="margin-right: 4px"><Monitor /></el-icon>
                        <span class="app-header-switch-ui-label">{{ t('app.switchToOldUi') }}</span>
                    </el-button>
                </div>
            </el-header>

            <el-main class="app-main">
                <router-view v-slot="{ Component }">
                    <transition name="el-fade-in-linear" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </el-main>
        </el-container>
    </el-container>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { ElMessage } from 'element-plus';
import { useAuthStore } from '../stores/auth';

const { t, locale } = useI18n();
const route = useRoute();
const authStore = useAuthStore();

const config = window.__APP_CONFIG__ || {};

const savedLocale = localStorage.getItem('admin_locale');
if (savedLocale) locale.value = savedLocale;

// SQL Console is dangerous — hidden by default, revealed only when the
// admin explicitly presses Ctrl/Cmd+Shift+A. Reset on every page load.
const showAdvanced = ref(false);

function handleAdvancedShortcut(e) {
    if (!(e.ctrlKey || e.metaKey) || !e.shiftKey) return;
    if (e.key !== 'A' && e.key !== 'a') return;
    e.preventDefault();
    showAdvanced.value = !showAdvanced.value;
    ElMessage({
        message: showAdvanced.value
            ? t('menu.sqlConsoleRevealed')
            : t('menu.sqlConsoleHidden'),
        type: showAdvanced.value ? 'warning' : 'info',
        duration: 1500,
    });
}

onMounted(() => window.addEventListener('keydown', handleAdvancedShortcut));
onBeforeUnmount(() => window.removeEventListener('keydown', handleAdvancedShortcut));

const drawerOpen = ref(false);
watch(() => route.path, () => (drawerOpen.value = false));

const sidebarCollapsed = ref(localStorage.getItem('admin_sidebar_collapsed') === '1');
const isMobileViewport = window.matchMedia('(max-width: 768px)');

function toggleSidebar() {
    if (isMobileViewport.matches) {
        drawerOpen.value = true;
        return;
    }
    sidebarCollapsed.value = !sidebarCollapsed.value;
    localStorage.setItem('admin_sidebar_collapsed', sidebarCollapsed.value ? '1' : '0');
}

const menuItems = computed(() => {
    const items = [
        { path: '/admin/app/system', icon: 'Setting', labelKey: 'menu.systemSettings' },
        { path: '/admin/app/users', icon: 'User', labelKey: 'menu.users' },
        { path: '/admin/app/groups', icon: 'Collection', labelKey: 'menu.groups' },
        { path: '/admin/app/profiles', icon: 'UserFilled', labelKey: 'menu.profiles' },
        { path: '/admin/app/proxies', icon: 'Connection', labelKey: 'menu.proxies' },
        { path: '/admin/app/logs', icon: 'Document', labelKey: 'menu.logs' },
        { path: '/admin/app/system-logs', icon: 'Tickets', labelKey: 'menu.systemLogs' },
    ];
    if (showAdvanced.value) {
        items.push({ path: '/admin/app/sql', icon: 'Operation', labelKey: 'menu.sqlConsole' });
    }
    return items;
});

const activeMenu = computed(() => route.path);

const localeOptions = [
    { value: 'vi', label: 'Tiếng Việt', short: 'VI' },
    { value: 'en', label: 'English', short: 'EN' },
    { value: 'zh', label: '中文', short: '中' },
];

const currentLocaleLabel = computed(() => {
    const opt = localeOptions.find((o) => o.value === locale.value);
    return opt ? opt.label : locale.value;
});

const currentPageTitle = computed(() => {
    const key = route.meta?.titleKey;
    return key ? t(key) : '';
});

function onLocaleChange(value) {
    localStorage.setItem('admin_locale', value);
    // Reload so Element Plus locale picks up the change.
    window.location.reload();
}

function switchToOldUi() {
    window.location.href = config.legacyAdminUrl || '/admin';
}

function handleLogout() {
    authStore.logout();
}
</script>

<style scoped>
.app-menu {
    margin-top: 8px;
}
</style>
