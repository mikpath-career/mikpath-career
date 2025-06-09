<!-- Add Career Modal -->
<div id="addCareerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addCareerModal')">&times;</span>
        <h2><i class="fas fa-plus"></i> Tambah Karier Baru</h2>
        <form id="addCareerForm" method="POST" action="api/add_career.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="level">Level Karier *</label>
                <select name="level" required>
                    <option value="">Pilih Level</option>
                    <option value="Entry Level">Entry Level</option>
                    <option value="Junior">Junior</option>
                    <option value="Mid Level">Mid Level</option>
                    <option value="Senior">Senior</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="position">Posisi *</label>
                <input type="text" name="position" required placeholder="Contoh: Data Analyst">
            </div>
            
            <div class="form-group">
                <label for="competencies">Kompetensi *</label>
                <textarea name="competencies" required placeholder="Daftar kompetensi yang diperlukan"></textarea>
            </div>
            
            <div class="form-group">
                <label for="certifications">Sertifikasi *</label>
                <textarea name="certifications" required placeholder="Sertifikasi yang diperlukan"></textarea>
            </div>
            
            <div class="form-group">
                <label for="description">Deskripsi *</label>
                <textarea name="description" required placeholder="Deskripsi posisi"></textarea>
            </div>
            
            <div class="form-group">
                <label for="salary_range">Rentang Gaji</label>
                <input type="text" name="salary_range" placeholder="Contoh: Rp 5-8 juta">
            </div>
            
            <div class="form-group">
                <label for="career_image">Gambar</label>
                <input type="file" name="career_image" accept="image/*" onchange="previewImage(this, 'careerImagePreview')">
                <img id="careerImagePreview" style="display: none; max-width: 200px; margin-top: 10px;">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan
            </button>
        </form>
    </div>
</div>

<!-- Add Story Modal -->
<div id="addStoryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addStoryModal')">&times;</span>
        <div class="modal-header">
            <i class="fas fa-heart" style="color: var(--primary-color); font-size: 1.5rem; margin-right: 0.5rem;"></i>
            <h2>Bagikan Ceritamu</h2>
        </div>
        <p style="color: var(--gray-color); margin-bottom: 1.5rem; font-size: 0.9rem;">
            Cerita yang Anda kirim akan langsung ditampilkan di halaman alumni untuk menginspirasi adik-adik tingkat.
        </p>
        <form id="addStoryForm" method="POST" action="api/add_story.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="alumni_name">Nama *</label>
                <input type="text" name="alumni_name" required placeholder="Nama lengkap atau inisial">
                <small>Gunakan inisial jika ingin tetap anonim</small>
            </div>
            
            <div class="form-group">
                <label for="graduation_year">Tahun Lulus *</label>
                <select name="graduation_year" required>
                    <option value="">Pilih Tahun</option>
                    <?php for($year = date('Y'); $year >= 2015; $year--): ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="institution">Institusi/Tempat Kerja *</label>
                <input type="text" name="institution" required placeholder="Nama tempat kerja">
            </div>
            
            <div class="form-group">
                <label for="position">Posisi *</label>
                <input type="text" name="position" required placeholder="Jabatan saat ini">
            </div>
            
            <div class="form-group">
                <label for="story">Cerita Pengalaman *</label>
                <textarea name="story" required placeholder="Ceritakan pengalaman kerjamu, tips untuk adik tingkat, atau hal menarik dari pekerjaanmu..." rows="5"></textarea>
            </div>
            
            <div class="form-group">
                <label for="job_opportunities">Lowongan Terkait</label>
                <input type="text" name="job_opportunities" placeholder="Info lowongan yang tersedia di tempat kerjamu">
            </div>
            
            <div class="form-group">
                <label for="alumni_image">Foto (Opsional)</label>
                <div class="file-upload">
                    <input type="file" name="alumni_image" accept="image/*" onchange="previewImage(this, 'alumniImagePreview')">
                    <div class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Pilih file atau drag and drop disini</span>
                    </div>
                </div>
                <img id="alumniImagePreview" style="display: none; max-width: 200px; margin-top: 10px; border-radius: 8px;">
                <small>Foto akan membantu membuat cerita lebih personal</small>
            </div>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_anonymous" value="1" checked>
                    <span>Posting secara anonim</span>
                </label>
                <small>Centang jika tidak ingin nama asli ditampilkan</small>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-paper-plane"></i> Kirim Cerita
            </button>
        </form>
    </div>
</div>

<!-- Edit Career Modal -->
<div id="editCareerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editCareerModal')">&times;</span>
        <h2><i class="fas fa-edit"></i> Edit Karier</h2>
        <form id="editCareerForm" method="POST" action="api/edit_career.php" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editCareerId">
            
            <div class="form-group">
                <label for="edit_level">Level Karier *</label>
                <select name="level" id="edit_level" required>
                    <option value="">Pilih Level</option>
                    <option value="Entry Level">Entry Level</option>
                    <option value="Junior">Junior</option>
                    <option value="Mid Level">Mid Level</option>
                    <option value="Senior">Senior</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_position">Posisi *</label>
                <input type="text" name="position" id="edit_position" required>
            </div>
            
            <div class="form-group">
                <label for="edit_competencies">Kompetensi *</label>
                <textarea name="competencies" id="edit_competencies" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_certifications">Sertifikasi *</label>
                <textarea name="certifications" id="edit_certifications" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_description">Deskripsi *</label>
                <textarea name="description" id="edit_description" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_salary_range">Rentang Gaji</label>
                <input type="text" name="salary_range" id="edit_salary_range">
            </div>
            
            <div class="form-group">
                <label for="edit_career_image">Gambar Baru</label>
                <input type="file" name="career_image" accept="image/*" onchange="previewImage(this, 'editCareerImagePreview')">
                <img id="editCareerImagePreview" style="display: none; max-width: 200px; margin-top: 10px;">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update
            </button>
        </form>
    </div>
</div>
