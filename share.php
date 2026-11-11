<?php
/**
 * Link bagikan event di domain sendiri: /share/<slug>  (lihat .htaccess → share.php?slug=<slug>)
 *
 * Situs utama adalah SPA dengan hash routing (#/event/<slug>), jadi crawler WhatsApp/Facebook
 * yang tidak menjalankan JavaScript hanya melihat meta tag umum. Halaman ini menuliskan
 * og:title / og:description / og:image milik event, lalu mengalihkan pengunjung manusia ke
 * halaman event yang sebenarnya. ?ref=KODE (affiliate) diteruskan ke situs.
 *
 * Pengganti https://yakesma-event-api.vercel.app/share/<slug> (rute itu tetap hidup untuk link lama).
 */

const API_CONTENT = 'https://yakesma-event-api.vercel.app/api/content';
const CACHE_SECONDS = 120;

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, max-age=0');
header('X-Robots-Tag: noindex');

function e($s) { return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8'); }

/** Konten situs dari API, disimpan sebentar di folder temp supaya tidak memanggil API setiap klik. */
function site_content() {
  $cache = rtrim(sys_get_temp_dir(), '/') . '/yakesma-share-content.json';
  if (is_file($cache) && time() - filemtime($cache) < CACHE_SECONDS) {
    $data = json_decode((string) @file_get_contents($cache), true);
    if (is_array($data)) return $data;
  }
  $body = false;
  if (function_exists('curl_init')) {
    $ch = curl_init(API_CONTENT);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_CONNECTTIMEOUT => 3]);
    $body = curl_exec($ch);
    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) $body = false;
    curl_close($ch);
  } else {
    $ctx = stream_context_create(['http' => ['timeout' => 5]]);
    $body = @file_get_contents(API_CONTENT, false, $ctx);
  }
  $data = $body ? json_decode($body, true) : null;
  if (is_array($data)) {
    @file_put_contents($cache, $body, LOCK_EX);
    return $data;
  }
  // API tidak terjangkau: pakai salinan lama bila ada, supaya link tetap bergambar.
  $old = is_file($cache) ? json_decode((string) @file_get_contents($cache), true) : null;
  return is_array($old) ? $old : [];
}

// Hanya dua domain resmi; host lain (header palsu) jatuh ke domain utama. Situs selalu HTTPS (.htaccess).
$host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
if (!in_array($host, ['yakesma-event.morningflow.id', 'runforcharity.morningflow.id'], true)) $host = 'yakesma-event.morningflow.id';
$origin = 'https://' . $host;

$slug = strtolower((string) ($_GET['slug'] ?? ''));
if (!preg_match('/^[a-z0-9-]{1,60}$/', $slug)) $slug = '';
$ref = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) ($_GET['ref'] ?? '')));
$ref = strlen($ref) >= 3 ? substr($ref, 0, 20) : '';
$refQuery = $ref ? '?ref=' . $ref : '';

$content = $slug ? site_content() : [];
$event = null;
foreach (($content['events'] ?? []) as $ev) {
  if (is_array($ev) && ($ev['slug'] ?? '') === $slug) { $event = $ev; break; }
}

$siteName = 'YAKESMA';
if ($event) {
  $title = (string) ($event['title'] ?? 'Event YAKESMA');
  $desc = (string) ($event['excerpt'] ?? '');
  $image = (string) (($event['imageWide'] ?? '') ?: (($event['imageSquare'] ?? '') ?: $origin . '/yakesma-logo.png'));
  $target = $origin . '/#/event/' . $slug . $refQuery;
} else {
  $title = 'Event YAKESMA';
  $desc = 'Ikuti event dan program kebaikan YAKESMA — Lembaga Amil Zakat Nasional.';
  $image = $origin . '/yakesma-logo.png';
  $target = $origin . '/#/event' . $refQuery;
  http_response_code($slug ? 404 : 200);
}
if ($image !== '' && $image[0] === '/') $image = $origin . $image;
$shareUrl = $origin . '/share/' . $slug . $refQuery;
?><!doctype html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= e($title) ?> · YAKESMA</title>
<meta name="description" content="<?= e($desc) ?>" />
<meta property="og:type" content="website" />
<meta property="og:site_name" content="<?= e($siteName) ?>" />
<meta property="og:title" content="<?= e($title) ?>" />
<meta property="og:description" content="<?= e($desc) ?>" />
<meta property="og:url" content="<?= e($shareUrl) ?>" />
<meta property="og:image" content="<?= e($image) ?>" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?= e($title) ?>" />
<meta name="twitter:description" content="<?= e($desc) ?>" />
<meta name="twitter:image" content="<?= e($image) ?>" />
<link rel="icon" type="image/png" href="/yakesma-mark.png" />
<style>body{font-family:system-ui,sans-serif;text-align:center;padding:3rem 1.5rem;color:#185838}a{color:#28a840}</style>
</head>
<body>
<p>Mengalihkan ke <?= e($title) ?>…</p>
<p><a href="<?= e($target) ?>">Klik di sini jika tidak dialihkan otomatis</a></p>
<script>location.replace(<?= json_encode($target, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);</script>
</body>
</html>
