<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const kode = ref('SRV-DEMO01');
const hasil = ref(null);
const errorMsg = ref('');
const loading = ref(false);

const langkahStatus = ['menunggu', 'diproses', 'selesai', 'diambil'];

const cekStatus = async () => {
    if (!kode.value) return;
    loading.value = true;
    errorMsg.value = '';
    hasil.value = null;

    try {
        const res = await axios.get(`/api/v1/cek-tiket/${kode.value.trim()}`);
        hasil.value = res.data.data || res.data;
    } catch (e) {
        errorMsg.value = e.response?.data?.message || 'Kode tiket tidak ditemukan di sistem kami.';
    } finally {
        loading.value = false;
    }
};

const indexLangkah = (st) => langkahStatus.indexOf(st);
</script>

<template>
    <div class="min-h-screen bg-[#060913] text-slate-100 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header -->
        <header class="relative z-10 max-w-6xl mx-auto px-6 py-6 flex justify-between items-center border-b border-blue-900/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center font-black text-slate-950 text-lg shadow-lg shadow-blue-500/30">
                    ⚡
                </div>
                <div>
                    <h1 class="font-extrabold text-xl tracking-wide text-white">ELECTRO<span class="text-blue-400">FIX</span></h1>
                    <p class="text-xs text-slate-400">Pusat Layanan Servis & Kalibrasi Elektronik</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <Link href="/register"
                    class="px-4 py-2.5 rounded-xl bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-400/40 text-sm font-semibold transition">
                    Daftar Pelanggan
                </Link>
                <Link href="/login"
                    class="px-5 py-2.5 rounded-xl bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white border border-blue-500/40 text-sm font-semibold transition shadow-lg shadow-blue-950/40">
                    Login Staf / Pelanggan →
                </Link>
            </div>
        </header>

        <!-- Hero Search -->
        <main class="relative z-10 max-w-3xl mx-auto px-6 pt-16 pb-20">
            <div class="text-center mb-10">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest bg-blue-500/10 text-blue-400 border border-blue-500/30 mb-4">
                    Real-Time Service Tracking
                </span>
                <h2 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight mb-4">
                    Lacak Status <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Servis Perangkat</span>
                </h2>
                <p class="text-slate-400 text-base max-w-xl mx-auto">
                    Masukkan kode tiket servis Anda untuk memantau progres perbaikan, teknisi yang bertugas, hingga rincian suku cadang secara transparan.
                </p>
            </div>

            <form @submit.prevent="cekStatus" class="flex flex-col sm:flex-row gap-3 bg-[#0b1120] p-2.5 rounded-2xl border border-blue-800/50 shadow-2xl shadow-blue-950/50">
                <input v-model="kode" type="text" placeholder="Masukkan kode tiket (cth: SRV-DEMO01)..."
                    class="flex-1 bg-transparent px-4 py-3 text-white font-mono text-lg placeholder-slate-500 focus:outline-none" />
                <button type="submit" :disabled="loading"
                    class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-slate-950 font-extrabold tracking-wide transition shadow-lg shadow-blue-500/30 disabled:opacity-50">
                    {{ loading ? 'Mencari...' : 'Lacak Tiket' }}
                </button>
            </form>

            <!-- Pesan Error -->
            <div v-if="errorMsg" class="mt-6 p-4 rounded-xl bg-red-950/50 border border-red-500/40 text-red-300 text-center text-sm">
                {{ errorMsg }}
            </div>

            <!-- Kartu Hasil Pelacakan -->
            <div v-if="hasil" class="mt-10 bg-[#0b1120]/95 border border-blue-800/50 rounded-2xl p-6 sm:p-8 shadow-2xl shadow-blue-950/60 space-y-6">
                <div class="flex flex-wrap justify-between items-start gap-4 border-b border-slate-800 pb-5">
                    <div>
                        <span class="text-xs font-mono text-blue-400 uppercase tracking-widest">{{ hasil.kode_tiket }}</span>
                        <h3 class="text-2xl font-bold text-white mt-1">{{ hasil.nama_barang }}</h3>
                        <p class="text-sm text-slate-400 mt-1">Pelanggan: <span class="text-slate-200 font-medium">{{ hasil.pelanggan?.name || hasil.pelanggan || '-' }}</span></p>
                    </div>
                    <span class="px-4 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-500/20 text-blue-300 border border-blue-400/40">
                        {{ hasil.status }}
                    </span>
                </div>

                <!-- Stepper Status 4 Tahap -->
                <div class="grid grid-cols-4 gap-2 pt-2">
                    <div v-for="(step, idx) in langkahStatus" :key="step" class="text-center">
                        <div class="h-2 rounded-full mb-2 transition-all"
                            :class="idx <= indexLangkah(hasil.status) ? 'bg-gradient-to-r from-blue-500 to-cyan-400 shadow-sm shadow-blue-400/50' : 'bg-slate-800'">
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider"
                            :class="idx <= indexLangkah(hasil.status) ? 'text-blue-300' : 'text-slate-600'">
                            {{ step }}
                        </span>
                    </div>
                </div>

                <!-- Detail Keluhan, Diagnosa, Perbaikan & Teknisi -->
                <div class="grid sm:grid-cols-2 gap-4 bg-slate-950/70 p-4 rounded-xl border border-slate-800/80">
                    <div>
                        <span class="text-xs text-slate-500 uppercase font-semibold">Keluhan Kerusakan</span>
                        <p class="text-sm text-slate-200 mt-1">{{ hasil.keluhan || '-' }}</p>

                        <span class="text-xs text-slate-500 uppercase font-semibold block mt-3">Hasil Diagnosa</span>
                        <p class="text-sm text-slate-200 mt-1">{{ hasil.diagnosa_kerusakan || 'Belum ada diagnosa' }}</p>

                        <span class="text-xs text-slate-500 uppercase font-semibold block mt-3">Yang Diperbaiki / Diubah</span>
                        <p class="text-sm text-cyan-300 font-medium mt-1">{{ hasil.tindakan_perbaikan || 'Belum ada catatan perbaikan' }}</p>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 uppercase font-semibold">Teknisi Penanggung Jawab</span>
                        <p class="text-sm text-blue-300 font-semibold mt-1">{{ hasil.teknisi?.name || hasil.teknisi || 'Menunggu Penugasan Teknisi' }}</p>
                        <p class="text-xs text-slate-500 mt-1">Tanggal Masuk: {{ hasil.tanggal_masuk }}</p>
                        <p v-if="hasil.tanggal_diambil" class="text-xs text-cyan-400 mt-1">
                            Tanggal Diambil: {{ hasil.tanggal_diambil }}
                        </p>
                    </div>
                </div>

                <!-- Rincian Suku Cadang & Total Biaya -->
                <div class="border-t border-slate-800 pt-5">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Rincian Biaya & Suku Cadang</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-slate-300">
                            <span>Biaya Jasa Servis</span>
                            <span class="font-mono">Rp {{ Number(hasil.biaya_servis || 0).toLocaleString('id-ID') }}</span>
                        </div>
                        <div v-for="part in (hasil.suku_cadangs || [])" :key="part.id" class="flex justify-between text-slate-400">
                            <span>{{ part.nama_part }} ({{ part.pivot?.jumlah }}x)</span>
                            <span class="font-mono">Rp {{ Number((part.pivot?.harga_satuan || part.harga) * (part.pivot?.jumlah || 1)).toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-extrabold text-cyan-300 border-t border-slate-800 pt-3 mt-3">
                            <span>Total Estimasi Biaya</span>
                            <span class="font-mono">
                                Rp {{
                                    (Number(hasil.biaya_servis || 0) +
                                    (hasil.suku_cadangs || []).reduce((acc, p) => acc + Number((p.pivot?.harga_satuan || p.harga) * (p.pivot?.jumlah || 1)), 0))
                                    .toLocaleString('id-ID')
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>