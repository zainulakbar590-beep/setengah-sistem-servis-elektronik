<script setup>
import { computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import CyberLayout from '@/Layouts/CyberLayout.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const roles = computed(() => page.props.auth?.roles || []);
const isPelanggan = computed(() => roles.value.includes('pelanggan'));

const profileForm = useForm({
    name: user.value?.name || '',
    email: user.value?.email || '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updateProfile = () => {
    profileForm.patch('/profile', { preserveScroll: true });
};

const updatePassword = () => {
    passwordForm.put('/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

<template>
    <CyberLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Pengaturan Profil Akun</h1>
                <p class="text-sm text-slate-400">Kelola alamat email dan keamanan kata sandi akun Anda.</p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Kartu 1: Informasi Akun -->
                <div class="bg-[#0b1120] border border-blue-900/50 rounded-2xl p-6 shadow-xl">
                    <h2 class="text-lg font-bold text-white mb-1">Informasi Akun</h2>
                    <p class="text-xs text-slate-400 mb-5">
                        Role aktif: <span class="uppercase font-bold text-cyan-400">{{ roles[0] || 'User' }}</span>
                    </p>

                    <form @submit.prevent="updateProfile" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">Nama Lengkap</label>
                            <input v-model="profileForm.name" type="text" :readonly="isPelanggan"
                                :class="isPelanggan ? 'bg-slate-900/60 text-slate-400 border-slate-800 cursor-not-allowed' : 'bg-slate-950 text-white border-slate-800 focus:border-blue-500'"
                                class="w-full border rounded-xl px-4 py-2.5 text-sm" />
                            <p v-if="isPelanggan" class="text-[11px] text-amber-400/80 mt-1">
                                * Nama akun pelanggan dikunci untuk menjaga konsistensi data riwayat servis.
                            </p>
                            <p v-if="profileForm.errors.name" class="text-red-400 text-xs mt-1">{{ profileForm.errors.name }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">Alamat Email</label>
                            <input v-model="profileForm.email" type="email" required
                                class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-2.5 text-sm text-white" />
                            <p v-if="profileForm.errors.email" class="text-red-400 text-xs mt-1">{{ profileForm.errors.email }}</p>
                        </div>

                        <div class="pt-2 flex items-center gap-3">
                            <button type="submit" :disabled="profileForm.processing"
                                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-slate-950 font-extrabold text-xs uppercase tracking-wider">
                                Simpan Profil
                            </button>
                            <span v-if="profileForm.recentlySuccessful" class="text-xs text-emerald-400 font-semibold">✓ Tersimpan</span>
                        </div>
                    </form>
                </div>

                <!-- Kartu 2: Ubah Kata Sandi -->
                <div class="bg-[#0b1120] border border-blue-900/50 rounded-2xl p-6 shadow-xl">
                    <h2 class="text-lg font-bold text-white mb-1">Ubah Kata Sandi</h2>
                    <p class="text-xs text-slate-400 mb-5">Gunakan minimal 8 karakter untuk menjaga keamanan akun.</p>

                    <form @submit.prevent="updatePassword" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">Kata Sandi Saat Ini</label>
                            <input v-model="passwordForm.current_password" type="password" required
                                class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-2.5 text-sm text-white" />
                            <p v-if="passwordForm.errors.current_password" class="text-red-400 text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">Kata Sandi Baru</label>
                            <input v-model="passwordForm.password" type="password" required
                                class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-2.5 text-sm text-white" />
                            <p v-if="passwordForm.errors.password" class="text-red-400 text-xs mt-1">{{ passwordForm.errors.password }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">Konfirmasi Sandi Baru</label>
                            <input v-model="passwordForm.password_confirmation" type="password" required
                                class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-2.5 text-sm text-white" />
                        </div>

                        <div class="pt-2 flex items-center gap-3">
                            <button type="submit" :disabled="passwordForm.processing"
                                class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs uppercase tracking-wider">
                                Update Sandi
                            </button>
                            <span v-if="passwordForm.recentlySuccessful" class="text-xs text-emerald-400 font-semibold">✓ Sandi Diubah</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </CyberLayout>
</template>