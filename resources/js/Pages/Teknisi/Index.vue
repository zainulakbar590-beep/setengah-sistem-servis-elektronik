<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import CyberLayout from '@/Layouts/CyberLayout.vue';

defineProps({
    teknisis: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/teknisi', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <CyberLayout>
        <div class="max-w-5xl mx-auto space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Kelola Akun Teknisi</h1>
                    <p class="text-xs text-slate-400 mt-1">Tambah email/Gmail teknisi baru agar muncul pada pilihan penugasan servis.</p>
                </div>
                <Link href="/tiket-servis" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold transition">
                    Kembali ke Tiket
                </Link>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form Tambah Teknisi -->
                <div class="lg:col-span-1 bg-[#0b1120] border border-blue-900/40 rounded-2xl p-6">
                    <h2 class="text-base font-bold text-white mb-4">Tambah Teknisi Baru</h2>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Nama Teknisi</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                placeholder="Contoh: Rian Teknisi"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-sm text-white"
                            />
                            <div v-if="form.errors.name" class="text-xs text-red-400 mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Email / Gmail</label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                placeholder="rian.teknisi@gmail.com"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-sm text-white"
                            />
                            <div v-if="form.errors.email" class="text-xs text-red-400 mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Password</label>
                            <input
                                v-model="form.password"
                                type="password"
                                required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-sm text-white"
                            />
                            <div v-if="form.errors.password" class="text-xs text-red-400 mt-1">{{ form.errors.password }}</div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-300 mb-1.5">Konfirmasi Password</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                required
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-sm text-white"
                            />
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-sm transition disabled:opacity-50"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Teknisi' }}
                        </button>
                    </form>
                </div>

                <!-- Daftar Teknisi Aktif -->
                <div class="lg:col-span-2 bg-[#0b1120] border border-blue-900/40 rounded-2xl p-6">
                    <h2 class="text-base font-bold text-white mb-4">Daftar Teknisi Terdaftar ({{ teknisis.length }})</h2>

                    <div v-if="teknisis.length > 0" class="divide-y divide-slate-800/80">
                        <div v-for="tek in teknisis" :key="tek.id" class="py-3 flex items-center justify-between gap-4">
                            <div>
                                <div class="text-sm font-semibold text-white">{{ tek.name }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ tek.email }}</div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                                Teknisi
                            </span>
                        </div>
                    </div>
                    <p v-else class="text-sm text-slate-500">Belum ada akun teknisi terdaftar.</p>
                </div>
            </div>
        </div>
    </CyberLayout>
</template>