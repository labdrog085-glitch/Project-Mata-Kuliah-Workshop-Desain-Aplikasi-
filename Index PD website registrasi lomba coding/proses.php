
<?php

header('Content-Type: application/json; charset=utf-8');

// Mengambil data dari form
$nama = trim($_POST['nama'] ?? '');
$email = trim($_POST['email'] ?? '');
$hp = trim($_POST['hp'] ?? '');
$umur = trim($_POST['umur'] ?? '');
$github = trim($_POST['github'] ?? '');
$kategori = trim($_POST['kategori'] ?? '');
$peserta = trim($_POST['peserta'] ?? '');

$error = [];

// Whitelist kategori dan peserta
$kategoriValid = [
    'Web Development',
    'Mobile Development',
    'Data Science'
];

$pesertaValid = [
    'Individu',
    'Tim'
];

// Validasi Nama
$jumlahKarakter = preg_match_all('/\X/u', $nama);

if ($nama === '') {
    $error['nama'] = 'Nama wajib diisi.';
} elseif ($jumlahKarakter < 3 || $jumlahKarakter > 50) {
    $error['nama'] = 'Nama harus 3-50 karakter.';
}

// Validasi Email
if ($email === '') {
    $error['email'] = 'Email wajib diisi.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error['email'] = 'Format email tidak valid.';
}

// Validasi Nomor HP menggunakan REGEX
if ($hp === '') {
    $error['hp'] = 'Nomor HP wajib diisi.';
} elseif (!preg_match('/^(?:\+62|62|0)8[1-9][0-9]{7,10}$/', $hp)) {
    $error['hp'] = 'Format nomor HP tidak valid.';
}

// Validasi Umur
if ($umur === '') {
    $error['umur'] = 'Umur wajib diisi.';
} elseif (
    !ctype_digit($umur) ||
    (int)$umur < 17 ||
    (int)$umur > 19
) {
    $error['umur'] = 'Peringatan: Umur harus antara 17-19 tahun.';
}

// Validasi URL GitHub (Opsional)
if ($github !== '') {

    $url = filter_var($github, FILTER_VALIDATE_URL);
    $parsed = $url ? parse_url($url) : false;

    if (
        !$parsed ||
        !in_array(
            strtolower($parsed['scheme'] ?? ''),
            ['http', 'https']
        ) ||
        !in_array(
            strtolower($parsed['host'] ?? ''),
            ['github.com', 'www.github.com']
        ) ||
        !preg_match(
            '/^\/[^\/]+\/[^\/]+\/?$/',
            $parsed['path'] ?? ''
        )
    ) {
        $error['github'] = 'URL repositori GitHub tidak valid.';
    }
}

// Validasi Kategori dengan Whitelist
if (!in_array($kategori, $kategoriValid, true)) {
    $error['kategori'] = 'Silakan pilih kategori yang tersedia.';
}

// Validasi Peserta dengan Whitelist
if (!in_array($peserta, $pesertaValid, true)) {
    $error['peserta'] = 'Silakan pilih jenis peserta.';
}

// Jika terdapat error
if (!empty($error)) {

    echo json_encode([
        'errors' => $error
    ]);

    exit;
}

// Fungsi keamanan output
function aman($data) {
    return htmlspecialchars(
        $data,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Menampilkan hasil registrasi
$output = '
    <h3>Registrasi Berhasil!</h3>
    <p>Nama: ' . aman($nama) . '</p>
    <p>Email: ' . aman($email) . '</p>
    <p>Nomor HP: ' . aman($hp) . '</p>
    <p>Umur: ' . aman($umur) . '</p>
    <p>GitHub: ' . aman($github) . '</p>
    <p>Kategori: ' . aman($kategori) . '</p>
    <p>Peserta: ' . aman($peserta) . '</p>
';

// Mengirim hasil ke HTML
echo json_encode([
    'success' => true,
    'output' => $output
]);

?>
