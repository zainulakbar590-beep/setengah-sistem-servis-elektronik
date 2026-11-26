<script setup>
import { useForm, router } from '@inertiajs/vue3';
import CyberLayout from '@/Layouts/CyberLayout.vue';

defineProps({ kategoris: Array });

const form = useForm({ nama_kategori: '' });
const submit = () => form.post('/kategori-barang', { onSuccess: () => form.reset() });
const hapus = (id) => {
    if (confirm('Hapus kategori ini?')) router.delete(`/kategori-barang/${id}`);
};
</script>

<template>
    <CyberLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Master Kategori Perangkat</h1>
                <p class="text-sm text-slate-400">Kelola jenis kategori elektronik yang diterima untuk servis.</p>
            </div>

            <form @submit.prevent="submit" class="flex gap-3 bg-[#0b1120] p-4 rounded-2xl border border-blue-900/40">
                <input v-model="form.nama_kategori" type="text" required placeholder="Nama kategori baru (cth: Konsol Game / Monitor)..."
                    class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white" />
                <button type="submit" :disabled="form.processing"
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-slate-950 font-extrabold text-sm">
                    + Tambah Kategori
                </button>
            </form>

            <div class="bg-[#0b1120] border border-blue-900/40 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-950 text-blue-300 text-xs uppercase">
                        <tr>
                            <th class="p-4">Nama Kategori</th>
                            <th class="p-4">Jumlah Tiket</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr v-for="k in kategoris" :key="k.id" class="hover:bg-blue-950/20">
                            <td class="p-4 font-bold text-white">{{ k.nama_kategori }}</td>
                            <td class="p-4 text-slate-400">{{ k.tiket_servis_count }} tiket</td>
                            <td class="p-4 text-right">
                                <button @click="hapus(k.id)" class="text-red-400 hover:text-red-300 text-xs font-bold">Hapus</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </CyberLayout>
</template>