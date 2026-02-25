<!DOCTYPE html>
<html lang="lo">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ອອກໃບຍັນຍືນ FNT</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&family=Space+Mono:wght@400;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="./css/Contract.css">
  <style>

  </style>
</head>

<body>
  <div class="wrap">

    <header>
      <div class="logo">
        <span class="badge-icon">ອອກໃບຍັນຍືນ</span>FNT<span class="accent"></span>
        <button id="pinataBtn" onclick="openPinataModal()">Connect Pinata API</button>
      </div>
      <button id="walletBtn" onclick="connectWallet()">🦊 Connect Wallet</button>
    </header>

    <div id="heroSection">
      <div class="hero">
        <h1>ອອກໃບຍັນຍືນ<br><span class="grad">NFT</span></h1>
        <p>ຍົກລະດັບຄວາມປອດພັຍອອກໃບຍັນຍືນໃນໂລກ blockchain</p>
        <button class="btn-primary" onclick="connectWallet()">🦊 Connect MetaMask</button>
        <!-- <button class="btn-secondary" onclick="scrollToDemo()">ເບີ່ງຕົວຢາງ
        </button> -->
      </div>
    </div>


    <div id="appSection" style="display:none;">


      <div style="padding:2rem 2rem 0;max-width:1100px;margin:0 auto;">
        <div class="tabs">
          <button class="tab active" onclick="switchTab('my')">ໃບຍັນຍືນ</button>
          <button class="tab" onclick="switchTab('mint')" id="mintTab">⚡ ອອກໃບຍັນຍືນ</button>
        </div>
      </div>


      <div id="tabMy" class="section">
        <div class="section-title">ໃບຍັນຍືນທັງໝົດ</div>
        <div class="section-sub">NFT on LabChain</div>

        <div class="stats-bar">
          <div class="stat-card">
            <div class="num" id="myBadgeCount">0</div>
            <div class="lbl">ໃບຍັນຍືນທີ່ມີຢູ່</div>
          </div>
          <div class="stat-card">
            <div class="num" style="color:var(--accent2)" id="totalMinted">—</div>
            <div class="lbl">ໃບຍັນຍືນທັງໝົດທີ່ອອກ</div>
          </div>
          <div class="stat-card">
            <div class="num" style="color:var(--gold)">LabChain</div>
            <div class="lbl">Network</div>
          </div>
        </div>

        <div id="status"></div>
        <div id="badgeGrid" class="badge-grid"></div>
      </div>


      <div id="tabMint" class="section" style="display:none;">
        <div class="section-title">ອອກໃບຍັນຍືນໃໝ່</div>
        <div class="section-sub"> ສະເພາະ admin/owner ເທົ່ານັ້ນທີ່ມີສິດ</div>

        <div class="mint-form">

          <div style="margin-bottom:1.4rem;">
            <div class="badge-presets-title">
              ຕົວເລືອກໃບຍັນຍືນ (Quick Presets)
            </div>
            <div class="badge-presets">
              <button class="preset-btn" onclick="fillPreset('Blockchain Beginner','ຜ່ານການຮຽນຮູ້ Blockchain')">🔗
                Blockchain Beginner</button>
              <button class="preset-btn" onclick="fillPreset('Smart Contract Dev','ຂຽນ Smart Contract ສຳເລັດ')">⚡
                Smart Contract</button>
              <button class="preset-btn" onclick="fillPreset('Web3 Explorer','ຮຽນຮູ້ Web3 ຄົບທຸກດ້ານ')">🌐 Web3
                Explorer</button>
              <button class="preset-btn" onclick="fillPreset('Hackathon Winner','ຊະນະການແຂ່ງຂັນ Blockchain')">🏆
                Hackathon Winner</button>
            </div>
          </div>

          <div class="form-group">
            <label>Wallet Address</label>
            <input type="text" id="mintTo" placeholder="0x..." />
          </div>
          <div class="form-group">
            <label>Name ໃບຍັນຍືນ</label>
            <input type="text" id="mintName" placeholder="ຕົວຢ່າງ Blockchain Beginner" />
          </div>
          <div class="form-group">
            <label>ຄຳອະທິບາຍ</label>
            <textarea id="mintActivity" placeholder="ຕົວຢ່າງ ຜ່ານການຮຽນຮູ້ Blockchain "></textarea>
          </div>

          <div class="form-group">
            <label>Upload Image</label>
            <input type="file" name="file" id="file" class="inputfile" accept="image/*" />
            <label for="file"><i class="fas fa-upload"></i> <span>Choose a file</span></label>
            <button onclick="uploadAll()" style="margin-left:0.5rem;">⬆ Upload Image</button>
            <div id="uploadProgress">⏳ Uploading...</div>
            <div id="uploadResult"></div>
            <button id="copyUriBtn" onclick="copyUri()">📋 Copy URL</button>
          </div>

          <div class="form-group">
            <label>Image/Metadata URI (IPFS ro URL)</label>
            <input type="text" id="mintURI" placeholder="ipfs://... ro https://... (auto-fill on upload)" />
          </div>

          <button class="submit-btn" id="mintBtn" onclick="mintBadge()">
            ⚡ Mint Badge
          </button>

          <div id="mintStatus"></div>


        </div>
      </div>

    </div>

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


    <div id="pinataModal" onclick="closePinataModalOutside(event)">
      <div class="pinata-inner">
        <h3>🔑 Pinata API JWT</h3>
        <p>ໃສ່ JWT ສໍາລັບ upload ຮູບຂຶ້ນ IPFS ຜ່ານ Pinata</p>
        <input type="text" id="jwtInput" placeholder="eyJhbGciOi..." />
        <div id="pinataStatus"></div>
        <div class="pinata-row">
          <button class="btn-save" onclick="saveJWT()">Save JWT</button>
          <button class="btn-clear" onclick="clearJWT()">Clear</button>
        </div>
        <button class="btn-cancel" onclick="closePinataModal()" style="padding:0.5rem;">✕ close</button>
      </div>
    </div>

    <div class="toast" id="toast"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.umd.min.js"></script>
    <script src="./script/index.js"></script>
    <script src="./script/contract_address.js"></script>


  </div>
</body>

</html>