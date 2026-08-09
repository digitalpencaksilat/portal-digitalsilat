<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="no-referrer">
    <title>Publishing Token Dibuat</title>
    <style>
        :root { color-scheme: light; font-family: Arial, sans-serif; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f1f3f5; color: #202124; }
        main { width: min(720px, 100%); padding: 32px; border-top: 6px solid #c60000; border-radius: 16px; background: #fff; box-shadow: 0 18px 50px rgba(0, 0, 0, .12); }
        h1 { margin: 0 0 8px; font-size: 1.7rem; }
        .warning { margin: 22px 0; padding: 14px 16px; border-radius: 10px; background: #fff3cd; color: #664d03; }
        label { display: block; margin-bottom: 8px; font-weight: 700; }
        textarea { width: 100%; min-height: 100px; resize: none; padding: 14px; border: 1px solid #adb5bd; border-radius: 10px; font: 14px/1.5 monospace; overflow-wrap: anywhere; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        button, a { display: inline-block; padding: 11px 16px; border: 0; border-radius: 8px; font: inherit; font-weight: 700; text-decoration: none; cursor: pointer; }
        button { background: #c60000; color: #fff; }
        a { background: #e9ecef; color: #202124; }
        small { color: #6c757d; }
    </style>
</head>
<body>
    <main>
        <h1>Publishing Token Berhasil Dibuat</h1>
        <small><?= html_escape($publisher_token_name); ?></small>
        <div class="warning"><strong>Simpan token sekarang.</strong> Token tidak dapat ditampilkan kembali. Jangan refresh atau membagikan halaman ini.</div>
        <label for="publisher-token">Token</label>
        <textarea id="publisher-token" readonly spellcheck="false"><?= html_escape($publisher_plain_token); ?></textarea>
        <div class="actions">
            <button type="button" id="copy-token">Salin Token</button>
            <a href="<?= base_url('admin/api_management#publishing-api'); ?>">Kembali ke Manajemen API</a>
        </div>
    </main>
    <script>
        document.getElementById('copy-token').addEventListener('click', function () {
            var field = document.getElementById('publisher-token');
            field.select();
            navigator.clipboard.writeText(field.value).then(function () {
                document.getElementById('copy-token').textContent = 'Token Disalin';
            });
        });
    </script>
</body>
</html>
