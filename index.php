<?php
require 'auth.php';

$total_lost  = 0;
$total_found = 0;
$r = $conn->query("SELECT tag, COUNT(*) as cnt FROM items GROUP BY tag");
while ($row = $r->fetch_assoc()) {
    if ($row['tag'] === 'lost')  $total_lost  = $row['cnt'];
    if ($row['tag'] === 'found') $total_found = $row['cnt'];
}
$total = $total_lost + $total_found;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lost &amp; Found Portal</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#0f0e0c;--paper:#faf8f4;--cream:#f0ece3;
  --amber:#e8a020;--amber-deep:#c4820f;--amber-light:#fef3dc;
  --green:#2e7d32;--green-light:#e8f5e9;--green-border:#a5d6a7;
  --blue:#1565c0;--blue-light:#e3f2fd;--blue-border:#90caf9;
  --red:#c62828;--red-light:#ffebee;--red-border:#ef9a9a;
  --muted:#7a7468;--border:#ddd9d0;--card-bg:#ffffff;
  --shadow-sm:0 1px 3px rgba(0,0,0,.06);
  --shadow-md:0 4px 16px rgba(0,0,0,.08);
  --shadow-lg:0 12px 40px rgba(0,0,0,.12);
  --radius:12px;--radius-sm:8px;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'DM Sans',sans-serif;background:var(--paper);color:var(--ink);min-height:100vh;}

/* HEADER */
header{background:var(--ink);position:sticky;top:0;z-index:100;box-shadow:0 2px 20px rgba(0,0,0,.25);}
.header-inner{max-width:1200px;margin:auto;padding:14px 32px;display:flex;align-items:center;justify-content:space-between;gap:16px;}
.brand{display:flex;align-items:center;gap:12px;text-decoration:none;}
.brand-icon{width:38px;height:38px;background:var(--amber);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.brand-name{font-family:'Playfair Display',serif;font-size:20px;font-weight:700;color:white;line-height:1;}
.brand-tagline{font-size:10px;color:rgba(255,255,255,.45);letter-spacing:.08em;text-transform:uppercase;margin-top:2px;}
nav{display:flex;gap:6px;align-items:center;}
nav a{color:rgba(255,255,255,.7);text-decoration:none;font-size:13.5px;font-weight:500;padding:7px 14px;border-radius:8px;transition:all .2s;}
nav a:hover{color:white;background:rgba(255,255,255,.1);}
.nav-user{color:rgba(255,255,255,.5);font-size:13px;padding:7px 0;}
.nav-btn{background:var(--amber);color:var(--ink)!important;font-weight:600!important;}
.nav-btn:hover{background:#f5b030!important;}
.nav-logout{border:1px solid rgba(255,255,255,.2);}
.nav-logout:hover{border-color:rgba(255,255,255,.4)!important;}

/* HERO */
.hero{background:var(--ink);color:white;padding:70px 32px 80px;text-align:center;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 70% 60% at 50% 120%,rgba(232,160,32,.18) 0%,transparent 70%);}
.hero-content{position:relative;max-width:620px;margin:auto;}
.hero-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(232,160,32,.15);border:1px solid rgba(232,160,32,.3);color:var(--amber);font-size:11px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;padding:4px 14px;border-radius:100px;margin-bottom:20px;}
.hero h1{font-family:'Playfair Display',serif;font-size:clamp(34px,5.5vw,56px);font-weight:900;line-height:1.1;margin-bottom:14px;letter-spacing:-.02em;}
.hero h1 em{font-style:normal;color:var(--amber);}
.hero p{color:rgba(255,255,255,.6);font-size:16px;line-height:1.7;margin-bottom:32px;}
.hero-actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
.btn{display:inline-flex;align-items:center;gap:7px;padding:11px 22px;border-radius:9px;font-size:14px;font-weight:600;text-decoration:none;transition:all .2s;cursor:pointer;border:none;font-family:inherit;}
.btn-amber{background:var(--amber);color:var(--ink);}
.btn-amber:hover{background:#f5b030;transform:translateY(-1px);box-shadow:0 6px 20px rgba(232,160,32,.35);}
.btn-outline{background:transparent;color:white;border:1px solid rgba(255,255,255,.25);}
.btn-outline:hover{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.4);}

