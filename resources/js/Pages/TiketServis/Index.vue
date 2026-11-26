<script setup>
import { ref, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import CyberLayout from '@/Layouts/CyberLayout.vue';

const props = defineProps({
    tikets: Object,
    kategoris: Array,
    filters: Object,
    stats: Object,
});

const page = usePage();
const roles = computed(() => page.props.auth?.roles || []);
const isStaf = computed(() => roles.value.includes('admin') || roles.value.includes('teknisi'));

const cari = ref(props.filters?.cari || '');
const status = ref(props.filters?.status || '');

const filterData = () => {
    router.get('/tiket-servis', { cari: cari.value, status: status.value }, { preserveState: true });
};

const hapusTiket = (id) => {
    if (confirm('Yakin ingin menghapus tiket servis ini?')) {
        router.delete(`/tiket-servis/${id}`);
    }
};

const totalBiaya = (t) => {
    const partTotal = (t.suku_cadangs || []).reduce((sum, p) => sum + Number((p.pivot?.harga_satuan || p.harga) * (p.pivot?.jumlah || 1)), 0);
    return Number(t.biaya_servis || 0) + partTotal;
};

const badgeStatus = (st) => {
    const map = {
        menunggu: 'bg-amber-500/15 text-amber-300 border-amber-500/30',
        diproses: 'bg-blue-500/15 text-blue-300 border-blue-500/30',
        selesai: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30',
        diambil: 'bg-slate-700/50 text-slate-300 border-slate-600',
    };
    return map[st] || 'bg-slate-800 text-slate-300';
};
</script>

<template>
    <CyberLayout>
        <!-- Header & Tombol Ajukan (Khusus Pelanggan) -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">Manajemen Tiket Servis</h1>
                <p class="text-sm text-slate-400 mt-1">Pantau antrean perbaikan elektronik, penugasan teknisi, dan rincian biaya.</p>
            </div>
            <Link v-if="!isStaf" href="/tiket-servis/create"
                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-slate-950 font-extrabold text-sm shadow-lg shadow-blue-500/25 transition">
                + Ajukan Servis Baru
            </Link>
        </div>

        <!-- Kartu Statistik Ringkasan -->
        <div v-if="stats" class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-[#0b1120] border border-blue-900/40 rounded-2xl p-5">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tiket</span>
                <div class="text-3xl font-black text-white mt-2">{{ stats.total }}</div>
            </div>
            <div class="bg-[#0b1120] border border-amber-500/20 rounded-2xl p-5">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Menunggu Antrean</span>
                <div class="text-3xl font-black text-amber-300 mt-2">{{ stats.menunggu }}</div>
            </div>
            <div class="bg-[#0b1120] border border-blue-500/30 rounded-2xl p-5">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-400">Sedang Diproses</span>
                <div class="text-3xl font-black text-blue-300 mt-2">{{ stats.diproses }}</div>
            </div>
            <div class="bg-[#0b1120] border border-emerald-500/20 rounded-2xl p-5">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Selesai / Diambil</span>
                <div class="text-3xl font-black text-emerald-300 mt-2">{{ stats.selesai }}</div>
            </div>
        </div>

        <!-- Bar Pencarian & Filter -->
        <form @submit.prevent="filterData" class="flex flex-col sm:flex-row gap-3 mb-6 bg-[#0b1120] p-4 rounded-2xl border border-blue-900/40">
            <input v-model="cari" type="text" placeholder="Cari kode tiket atau nama perangkat..."
                class="flex-1 bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none" />
            <select v-model="status"
                class="bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-2.5 text-sm text-slate-200 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
                <option value="diambil">Diambil</option>
            </select>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm transition">
                Filter Data
            </button>
        </form>

        <!-- Tabel Tiket Servis -->
        <div class="bg-[#0b1120] border border-blue-900/40 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-950/80 border-b border-blue-900/40 text-xs font-bold uppercase tracking-wider text-blue-300">
                            <th class="p-4">Kode Tiket</th>
                            <th class="p-4">Perangkat & Keluhan</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4">Teknisi</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Total Biaya</th>
                            <th v-if="isStaf" class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/70 text-sm">
                        <tr v-for="t in tikets.data" :key="t.id" class="hover:bg-blue-950/20 transition">
                            <td class="p-4 font-mono font-bold text-cyan-400">{{ t.kode_tiket }}</td>
                            <td class="p-4">
                                <div class="font-bold text-white">{{ t.nama_barang }}</div>
                                <div class="text-xs text-blue-400">{{ t.kategori_barang?.nama_kategori }}</div>
                                <div class="text-xs text-slate-400 mt-1 line-clamp-1">{{ t.keluhan }}</div>
                            </td>
                            <td class="p-4 text-slate-300">{{ t.pelanggan?.name }}</td>
                            <td class="p-4 text-slate-400">{{ t.teknisi?.name || '-' }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider border" :class="badgeStatus(t.status)">
                                    {{ t.status }}
                                </span>
                            </td>
                            <td class="p-4 font-mono font-semibold text-white">
                                Rp {{ totalBiaya(t).toLocaleString('id-ID') }}
                            </td>
                            <td v-if="isStaf" class="p-4 text-right space-x-2">
                                <Link :href="`/tiket-servis/${t.id}/edit`"
                                    class="inline-block px-3 py-1.5 rounded-lg bg-blue-600/20 text-blue-300 hover:bg-blue-600 hover:text-white border border-blue-500/30 text-xs font-semibold transition">
                                    Kelola / Edit
                                </Link>
                                <button @click="hapusTiket(t.id)"
                                    class="inline-block px-3 py-1.5 rounded-lg bg-red-500/10 text-red-400 hover:bg-red-600 hover:text-white border border-red-500/30 text-xs font-semibold transition">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!tikets.data.length">
                            <td :colspan="isStaf ? 7 : 6" class="p-8 text-center text-slate-500">
                                Tidak ada data tiket servis yang ditemukan.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </CyberLayout>
</template>