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

    <!-- Network Settings Card -->
    <div style="background:#111827;border:1px solid #1f2937;border-radius:16px;padding:1.5rem 2rem;max-width:480px;margin:0 auto 2rem;text-align:left;">
      <div style="font-size:0.75rem;color:#64748b;font-family:'Space Mono',monospace;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:1rem;">⚙️ Network Settings</div>

      <div style="margin-bottom:1rem;">
        <label style="display:block;font-size:0.75rem;color:#64748b;font-family:'Space Mono',monospace;margin-bottom:6px;">CONTRACT ADDRESS</label>
        <input id="inputContract" type="text" placeholder="0x..."
          style="width:100%;background:#0d1117;border:1px solid #1f2937;border-radius:8px;padding:10px 14px;color:#f1f5f9;font-family:'Space Mono',monospace;font-size:0.8rem;outline:none;"
          onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#1f2937'" />
      </div>

      <div style="margin-bottom:1.2rem;">
        <label style="display:block;font-size:0.75rem;color:#64748b;font-family:'Space Mono',monospace;margin-bottom:6px;">CHAIN ID <span style="color:#374151;font-size:0.65rem;">(ตัวเลข เช่น 11155111 = Sepolia)</span></label>
        <input id="inputChainId" type="text" placeholder="เช่น 11155111"
          oninput="previewChainId(this.value)"
          style="width:100%;background:#0d1117;border:1px solid #1f2937;border-radius:8px;padding:10px 14px;color:#f1f5f9;font-family:'Space Mono',monospace;font-size:0.8rem;outline:none;"
          onfocus="this.style.borderColor='#6366f1'" onblur="this.style.borderColor='#1f2937'" />
        <div id="chainIdPreview" style="font-size:0.7rem;color:#34d399;font-family:'Space Mono',monospace;margin-top:5px;min-height:18px;"></div>
      </div>

      <!-- Quick Presets -->
      <div style="margin-bottom:0.8rem;">
        <label style="display:block;font-size:0.72rem;color:#64748b;font-family:'Space Mono',monospace;margin-bottom:8px;">QUICK FILL</label>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
          <button onclick="detectFromMetaMask()"
            style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.4);color:#f59e0b;padding:6px 14px;border-radius:6px;font-size:0.72rem;font-family:'Space Mono',monospace;cursor:pointer;display:flex;align-items:center;gap:6px;">
            🦊 ดึงจาก MetaMask
          </button>
          <button onclick="setNetwork('0xE76CAF6C344230f319D70f97F712a424E6b61B72','11155111')"
            style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);color:#6366f1;padding:6px 12px;border-radius:6px;font-size:0.72rem;font-family:'Space Mono',monospace;cursor:pointer;">
            Sepolia
          </button>
          <button onclick="setNetwork('','1337')"
            style="background:rgba(52,211,153,0.1);border:1px solid rgba(52,211,153,0.2);color:#34d399;padding:6px 12px;border-radius:6px;font-size:0.72rem;font-family:'Space Mono',monospace;cursor:pointer;">
            LabChain
          </button>
        </div>
      </div>
      <div id="detectResult" style="font-size:0.72rem;font-family:'Space Mono',monospace;color:#34d399;min-height:18px;"></div>
    </div>

    <button class="btn-primary" onclick="connectWallet()">
      🦊 Connect MetaMask
    </button>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.umd.min.js"></script>
<script>
// ============================================================
//  CONFIG — อ่านจาก UI input แทน hardcode
// ============================================================
let CONTRACT_ADDRESS = '0xE76CAF6C344230f319D70f97F712a424E6b61B72'; // default Sepolia
let LABCHAIN_ID      = '0xaa36a7'; // default Sepolia

const ABI = [
  'function getBadgesOf(address student) view returns (uint256[])',
  'function badges(uint256 tokenId) view returns (string badgeName, string activity, address issuedTo, uint256 issuedAt)',
  'function totalMinted() view returns (uint256)',
  'function owner() view returns (address)',
  'function mintBadge(address student, string badgeName, string activity, string tokenURI_) returns (uint256)',
  'event BadgeMinted(uint256 indexed tokenId, address indexed student, string badgeName, string activity)',
];

let account  = null;
let provider = null;
let signer   = null;
let contract = null;
let myBadges = [];

// ── Settings helpers ──────────────────────────────────────
function previewChainId(val) {
  const el = document.getElementById('chainIdPreview');
  const n = parseInt(val);
  if (!val || isNaN(n)) { el.textContent = ''; return; }
  el.textContent = '→ hex: 0x' + n.toString(16);
}

