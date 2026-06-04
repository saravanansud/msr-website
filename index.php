<?php
/* ===========================================================================
   Midwest Specialty Robotics — pre-launch access gate
   Public splash (Coming Soon) -> password -> serves the real site (app.html).
   The password lives ONLY in auth.config.php on the server (git-ignored),
   or in a MSR_SITE_PASSWORD environment variable. Never in the repo.
   =========================================================================== */

$cookieSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (PHP_VERSION_ID >= 70300) {
  session_set_cookie_params(['lifetime'=>0,'path'=>'/','httponly'=>true,'secure'=>$cookieSecure,'samesite'=>'Lax']);
} else {
  session_set_cookie_params(0, '/; samesite=Lax', '', $cookieSecure, true);
}
session_start();

/* Load the password: env var first, then server-only config file. */
$PASSWORD = getenv('MSR_SITE_PASSWORD');
if ($PASSWORD === false || $PASSWORD === '') {
  $PASSWORD = null;
  $cfg = __DIR__ . '/auth.config.php';
  if (is_file($cfg)) {
    $val = require $cfg;
    if (is_string($val) && $val !== '') { $PASSWORD = $val; }
  }
}

/* Logout */
if (isset($_GET['logout'])) {
  $_SESSION = [];
  session_destroy();
  header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
  exit;
}

/* Handle password submit */
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
  if ($PASSWORD !== null && hash_equals($PASSWORD, (string) $_POST['password'])) {
    session_regenerate_id(true);
    $_SESSION['msr_auth'] = true;
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
  }
  usleep(400000); // small delay to slow brute-force
  $error = 'That password is not right. Try again.';
}

