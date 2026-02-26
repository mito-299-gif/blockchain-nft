<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NFT Gallery</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="./css/index.css">
</head>

<body>

    <header>
        <div class="logo">NFT<span>.</span>Gallery</div>
        <div class="network-badge">
            <span class="dot"></span>
            <span id="networkName">—</span>
        </div>
        <button id="alchemyBtn" onclick="openAlchemyModal()">🔑 Connect Alchemy</button>
    </header>


    <div class="hero" id="heroSection">
        <h1>YOUR<br><span class="gradient">NFT GALLERY</span></h1>
        <p>connect metamask view your NFTs on any network</p>
        <button id="connectBtn" onclick="connectWallet()">
            <svg viewBox="0 0 35 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M32.9582 1L18.6349 11.6769L21.3298 5.30603L32.9582 1Z" fill="#E17726" />
                <path d="M2.04102 1L16.2335 11.7826L13.6694 5.30603L2.04102 1Z" fill="#E27625" />
                <path d="M28.2141 23.5185L24.3736 29.2511L32.0948 31.3965L34.3229 23.6389L28.2141 23.5185Z"
                    fill="#E27625" />
                <path d="M0.689453 23.6389L2.90315 31.3965L10.6244 29.2511L6.78387 23.5185L0.689453 23.6389Z"
                    fill="#E27625" />
            </svg>
            Connect MetaMask
        </button>
    </div>

    <div id="noMetamask" style="display:none;">
        <h3>ບໍ່ພົບ MetaMask</h3>
        <p>install <a href="https://metamask.io/download/" target="_blank">MetaMask</a> extension in your browser</p>
    </div>

    <div class="container">
        <div id="walletInfo" style="display:none;">
            <div class="label">Connected Wallet</div>
            <div class="address" id="walletAddress">—</div>
            <div class="stats">
                <div class="stat">
                    <div class="num" id="nftCount">0</div>
                    <div class="stat-label">NFTs Found</div>
                </div>
                <div class="stat">
                    <div class="num" style="color:var(--accent2)" id="statNetwork">—</div>
                    <div class="stat-label">Network</div>
                </div>
            </div>
        </div>

        <div id="contractForm" style="display:none;">
            <h3>ດືງ NFT ຈາກ Contract Address (ບໍ່ບັງຄັບ)</h3>
            <div class="input-row">
                <input type="text" id="contractInput" placeholder="0x... contract address (ວ່າງໄວ້ = ດືງຈາກ wallet)" />
                <button onclick="fetchFromContract()">ດືງ NFT</button>
            </div>
        </div>

        <div id="status"></div>
        <div id="loading" style="display:none;">
            <div class="spinner"></div>
            <p>loading NFT...</p>
        </div>
    </div>

    <div id="nftControls" class="container" style="display:none;">
        <div class="section-title">My NFTs</div>
    </div>

    <div id="nftGrid"></div>

    <div id="modal" onclick="closeModal(event)">
        <div class="modal-inner" id="modalInner">
            <img id="modalImg" class="modal-img" src="" alt="">
            <div class="modal-body">
                <div class="modal-name" id="modalName"></div>
                <div class="modal-collection" id="modalCollection"></div>
                <div class="modal-desc" id="modalDesc"></div>

                <div class="modal-verify">
                    <h4>Verification</h4>

                    <div class="modal-verify-row">
                        <span class="vkey"> Contract Verified</span>
                        <span id="modalVerified">loading...</span>
                    </div>
                    <div class="modal-verify-row">
                        <span class="vkey"> Contract Address</span>
                        <a id="modalEtherscan" href="#" target="_blank"
                            style="color:#a78bfa; font-family:'Space Mono',monospace; font-size:0.72rem;">—</a>
                    </div>

                </div>

                <div class="modal-attrs" id="modalAttrs"></div>
                <button class="modal-close" onclick="document.getElementById('modal').classList.remove('open')">✕
                    close</button>
            </div>
        </div>
    </div>

    <div id="alchemyModal" onclick="closeBgAlchemy(event)">
        <div class="alchemy-inner">
            <h3>🔑 Alchemy API Key</h3>
            <p>ໃສ່ API Key ຈາກ <a href="https://www.alchemy.com" target="_blank">alchemy.com</a><br>
                Key ຈະເກັບໃນ sessionStorage ເທົ່ານັ້ນ ບໍ່ສົ່ງອອກໄປໃສ</p>
            <input type="text" id="alchemyInput" placeholder="YRV0uMkH..." />
            <div id="alchemyStatus"></div>
            <div class="alchemy-row">
                <button class="btn-save" onclick="saveAlchemyKey()">Save</button>
                <button class="btn-clear" onclick="clearAlchemyKey()">Clear</button>
            </div>
            <button class="alchemy-cancel" onclick="closeAlchemyModal()">✕ Close</button>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.umd.min.js"></script>
    <script src="../script/Gallery.js"></script>

</body>

</html>