// ── Detect Chain ID จาก MetaMask ──────────────────────────
async function detectFromMetaMask() {
  const resultEl = document.getElementById('detectResult');
  if (!window.ethereum) {
    resultEl.style.color = '#f43f5e';
    resultEl.textContent = '❌ MetaMask ไม่พบ กรุณาติดตั้งก่อน';
    return;
  }

  try {
    resultEl.textContent = '⏳ กำลังดึงข้อมูล...';
    resultEl.style.color = '#64748b';

    // ดึง Chain ID ที่ MetaMask ใช้อยู่
    const chainHex = await ethereum.request({ method: 'eth_chainId' });
    const chainDec = parseInt(chainHex, 16);

    // ใส่ค่าลง input
    document.getElementById('inputChainId').value = chainDec;
    previewChainId(chainDec.toString());

    // หาชื่อ network
    const names = {
      1: 'Ethereum Mainnet', 11155111: 'Sepolia Testnet',
      1337: 'LabChain / Localhost', 31337: 'Hardhat',
      137: 'Polygon', 56: 'BNB Chain'
    };
    const netName = names[chainDec] || `Chain ID: ${chainDec}`;

    resultEl.style.color = '#34d399';
    resultEl.textContent = `✅ ดึงสำเร็จ! ${netName} (${chainDec})`;

    // ดึง accounts ด้วย ถ้า MetaMask connect อยู่แล้ว
    try {
      const accs = await ethereum.request({ method: 'eth_accounts' });
      if (accs.length > 0) {
        resultEl.textContent += ` · wallet: ${accs[0].slice(0,6)}...${accs[0].slice(-4)}`;
      }
    } catch(e) {}

  } catch(e) {
    resultEl.style.color = '#f43f5e';
    resultEl.textContent = '❌ ดึงไม่ได้: ' + (e.message || 'unknown error');
  }
}


function setNetwork(addr, chainId) {
  if (addr) document.getElementById('inputContract').value = addr;
  document.getElementById('inputChainId').value = chainId;
  previewChainId(chainId);
}

function getSettingsFromUI() {
  const addr    = document.getElementById('inputContract').value.trim();
  const chainRaw = document.getElementById('inputChainId').value.trim();
  const chainNum = parseInt(chainRaw);

  if (addr && addr.startsWith('0x') && addr.length === 42)
    CONTRACT_ADDRESS = addr;

  if (chainRaw && !isNaN(chainNum))
    LABCHAIN_ID = '0x' + chainNum.toString(16);
  else if (chainRaw.startsWith('0x'))
    LABCHAIN_ID = chainRaw;
}

// ── Connect Wallet ─────────────────────────────────────────
async function connectWallet() {
  if (!window.ethereum) { toast('❌ กรุณาติดตั้ง MetaMask ก่อน', 'error'); return; }

  // อ่านค่าจาก UI ก่อน connect
  getSettingsFromUI();

  if (!CONTRACT_ADDRESS || CONTRACT_ADDRESS === '0x') {
    toast('❌ กรุณาใส่ Contract Address ก่อน', 'error'); return;
  }

  try {
    provider = new ethers.BrowserProvider(window.ethereum);
    await provider.send('eth_requestAccounts', []);
    signer   = await provider.getSigner();
    account  = await signer.getAddress();
    contract = new ethers.Contract(CONTRACT_ADDRESS, ABI, provider);

    document.getElementById('walletBtn').textContent = shortenAddr(account);
    document.getElementById('walletBtn').classList.add('connected');
    document.getElementById('heroSection').style.display = 'none';
    document.getElementById('appSection').style.display  = 'block';

    await loadBadges();
    await checkOwner();

    window.ethereum.on('accountsChanged', async (accs) => {
      account  = accs[0];
      signer   = await provider.getSigner();
      contract = new ethers.Contract(CONTRACT_ADDRESS, ABI, provider);
      loadBadges();
      checkOwner();
    });
  } catch(e) {
    toast('❌ ' + (e.message || 'Connection failed'), 'error');
  }
}

// ── Load Badges ───────────────────────────────────────────
async function loadBadges() {
  showStatus('loading');
  try {
    const tokenIds = await contract.getBadgesOf(account);

    document.getElementById('myBadgeCount').textContent = tokenIds.length;

    try {
      const total = await contract.totalMinted();
      document.getElementById('totalMinted').textContent = total.toString();
    } catch(e) {}

    if (tokenIds.length === 0) { showStatus('empty'); return; }

    myBadges = [];
    for (const tid of tokenIds) {
      try {
        const b = await contract.badges(tid);
        myBadges.push({
          tokenId:   Number(tid),
          badgeName: b.badgeName,
          activity:  b.activity,
          issuedAt:  Number(b.issuedAt),
        });
      } catch(e) {
        myBadges.push({ tokenId: Number(tid), badgeName: `Badge #${tid}`, activity: '', issuedAt: 0 });
      }
    }

    showStatus('');
    renderBadges(myBadges);

  } catch(e) {
    console.error('loadBadges error:', e);
    showStatus('error', 'ไม่สามารถดึงข้อมูลได้\n' + (e.reason || e.message || ''));
  }
}

