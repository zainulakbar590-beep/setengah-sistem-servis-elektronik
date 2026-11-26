<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import CyberLayout from '@/Layouts/CyberLayout.vue';

defineProps({ kategoris: Array });

const form = useForm({
    kategori_barang_id: '',
    nama_barang: '',
    keluhan: '',
});

const submit = () => form.post('/tiket-servis');
</script>

<template>
    <CyberLayout>
        <div class="max-w-2xl mx-auto bg-[#0b1120] border border-blue-900/50 rounded-2xl p-6 sm:p-8 shadow-2xl">
            <h1 class="text-2xl font-extrabold text-white mb-2">Ajukan Tiket Servis Baru</h1>
            <p class="text-sm text-slate-400 mb-6">Isi detail perangkat elektronik dan keluhan kerusakan Anda.</p>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">Kategori Perangkat</label>
                    <select v-model="form.kategori_barang_id" required
                        class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-3 text-white">
                        <option value="" disabled>-- Pilih Kategori Elektronik --</option>
                        <option v-for="k in kategoris" :key="k.id" :value="k.id">{{ k.nama_kategori }}</option>
                    </select>
                    <p v-if="form.errors.kategori_barang_id" class="text-red-400 text-xs mt-1">{{ form.errors.kategori_barang_id }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">Merk & Tipe Perangkat</label>
                    <input v-model="form.nama_barang" type="text" required placeholder="Contoh: Laptop ASUS ROG G14 / Samsung S23"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-3 text-white placeholder-slate-600" />
                    <p v-if="form.errors.nama_barang" class="text-red-400 text-xs mt-1">{{ form.errors.nama_barang }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-300 mb-2">Deskripsi Keluhan Kerusakan</label>
                    <textarea v-model="form.keluhan" rows="4" required placeholder="Jelaskan gejala kerusakan secara detail..."
                        class="w-full bg-slate-950 border border-slate-800 focus:border-blue-500 rounded-xl px-4 py-3 text-white placeholder-slate-600"></textarea>
                    <p v-if="form.errors.keluhan" class="text-red-400 text-xs mt-1">{{ form.errors.keluhan }}</p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing"
                        class="px-6 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-slate-950 font-extrabold text-sm shadow-lg shadow-blue-500/25">
                        Kirim Pengajuan Servis
                    </button>
                    <Link href="/tiket-servis" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm">
                        Batal
                    </Link>
                </div>
            </form>
        </div>
    </CyberLayout>
</template>