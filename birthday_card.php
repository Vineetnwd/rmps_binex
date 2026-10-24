<?php
/**
 * Birthday Card — with animated GIF download for social media
 * Requires: make_birthday_gif.py + Pillow in the same directory
 */
include('system/function.php');

$id   = $_GET['id']   ?? '';
$type = $_GET['type'] ?? 'student';

if ($type == 'student') {
    $data  = get_data('student', $id)['data'];
    $name  = $data['student_name'];
    $photo = $data['student_photo'];
} else {
    $data  = get_data('employee', $id)['data'];
    $name  = $data['name'];
    $photo = $data['photo'];
}

$name        = htmlspecialchars($name ?? 'Birthday Person');
$photo_path  = $base_url . 'upload/' . ($photo ?? '');
$photo_local = __DIR__ . '/upload/' . ($photo ?? '');

// ── GIF download action (pure PHP — no Python needed) ────────────────────────
if (isset($_GET['download_gif'])) {
    require_once __DIR__ . '/make_birthday_gif.php';

    $safe_name = preg_replace('/[^a-zA-Z0-9 ]/', '', html_entity_decode($name));
    $out_file  = sys_get_temp_dir() . '/bday_' . md5($id . $type) . '.gif';
    $photo_src = file_exists($photo_local) ? $photo_local : null;

    $ok = generate_birthday_gif($safe_name, $photo_src, $out_file);

    if ($ok && file_exists($out_file)) {
        $slug = preg_replace('/\s+/', '_', strtolower($safe_name));
        header('Content-Type: image/gif');
        header('Content-Disposition: attachment; filename="birthday_' . $slug . '.gif"');
        header('Content-Length: ' . filesize($out_file));
        readfile($out_file);
        @unlink($out_file);
        exit;
    } else {
        http_response_code(500);
        echo 'GIF generation failed. Please ensure the GD extension is enabled (php-gd).';
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Happy Birthday – <?= $name ?></title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@300;400&display=swap" rel="stylesheet"/>
<style>
/* ── Reset ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --gold:#e8c97e; --rose:#f2a7b3; --deep:#1a0a2e;
  --ink:#0d0620;  --cream:#fdf6ee; --purple:#b084f5;
}
body{
  min-height:100vh;display:flex;flex-direction:column;
  align-items:center;justify-content:center;gap:28px;
  background:var(--ink);font-family:'DM Sans',sans-serif;
  overflow-x:hidden;padding:24px;
}

/* ── Stars ── */
.stars{position:fixed;inset:0;pointer-events:none;z-index:0}
.star{position:absolute;border-radius:50%;background:#fff;
  animation:twinkle var(--d,3s) ease-in-out infinite var(--delay,0s);opacity:0}
@keyframes twinkle{
  0%,100%{opacity:0;transform:scale(.6)}
  50%{opacity:var(--op,.8);transform:scale(1)}
}

/* ── Card ── */
.scene{position:relative;z-index:1;perspective:1200px;
  animation:rise .9s cubic-bezier(.16,1,.3,1) both}
@keyframes rise{
  from{opacity:0;transform:translateY(60px) scale(.92)}
  to  {opacity:1;transform:translateY(0)    scale(1)}
}
.card{
  width:520px;
  background:linear-gradient(160deg,#1f0d3a 0%,#0d0620 60%,#1a0525 100%);
  border-radius:28px;padding:52px 44px 44px;text-align:center;
  border:1px solid rgba(232,201,126,.22);
  box-shadow:0 0 0 1px rgba(232,201,126,.07),
             0 30px 80px rgba(0,0,0,.7),
             0 0 120px rgba(232,201,126,.07) inset;
  position:relative;overflow:hidden;
}
.card::before,.card::after{
  content:'';position:absolute;border-radius:50%;filter:blur(60px);pointer-events:none;
}
.card::before{width:200px;height:200px;top:-70px;left:-70px;
  background:rgba(242,167,179,.18);animation:pulse 5s ease-in-out infinite}
.card::after{width:200px;height:200px;bottom:-70px;right:-70px;
  background:rgba(232,201,126,.15);animation:pulse 5s ease-in-out infinite 2.5s}
@keyframes pulse{
  0%,100%{transform:scale(1);opacity:.6}
  50%    {transform:scale(1.2);opacity:1}
}

/* ── Badge ── */
.badge{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(232,201,126,.1);border:1px solid rgba(232,201,126,.28);
  border-radius:99px;padding:6px 20px;
  font-size:.7rem;letter-spacing:.2em;text-transform:uppercase;
  color:var(--gold);margin-bottom:30px;
  animation:fadein .6s .4s both;
}
@keyframes fadein{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)}}

/* ── Photo ── */
.photo-ring{
  position:relative;display:inline-block;margin-bottom:26px;
  animation:fadein .6s .5s both;
}
.photo-ring::before{
  content:'';position:absolute;inset:-6px;border-radius:50%;
  background:conic-gradient(var(--gold) 0deg,var(--rose) 120deg,var(--purple) 240deg,var(--gold) 360deg);
  animation:spin 8s linear infinite;z-index:0;
}
@keyframes spin{to{transform:rotate(360deg)}}
.photo-ring::after{
  content:'';position:absolute;inset:-3px;border-radius:50%;
  background:linear-gradient(160deg,#1f0d3a,#0d0620);z-index:1;
}
.photo-ring img{
  position:relative;z-index:2;
  width:148px;height:148px;border-radius:50%;object-fit:cover;display:block;
}

/* ── Name / Headline ── */
.name{
  font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:700;
  color:var(--cream);line-height:1.15;margin-bottom:4px;
  animation:fadein .6s .65s both;
}
.headline{
  font-family:'Playfair Display',serif;font-style:italic;
  font-size:.95rem;color:var(--gold);letter-spacing:.04em;
  margin-bottom:26px;animation:fadein .6s .75s both;
}

/* ── Divider ── */
.divider{
  display:flex;align-items:center;gap:12px;margin:0 auto 24px;
  animation:fadein .6s .85s both;
}
.divider span{flex:1;height:1px;background:rgba(232,201,126,.2)}

/* ── Message ── */
.message{
  font-size:.9rem;font-weight:300;color:rgba(255,255,255,.6);
  line-height:1.8;max-width:340px;margin:0 auto 32px;
  animation:fadein .6s .95s both;
}

/* ── Candles ── */
.candles{
  display:flex;justify-content:center;gap:18px;
  margin-bottom:32px;animation:fadein .6s 1.05s both;
}
.candle{position:relative;display:flex;flex-direction:column;align-items:center}
.flame{
  width:11px;height:18px;margin-bottom:2px;
  background:radial-gradient(ellipse at 50% 80%,#ffe26b 0%,#ff9d3d 60%,transparent 100%);
  border-radius:50% 50% 30% 30%;
  animation:flicker 1.2s ease-in-out infinite;
  transform-origin:bottom center;filter:blur(.5px);
}
.candle:nth-child(2) .flame{animation-delay:.22s}
.candle:nth-child(3) .flame{animation-delay:.44s}
.candle:nth-child(4) .flame{animation-delay:.16s}
.candle:nth-child(5) .flame{animation-delay:.38s}
@keyframes flicker{
  0%,100%{transform:scaleX(1)   scaleY(1)    rotate(-2deg);opacity:1}
  25%    {transform:scaleX(.84) scaleY(1.12) rotate(2deg);opacity:.9}
  50%    {transform:scaleX(1.1) scaleY(.94)  rotate(-1deg);opacity:1}
  75%    {transform:scaleX(.9)  scaleY(1.06) rotate(1.5deg);opacity:.85}
}
.candle-body{width:11px;height:36px;border-radius:3px 3px 2px 2px}
.c1 .candle-body{background:linear-gradient(to bottom,#f9d4dc,#e8a0ad)}
.c2 .candle-body{background:linear-gradient(to bottom,#d4eaf9,#8ab4d4)}
.c3 .candle-body{background:linear-gradient(to bottom,#d4f9d8,#8ad49a)}
.c4 .candle-body{background:linear-gradient(to bottom,#f9f0d4,#d4b88a)}
.c5 .candle-body{background:linear-gradient(to bottom,#e8d4f9,#b48ad4)}

/* ── Footer label ── */
.footer{
  font-size:.7rem;letter-spacing:.15em;text-transform:uppercase;
  color:rgba(232,201,126,.4);animation:fadein .6s 1.15s both;
}

/* ── Confetti ── */
.confetti-piece{
  position:fixed;top:-14px;border-radius:2px;opacity:0;pointer-events:none;z-index:10;
  animation:confettiFall var(--cf-d,3.5s) var(--cf-delay,0s) ease-in forwards;
}
@keyframes confettiFall{
  0%  {opacity:1;transform:translateY(0) rotate(0deg)}
  100%{opacity:0;transform:translateY(110vh) rotate(720deg)}
}

/* ── Download toolbar ── */
.toolbar{
  position:relative;z-index:1;display:flex;gap:14px;flex-wrap:wrap;
  justify-content:center;animation:fadein .6s 1.3s both;
}
.btn{
  display:inline-flex;align-items:center;gap:9px;
  padding:12px 24px;border-radius:99px;
  font-family:'DM Sans',sans-serif;font-size:.85rem;font-weight:400;
  cursor:pointer;border:none;text-decoration:none;transition:all .25s;
}
.btn-gif{
  background:linear-gradient(135deg,var(--gold),#c8960a);
  color:#1a0a2e;box-shadow:0 4px 24px rgba(232,201,126,.35);
}
.btn-gif:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(232,201,126,.5)}
.btn-gif:active{transform:translateY(0)}
.btn-png{
  background:rgba(255,255,255,.06);color:var(--cream);
  border:1px solid rgba(255,255,255,.15);
}
.btn-png:hover{background:rgba(255,255,255,.12);transform:translateY(-2px)}
.spinner{display:none;width:16px;height:16px;
  border:2px solid rgba(26,10,46,.3);border-top-color:#1a0a2e;
  border-radius:50%;animation:spin .7s linear infinite}

/* ── Tip ── */
.tip{
  position:relative;z-index:1;
  font-size:.72rem;color:rgba(255,255,255,.3);letter-spacing:.05em;
  animation:fadein .6s 1.5s both;
}

@media(max-width:560px){
  .card{width:95vw;padding:36px 24px 32px}
  .name{font-size:1.9rem}
}
</style>
</head>
<body>

<!-- Stars -->
<div class="stars" id="stars"></div>

<!-- Card -->
<div class="scene">
  <div class="card" id="birthdayCard">

    <div class="badge">
      <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
      </svg>
      Special Day
    </div>

    <div class="photo-ring">
      <img src="<?= $photo_path ?>"
           alt="<?= $name ?>"
           onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode(html_entity_decode($name)) ?>&background=1f0d3a&color=e8c97e&size=148'"/>
    </div>

    <div class="name"><?= $name ?></div>
    <div class="headline">Today is all about you ✦</div>

    <div class="divider">
      <span></span>
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#e8c97e" stroke-width="1.5">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
      </svg>
      <span></span>
    </div>

    <p class="message">
      May this birthday bring you boundless joy,<br>
      beautiful memories &amp; every happiness<br>
      your heart desires. 🌟
    </p>

    <div class="candles">
      <div class="candle c1"><div class="flame"></div><div class="candle-body"></div></div>
      <div class="candle c2"><div class="flame"></div><div class="candle-body"></div></div>
      <div class="candle c3"><div class="flame"></div><div class="candle-body"></div></div>
      <div class="candle c4"><div class="flame"></div><div class="candle-body"></div></div>
      <div class="candle c5"><div class="flame"></div><div class="candle-body"></div></div>
    </div>

    <div class="footer">🎂 &nbsp; Happy Birthday &nbsp; 🎂</div>
  </div>
</div>

<!-- Download toolbar -->
<!--<div class="toolbar">-->

  <!-- Animated GIF (server-side Python render) -->
<!--  <a id="gifBtn"-->
<!--     href="?id=<?= urlencode($_GET['id'] ?? '') ?>&type=<?= urlencode($type) ?>&download_gif=1"-->
<!--     class="btn btn-gif"-->
<!--     onclick="return startGif(this)">-->
<!--    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"-->
<!--         stroke="currentColor" stroke-width="2">-->
<!--      <rect x="2" y="2" width="20" height="20" rx="3"/>-->
<!--      <path d="M8 12h3m0 0v-2m0 2v2M14 10v4m3-4v2h-2v-2M14 14h0"/>-->
<!--    </svg>-->
<!--    <span id="gifLabel">Download Animated GIF</span>-->
<!--    <div class="spinner" id="gifSpinner"></div>-->
<!--  </a>-->

  <!-- Static PNG (client-side html2canvas) -->
<!--  <button class="btn btn-png" onclick="downloadPng()">-->
<!--    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"-->
<!--         stroke="currentColor" stroke-width="2">-->
<!--      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>-->
<!--      <polyline points="7 10 12 15 17 10"/>-->
<!--      <line x1="12" y1="15" x2="12" y2="3"/>-->
<!--    </svg>-->
<!--    Save as PNG-->
<!--  </button>-->

<!--</div>-->

<p class="tip">📲 &nbsp; 1080 × 1080 px &nbsp;·&nbsp; Ready for Instagram, WhatsApp &amp; Facebook</p>

<!-- html2canvas for PNG export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
/* ── Stars ── */
const starsEl = document.getElementById('stars');
for (let i = 0; i < 130; i++) {
  const s   = document.createElement('div');
  s.className = 'star';
  const sz = Math.random() * 2.5 + .5;
  s.style.cssText = `left:${Math.random()*100}%;top:${Math.random()*100}%;`
    + `width:${sz}px;height:${sz}px;`
    + `--d:${(Math.random()*4+2).toFixed(1)}s;`
    + `--delay:-${(Math.random()*6).toFixed(1)}s;`
    + `--op:${(Math.random()*.7+.3).toFixed(2)};`;
  starsEl.appendChild(s);
}

/* ── Confetti ── */
const colors = ['#e8c97e','#f2a7b3','#b084f5','#7ed4e8','#f9f0d4','#8ad49a'];
function launchConfetti() {
  for (let i = 0; i < 60; i++) {
    const p = document.createElement('div');
    p.className = 'confetti-piece';
    p.style.cssText = `left:${Math.random()*100}vw;`
      + `background:${colors[Math.floor(Math.random()*colors.length)]};`
      + `width:${Math.random()*10+6}px;height:${Math.random()*10+6}px;`
      + `border-radius:${Math.random()>.5?'50%':'2px'};`
      + `--cf-d:${(Math.random()*2.5+2.5).toFixed(1)}s;`
      + `--cf-delay:${(Math.random()*1.5).toFixed(2)}s;`;
    document.body.appendChild(p);
    p.addEventListener('animationend', () => p.remove());
  }
}
launchConfetti();
setInterval(launchConfetti, 5000);

/* ── GIF download (server) ── */
function startGif(el) {
  document.getElementById('gifLabel').textContent = 'Generating GIF…';
  document.getElementById('gifSpinner').style.display = 'block';
  el.style.pointerEvents = 'none';
  // Re-enable after download initiates
  setTimeout(() => {
    document.getElementById('gifLabel').textContent = 'Download Animated GIF';
    document.getElementById('gifSpinner').style.display = 'none';
    el.style.pointerEvents = '';
  }, 12000);
  return true; // allow href
}

/* ── PNG download (client) ── */
function downloadPng() {
  const card = document.getElementById('birthdayCard');
  html2canvas(card, { scale: 2, useCORS: true, backgroundColor: null })
    .then(canvas => {
      const a = document.createElement('a');
      a.download = 'birthday_<?= preg_replace('/[^a-z0-9]/i','_', html_entity_decode($name)) ?>.png';
      a.href = canvas.toDataURL('image/png');
      a.click();
    });
}
</script>
</body>
</html>