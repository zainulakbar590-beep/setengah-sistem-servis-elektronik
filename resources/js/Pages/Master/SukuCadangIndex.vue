<script setup>
import { useForm, router } from '@inertiajs/vue3';
import CyberLayout from '@/Layouts/CyberLayout.vue';

defineProps({ sukuCadangs: Array });

const form = useForm({
    kode_part: '',
    nama_part: '',
    stok: 10,
    harga: 0,
});

const submit = () => form.post('/suku-cadang', { onSuccess: () => form.reset() });
const hapus = (id) => {
    if (confirm('Hapus suku cadang ini?')) router.delete(`/suku-cadang/${id}`);
};
</script>

<template>
    <CyberLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Gudang Stok Suku Cadang</h1>
                <p class="text-sm text-slate-400">Manajemen inventaris sparepart dan harga satuan.</p>
            </div>

            <form @submit.prevent="submit" class="grid sm:grid-cols-5 gap-3 bg-[#0b1120] p-4 rounded-2xl border border-blue-900/40">
                <input v-model="form.kode_part" type="text" required placeholder="Kode (PRT-005)"
                    class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-sm text-white font-mono" />
                <input v-model="form.nama_part" type="text" required placeholder="Nama Suku Cadang"
                    class="sm:col-span-2 bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-sm text-white" />
                <input v-model="form.stok" type="number" min="0" required placeholder="Stok"
                    class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-sm text-white" />
                <div class="flex gap-2">
                    <input v-model="form.harga" type="number" min="0" required placeholder="Harga (Rp)"
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-sm text-white" />
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm">
                        +
                    </button>
                </div>
            </form>

            <div class="bg-[#0b1120] border border-blue-900/40 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-950 text-blue-300 text-xs uppercase">
                        <tr>
                            <th class="p-4">Kode Part</th>
                            <th class="p-4">Nama Suku Cadang</th>
                            <th class="p-4">Stok Tersedia</th>
                            <th class="p-4">Harga Satuan</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr v-for="p in sukuCadangs" :key="p.id" class="hover:bg-blue-950/20">
                            <td class="p-4 font-mono text-cyan-400 font-bold">{{ p.kode_part }}</td>
                            <td class="p-4 font-semibold text-white">{{ p.nama_part }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                    :class="p.stok > 5 ? 'bg-emerald-500/15 text-emerald-300' : 'bg-red-500/15 text-red-300'">
                                    {{ p.stok }} unit
                                </span>
                            </td>
                            <td class="p-4 font-mono text-slate-200">Rp {{ Number(p.harga).toLocaleString('id-ID') }}</td>
                            <td class="p-4 text-right">
                                <button @click="hapus(p.id)" class="text-red-400 hover:text-red-300 text-xs font-bold">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </CyberLayout>
</template>