/* STATS */
.stats-strip{background:var(--cream);border-bottom:1px solid var(--border);padding:18px 32px;}
.stats-inner{max-width:1200px;margin:auto;display:flex;align-items:center;justify-content:center;}
.stat{text-align:center;padding:0 36px;}
.stat+.stat{border-left:1px solid var(--border);}
.stat-num{font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:var(--ink);}
.stat-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-top:2px;}

/* MAIN */
.main{max-width:1200px;margin:auto;padding:56px 32px;display:grid;grid-template-columns:390px 1fr;gap:48px;align-items:start;}

/* FORM */
.form-panel{position:sticky;top:72px;}
.panel-label{font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--amber-deep);margin-bottom:10px;display:flex;align-items:center;gap:8px;}
.panel-label::after{content:'';flex:1;height:1px;background:var(--border);}
.form-card{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);padding:26px;box-shadow:var(--shadow-md);}
.form-card h2{font-family:'Playfair Display',serif;font-size:21px;font-weight:700;margin-bottom:5px;}
.form-card .subtitle{font-size:13px;color:var(--muted);margin-bottom:20px;line-height:1.5;}
.form-group{margin-bottom:14px;}
label{display:block;font-size:13px;font-weight:600;color:var(--ink);margin-bottom:5px;}
label .req{color:#c0392b;margin-left:2px;}
input[type=text],input[type=email],textarea,select{
  width:100%;padding:10px 13px;border:1.5px solid var(--border);border-radius:var(--radius-sm);
  font-family:inherit;font-size:14px;color:var(--ink);background:var(--paper);
  transition:border-color .2s,box-shadow .2s;outline:none;
}
input:focus,textarea:focus,select:focus{border-color:var(--amber);box-shadow:0 0 0 3px rgba(232,160,32,.12);background:white;}
textarea{resize:vertical;min-height:80px;}

/* TAG TOGGLE */
.tag-toggle{display:flex;gap:0;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;background:var(--paper);}
.tag-toggle input[type=radio]{display:none;}
.tag-toggle label{flex:1;padding:9px 12px;text-align:center;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;border:none;margin:0;color:var(--muted);}
.tag-toggle input[type=radio]#tag_lost:checked ~ * .label-lost,
.tag-toggle .label-lost-wrap input:checked + label { background:var(--red-light);color:var(--red);border-color:var(--red); }
.tag-option{display:contents;}
.tag-lost-label{flex:1;padding:9px 12px;text-align:center;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;color:var(--muted);}
.tag-found-label{flex:1;padding:9px 12px;text-align:center;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;color:var(--muted);border-left:1px solid var(--border);}
#tag_lost:checked ~ .tag-lost-label{background:var(--red-light);color:var(--red);}
#tag_found:checked ~ .tag-found-label{background:var(--green-light);color:var(--green);}

.file-upload{border:2px dashed var(--border);border-radius:var(--radius-sm);padding:18px;text-align:center;cursor:pointer;transition:all .2s;background:var(--paper);position:relative;}
.file-upload:hover{border-color:var(--amber);background:var(--amber-light);}
.file-upload input{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.file-upload-icon{font-size:24px;margin-bottom:5px;}
.file-upload-text{font-size:12.5px;color:var(--muted);}
.file-upload-text strong{color:var(--amber-deep);}
.submit-btn{width:100%;padding:12px;background:var(--ink);color:white;font-family:inherit;font-size:14.5px;font-weight:600;border:none;border-radius:var(--radius-sm);cursor:pointer;transition:all .2s;margin-top:6px;display:flex;align-items:center;justify-content:center;gap:7px;}
.submit-btn:hover{background:#2a2824;transform:translateY(-1px);box-shadow:var(--shadow-md);}
.login-prompt{background:var(--amber-light);border:1px solid rgba(200,130,15,.25);border-radius:var(--radius-sm);padding:14px 16px;font-size:13.5px;text-align:center;color:var(--ink);}
.login-prompt a{color:var(--amber-deep);font-weight:600;text-decoration:none;}
.login-prompt a:hover{text-decoration:underline;}

/* ALERTS */
.alert{padding:12px 15px;border-radius:8px;font-size:13px;font-weight:500;margin-bottom:16px;display:flex;align-items:flex-start;gap:8px;}
.alert-success{background:var(--green-light);border:1px solid var(--green-border);color:var(--green);}
.alert-match{background:var(--blue-light);border:1px solid var(--blue-border);color:var(--blue);}

/* ITEMS */
.found-section{}
.section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
.section-header h2{font-family:'Playfair Display',serif;font-size:24px;font-weight:700;}
.filter-tabs{display:flex;gap:6px;}
.filter-tab{padding:5px 14px;border-radius:100px;font-size:12.5px;font-weight:600;cursor:pointer;border:1px solid var(--border);background:white;color:var(--muted);transition:all .2s;text-decoration:none;}
.filter-tab.active,.filter-tab:hover{background:var(--ink);color:white;border-color:var(--ink);}
.items-count{background:var(--amber-light);color:var(--amber-deep);font-size:12.5px;font-weight:600;padding:4px 12px;border-radius:100px;border:1px solid rgba(200,130,15,.2);}
.items-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:18px;}
.card{background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);transition:box-shadow .2s,transform .2s;animation:fadeUp .4s ease both;}
.card:hover{box-shadow:var(--shadow-lg);transform:translateY(-3px);}
@keyframes fadeUp{from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);}}
.card-image-wrap{aspect-ratio:4/3;overflow:hidden;background:var(--cream);}
.card-image-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .3s;}
.card:hover .card-image-wrap img{transform:scale(1.04);}
.card-no-image{aspect-ratio:4/3;background:var(--cream);display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--muted);font-size:32px;gap:7px;}
.card-no-image span{font-size:11px;letter-spacing:.06em;text-transform:uppercase;}
.card-body{padding:14px 16px 16px;}
.card-tag-badge{display:inline-block;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:2px 9px;border-radius:4px;margin-bottom:7px;}
.tag-lost-badge{background:var(--red-light);color:var(--red);}
.tag-found-badge{background:var(--green-light);color:var(--green);}
.card h3{font-family:'Playfair Display',serif;font-size:15.5px;font-weight:700;margin-bottom:5px;color:var(--ink);}
.card-desc{font-size:12.5px;color:var(--muted);line-height:1.6;margin-bottom:11px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.card-meta{border-top:1px solid var(--border);padding-top:10px;display:flex;flex-direction:column;gap:4px;}
.meta-row{display:flex;align-items:center;gap:6px;font-size:12px;color:var(--muted);}
.meta-row .icon{font-size:12px;width:15px;text-align:center;}
.meta-row a{color:var(--amber-deep);text-decoration:none;font-size:12px;}
.meta-row a:hover{text-decoration:underline;}
.matched-banner{background:var(--blue-light);border:1px solid var(--blue-border);border-radius:var(--radius-sm);padding:14px 16px;font-size:13px;color:var(--blue);display:flex;align-items:flex-start;gap:10px;margin-bottom:18px;}
.matched-banner strong{display:block;font-weight:700;margin-bottom:3px;}
.empty-state{text-align:center;padding:52px 20px;color:var(--muted);grid-column:1/-1;}
.empty-icon{font-size:48px;margin-bottom:14px;}
.empty-state h3{font-family:'Playfair Display',serif;font-size:18px;color:var(--ink);margin-bottom:6px;}
.empty-state p{font-size:13px;}

footer{background:var(--ink);color:rgba(255,255,255,.4);text-align:center;padding:28px;font-size:12.5px;}
footer strong{color:white;}

@media(max-width:900px){
  .main{grid-template-columns:1fr;padding:28px 18px;gap:36px;}
  .form-panel{position:static;}
  .stat{padding:0 18px;}
  .header-inner{padding:12px 18px;}
  nav a.hm{display:none;}
}
@media(max-width:560px){
  .stats-strip{display:none;}
  .hero{padding:44px 18px 54px;}
  .items-grid{grid-template-columns:1fr;}
  .section-header{flex-direction:column;align-items:flex-start;}
}
</style>
</head>
<body>

<!-- HEADER -->
<header>
  <div class="header-inner">
    <a href="index.php" class="brand">
      <div class="brand-icon">&#128269;</div>
      <div>
        <div class="brand-name">Lost &amp; Found</div>
        <div class="brand-tagline">Reuniting people with their belongings</div>
      </div>
    </a>
    <nav>
      <a href="#" class="hm">Home</a>
      <a href="#items" class="hm">Browse Items</a>
      <?php if (isLoggedIn()): ?>
        <span class="nav-user">Hi, <strong style="color:white"><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
        <a href="logout.php" class="nav-logout">Log Out</a>
      <?php else: ?>
        <a href="login.php">Log In</a>
        <a href="register.php" class="nav-btn">Sign Up</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<!-- HERO -->
<section class="hero">
  <div class="hero-content">
    <div class="hero-badge">&#127991; Community Lost &amp; Found</div>
    <h1>Lost something? <em>We can help.</em></h1>
    <p>Browse lost and found items, or report your own. When a lost report matches a found report, they cancel each other out — item reunited!</p>
    <div class="hero-actions">
      <a href="#items" class="btn btn-amber">Browse All Items</a>
      <a href="#report" class="btn btn-outline">Report an Item</a>
    </div>
  </div>
</section>

<!-- STATS -->
<div class="stats-strip">
  <div class="stats-inner">
    <div class="stat">
      <div class="stat-num"><?= $total ?></div>
      <div class="stat-label">Active Listings</div>
    </div>
    <div class="stat">
      <div class="stat-num"><?= $total_lost ?></div>
      <div class="stat-label">Lost Items</div>
    </div>
    <div class="stat">
      <div class="stat-num"><?= $total_found ?></div>
      <div class="stat-label">Found Items</div>
    </div>
  </div>
</div>

<!-- MAIN -->
<main class="main">

  <!-- FORM PANEL -->
  <aside class="form-panel" id="report">
    <div class="panel-label">Report Item</div>
    <div class="form-card">
      <h2>Report an Item</h2>
      <p class="subtitle">Tag it as <strong>Lost</strong> (you lost it) or <strong>Found</strong> (you found it). Matching reports cancel each other out.</p>

      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">&#10003; Report submitted successfully!</div>
      <?php endif; ?>
      <?php if (isset($_GET['matched'])): ?>
        <div class="alert alert-match">&#127881; A match was found! Both reports have been resolved — item reunited with its owner.</div>
      <?php endif; ?>

      <?php if (isLoggedIn()): ?>
      <form action="save.php" method="POST" enctype="multipart/form-data">

        <!-- TAG TOGGLE -->
        <div class="form-group">
          <label>Item Status <span class="req">*</span></label>
          <div style="display:flex;border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;">
            <input type="radio" name="tag" id="tag_lost" value="lost" checked style="display:none">
            <input type="radio" name="tag" id="tag_found" value="found" style="display:none">
            <label for="tag_lost" class="tag-lost-label" id="lbl_lost">&#128308; Lost</label>
            <label for="tag_found" class="tag-found-label" id="lbl_found" style="border-left:1px solid var(--border);">&#128994; Found</label>
          </div>
        </div>

        <div class="form-group">
          <label>Item Name <span class="req">*</span></label>
          <input type="text" name="name" placeholder="e.g. Black leather wallet" required>
        </div>
        <div class="form-group">
          <label>Description <span class="req">*</span></label>
          <textarea name="description" placeholder="Colour, size, brand, any unique markings…" required></textarea>
        </div>
        <div class="form-group">
          <label>Location <span class="req">*</span></label>
          <input type="text" name="location" placeholder="Where was it lost/found?" required>
        </div>
        <div class="form-group">
          <label>Contact Email <span class="req">*</span></label>
          <input type="email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label>Phone Number <span class="req">*</span></label>
          <input type="text" name="phone" placeholder="+1 (555) 000-0000" required>
        </div>
        <div class="form-group">
          <label>Upload Photo <span style="font-weight:400;color:var(--muted)">(optional)</span></label>
          <div class="file-upload" id="fileLabel">
            <input type="file" name="image" accept="image/*" onchange="updateLabel(this)">
            <div class="file-upload-icon">&#128247;</div>
            <div class="file-upload-text"><strong>Click to upload</strong> or drag &amp; drop</div>
          </div>
        </div>
        <button type="submit" class="submit-btn">&#128203; Submit Report</button>
      </form>
      <?php else: ?>
        <div class="login-prompt">
          &#128274; You need to be logged in to submit a report.<br><br>
          <a href="login.php">Log In</a> &nbsp;or&nbsp; <a href="register.php">Sign Up Free</a>
        </div>
      <?php endif; ?>
    </div>
  </aside>

  <!-- ITEMS SECTION -->
  <section class="found-section" id="items">
    <?php
    $filter = $_GET['filter'] ?? 'all';
    if ($filter === 'lost') {
        $where = "WHERE tag='lost'";
    } elseif ($filter === 'found') {
        $where = "WHERE tag='found'";
    } else {
        $where = "";
    }
    $items = [];
    $result = $conn->query("SELECT * FROM items $where ORDER BY id DESC");
    while ($row = $result->fetch_assoc()) $items[] = $row;
    ?>

    <div class="section-header">
      <h2>All Items</h2>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <div class="filter-tabs">
          <a href="?filter=all#items" class="filter-tab <?= $filter==='all'?'active':'' ?>">All</a>
          <a href="?filter=lost#items" class="filter-tab <?= $filter==='lost'?'active':'' ?>">&#128308; Lost</a>
          <a href="?filter=found#items" class="filter-tab <?= $filter==='found'?'active':'' ?>">&#128994; Found</a>
        </div>
        <span class="items-count"><?= count($items) ?> item<?= count($items)!==1?'s':'' ?></span>
      </div>
    </div>

    <div class="items-grid">
      <?php if (empty($items)): ?>
        <div class="empty-state">
          <div class="empty-icon">&#128269;</div>
          <h3>No items here yet</h3>
          <p>Use the form to submit the first report.</p>
        </div>
      <?php else: ?>
        <?php foreach ($items as $i => $row): ?>
          <div class="card" style="animation-delay:<?= $i*0.05 ?>s">
            <?php if (!empty($row['image']) && file_exists("uploads/".$row['image'])): ?>
              <div class="card-image-wrap">
                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
              </div>
            <?php else: ?>
              <div class="card-no-image">
                <span>&#128230;</span>
                <span>No Photo</span>
              </div>
            <?php endif; ?>
            <div class="card-body">
              <?php $isLost = ($row['tag'] ?? 'lost') === 'lost'; ?>
              <div class="card-tag-badge <?= $isLost ? 'tag-lost-badge' : 'tag-found-badge' ?>">
                <?= $isLost ? '&#128308; Lost' : '&#128994; Found' ?>
              </div>
              <h3><?= htmlspecialchars($row['name']) ?></h3>
              <p class="card-desc"><?= htmlspecialchars($row['description']) ?></p>
              <div class="card-meta">
                <div class="meta-row"><span class="icon">&#128205;</span><?= htmlspecialchars($row['location']) ?></div>
                <div class="meta-row"><span class="icon">&#9993;</span><a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></div>
                <div class="meta-row"><span class="icon">&#128222;</span><?= htmlspecialchars($row['phone']) ?></div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </section>

</main>

<footer>
  <p>&copy; 2026 <strong>Lost &amp; Found Portal</strong> &middot; Helping communities reconnect with lost belongings</p>
</footer>

<script>
// Tag toggle visual highlight
const lostRadio  = document.getElementById('tag_lost');
const foundRadio = document.getElementById('tag_found');
const lblLost    = document.getElementById('lbl_lost');
const lblFound   = document.getElementById('lbl_found');

function updateTagStyle() {
  if (!lostRadio) return;
  if (lostRadio.checked) {
    lblLost.style.cssText  = 'flex:1;padding:9px 12px;text-align:center;font-size:13px;font-weight:700;cursor:pointer;background:#ffebee;color:#c62828;transition:all .2s;';
    lblFound.style.cssText = 'flex:1;padding:9px 12px;text-align:center;font-size:13px;font-weight:600;cursor:pointer;color:#7a7468;transition:all .2s;border-left:1px solid var(--border);';
  } else {
    lblFound.style.cssText = 'flex:1;padding:9px 12px;text-align:center;font-size:13px;font-weight:700;cursor:pointer;background:#e8f5e9;color:#2e7d32;transition:all .2s;border-left:1px solid var(--border);';
    lblLost.style.cssText  = 'flex:1;padding:9px 12px;text-align:center;font-size:13px;font-weight:600;cursor:pointer;color:#7a7468;transition:all .2s;';
  }
}
if (lostRadio) {
  lostRadio.addEventListener('change', updateTagStyle);
  foundRadio.addEventListener('change', updateTagStyle);
  updateTagStyle();
}

function updateLabel(input) {
  const wrap = document.getElementById('fileLabel');
  const icon = wrap.querySelector('.file-upload-icon');
  const text = wrap.querySelector('.file-upload-text');
  if (input.files && input.files[0]) {
    icon.textContent = '\u2705';
    text.innerHTML = '<strong>' + input.files[0].name + '</strong>';
    wrap.style.borderColor = '#e8a020';
    wrap.style.background  = '#fef3dc';
  }
}
</script>
</body>
</html>
