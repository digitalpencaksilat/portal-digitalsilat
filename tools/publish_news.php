<?php
if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$root = dirname(__DIR__);
$env_path = $root . '/.secrets/.env.publisher';
if (!is_file($env_path)) fail('Konfigurasi .secrets/.env.publisher tidak ditemukan.');

$config = parse_env_file($env_path);
$api_url = rtrim(isset($config['NEWS_API_URL']) ? $config['NEWS_API_URL'] : '', '/');
$token = isset($config['NEWS_API_TOKEN']) ? trim($config['NEWS_API_TOKEN']) : '';

if ($api_url !== 'https://portal.digitalsilat.com') fail('NEWS_API_URL harus https://portal.digitalsilat.com.');
if (!preg_match('/^[a-f0-9]{64}$/i', $token)) fail('NEWS_API_TOKEN belum diisi atau formatnya salah.');
if ($argc < 3) fail('Penggunaan: php tools/publish_news.php article.json cover.jpg');

$article_path = realpath($argv[1]);
$cover_path = realpath($argv[2]);
if (!$article_path || !is_file($article_path)) fail('File artikel JSON tidak ditemukan.');
if (!$cover_path || !is_file($cover_path)) fail('File cover tidak ditemukan.');

$article = json_decode(file_get_contents($article_path), TRUE);
if (!is_array($article)) fail('Format article.json tidak valid.');

$required = ['title', 'excerpt', 'content', 'image_alt'];
foreach ($required as $field) {
    if (!isset($article[$field]) || trim((string) $article[$field]) === '') fail('Field ' . $field . ' wajib diisi.');
}

$mime = function_exists('mime_content_type') ? mime_content_type($cover_path) : 'application/octet-stream';
$fields = [
    'title' => $article['title'],
    'excerpt' => $article['excerpt'],
    'content' => $article['content'],
    'image_alt' => $article['image_alt'],
    'author_name' => isset($article['author_name']) ? $article['author_name'] : 'Digital Pencak Silat',
    'related_event_slug' => isset($article['related_event_slug']) ? $article['related_event_slug'] : '',
    'cover' => new CURLFile($cover_path, $mime, basename($cover_path)),
];

fwrite(STDOUT, "Mengirim draft ke portal.digitalsilat.com...\n");
$curl = curl_init($api_url . '/api/v1/publisher/news/drafts');
curl_setopt_array($curl, [
    CURLOPT_POST => TRUE,
    CURLOPT_POSTFIELDS => $fields,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token, 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_FOLLOWLOCATION => FALSE,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_TIMEOUT => 90,
    CURLOPT_SSL_VERIFYPEER => TRUE,
    CURLOPT_SSL_VERIFYHOST => 2,
]);
$body = curl_exec($curl);
$http_code = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
$curl_error = curl_error($curl);
curl_close($curl);

if ($body === FALSE) fail('Koneksi gagal: ' . $curl_error);
$result = json_decode($body, TRUE);
if ($http_code !== 201 || !is_array($result) || !isset($result['status']) || $result['status'] !== 'success') {
    $message = is_array($result) && isset($result['message']) ? $result['message'] : 'Respons API tidak valid.';
    fail('API menolak request (HTTP ' . $http_code . '): ' . $message);
}

fwrite(STDOUT, "Draft berita berhasil dibuat.\n\n");
fwrite(STDOUT, 'Article ID : ' . $result['article']['id'] . "\n");
fwrite(STDOUT, 'Status     : Draft' . "\n");
fwrite(STDOUT, 'Preview    : ' . $result['article']['preview_url'] . "\n");

function parse_env_file($path)
{
    $values = [];
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === FALSE) continue;
        list($key, $value) = explode('=', $line, 2);
        $values[trim($key)] = trim($value, " \t\n\r\0\x0B\"'");
    }
    return $values;
}

function fail($message)
{
    fwrite(STDERR, "Error: " . $message . "\n");
    exit(1);
}
