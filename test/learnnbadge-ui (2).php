<?php
// LearnBadge — NFT Badge Viewer & Mint UI
// วางไฟล์นี้บน PHP server แล้วเปิดผ่าน browser
// แก้ CONTRACT_ADDRESS หลังจาก Deploy แล้ว
?>
<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LearnBadge — NFT Certificate</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg:       #05080f;
  --surface:  #0d1117;
  --card:     #111827;
  --border:   #1f2937;
  --accent:   #6366f1;
  --accent2:  #34d399;
  --gold:     #f59e0b;
  --text:     #f1f5f9;
  --muted:    #64748b;
  --danger:   #f43f5e;
}
*{margin:0;padding:0;box-sizing:border-box;}
body{background:var(--bg);color:var(--text);font-family:'Outfit',sans-serif;min-height:100vh;overflow-x:hidden;}

/* ── Background ── */
body::before{
  content:'';position:fixed;inset:0;
  background:
    radial-gradient(ellipse 80% 60% at 20% 0%, rgba(99,102,241,0.08) 0%, transparent 60%),
    radial-gradient(ellipse 60% 40% at 80% 100%, rgba(52,211,153,0.06) 0%, transparent 60%);
  pointer-events:none;z-index:0;
}

.wrap{position:relative;z-index:1;}

/* ── Header ── */
header{
  display:flex;justify-content:space-between;align-items:center;
  padding:1.2rem 2rem;
  background:rgba(5,8,15,0.8);
  backdrop-filter:blur(20px);
  border-bottom:1px solid var(--border);
  position:sticky;top:0;z-index:100;
}
.logo{font-size:1.3rem;font-weight:800;letter-spacing:-0.03em;}
.logo .accent{color:var(--accent);}
.logo .badge-icon{
  display:inline-block;margin-right:8px;
  background:linear-gradient(135deg,var(--accent),var(--accent2));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}

#walletBtn{
  background:var(--card);border:1px solid var(--border);
  color:var(--text);padding:8px 18px;border-radius:10px;
  font-family:'Outfit',sans-serif;font-weight:600;font-size:0.85rem;
  cursor:pointer;transition:all 0.2s;
}
#walletBtn:hover{border-color:var(--accent);color:var(--accent);}
#walletBtn.connected{border-color:var(--accent2);color:var(--accent2);}

