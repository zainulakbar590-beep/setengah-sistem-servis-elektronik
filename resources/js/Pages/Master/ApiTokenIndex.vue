<script setup>
import { useForm, router, usePage } from '@inertiajs/vue3';
import CyberLayout from '@/Layouts/CyberLayout.vue';

defineProps({ tokens: Array });
const page = usePage();

const form = useForm({ name: '' });
const submit = () => form.post('/api-tokens', { onSuccess: () => form.reset() });
const hapus = (id) => {
    if (confirm('Cabut token API ini?')) router.delete(`/api-tokens/${id}`);
};
</script>

<template>
    <CyberLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-extrabold text-white">Manajemen REST API Token (Sanctum)</h1>
                <p class="text-sm text-slate-400">Buat Bearer Token untuk mengakses endpoint terproteksi <code class="text-cyan-400">/api/v1/tiket-servis</code>.</p>
            </div>

            <div v-if="page.props.flash?.plainToken" class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/40">
                <p class="text-xs font-bold text-emerald-300 uppercase mb-1">Token Berhasil Dibuat (Salin sekarang, hanya tampil sekali):</p>
                <code class="block bg-slate-950 p-3 rounded-xl text-cyan-300 font-mono text-sm select-all">{{ page.props.flash.plainToken }}</code>
            </div>

            <form @submit.prevent="submit" class="flex gap-3 bg-[#0b1120] p-4 rounded-2xl border border-blue-900/40">
                <input v-model="form.name" type="text" required placeholder="Nama klien API (cth: Aplikasi Mobile Android)..."
                    class="flex-1 bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white" />
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-cyan-500 text-slate-950 font-extrabold text-sm">
                    Generate Token
                </button>
            </form>

            <div class="bg-[#0b1120] border border-blue-900/40 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-950 text-blue-300 text-xs uppercase">
                        <tr>
                            <th class="p-4">Nama Token</th>
                            <th class="p-4">Dibuat Pada</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <tr v-for="t in tokens" :key="t.id">
                            <td class="p-4 font-bold text-white">{{ t.name }}</td>
                            <td class="p-4 text-slate-400 text-xs">{{ new Date(t.created_at).toLocaleString('id-ID') }}</td>
                            <td class="p-4 text-right">
                                <button @click="hapus(t.id)" class="text-red-400 hover:text-red-300 text-xs font-bold">Cabut Token</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </CyberLayout>
</template>