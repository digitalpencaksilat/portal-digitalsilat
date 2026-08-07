<?php defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Partial leaderboard (podium top 3 + tabel sisanya).
 * Dipakai oleh landing_page (initial render) & Peringkat::data (AJAX filter).
 * Variabel: $list (array atlet terurut, sudah dibatasi).
 */
$podium = array_slice($list, 0, 3);
$rest   = array_slice($list, 3);
$medal_colors = ['rank-1', 'rank-2', 'rank-3'];
?>
<?php if (empty($list)): ?>
    <div class="text-center text-muted py-5">Belum ada data peringkat untuk kategori ini.</div>
<?php else: ?>

    <!-- Podium Top 3 -->
    <div class="row g-3 justify-content-center mb-4 lb-podium">
        <?php foreach ($podium as $i => $a): ?>
            <div class="col-md-4 col-sm-6">
                <a href="<?= base_url('peringkat/atlet/' . ($a['row_id'] ?? urlencode($a['name_key']))); ?>" class="text-decoration-none">
                    <div class="podium-card podium-<?= $i + 1; ?>">
                        <div class="podium-rank"><?= $i + 1; ?></div>
                        <div class="podium-medal">
                            <?php if ($i === 0): ?><i class="fas fa-crown"></i>
                            <?php else: ?><i class="fas fa-medal"></i><?php endif; ?>
                        </div>
                        <h5 class="podium-name"><?= htmlspecialchars($a['display_name']); ?></h5>
                        <p class="podium-contingent"><?= htmlspecialchars($a['last_contingent']); ?></p>
                        <div class="podium-medals">
                            <span title="Emas"><i class="fas fa-medal medal-emas"></i> <?= $a['emas']; ?></span>
                            <span title="Perak"><i class="fas fa-medal medal-perak"></i> <?= $a['perak']; ?></span>
                            <span title="Perunggu"><i class="fas fa-medal medal-perunggu"></i> <?= $a['perunggu']; ?></span>
                        </div>
                        <div class="podium-poin"><?= $a['poin']; ?> Poin</div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabel sisanya -->
    <?php if (!empty($rest)): ?>
        <div class="lb-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 lb-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:60px;">#</th>
                            <th>Nama Atlet</th>
                            <th class="d-none d-md-table-cell">Kontingen</th>
                            <th class="text-center" title="Emas"><i class="fas fa-medal medal-emas"></i></th>
                            <th class="text-center" title="Perak"><i class="fas fa-medal medal-perak"></i></th>
                            <th class="text-center" title="Perunggu"><i class="fas fa-medal medal-perunggu"></i></th>
                            <th class="text-center">Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rest as $i => $a): ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $i + 4; ?></td>
                                <td>
                                    <a class="atlet-link" href="<?= base_url('peringkat/atlet/' . ($a['row_id'] ?? urlencode($a['name_key']))); ?>">
                                        <?= htmlspecialchars($a['display_name']); ?>
                                    </a>
                                    <div class="d-md-none text-muted small"><?= htmlspecialchars($a['last_contingent']); ?></div>
                                </td>
                                <td class="d-none d-md-table-cell text-muted small"><?= htmlspecialchars($a['last_contingent']); ?></td>
                                <td class="text-center medal-emas fw-semibold"><?= $a['emas']; ?></td>
                                <td class="text-center medal-perak fw-semibold"><?= $a['perak']; ?></td>
                                <td class="text-center medal-perunggu fw-semibold"><?= $a['perunggu']; ?></td>
                                <td class="text-center"><span class="poin-pill"><?= $a['poin']; ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>
