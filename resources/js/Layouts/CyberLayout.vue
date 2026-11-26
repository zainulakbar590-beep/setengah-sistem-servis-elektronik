<script setup>
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const roles = computed(() => page.props.auth?.roles || []);
const isAdmin = computed(() => roles.value.includes('admin'));

const logout = () => router.post('/logout');
</script>

<template>
    <div class="min-h-screen bg-[#060913] text-slate-100 font-sans selection:bg-blue-600 selection:text-white">
        <!-- Efek Cahaya Biru Latar Belakang -->
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/15 rounded-full blur-3xl"></div>
            <div class="absolute top-1/3 -right-40 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Navbar Utama -->
        <nav class="sticky top-0 z-40 bg-[#0b1120]/90 backdrop-blur-md border-b border-blue-900/40 shadow-lg shadow-blue-950/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-8">
                    <Link href="/tiket-servis" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center font-black text-slate-950 shadow-md shadow-blue-500/30">
                            ⚡
                        </div>
                        <div>
                            <span class="font-extrabold tracking-wide text-white text-lg">ELECTRO<span class="text-blue-400">FIX</span></span>
                            <span class="block text-[10px] uppercase tracking-widest text-blue-400/80 -mt-1">Service Center System</span>
                        </div>
                    </Link>

                    <div class="hidden md:flex items-center gap-1">
                        <Link href="/tiket-servis" class="px-3.5 py-2 rounded-lg text-sm font-medium transition hover:bg-blue-950/60 hover:text-blue-300"
                            :class="$page.url.startsWith('/tiket-servis') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-300'">
                            Tiket Servis
                        </Link>
                        <template v-if="isAdmin">
                            <Link href="/kategori-barang" class="px-3.5 py-2 rounded-lg text-sm font-medium transition hover:bg-blue-950/60 hover:text-blue-300"
                                :class="$page.url.startsWith('/kategori-barang') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-300'">
                                Kategori
                            </Link>
                            <Link href="/suku-cadang" class="px-3.5 py-2 rounded-lg text-sm font-medium transition hover:bg-blue-950/60 hover:text-blue-300"
                                :class="$page.url.startsWith('/suku-cadang') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-300'">
                                Suku Cadang
                            </Link>
                            <Link href="/teknisi" class="px-3.5 py-2 rounded-lg text-sm font-medium transition hover:bg-blue-950/60 hover:text-cyan-300"
                                :class="$page.url.startsWith('/teknisi') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'text-cyan-400'">
                                + Teknisi
                            </Link>
                            <Link href="/api-tokens" class="px-3.5 py-2 rounded-lg text-sm font-medium transition hover:bg-blue-950/60 hover:text-blue-300"
                                :class="$page.url.startsWith('/api-tokens') ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30' : 'text-slate-300'">
                                REST API Token
                            </Link>
                        </template>
                        <Link href="/" class="px-3.5 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-cyan-300 transition">
                            Portal Publik ↗
                        </Link>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link href="/profile" class="text-right group px-2.5 py-1 rounded-lg hover:bg-blue-950/50 transition"
                        :class="$page.url.startsWith('/profile') ? 'bg-blue-600/20 border border-blue-500/30' : ''">
                        <div class="text-sm font-semibold text-white group-hover:text-cyan-300 transition">{{ user?.name }}</div>
                        <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider rounded bg-blue-500/20 text-blue-300 border border-blue-500/30">
                            {{ roles[0] || 'User' }} • Edit Profil
                        </span>
                    </Link>
                    <button @click="logout" type="button"
                        class="px-3.5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider bg-red-500/10 text-red-400 border border-red-500/30 hover:bg-red-600 hover:text-white transition">
                        Logout
                    </button>
                </div>
            </div>
        </nav>

        <!-- Konten Utama -->
        <main class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>