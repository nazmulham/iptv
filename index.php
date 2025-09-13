<?php
// Load channels from channels.php (parses remote M3U)
require_once "channels.php";
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>RoarZone — Live TV (PHP Clone)</title>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.4.0/dist/hls.min.js"></script>
<style>
body{margin:0;font-family:sans-serif;background:#0f1724;color:#eee;}
header{display:flex;align-items:center;gap:12px;padding:15px;background:#06101d;}
.brand{font-weight:700;font-size:18px;color:#06b6d4;}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:14px;padding:14px;}
.card{background:#0b1220;padding:8px;border-radius:8px;cursor:pointer}
.card:hover{background:#132033}
.thumb{height:100px;background:#222;display:flex;align-items:center;justify-content:center;color:#888;font-size:13px;border-radius:6px}
.title{font-weight:600;font-size:14px;margin-top:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sub{font-size:12px;color:#aaa}
.modal{position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;align-items:center;justify-content:center;z-index:999;}
.modal.open{display:flex}
.player{background:#000;padding:10px;border-radius:8px;width:90%;max-width:900px}
.close{float:right;margin-bottom:6px;padding:6px 12px;background:#06b6d4;border:none;border-radius:6px;cursor:pointer}
video{width:100%;height:480px;max-height:70vh;background:#000}
</style>
</head>
<body>

<header>
  <div class="brand">RoarZone Clone (PHP Remote M3U)</div>
</header>

<main>
  <div class="grid" id="grid">
    <?php foreach($channels as $ch): ?>
    <div class="card" data-title="<?=htmlspecialchars($ch['title'])?>"
         data-cat="<?=htmlspecialchars($ch['group'])?>"
         data-url="<?=htmlspecialchars($ch['url'])?>">
      <div class="thumb">
        <?php if($ch['logo']): ?>
          <img src="<?=htmlspecialchars($ch['logo'])?>" style="width:100%;height:100%;object-fit:cover;border-radius:6px">
        <?php else: ?>
          ▶ Live
        <?php endif; ?>
      </div>
      <div class="title"><?=$ch['title']?></div>
      <div class="sub"><?=$ch['group']?></div>
    </div>
    <?php endforeach; ?>
  </div>
</main>

<!-- Player Modal -->
<div class="modal" id="modal">
  <div class="player">
    <button class="close" id="closeBtn">Close</button>
    <div style="margin-bottom:6px;font-weight:700" id="playerTitle">Channel</div>
    <video id="video" controls playsinline></video>
  </div>
</div>

<script>
const modal=document.getElementById('modal');
const videoEl=document.getElementById('video');
const titleEl=document.getElementById('playerTitle');
let hls=null;

document.querySelectorAll('.card').forEach(card=>{
  card.addEventListener('click',()=>{
    const url=card.dataset.url;
    titleEl.textContent=card.dataset.title;
    modal.classList.add('open');
    if(hls){hls.destroy();hls=null;}
    if(videoEl.canPlayType('application/vnd.apple.mpegurl')){
      videoEl.src=url;videoEl.play().catch(()=>{});
    }else if(Hls.isSupported()){
      hls=new Hls();hls.loadSource(url);hls.attachMedia(videoEl);
      hls.on(Hls.Events.MANIFEST_PARSED,()=>videoEl.play().catch(()=>{}));
    }else alert("HLS not supported in this browser");
  });
});
document.getElementById('closeBtn').onclick=()=>{
  modal.classList.remove('open');
  if(hls){hls.destroy();hls=null;}
  videoEl.pause();videoEl.removeAttribute('src');videoEl.load();
};
</script>
</body>
</html>