/* ── Hero ── */
.hero{
  text-align:center;padding:5rem 2rem 3rem;
  animation:fadeUp 0.7s ease forwards;
}
.hero h1{
  font-size:clamp(2.8rem,7vw,5.5rem);font-weight:800;
  line-height:1;letter-spacing:-0.04em;margin-bottom:1rem;
}
.hero h1 .grad{
  background:linear-gradient(135deg,#6366f1 0%,#34d399 100%);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.hero p{color:var(--muted);font-size:1.05rem;font-family:'Space Mono',monospace;margin-bottom:2.5rem;}

/* ── Connect Button ── */
.btn-primary{
  display:inline-flex;align-items:center;gap:10px;
  background:linear-gradient(135deg,var(--accent),#818cf8);
  color:#fff;border:none;padding:15px 36px;
  font-size:1rem;font-family:'Outfit',sans-serif;font-weight:700;
  border-radius:14px;cursor:pointer;transition:all 0.3s;
  box-shadow:0 0 30px rgba(99,102,241,0.35);
}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 0 50px rgba(99,102,241,0.5);}
.btn-secondary{
  display:inline-flex;align-items:center;gap:8px;
  background:transparent;border:1px solid var(--border);
  color:var(--muted);padding:12px 24px;border-radius:10px;
  font-family:'Outfit',sans-serif;font-weight:600;font-size:0.9rem;
  cursor:pointer;transition:all 0.2s;margin-left:12px;
}
.btn-secondary:hover{border-color:var(--accent2);color:var(--accent2);}

/* ── Tabs ── */
.tabs{
  display:flex;gap:4px;
  background:var(--surface);border:1px solid var(--border);
  border-radius:12px;padding:4px;
  max-width:400px;margin:0 auto 2rem;
}
.tab{
  flex:1;padding:10px;text-align:center;border-radius:9px;
  font-weight:600;font-size:0.9rem;cursor:pointer;
  transition:all 0.2s;color:var(--muted);border:none;background:transparent;
  font-family:'Outfit',sans-serif;
}
.tab.active{background:var(--accent);color:#fff;}

/* ── Section container ── */
.section{max-width:1100px;margin:0 auto;padding:0 2rem 4rem;}
.section-title{
  font-size:1.6rem;font-weight:800;letter-spacing:-0.02em;
  margin-bottom:0.4rem;
}
.section-sub{color:var(--muted);font-size:0.9rem;font-family:'Space Mono',monospace;margin-bottom:2rem;}

/* ── Stats Bar ── */
.stats-bar{
  display:flex;gap:1rem;margin-bottom:2rem;flex-wrap:wrap;
}
.stat-card{
  background:var(--card);border:1px solid var(--border);
  border-radius:14px;padding:1.2rem 1.8rem;flex:1;min-width:150px;
}
.stat-card .num{font-size:2rem;font-weight:800;color:var(--accent);}
.stat-card .lbl{font-size:0.75rem;color:var(--muted);font-family:'Space Mono',monospace;margin-top:2px;}

/* ── Badge Grid ── */
.badge-grid{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(240px,1fr));
  gap:1.2rem;
}
.badge-card{
  background:var(--card);border:1px solid var(--border);
  border-radius:18px;overflow:hidden;
  transition:all 0.3s;cursor:pointer;
  animation:cardIn 0.5s ease backwards;
}
.badge-card:hover{
  border-color:var(--accent);
  transform:translateY(-5px);
  box-shadow:0 16px 40px rgba(0,0,0,0.4),0 0 20px rgba(99,102,241,0.12);
}
@keyframes cardIn{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

.badge-img-wrap{
  aspect-ratio:1;background:linear-gradient(135deg,#111827,#1e1b4b);
  display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;
}
.badge-img-wrap img{width:100%;height:100%;object-fit:cover;}
.badge-img-wrap .placeholder{font-size:4rem;opacity:0.6;}
.badge-img-wrap .token-chip{
  position:absolute;top:10px;right:10px;
  background:rgba(0,0,0,0.6);backdrop-filter:blur(8px);
  border:1px solid rgba(255,255,255,0.1);
  padding:4px 10px;border-radius:100px;
  font-family:'Space Mono',monospace;font-size:0.65rem;color:var(--muted);
}
.badge-body{padding:1rem 1.2rem 1.3rem;}
.badge-name{font-weight:700;font-size:1rem;margin-bottom:4px;}
.badge-activity{font-size:0.78rem;color:var(--muted);line-height:1.5;margin-bottom:10px;}
.badge-date{
  font-family:'Space Mono',monospace;font-size:0.65rem;
  color:var(--accent2);opacity:0.8;
}

/* ── Mint Form ── */
.mint-form{
  background:var(--card);border:1px solid var(--border);
  border-radius:20px;padding:2rem;max-width:600px;
}
.form-group{margin-bottom:1.4rem;}
.form-group label{
  display:block;font-size:0.8rem;font-weight:600;
  color:var(--muted);font-family:'Space Mono',monospace;
  text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px;
}
.form-group input,.form-group select,.form-group textarea{
  width:100%;background:var(--surface);border:1px solid var(--border);
  border-radius:10px;padding:12px 16px;color:var(--text);
  font-family:'Outfit',sans-serif;font-size:0.95rem;
  outline:none;transition:border-color 0.2s;
}
.form-group input:focus,.form-group textarea:focus{border-color:var(--accent);}
.form-group textarea{height:80px;resize:vertical;}
.form-group input::placeholder,.form-group textarea::placeholder{color:var(--muted);}

.badge-presets{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:1.4rem;}
.preset-btn{
  background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.3);
  color:var(--accent);padding:6px 14px;border-radius:8px;
  font-size:0.8rem;font-family:'Outfit',sans-serif;font-weight:600;
  cursor:pointer;transition:all 0.2s;
}
.preset-btn:hover{background:rgba(99,102,241,0.2);}

.submit-btn{
  width:100%;background:linear-gradient(135deg,var(--accent),#818cf8);
  color:#fff;border:none;padding:14px;border-radius:12px;
  font-size:1rem;font-family:'Outfit',sans-serif;font-weight:700;
  cursor:pointer;transition:all 0.3s;
  box-shadow:0 0 20px rgba(99,102,241,0.3);
}
.submit-btn:hover{box-shadow:0 0 35px rgba(99,102,241,0.5);}
.submit-btn:disabled{opacity:0.5;cursor:not-allowed;}

/* ── Status ── */
#status{
  text-align:center;padding:1.5rem;
  font-family:'Space Mono',monospace;font-size:0.85rem;
  color:var(--muted);display:none;white-space:pre-line;
}
#status.error{color:var(--danger);}
#status.success{color:var(--accent2);}

.spinner{
  width:40px;height:40px;border:3px solid var(--border);
  border-top-color:var(--accent);border-radius:50%;
  animation:spin 0.8s linear infinite;margin:0 auto 1rem;
}
@keyframes spin{to{transform:rotate(360deg)}}
@keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}

/* ── Toast ── */
.toast{
  position:fixed;bottom:2rem;right:2rem;z-index:999;
  background:var(--card);border:1px solid var(--border);
  border-radius:14px;padding:1rem 1.5rem;
  font-size:0.9rem;max-width:320px;
  transform:translateY(100px);opacity:0;
  transition:all 0.3s;pointer-events:none;
}
.toast.show{transform:translateY(0);opacity:1;}
.toast.success{border-color:var(--accent2);color:var(--accent2);}
.toast.error{border-color:var(--danger);color:var(--danger);}

/* ── Empty ── */
.empty{
  text-align:center;padding:5rem 2rem;
  color:var(--muted);font-family:'Space Mono',monospace;
}
.empty .icon{font-size:3rem;margin-bottom:1rem;opacity:0.3;}

/* ── Modal ── */
#modal{
  display:none;position:fixed;inset:0;
  background:rgba(0,0,0,0.8);backdrop-filter:blur(12px);
  z-index:200;justify-content:center;align-items:center;padding:2rem;
}
#modal.open{display:flex;}
.modal-inner{
  background:var(--card);border:1px solid var(--border);
  border-radius:22px;max-width:500px;width:100%;overflow:hidden;
  animation:modalIn 0.3s ease;
}
@keyframes modalIn{from{opacity:0;transform:scale(0.9)}to{opacity:1;transform:scale(1)}}
.modal-img{width:100%;aspect-ratio:1;object-fit:cover;
  background:linear-gradient(135deg,#111827,#1e1b4b);
  display:flex;align-items:center;justify-content:center;font-size:6rem;}
.modal-body{padding:1.5rem 2rem 2rem;}
.modal-badge-name{font-size:1.6rem;font-weight:800;margin-bottom:6px;}
.modal-activity{color:var(--muted);font-size:0.9rem;line-height:1.6;margin-bottom:1rem;}
.modal-meta{
  background:var(--surface);border-radius:12px;padding:1rem;
  font-family:'Space Mono',monospace;font-size:0.75rem;color:var(--muted);
  line-height:2;margin-bottom:1.2rem;
}
.modal-meta span{color:var(--text);}
.modal-close{
  background:var(--border);border:none;padding:10px 24px;
  border-radius:10px;color:var(--text);cursor:pointer;
  font-family:'Outfit',sans-serif;font-weight:600;
  transition:background 0.2s;
}
.modal-close:hover{background:var(--accent);}

@media(max-width:600px){
  header{padding:1rem;}
  .hero{padding:3rem 1rem 2rem;}
  .section{padding:0 1rem 3rem;}
  .stats-bar{flex-direction:column;}
  .badge-grid{grid-template-columns:repeat(2,1fr);gap:0.8rem;}
}
</style>
</head>
<body>
<div class="wrap">

<!-- Header -->
<header>
  <div class="logo">
    <span class="badge-icon">🎓</span>Learn<span class="accent">Badge</span>
  </div>
  <button id="walletBtn" onclick="connectWallet()">🦊 Connect Wallet</button>
</header>

<!-- Hero (ก่อน connect) -->
<div id="heroSection">
  <div class="hero">
    <h1>EARN YOUR<br><span class="grad">NFT BADGE</span></h1>
    <p>// blockchain certificate for students · labchain network</p>
    <button class="btn-primary" onclick="connectWallet()">
      🦊 Connect MetaMask
    </button>
    <button class="btn-secondary" onclick="scrollToDemo()">ดูตัวอย่าง</button>
  </div>
</div>

<!-- Main App (หลัง connect) -->
<div id="appSection" style="display:none;">

  <!-- Tabs -->
  <div style="padding:2rem 2rem 0;max-width:1100px;margin:0 auto;">
    <div class="tabs">
      <button class="tab active" onclick="switchTab('my')">🏅 Badge ของฉัน</button>
      <button class="tab" onclick="switchTab('mint')" id="mintTab">⚡ ออก Badge</button>
    </div>
  </div>

  <!-- Tab: My Badges -->
  <div id="tabMy" class="section">
    <div class="section-title">Badge ของฉัน</div>
    <div class="section-sub">// NFT certificates on LabChain</div>

    <div class="stats-bar">
      <div class="stat-card">
        <div class="num" id="myBadgeCount">0</div>
        <div class="lbl">Total Badges</div>
      </div>
      <div class="stat-card">
        <div class="num" style="color:var(--accent2)" id="totalMinted">—</div>
        <div class="lbl">Total Minted</div>
      </div>
      <div class="stat-card">
        <div class="num" style="color:var(--gold)">LabChain</div>
        <div class="lbl">Network</div>
      </div>
    </div>

    <div id="status"></div>
    <div id="badgeGrid" class="badge-grid"></div>
  </div>

  <!-- Tab: Mint Badge (Admin) -->
  <div id="tabMint" class="section" style="display:none;">
    <div class="section-title">ออก Badge ใหม่</div>
    <div class="section-sub">// เฉพาะ admin/owner เท่านั้น</div>

    <div class="mint-form">
      <!-- Preset Badges -->
      <div style="margin-bottom:1.4rem;">
        <div style="font-size:0.8rem;color:var(--muted);font-family:'Space Mono',monospace;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:10px;">Quick Presets</div>
        <div class="badge-presets">
          <button class="preset-btn" onclick="fillPreset('Blockchain Beginner','ผ่านการทดสอบพื้นฐาน Blockchain','🔗')">🔗 Blockchain Beginner</button>
          <button class="preset-btn" onclick="fillPreset('Smart Contract Dev','เขียน Smart Contract สำเร็จ','⚡')">⚡ Smart Contract</button>
          <button class="preset-btn" onclick="fillPreset('Web3 Explorer','สำรวจ Web3 ครบทุกด้าน','🌐')">🌐 Web3 Explorer</button>
          <button class="preset-btn" onclick="fillPreset('Hackathon Winner','ชนะการแข่งขัน Blockchain 2026','🏆')">🏆 Hackathon Winner</button>
        </div>
      </div>

      <div class="form-group">
        <label>Student Wallet Address</label>
        <input type="text" id="mintTo" placeholder="0x..." />
      </div>
      <div class="form-group">
        <label>Badge Name</label>
        <input type="text" id="mintName" placeholder="เช่น Blockchain Beginner" />
      </div>
      <div class="form-group">
        <label>Activity / คำอธิบาย</label>
        <textarea id="mintActivity" placeholder="เช่น ผ่านการทดสอบพื้นฐาน Blockchain 101"></textarea>
      </div>
      <div class="form-group">
        <label>Image/Metadata URI (IPFS หรือ URL)</label>
        <input type="text" id="mintURI" placeholder="ipfs://... หรือ https://..." />
      </div>

      <button class="submit-btn" id="mintBtn" onclick="mintBadge()">
        ⚡ Mint Badge
      </button>

      <div id="mintStatus" style="margin-top:1rem;text-align:center;font-family:'Space Mono',monospace;font-size:0.85rem;color:var(--muted);display:none;"></div>
    </div>
  </div>

</div><!-- /appSection -->

<!-- Modal -->
<div id="modal" onclick="closeModal(event)">
  <div class="modal-inner">
    <div class="modal-img" id="modalImgWrap">🎓</div>
    <div class="modal-body">
      <div class="modal-badge-name" id="modalName"></div>
      <div class="modal-activity" id="modalActivity"></div>
      <div class="modal-meta" id="modalMeta"></div>
      <button class="modal-close" onclick="document.getElementById('modal').classList.remove('open')">✕ ปิด</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
// ============================================================
//  CONFIG — แก้ค่านี้หลัง Deploy
// ============================================================
const CONTRACT_ADDRESS = '0xYOUR_CONTRACT_ADDRESS_HERE'; // ← ใส่ address หลัง deploy
const LABCHAIN_ID      = '0x'; // ← ใส่ Chain ID ของ LabChain (hex) เช่น '0x7a69'

// ABI เฉพาะ function ที่ใช้
const ABI = [
  // Read
  'function balanceOf(address owner) view returns (uint256)',
  'function tokenOfOwnerByIndex(address owner, uint256 index) view returns (uint256)',
  'function getBadgesOf(address student) view returns (uint256[])',
  'function badges(uint256 tokenId) view returns (string badgeName, string activity, address issuedTo, uint256 issuedAt)',
  'function tokenURI(uint256 tokenId) view returns (string)',
  'function totalMinted() view returns (uint256)',
  'function owner() view returns (address)',
  // Write
  'function mintBadge(address student, string badgeName, string activity, string tokenURI_) returns (uint256)',
  // Events
  'event BadgeMinted(uint256 indexed tokenId, address indexed student, string badgeName, string activity)',
];

// ── Minimal ABI Encoder ────────────────────────────────────
// ใช้ eth_call / eth_sendTransaction โดยตรงผ่าน MetaMask (ไม่ต้อง ethers.js)

let account  = null;
let isOwner  = false;
let myBadges = [];

// ── Connect Wallet ─────────────────────────────────────────
async function connectWallet() {
  if (!window.ethereum) { toast('❌ กรุณาติดตั้ง MetaMask ก่อน', 'error'); return; }

  try {
    const accounts = await ethereum.request({ method: 'eth_requestAccounts' });
    account = accounts[0];

    // อัปเดต UI
    document.getElementById('walletBtn').textContent = shortenAddr(account);
    document.getElementById('walletBtn').classList.add('connected');
    document.getElementById('heroSection').style.display = 'none';
    document.getElementById('appSection').style.display = 'block';

    await loadBadges();
    await checkOwner();

    ethereum.on('accountsChanged', a => { account = a[0]; loadBadges(); checkOwner(); });
  } catch(e) {
    toast('❌ ' + (e.message || 'Connection failed'), 'error');
  }
}

// ── eth_call helper ────────────────────────────────────────
async function ethCall(data) {
  return ethereum.request({
    method: 'eth_call',
    params: [{ to: CONTRACT_ADDRESS, data }, 'latest']
  });
}

// ── Keccak selector (hardcoded สำหรับ function ที่ใช้) ────
const SEL = {
  getBadgesOf: '0x8a8c9c22', // getBadgesOf(address)
  badges:      '0x9e21e8ad', // badges(uint256)
  totalMinted: '0x2ab4d052', // totalMinted()
  owner:       '0x8da5cb5b', // owner()
};

// ── Encode address param ──────────────────────────────────
function encAddr(addr) { return addr.slice(2).toLowerCase().padStart(64, '0'); }
function encUint(n)    { return BigInt(n).toString(16).padStart(64, '0'); }

// ── Decode helpers ────────────────────────────────────────
function decUint(hex, offset=0) { return parseInt(hex.slice(offset*2, offset*2+64), 16); }
function decAddr(hex, offset=0) { return '0x' + hex.slice(offset*2+24, offset*2+64); }
function decString(hex, baseOffset) {
  const strOffset = decUint(hex, baseOffset) * 2;
  const len = decUint(hex, (strOffset/2)) * 2;
  const raw = hex.slice(strOffset + 64, strOffset + 64 + len);
  let str = '';
  for(let i=0;i<raw.length;i+=2) str += String.fromCharCode(parseInt(raw.substr(i,2),16));
  try { return decodeURIComponent(escape(str)); } catch(e) { return str; }
}

// ── Load Badges ───────────────────────────────────────────
async function loadBadges() {
  if (CONTRACT_ADDRESS === '0xYOUR_CONTRACT_ADDRESS_HERE') {
    showMockData();
    return;
  }

  showStatus('loading');
  try {
    // getBadgesOf(account)
    const res = await ethCall(SEL.getBadgesOf + encAddr(account));
    const hex = res.slice(2);

    // decode dynamic array of uint256
    const arrOffset = decUint(hex, 0) * 2;
    const count = decUint(hex, arrOffset / 2);
    const tokenIds = [];
    for(let i=0;i<count;i++) tokenIds.push(decUint(hex, arrOffset/2 + 1 + i));

    document.getElementById('myBadgeCount').textContent = count;

    // totalMinted
    const totRes = await ethCall(SEL.totalMinted);
    document.getElementById('totalMinted').textContent = parseInt(totRes, 16);

    if (count === 0) { showStatus('empty'); return; }

    // fetch badge details
    myBadges = [];
    for(const tid of tokenIds) {
      const bRes = await ethCall(SEL.badges + encUint(tid));
      const bHex = bRes.slice(2);
      myBadges.push({
        tokenId:   tid,
        badgeName: decString(bHex, 0),
        activity:  decString(bHex, 2),
        issuedAt:  decUint(bHex, 8),
      });
    }

    showStatus('');
    renderBadges(myBadges);

  } catch(e) {
    console.error(e);
    showStatus('error', 'ไม่สามารถดึงข้อมูลได้\n' + (e.message || ''));
  }
}

// ── Check Owner ───────────────────────────────────────────
async function checkOwner() {
  if (CONTRACT_ADDRESS === '0xYOUR_CONTRACT_ADDRESS_HERE') { isOwner = true; showMintTab(); return; }
  try {
    const res = await ethCall(SEL.owner);
    const ownerAddr = '0x' + res.slice(-40);
    isOwner = ownerAddr.toLowerCase() === account.toLowerCase();
    if (isOwner) showMintTab();
  } catch(e) {}
}

function showMintTab() {
  document.getElementById('mintTab').style.display = 'block';
}

// ── Render Badges ─────────────────────────────────────────
function renderBadges(badges) {
  const grid = document.getElementById('badgeGrid');
  grid.innerHTML = '';
  badges.forEach((b, i) => {
    const card = document.createElement('div');
    card.className = 'badge-card';
    card.style.animationDelay = `${i * 0.07}s`;
    card.onclick = () => openModal(b);

    const emoji = getBadgeEmoji(b.badgeName);
    const dateStr = b.issuedAt ? new Date(b.issuedAt * 1000).toLocaleDateString('lo-LA') : '—';

    card.innerHTML = `
      <div class="badge-img-wrap">
        <div class="placeholder">${emoji}</div>
        <div class="token-chip">#${b.tokenId}</div>
      </div>
      <div class="badge-body">
        <div class="badge-name">${b.badgeName}</div>
        <div class="badge-activity">${b.activity}</div>
        <div class="badge-date">📅 ${dateStr}</div>
      </div>
    `;
    grid.appendChild(card);
  });
}

// ── Mock Data (preview ก่อน deploy) ──────────────────────
function showMockData() {
  document.getElementById('myBadgeCount').textContent = 3;
  document.getElementById('totalMinted').textContent = 12;
  document.getElementById('mintTab').style.display = 'block';
  renderBadges([
    { tokenId: 0, badgeName: 'Blockchain Beginner', activity: 'ผ่านการทดสอบพื้นฐาน Blockchain 101', issuedAt: 1740614400 },
    { tokenId: 1, badgeName: 'Smart Contract Dev',  activity: 'เขียนและ Deploy Smart Contract สำเร็จ', issuedAt: 1741219200 },
    { tokenId: 2, badgeName: 'Hackathon Winner',    activity: 'ชนะการแข่งขัน Blockchain 2026', issuedAt: 1741824000 },
  ]);
  showStatus('');
  toast('👀 Preview Mode — ใส่ CONTRACT_ADDRESS จริงเพื่อใช้งานจริง', 'success');
}

// ── Mint Badge ────────────────────────────────────────────
async function mintBadge() {
  const to       = document.getElementById('mintTo').value.trim();
  const name     = document.getElementById('mintName').value.trim();
  const activity = document.getElementById('mintActivity').value.trim();
  const uri      = document.getElementById('mintURI').value.trim() || 'ipfs://placeholder';

  if (!to || !name || !activity) { toast('❌ กรุณากรอกข้อมูลให้ครบ', 'error'); return; }
  if (!to.startsWith('0x')) { toast('❌ Wallet address ไม่ถูกต้อง', 'error'); return; }

  const btn = document.getElementById('mintBtn');
  btn.disabled = true; btn.textContent = '⏳ กำลัง Mint...';
  setMintStatus('loading');

  try {
    // Encode mintBadge(address,string,string,string)
    // selector: 0x... (ใช้ eth_sendTransaction)
    const data = encodeMintBadge(to, name, activity, uri);

    // ประมาณ gas ก่อนส่ง
    let gasEstimate = '0x493E0'; // default 300,000
    try {
      const est = await ethereum.request({
        method: 'eth_estimateGas',
        params: [{ from: account, to: CONTRACT_ADDRESS, data }]
      });
      // บวก 20% buffer
      gasEstimate = '0x' + Math.ceil(parseInt(est, 16) * 1.2).toString(16);
    } catch(e) { console.warn('Gas estimate failed, using default'); }

    const txHash = await ethereum.request({
      method: 'eth_sendTransaction',
      params: [{ from: account, to: CONTRACT_ADDRESS, data, gas: gasEstimate }]
    });

    setMintStatus('success', `✅ Mint สำเร็จ!\nTx: ${txHash}`);
    toast('🎉 Mint Badge สำเร็จ!', 'success');

    // reset form
    document.getElementById('mintTo').value = '';
    document.getElementById('mintName').value = '';
    document.getElementById('mintActivity').value = '';
    document.getElementById('mintURI').value = '';

    setTimeout(() => { switchTab('my'); loadBadges(); }, 2000);

  } catch(e) {
    setMintStatus('error', '❌ ' + (e.message || 'Transaction failed'));
    toast('❌ Mint ไม่สำเร็จ', 'error');
  } finally {
    btn.disabled = false; btn.textContent = '⚡ Mint Badge';
  }
}

// ── ABI Encode mintBadge ──────────────────────────────────
function encodeMintBadge(to, name, activity, uri) {
  const selector = '0x'; // จะถูก replace ด้วย keccak
  // mintBadge(address,string,string,string) selector = 0x... 
  // คำนวณ: keccak256("mintBadge(address,string,string,string)")
  const fnSel = '0x3ed5b2e4';

  const encStr = (s) => {
    const bytes = new TextEncoder().encode(s);
    const lenHex = bytes.length.toString(16).padStart(64,'0');
    let dataHex = '';
    for(const b of bytes) dataHex += b.toString(16).padStart(2,'0');
    dataHex = dataHex.padEnd(Math.ceil(bytes.length/32)*64, '0');
    return lenHex + dataHex;
  };

  const addrEnc = to.slice(2).toLowerCase().padStart(64,'0');
  // offsets: addr=0x20*1, str1=0x20*2, str2=dynamic, str3=dynamic
  const slot = 32;
  const off1 = 4 * slot;  // offset of string1 from start of params
  const s1   = encStr(name);
  const off2 = off1 + s1.length/2;
  const s2   = encStr(activity);
  const off3 = off2 + s2.length/2;
  const s3   = encStr(uri);

  const toHex = (n) => n.toString(16).padStart(64,'0');

  return fnSel
    + addrEnc
    + toHex(off1)
    + toHex(off2)
    + toHex(off3)
    + s1 + s2 + s3;
}

// ── Open Modal ────────────────────────────────────────────
function openModal(b) {
  document.getElementById('modalName').textContent = b.badgeName;
  document.getElementById('modalActivity').textContent = b.activity;
  document.getElementById('modalImgWrap').textContent = getBadgeEmoji(b.badgeName);
  document.getElementById('modal').classList.add('open');

  const date = b.issuedAt ? new Date(b.issuedAt*1000).toLocaleString('lo-LA') : '—';
  document.getElementById('modalMeta').innerHTML =
    `Token ID: <span>#${b.tokenId}</span><br>` +
    `ออกเมื่อ: <span>${date}</span><br>` +
    `Network: <span>LabChain</span>`;
}
function closeModal(e) { if(e.target.id==='modal') document.getElementById('modal').classList.remove('open'); }

// ── Tabs ──────────────────────────────────────────────────
function switchTab(tab) {
  document.getElementById('tabMy').style.display   = tab==='my'   ? 'block' : 'none';
  document.getElementById('tabMint').style.display = tab==='mint' ? 'block' : 'none';
  document.querySelectorAll('.tab').forEach((t,i)=>{
    t.classList.toggle('active', (i===0&&tab==='my')||(i===1&&tab==='mint'));
  });
}

// ── Fill Preset ───────────────────────────────────────────
function fillPreset(name, activity) {
  document.getElementById('mintName').value     = name;
  document.getElementById('mintActivity').value = activity;
}

// ── Helpers ───────────────────────────────────────────────
function getBadgeEmoji(name='') {
  const n = name.toLowerCase();
  if(n.includes('winner')||n.includes('hackathon')) return '🏆';
  if(n.includes('smart')||n.includes('contract'))   return '⚡';
  if(n.includes('web3')||n.includes('explorer'))    return '🌐';
  if(n.includes('blockchain'))                      return '🔗';
  return '🎓';
}

function shortenAddr(addr) { return addr.slice(0,6)+'...'+addr.slice(-4); }

function showStatus(type, msg='') {
  const el = document.getElementById('status');
  if(type==='loading') {
    el.style.display='block'; el.className='';
    el.innerHTML='<div class="spinner"></div><p>กำลังโหลด...</p>';
  } else if(type==='empty') {
    el.style.display='block'; el.className='';
    el.innerHTML='<div class="empty"><div class="icon">🎓</div><p>ยังไม่มี Badge</p></div>';
  } else if(type==='error') {
    el.style.display='block'; el.className='error'; el.textContent=msg;
  } else {
    el.style.display='none'; el.textContent='';
  }
}

function setMintStatus(type, msg='') {
  const el = document.getElementById('mintStatus');
  el.style.display = type ? 'block' : 'none';
  if(type==='loading') { el.innerHTML='<div class="spinner" style="width:28px;height:28px;border-width:2px"></div>กำลัง Mint...'; el.style.color='var(--muted)'; }
  else if(type==='success') { el.textContent=msg; el.style.color='var(--accent2)'; }
  else if(type==='error')   { el.textContent=msg; el.style.color='var(--danger)'; }
}

function toast(msg, type='') {
  const el = document.getElementById('toast');
  el.textContent = msg; el.className = 'toast ' + type + ' show';
  setTimeout(()=>el.classList.remove('show'), 3500);
}

function scrollToDemo() {
  connectWallet();
}
</script>

</div><!-- /wrap -->
</body>
</html>