// ── Check Owner ───────────────────────────────────────────
async function checkOwner() {
  try {
    const ownerAddr = await contract.owner();
    if (ownerAddr.toLowerCase() === account.toLowerCase()) showMintTab();
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

    const emoji  = getBadgeEmoji(b.badgeName);
    const dateStr = b.issuedAt ? new Date(b.issuedAt * 1000).toLocaleDateString('th-TH') : '—';

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

// ── Mint Badge ────────────────────────────────────────────
async function mintBadge() {
  const to       = document.getElementById('mintTo').value.trim();
  const name     = document.getElementById('mintName').value.trim();
  const activity = document.getElementById('mintActivity').value.trim();
  const uri      = document.getElementById('mintURI').value.trim() || 'ipfs://placeholder';

  if (!to || !name || !activity) { toast('❌ กรุณากรอกข้อมูลให้ครบ', 'error'); return; }
  if (!to.startsWith('0x'))      { toast('❌ Wallet address ไม่ถูกต้อง', 'error'); return; }

  const btn = document.getElementById('mintBtn');
  btn.disabled = true; btn.textContent = '⏳ กำลัง Mint...';
  setMintStatus('loading');

  try {
    const contractWithSigner = new ethers.Contract(CONTRACT_ADDRESS, ABI, signer);
    const tx = await contractWithSigner.mintBadge(to, name, activity, uri);
    setMintStatus('loading', `⏳ รอ confirm... Tx: ${tx.hash}`);
    await tx.wait();

    setMintStatus('success', `✅ Mint สำเร็จ!\nTx: ${tx.hash}`);
    toast('🎉 Mint Badge สำเร็จ!', 'success');

    document.getElementById('mintTo').value       = '';
    document.getElementById('mintName').value     = '';
    document.getElementById('mintActivity').value = '';
    document.getElementById('mintURI').value      = '';

    setTimeout(() => { switchTab('my'); loadBadges(); }, 2000);

  } catch(e) {
    setMintStatus('error', '❌ ' + (e.reason || e.message || 'Transaction failed'));
    toast('❌ Mint ไม่สำเร็จ', 'error');
  } finally {
    btn.disabled = false; btn.textContent = '⚡ Mint Badge';
  }
}

// ── Open Modal ────────────────────────────────────────────
function openModal(b) {
  document.getElementById('modalName').textContent     = b.badgeName;
  document.getElementById('modalActivity').textContent = b.activity;
  document.getElementById('modalImgWrap').textContent  = getBadgeEmoji(b.badgeName);
  document.getElementById('modal').classList.add('open');
  const date = b.issuedAt ? new Date(b.issuedAt*1000).toLocaleString('th-TH') : '—';
  document.getElementById('modalMeta').innerHTML =
    `Token ID: <span>#${b.tokenId}</span><br>` +
    `ออกเมื่อ: <span>${date}</span><br>` +
    `Network: <span>Sepolia Testnet</span>`;
}
function closeModal(e) { if(e.target.id==='modal') document.getElementById('modal').classList.remove('open'); }

// ── Tabs ──────────────────────────────────────────────────
function switchTab(tab) {
  document.getElementById('tabMy').style.display   = tab==='my'   ? 'block' : 'none';
  document.getElementById('tabMint').style.display = tab==='mint' ? 'block' : 'none';
  document.querySelectorAll('.tab').forEach((t,i) => {
    t.classList.toggle('active', (i===0&&tab==='my')||(i===1&&tab==='mint'));
  });
}

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
  if(type==='loading') { el.innerHTML='<div class="spinner" style="width:28px;height:28px;border-width:2px"></div>' + (msg||'กำลัง Mint...'); el.style.color='var(--muted)'; }
  else if(type==='success') { el.textContent=msg; el.style.color='var(--accent2)'; }
  else if(type==='error')   { el.textContent=msg; el.style.color='var(--danger)'; }
}

function toast(msg, type='') {
  const el = document.getElementById('toast');
  el.textContent = msg; el.className = 'toast ' + type + ' show';
  setTimeout(()=>el.classList.remove('show'), 3500);
}

function scrollToDemo() { connectWallet(); }
</script>

</div><!-- /wrap -->
</body>
</html>
