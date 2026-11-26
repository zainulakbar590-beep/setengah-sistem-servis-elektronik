<script setup>
import { computed } from 'vue';
import { useForm, usePage, Link, router } from '@inertiajs/vue3';
import CyberLayout from '@/Layouts/CyberLayout.vue';

const props = defineProps({
    tiket: Object,
    kategoris: Array,
    teknisis: Array,
    sukuCadangs: Array,
});

const page = usePage();

const isAdmin = computed(() => {
    const user = page.props.auth?.user;
    if (!user) return false;

    if (Array.isArray(user.roles)) {
        return user.roles.some((r) => (typeof r === 'string' ? r : r?.name) === 'admin');
    }

    if (Array.isArray(user.role_names)) {
        return user.role_names.includes('admin');
    }

    return user.role === 'admin';
});

const form = useForm({
    kategori_barang_id: props.tiket.kategori_barang_id ?? '',
    teknisi_id: props.tiket.teknisi_id ?? '',
    nama_barang: props.tiket.nama_barang ?? '',
    keluhan: props.tiket.keluhan ?? '',
    diagnosa_kerusakan: props.tiket.diagnosa_kerusakan ?? '',
    tindakan_perbaikan: props.tiket.tindakan_perbaikan ?? '',
    status: props.tiket.status ?? 'menunggu',
    biaya_servis: props.tiket.biaya_servis ?? 0,
});

const partForm = useForm({
    suku_cadang_id: '',
    jumlah: 1,
});

const daftarPart = computed(() => {
    const list = props.tiket.suku_cadangs ?? props.tiket.sukuCadangs ?? [];
    return list.map((item) => {
        const jumlah = Number(item.pivot?.jumlah ?? 0);
        const hargaSatuan = Number(item.pivot?.harga_satuan ?? item.harga ?? 0);
        return {
            id: item.id,
            nama_part: item.nama_part,
            jumlah,
            harga_satuan: hargaSatuan,
            subtotal: jumlah * hargaSatuan,
        };
    });
});

const totalPart = computed(() =>
    daftarPart.value.reduce((acc, item) => acc + item.subtotal, 0)
);

const totalBiaya = computed(() =>
    Number(form.biaya_servis || 0) + totalPart.value
);

const formatRupiah = (angka) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(Number(angka || 0));

const tambahPart = () => {
    if (!partForm.suku_cadang_id) return;
    partForm.post(`/tiket-servis/${props.tiket.id}/part`, {
        preserveScroll: true,
        onSuccess: () => {
            partForm.reset('suku_cadang_id');
            partForm.jumlah = 1;
        },
    });
};

const hapusPart = (partId) => {
    router.delete(`/tiket-servis/${props.tiket.id}/part/${partId}`, {
        preserveScroll: true,
    });
};