/* Authenticated -> serve the real site and stop */
if (!empty($_SESSION['msr_auth'])) {
  $app = __DIR__ . '/app.html';
  if (is_file($app)) {
    header('Content-Type: text/html; charset=utf-8');
    readfile($app);
    exit;
  }
  http_response_code(500);
  echo 'Site temporarily unavailable.';
  exit;
}
/* Otherwise fall through to the splash below. */
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Midwest Specialty Robotics — Coming Soon</title>
<meta name="description" content="Midwest Specialty Robotics is launching soon. Wisconsin's specialty robotics integrator." />
<meta name="robots" content="noindex,nofollow" />
<meta name="theme-color" content="#092342" />
<link rel="icon" href="favicon.ico" sizes="any" />
<link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png" />
<link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
:root{--navy:#092342;--navy-3:#071b35;--navy-4:#05152a;--teal:#47BDC5;--teal-line:rgba(71,189,197,.28);--orange:#E85D2F;--gray:#ABADB0;--text:#e9eef5;--text-dim:#9bb0cc;--font-display:"Montserrat",sans-serif;--font-body:"Helvetica Neue","Helvetica","Arial",system-ui,sans-serif}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-text-size-adjust:100%}
body{min-height:100dvh;background:var(--navy);color:var(--text);font-family:var(--font-body);font-weight:300;-webkit-font-smoothing:antialiased;display:flex;align-items:center;justify-content:center;padding:32px;position:relative;overflow:hidden}
/* animated brand glow */
.bg{position:absolute;inset:-25% -10%;z-index:0;pointer-events:none;filter:blur(70px);opacity:.6}
.bg i{position:absolute;display:block;border-radius:50%}
.bg .a{width:46%;height:46%;left:58%;top:-8%;background:radial-gradient(circle,rgba(71,189,197,.5),transparent 70%);animation:d1 20s ease-in-out infinite}
.bg .b{width:42%;height:42%;left:-8%;top:55%;background:radial-gradient(circle,rgba(232,93,47,.34),transparent 70%);animation:d2 24s ease-in-out infinite}
@keyframes d1{0%,100%{transform:translate(0,0)}50%{transform:translate(-12%,10%)}}
@keyframes d2{0%,100%{transform:translate(0,0)}50%{transform:translate(14%,-8%)}}
.grain{position:fixed;inset:0;z-index:5;pointer-events:none;background-image:url(assets/img/grain.png);background-size:150px 150px;opacity:.4;mix-blend-mode:soft-light}
.card{position:relative;z-index:2;width:100%;max-width:520px;text-align:center}
.brand{display:inline-flex;align-items:center;gap:14px;margin-bottom:40px}
.brand img{width:54px;height:54px}
.brand .name{font-family:var(--font-display);font-weight:900;font-size:15px;letter-spacing:.2em;color:#fff;text-align:left;line-height:1.3}
.eyebrow{display:inline-flex;align-items:center;gap:9px;font-family:var(--font-display);font-size:12px;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--orange);padding:8px 16px;border:1px solid rgba(232,93,47,.4);border-radius:100px;background:rgba(232,93,47,.1);margin-bottom:28px}
.eyebrow .dot{width:8px;height:8px;border-radius:50%;background:var(--orange);box-shadow:0 0 12px var(--orange);animation:blink 1.4s infinite}
@keyframes blink{50%{opacity:.3}}
h1{font-family:var(--font-display);font-weight:900;font-size:clamp(44px,9vw,76px);letter-spacing:-.02em;line-height:1;color:#fff;margin-bottom:22px}
h1 .t{color:var(--teal)}
.lede{font-size:18px;line-height:1.6;color:#cdd8e8;max-width:440px;margin:0 auto 38px}
.lede b{color:#fff;font-weight:600}
.btn{display:inline-flex;align-items:center;gap:10px;font-family:var(--font-display);font-weight:700;font-size:14px;letter-spacing:.08em;text-transform:uppercase;padding:16px 30px;border-radius:3px;cursor:pointer;border:none;transition:transform .15s,box-shadow .25s;background:var(--orange);color:#fff;box-shadow:0 6px 24px rgba(232,93,47,.32)}
.btn:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(232,93,47,.45)}
.gate{display:none;margin-top:8px;animation:fade .3s ease}
.gate.open{display:block}
@keyframes fade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.gate form{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;max-width:420px;margin:0 auto}
.gate input{flex:1 1 220px;min-width:0;padding:15px 18px;background:rgba(9,35,66,.6);border:1px solid var(--teal-line);border-radius:3px;color:#fff;font-family:var(--font-body);font-size:16px}
.gate input:focus{outline:none;border-color:var(--teal);background:rgba(9,35,66,.85)}
.gate input::placeholder{color:#7f93b0}
.gate .btn{flex:0 0 auto}
.gate label{display:block;font-family:var(--font-display);font-size:11px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--teal);margin-bottom:12px}
.err{color:#ff9b7a;font-size:14px;margin-top:14px;font-weight:400}
.hint{margin-top:18px;font-size:12px;color:var(--gray);letter-spacing:.04em}
.foot{position:relative;z-index:2;margin-top:56px;font-size:12px;color:var(--gray);letter-spacing:.06em}
@media (prefers-reduced-motion:reduce){.bg i{animation:none}.dot{animation:none}}
</style>
<noscript><style>.gate{display:block!important}#enterBtn{display:none}</style></noscript>
</head>
<body>
<div class="bg" aria-hidden="true"><i class="a"></i><i class="b"></i></div>
<div class="grain" aria-hidden="true"></div>

<main class="card">
  <div class="brand">
    <img src="assets/img/msr-logo-mark.png" width="54" height="54" alt="Midwest Specialty Robotics logo" />
    <span class="name">MIDWEST<br>SPECIALTY ROBOTICS</span>
  </div>

  <div class="eyebrow"><span class="dot"></span> Launching in Wisconsin</div>
  <h1>Coming <span class="t">soon.</span></h1>
  <p class="lede">Wisconsin's specialty robotics integrator. <b>Robots that work alongside your people</b> — so your team focuses on the work that matters.</p>

  <button class="btn" id="enterBtn" type="button" aria-expanded="false" aria-controls="gate">Preview Access</button>

  <div class="gate<?php echo $error ? ' open' : ''; ?>" id="gate">
    <form method="post" autocomplete="off">
      <label for="pw" style="flex-basis:100%">Enter preview password</label>
      <input id="pw" type="password" name="password" placeholder="Password" required autofocus aria-label="Preview password" />
      <button class="btn" type="submit">Enter</button>
    </form>
    <?php if ($error): ?><div class="err"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div><?php endif; ?>
    <div class="hint">Authorized preview access only.</div>
  </div>

  <div class="foot">&copy; 2026 Midwest Specialty Robotics, LLC · Milwaukee, Wisconsin</div>
</main>

<script>
(function(){
  var btn=document.getElementById('enterBtn'),gate=document.getElementById('gate');
  if(!btn||!gate)return;
  if(gate.classList.contains('open')){btn.style.display='none';}
  btn.addEventListener('click',function(){
    gate.classList.add('open');btn.style.display='none';
    btn.setAttribute('aria-expanded','true');
    var pw=document.getElementById('pw');if(pw)pw.focus();
  });
})();
</script>
</body>
</html>
