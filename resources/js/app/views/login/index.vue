<template>
    <div class="surface-ground flex align-items-center justify-content-center min-h-screen min-w-screen overflow-hidden">
        <div class="flex flex-column align-items-center justify-content-center">
            <img :src="logoUrl" alt="Sakai logo" class="mb-5 w-6rem flex-shrink-0" />
            <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
                <div class="w-full surface-card py-8 px-5 sm:px-8" style="border-radius: 53px">
                    <form @submit.prevent="handleLogin">
                        <div class="text-center mb-5">
                            <img src="" alt="Image" height="50" class="mb-3" />
                            <div class="text-900 text-3xl font-medium mb-3">Welcome, Isabel!</div>
                            <span class="text-600 font-medium">Sign in to continue</span>
                        </div>

                        <div>
                            <label for="email1" class="block text-900 text-xl font-medium mb-2">Email</label>
                            <InputText id="email1" type="text" placeholder="Email address" class="w-full md:w-30rem mb-5" style="padding: 1rem" v-model="loginForm.email" />

                            <label for="password1" class="block text-900 font-medium text-xl mb-2">Password</label>
                            <Password id="password1" v-model="loginForm.password" placeholder="Password" :toggleMask="true" class="w-full mb-3" inputClass="w-full" :inputStyle="{ padding: '1rem' }"></Password>

                            <div class="flex align-items-center justify-content-between mb-5 gap-5">
                                <div class="flex align-items-center">
                                    <Checkbox v-model="checked" id="rememberme1" binary class="mr-2"></Checkbox>
                                    <label for="rememberme1">Remember me</label>
                                </div>
                                <a class="font-medium no-underline ml-2 text-right cursor-pointer" style="color: var(--primary-color)">Forgot password?</a>
                            </div>
                            <Button label="Sign In" class="w-full p-3 text-xl" type="submit"></Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <AppConfig simple />
</template>

<script setup>
import { useLayout } from '@/app/views/layout/composables/layout';
import { userStore } from '@/app/store/user';
import { useRoute, useRouter } from 'vue-router';
import { csrf } from '@/app/api/auth';
import { ref, computed, watch } from 'vue';
import AppConfig from '@/app/views/layout/AppConfig.vue';

const { layoutConfig } = useLayout();
const router = useRouter();
const route = useRoute();
const useUserStore = userStore();

const loginForm = ref({
    email: '',
    password: '',
});
const redirect = ref(undefined);
const otherQuery = ref({});
const checked = ref(false);

const logoUrl = computed(() => {
    return `/layout/images/${layoutConfig.darkTheme.value ? 'logo-white' : 'logo-dark'}.svg`;
});

const handleLogin = async () => {
    try {
        await csrf();
        await useUserStore.login(loginForm.value);
        router.push({ path: redirect.value || '/intranet', query: otherQuery.value });
    } catch (error) {
        console.error('Login failed', error);
    }
};

const getOtherQuery = (query) => {
    return Object.keys(query).reduce((acc, cur) => {
        if (cur !== 'redirect') {
            acc[cur] = query[cur];
        }
        return acc;
    }, {});
};

watch(() => route.query, (query) => {
    if (query) {
        redirect.value = query.redirect;
        otherQuery.value = getOtherQuery(query);
    }
}, { immediate: true });
</script>

<style scoped>
.pi-eye {
    transform: scale(1.6);
    margin-right: 1rem;
}

.pi-eye-slash {
    transform: scale(1.6);
    margin-right: 1rem;
}
</style>
