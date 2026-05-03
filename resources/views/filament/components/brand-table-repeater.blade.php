<div x-data="{
    openModal: false,
    brandName: '',
    namaCabang: '',
    kotaCabang: '',
    editIndex: null,
    // Menghubungkan variabel brands langsung ke state filament
    brands: @entangle($getStatePath()),

    init() {
        // Pastikan brands selalu berupa array saat load pertama kali
        if (this.brands === null || typeof this.brands === 'undefined') {
            this.brands = [];
        }
    },

    // Membuka modal untuk Edit
    editBrand(index) {
        this.editIndex = index;
        this.brandName = this.brands[index].brand_name;
        this.namaCabang = this.brands[index].nama_cabang;
        this.kotaCabang = this.brands[index].kota_cabang;
        this.openModal = true;
    },

    // Fungsi tunggal untuk Simpan (Tambah atau Update)
    saveData() {
        if (this.brandName === '' || this.namaCabang === '' || this.kotaCabang === '') return;

        if (this.editIndex !== null) {
            // Logika Update (Edit)
            this.brands[this.editIndex].brand_name = this.brandName;
            this.brands[this.editIndex].nama_cabang = this.namaCabang;
            this.brands[this.editIndex].kota_cabang = this.kotaCabang;
        } else {
            // Logika Tambah Baru
            if (!Array.isArray(this.brands)) {
                this.brands = [];
            }
            this.brands.push({
                brand_name: this.brandName,
                nama_cabang: this.namaCabang,
                kota_cabang: this.kotaCabang
            });
        }

        this.closeAndReset();
    },

    deleteBrand(index) {
        if (confirm('Hapus cabang ini?')) {
            this.brands.splice(index, 1);
        }
    },

    closeAndReset() {
        this.openModal = false;
        this.brandName = '';
        this.namaCabang = '';
        this.kotaCabang = '';
        this.editIndex = null;
    }
}" class="modern-blue-container">

    <div class="table-card">
        <table class="pure-table">
            <thead>
                <tr>
                    <th><span style="margin-right: 8px;">🏷️</span> Nama Brand</th>
                    <th><span style="margin-right: 8px;">📍</span> Alamat Cabang</th>
                    <th><span style="margin-right: 8px;">📍</span> Kota Cabang</th>
                    <th style="width: 100px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(item, index) in brands" :key="index">
                    <tr>
                        <td x-text="item.brand_name"></td>
                        <td x-text="item.nama_cabang"></td>
                        <td x-text="item.kota_cabang"></td>
                        <td style="text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <button type="button" @click="editBrand(index)" class="btn-edit" title="Edit">
                                    ✏️
                                </button>
                                <button type="button" @click="deleteBrand(index)" class="btn-delete" title="Hapus">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <template x-if="!brands || brands.length === 0">
                    <tr>
                        <td colspan="3" class="empty-text">Belum punya cabang</td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div class="table-footer">
            <button type="button" @click="openModal = true" class="btn-add">
                + Tambah Brand
            </button>
        </div>
    </div>

    <template x-teleport="body">
        <div x-show="openModal" x-transition.opacity class="modal-overlay" style="display: none;">

            <div @click.away="closeAndReset()" x-show="openModal" x-transition.scale.95 class="modal-content">

                <div class="modal-header">
                    <h3 x-text="editIndex !== null ? 'Edit Brand & Cabang' : 'Tambah Brand & Cabang'"></h3>
                    <button type="button" @click="closeAndReset()" class="close-x">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Brand</label>
                        <input type="text" x-model="brandName" placeholder="Contoh: Brand A">
                    </div>
                    <div class="form-group">
                        <label>Alamat Cabang</label>
                        <input type="text" x-model="namaCabang" placeholder="Contoh: Cabang Jakarta">
                    </div>
                    <div class="form-group">
                        <label>Kota Cabang</label>
                        <input type="text" x-model="kotaCabang" placeholder="Contoh: Cabang Jakarta">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="closeAndReset()" class="btn-cancel">Batal</button>
                    <button type="button" @click="saveData()" class="btn-save"
                        x-text="editIndex !== null ? 'Simpan Perubahan' : 'Simpan Data'"></button>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    :root {
        --blue-main: #2563eb;
        --blue-dark: #1e40af;
        --blue-light: #eff6ff;
        --gray-border: #e2e8f0;
    }

    .modern-blue-container {
        font-family: 'Inter', sans-serif;
    }

    .table-card {
        border: 1px solid var(--gray-border);
        border-radius: 12px;
        overflow: hidden;
        background: white;
    }

    .pure-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pure-table th {
        background: #f8fafc;
        padding: 14px 16px;
        text-align: left;
        font-size: 11px;
        color: var(--blue-dark);
        text-transform: uppercase;
        border-bottom: 2px solid var(--blue-light);
        letter-spacing: 0.05em;
    }

    .pure-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #475569;
    }

    .empty-text {
        text-align: center;
        padding: 40px;
        color: #94a3b8;
        font-style: italic;
    }

    .btn-add {
        background: var(--blue-main);
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: 0.2s;
    }

    .btn-add:hover {
        background: var(--blue-dark);
    }

    /* Action Buttons Style */
    .btn-edit,
    .btn-delete {
        border: none;
        padding: 8px;
        border-radius: 8px;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }

    .btn-edit {
        background: #e0f2fe;
        color: #0369a1;
    }

    .btn-edit:hover {
        background: #bae6fd;
        transform: translateY(-2px);
    }

    .btn-delete {
        background: #fee2e2;
        color: #b91c1c;
    }

    .btn-delete:hover {
        background: #fecaca;
        transform: translateY(-2px);
    }

    .table-footer {
        padding: 16px;
        background: #f8fafc;
        border-top: 1px solid var(--gray-border);
    }

    /* Modal Styling */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background: white;
        width: 100%;
        max-width: 420px;
        border-radius: 20px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        overflow: hidden;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid var(--gray-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #1e293b;
    }

    .close-x {
        border: none;
        background: none;
        font-size: 26px;
        cursor: pointer;
        color: #94a3b8;
        line-height: 1;
    }

    .modal-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
    }

    .form-group input {
        width: 100%;
        padding: 12px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        outline: none;
        transition: 0.2s;
    }

    .form-group input:focus {
        border-color: var(--blue-main);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .modal-footer {
        padding: 16px 24px;
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-cancel {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }

    .btn-save {
        background: var(--blue-main);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        font-size: 13px;
    }
</style>