const submit = () => {
    form.put(`/tiket-servis/${props.tiket.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <CyberLayout>
        <div class="max-w-3xl mx-auto bg-[#0b1120] border border-blue-900/40 rounded-2xl p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-white">Kelola Pengerjaan Servis</h1>
                    <p class="text-xs text-slate-400 mt-1">Kode Tiket: {{ tiket.kode_tiket }}</p>
                </div>
                <span class="text-sm text-slate-400">Pelanggan: {{ tiket.pelanggan?.name || '-' }}</span>
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-2">Kategori Barang</label>
                        <select v-model="form.kategori_barang_id" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white">
                            <option value="" disabled>-- Pilih Kategori --</option>
                            <option v-for="k in kategoris" :key="k.id" :value="k.id">{{ k.nama_kategori }}</option>
                        </select>
                        <div v-if="form.errors.kategori_barang_id" class="text-xs text-red-400 mt-1">{{ form.errors.kategori_barang_id }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-2">Teknisi Bertugas</label>
                        <select
                            v-model="form.teknisi_id"
                            :disabled="!isAdmin"
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <option value="">-- Belum Ditugaskan --</option>
                            <option v-for="tek in teknisis" :key="tek.id" :value="tek.id">{{ tek.name }}</option>
                        </select>
                        <div v-if="form.errors.teknisi_id" class="text-xs text-red-400 mt-1">{{ form.errors.teknisi_id }}</div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-2">Nama Perangkat</label>
                    <input v-model="form.nama_barang" type="text" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white" />
                    <div v-if="form.errors.nama_barang" class="text-xs text-red-400 mt-1">{{ form.errors.nama_barang }}</div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-300 mb-2">Keluhan Awal Pelanggan</label>
                    <textarea v-model="form.keluhan" rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white"></textarea>
                    <div v-if="form.errors.keluhan" class="text-xs text-red-400 mt-1">{{ form.errors.keluhan }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-2">Apa yang Rusak (Hasil Diagnosa)</label>
                        <textarea
                            v-model="form.diagnosa_kerusakan"
                            rows="3"
                            placeholder="Contoh: Port DC jack short & kapasitor jalur daya terbakar"
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white"
                        ></textarea>
                        <div v-if="form.errors.diagnosa_kerusakan" class="text-xs text-red-400 mt-1">{{ form.errors.diagnosa_kerusakan }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-2">Apa yang Diperbaiki / Diubah</label>
                        <textarea
                            v-model="form.tindakan_perbaikan"
                            rows="3"
                            placeholder="Contoh: Ganti soket charger baru, re-solder jalur, dan bersihkan debu kipas"
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white"
                        ></textarea>
                        <div v-if="form.errors.tindakan_perbaikan" class="text-xs text-red-400 mt-1">{{ form.errors.tindakan_perbaikan }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-2">Status Pengerjaan</label>
                        <select v-model="form.status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white">
                            <option value="menunggu">Menunggu</option>
                            <option value="diproses">Diproses</option>
                            <option value="selesai">Selesai</option>
                            <option value="diambil">Diambil</option>
                        </select>
                        <div v-if="form.errors.status" class="text-xs text-red-400 mt-1">{{ form.errors.status }}</div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-300 mb-2">Biaya Jasa Servis (Rp)</label>
                        <input v-model="form.biaya_servis" type="number" min="0" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-sm text-white" />
                        <div v-if="form.errors.biaya_servis" class="text-xs text-red-400 mt-1">{{ form.errors.biaya_servis }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-sm transition">
                        Simpan Perubahan
                    </button>
                    <Link href="/tiket-servis" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold transition">
                        Kembali
                    </Link>
                </div>
            </form>

            <!-- Form Tambah Suku Cadang & Ringkasan Harga -->
            <div class="bg-slate-950/80 border border-slate-800 rounded-xl p-4 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-cyan-400">Rincian Suku Cadang & Total Harga</h2>
                    <span class="text-xs text-slate-400">{{ daftarPart.length }} item part</span>
                </div>

                <form @submit.prevent="tambahPart" class="flex flex-col sm:flex-row gap-2">
                    <select v-model="partForm.suku_cadang_id" class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white">
                        <option value="" disabled>-- Pilih Suku Cadang --</option>
                        <option v-for="sc in sukuCadangs" :key="sc.id" :value="sc.id">
                            {{ sc.nama_part }} (Stok: {{ sc.stok }} • {{ formatRupiah(sc.harga) }})
                        </option>
                    </select>
                    <input v-model="partForm.jumlah" type="number" min="1" class="w-24 bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-sm text-white" />
                    <button type="submit" :disabled="partForm.processing" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm transition">
                        + Tambah Part
                    </button>
                </form>
                <div v-if="partForm.errors.suku_cadang_id" class="text-xs text-red-400">{{ partForm.errors.suku_cadang_id }}</div>
                <div v-if="partForm.errors.jumlah" class="text-xs text-red-400">{{ partForm.errors.jumlah }}</div>

                <div v-if="daftarPart.length > 0" class="divide-y divide-slate-800/80 text-sm">
                    <div v-for="part in daftarPart" :key="part.id" class="py-2 flex items-center justify-between gap-2">
                        <div>
                            <div class="text-slate-200 font-medium">{{ part.nama_part }}</div>
                            <div class="text-xs text-slate-400">
                                {{ part.jumlah }} x {{ formatRupiah(part.harga_satuan) }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-slate-200 font-semibold">{{ formatRupiah(part.subtotal) }}</span>
                            <button type="button" @click="hapusPart(part.id)" class="text-xs text-red-400 hover:text-red-300 font-bold">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <p v-else class="text-xs text-slate-500">Belum ada suku cadang yang dipakai pada tiket ini.</p>

                <div class="border-t border-slate-800 pt-3 space-y-1 text-sm">
                    <div class="flex justify-between text-slate-400">
                        <span>Biaya Jasa Servis</span>
                        <span>{{ formatRupiah(form.biaya_servis) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>Subtotal Suku Cadang</span>
                        <span>{{ formatRupiah(totalPart) }}</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-cyan-300 pt-1">
                        <span>Total Biaya</span>
                        <span>{{ formatRupiah(totalBiaya) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </CyberLayout>
</template>