<?php
session_start();
$translations = [
    'pl' => [
        'title' => 'Hall of clowns',
        'meta_description' => 'Add their name, surname, nickname and photo🤡',
        'add_person' => 'Dodaj osobę',
        'first_name' => 'Imię',
        'last_name' => 'Nazwisko',
        'nickname' => 'Nickname',
        'description' => 'Opis',
        'choose_photo' => 'Wybierz zdjęcie',
        'submit' => 'Dodaj',
        'no_people' => 'Brak dodanych osób.',
        'lang_pl' => 'Polski',
        'lang_en' => 'English',
        'added_ok' => 'Osoba dodana.',
    ],
    'en' => [
        'title' => 'Hall of clowns',
        'meta_description' => 'Dodaj imię, nazwisko, nick i zdjęcie🤡',
        'add_person' => 'Add person',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'nickname' => 'Nickname',
        'description' => 'Description',
        'choose_photo' => 'Choose photo',
        'submit' => 'Add',
        'no_people' => 'No people added yet.',
        'lang_pl' => 'Polski',
        'lang_en' => 'English',
        'added_ok' => 'Person added.',
    ]
];

if (isset($_GET['lang']) && in_array($_GET['lang'], ['pl','en'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = 'pl';
}
$t = $translations[$lang];

$dataFile = __DIR__ . '/people.json';
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function load_people($file) {
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    $arr = json_decode($json, true);
    return is_array($arr) ? $arr : [];
}
function save_people($file, $arr) {
    $tmp = $file . '.tmp';
    $json = json_encode($arr, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($tmp, $json, LOCK_EX);
    rename($tmp, $file);
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $nick  = trim($_POST['nickname'] ?? '');
    $desc  = trim($_POST['description'] ?? '');

    if ($first === '' || $last === '') {
        $errors[] = ($lang === 'pl') ? 'Imię i nazwisko są wymagane.' : 'First and last name required.';
    }

    $photoFilename = null;
    if (!empty($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['photo'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = ($lang === 'pl') ? 'Błąd uploadu zdjęcia.' : 'Upload error.';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
            if (!isset($allowed[$mime])) {
                $errors[] = ($lang === 'pl') ? 'Tylko JPG/PNG/GIF/WEBP.' : 'Only JPG/PNG/GIF/WEBP allowed.';
            } else {
                $ext = $allowed[$mime];
                $safeName = bin2hex(random_bytes(8)) . '.' . $ext;
                $dst = $uploadDir . '/' . $safeName;
                if (!move_uploaded_file($file['tmp_name'], $dst)) {
                    $errors[] = ($lang === 'pl') ? 'Nie udało się zapisać zdjęcia.' : 'Failed to save photo.';
                } else {
                    $photoFilename = 'uploads/' . $safeName;
                }
            }
        }
    }

    if (empty($errors)) {
        $people = load_people($dataFile);
        $people[] = [
            'first_name' => $first,
            'last_name'  => $last,
            'nickname'   => $nick,
            'description'=> $desc,
            'photo'      => $photoFilename,
            'created_at' => date('c'),
        ];
        save_people($dataFile, $people);
        $success = true;
        $qs = '?lang=' . $lang . '&added=1';
        header('Location: ' . $_SERVER['PHP_SELF'] . $qs);
        exit;
    }
}

$people = load_people($dataFile);
$just_added = isset($_GET['added']);
?>
<!doctype html>
<html lang="<?php echo htmlspecialchars($lang); ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo htmlspecialchars($t['title']); ?></title>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <title><?= htmlspecialchars($t['title'], ENT_QUOTES) ?></title>
  <meta name="description" content="<?= htmlspecialchars($t['meta_description'], ENT_QUOTES) ?>">
  <link rel="icon" href="photos/website-icon.webp" type="image/png">
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    :root{
        --bg1: #041428;
        --bg2: #042b59;
        --panel: rgba(255,255,255,0.04);
        --glass: rgba(255,255,255,0.03);
        --accent: #3bb0ff;
        --card-bg: rgba(255,255,255,0.04);
        --muted: rgba(255,255,255,0.7);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
        margin:0;
        font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        background: radial-gradient(1200px 600px at 10% 10%, rgba(3,54,101,0.4), transparent),
                    linear-gradient(180deg, var(--bg1), var(--bg2));
        color:var(--muted);
        -webkit-font-smoothing:antialiased;
        -moz-osx-font-smoothing:grayscale;
        padding:28px;
    }

    .wrap{ max-width:1200px; margin:0 auto; display:flex; gap:22px; align-items:flex-start; }
    header{ display:flex; justify-content:space-between; align-items:center; width:100%; margin-bottom:18px; gap:12px }
    h1{ margin:0; color:#fff; font-size:20px; letter-spacing:0.3px }
    .top-right{ display:flex; align-items:center; gap:8px; }

    .flags a{ text-decoration:none; font-size:22px; display:inline-flex; align-items:center; justify-content:center; width:40px; height:36px; border-radius:8px; background:transparent; color:inherit; transition:transform .18s ease, background .18s ease; }
    .flags a:hover{ transform:translateY(-3px); background: rgba(255,255,255,0.03); }

    .left{
        width:340px;
        background:linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02));
        border-radius:14px;
        padding:18px;
        box-shadow: 0 8px 30px rgba(2,8,23,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
        border:1px solid rgba(255,255,255,0.04);
        backdrop-filter: blur(6px) saturate(120%);
    }
    h2{ margin:0 0 10px 0; color:#ecf9ff; font-size:16px }
    label{ display:block; margin-top:10px; font-size:13px; color:rgba(255,255,255,0.8) }
    input[type="text"], textarea{ width:100%; margin-top:6px; padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.04); background: rgba(255,255,255,0.02); color:var(--muted); font-size:14px; outline:none; transition:box-shadow .12s ease, transform .12s ease }
    input[type="text"]:focus, textarea:focus{ box-shadow: 0 4px 18px rgba(11,105,255,0.12); transform: translateY(-1px) }
    textarea{ min-height:96px; resize:vertical }
    input[type="file"]{ margin-top:8px; color:var(--muted) }

    .btn{
        display:inline-block; margin-top:12px; padding:10px 14px; border-radius:10px; font-weight:700; cursor:pointer;
        background:linear-gradient(180deg, #0b69ff, #004ad6); color:white; border:0; box-shadow: 0 8px 22px rgba(11,105,255,0.14);
        transition: transform .12s ease, box-shadow .12s ease;
    }
    .btn:hover{ transform: translateY(-3px); box-shadow: 0 16px 36px rgba(11,105,255,0.18) }

    .msgs{ margin-bottom:8px; }
    .error{ color:#ffcdd2; background:rgba(139,0,0,0.12); padding:8px; border-radius:8px; font-size:13px }
    .ok{ color:#c8f7da; background:rgba(0,120,80,0.08); padding:8px; border-radius:8px; font-size:13px }

    .grid-wrap{ flex:1; }
    .grid{
        display:grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap:18px;
    }

    .card{
        background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
        border-radius:12px;
        padding:14px;
        display:flex;
        gap:12px;
        align-items:flex-start;
        border:1px solid rgba(255,255,255,0.03);
        transform-origin:center;
        transition: transform .18s cubic-bezier(.2,.9,.2,1), box-shadow .18s ease, border-color .18s ease;
        box-shadow: 0 8px 26px rgba(2,8,23,0.45);
        overflow:hidden;
        position:relative;
        min-height:92px;
        animation: fadeInUp .36s ease both;
    }
    .card:hover{ transform: translateY(-8px) scale(1.01); border-color: rgba(59,176,255,0.12); box-shadow: 0 20px 60px rgba(2,8,23,0.55); }

    .avatar{
        width:76px; height:76px; border-radius:10px; overflow:hidden; flex:0 0 76px;
        display:flex; align-items:center; justify-content:center; background:linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.015));
        border:1px solid rgba(255,255,255,0.03);
    }
    .avatar img{ width:100%; height:100%; object-fit:cover; display:block }

    .meta{ flex:1; min-width:0; }
    .meta .name{ color:#fff; font-weight:700; font-size:15px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
    .meta .nick{ display:inline-block; margin-top:6px; font-size:13px; color:rgba(255,255,255,0.8); background: rgba(255,255,255,0.02); padding:4px 8px; border-radius:999px; border:1px solid rgba(255,255,255,0.02) }
    .meta .desc { margin-top:8px; font-size:13px; color:rgba(255,255,255,0.85); line-height:1.3; max-height:3.9em; overflow:hidden; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; transition: max-height .18s ease; }
    .card:hover .meta .desc{ max-height:20em; -webkit-line-clamp:999; }

    .meta .created{ margin-top:10px; font-size:12px; color:rgba(255,255,255,0.5) }

    .empty{ padding:28px; border-radius:12px; background: rgba(255,255,255,0.02); text-align:center; color:rgba(255,255,255,0.6) }

    @media (max-width:980px){
        .wrap{ flex-direction:column; padding:18px; gap:16px }
        .left{ width:100% }
    }
    @media (max-width:520px){
        .avatar{ width:62px; height:62px; flex:0 0 62px }
        .card{ padding:12px }
    }

    @keyframes fadeInUp {
        from { opacity:0; transform: translateY(8px) }
        to   { opacity:1; transform: translateY(0) }
    }

    .bg-shape{
        position:fixed; right:-120px; top:-80px; width:420px; height:420px;
        background: radial-gradient(circle at 20% 20%, rgba(59,176,255,0.12), transparent 30%),
                    radial-gradient(circle at 80% 80%, rgba(139,92,246,0.06), transparent 20%);
        filter: blur(36px); pointer-events:none; z-index:0;
    }

    footer{ margin-top:20px; color:rgba(255,255,255,0.45); font-size:13px; text-align:center }
</style>
</head>
<body class="<?php echo $just_added ? 'just-added' : ''; ?>">

<div class="bg-shape" aria-hidden="true"></div>

<header class="wrap" style="align-items:flex-end">
    <div style="display:flex;flex-direction:column">
        <h1><?php echo htmlspecialchars($t['title']); ?></h1>

    <div class="top-right">
        <div class="flags" aria-hidden="false">
            <a href="?lang=pl" title="<?php echo $t['lang_pl']; ?>">🇵🇱</a>
            <a href="?lang=en" title="<?php echo $t['lang_en']; ?>">🇬🇧</a>
        </div>
    </div>
</header>

<div class="wrap" style="align-items:flex-start; z-index:1;">
    <aside class="left" aria-label="form panel">
        <h2><?php echo htmlspecialchars($t['add_person']); ?></h2>

        <div class="msgs">
            <?php if (!empty($errors)): ?>
                <div class="error"><?php echo htmlspecialchars(implode(' | ', $errors)); ?></div>
            <?php elseif ($just_added): ?>
                <div class="ok"><?php echo htmlspecialchars($t['added_ok']); ?></div>
            <?php endif; ?>
        </div>

        <form method="post" enctype="multipart/form-data" novalidate>
            <label><?php echo htmlspecialchars($t['first_name']); ?>
                <input type="text" name="first_name" required autocomplete="given-name">
            </label>

            <label><?php echo htmlspecialchars($t['last_name']); ?>
                <input type="text" name="last_name" required autocomplete="family-name">
            </label>

            <label><?php echo htmlspecialchars($t['nickname']); ?>
                <input type="text" name="nickname" autocomplete="nickname">
            </label>

            <label><?php echo htmlspecialchars($t['description']); ?>
                <textarea name="description"></textarea>
            </label>

            <label><?php echo htmlspecialchars($t['choose_photo']); ?>
                <input type="file" name="photo" accept="image/*">
            </label>

            <button class="btn" type="submit"><?php echo htmlspecialchars($t['submit']); ?></button>
        </form>
    </aside>

    <main class="grid-wrap">
        <?php if (empty($people)): ?>
            <div class="empty"><?php echo htmlspecialchars($t['no_people']); ?></div>
        <?php else: ?>
            <div class="grid" aria-live="polite">
                <?php foreach (array_reverse($people) as $p): ?>
                    <article class="card" role="article">
                        <div class="avatar" aria-hidden="true">
                            <?php if (!empty($p['photo']) && file_exists(__DIR__ . '/' . $p['photo'])): ?>
                                <img src="<?php echo htmlspecialchars($p['photo']); ?>" alt="<?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?>">
                            <?php else: ?>
                                <?php
                                    $initials = mb_strtoupper((mb_substr($p['first_name'],0,1) . mb_substr($p['last_name'],0,1)));
                                ?>
                                <div style="font-weight:800; font-size:20px; color:#dff7ff"><?php echo htmlspecialchars($initials); ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="meta">
                            <div class="name"><?php echo htmlspecialchars($p['first_name'] . ' ' . $p['last_name']); ?></div>
                            <?php if (!empty($p['nickname'])): ?>
                                <div class="nick"><?php echo htmlspecialchars($p['nickname']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($p['description'])): ?>
                                <div class="desc"><?php echo nl2br(htmlspecialchars($p['description'])); ?></div>
                            <?php endif; ?>
                            <div class="created"><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($p['created_at']))); ?></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

</body>
</html>
