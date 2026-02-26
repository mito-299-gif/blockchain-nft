// @Chain ID
// @contract address in deploy
const LAB_CHAIN_ID = 5222;
const LAB_CONTRACT_DEFAULT = "0xE76CAF6C344230f319D70f97F712a424E6b61B72";

// @ຂໍ້ມູນ Network ທີ່ຮອງຮັບ ແລະ API Alchemy ສໍາລັບແຕ່ລະ Network
const NETWORK_MAP = {
  1: { name: "Ethereum", alchemy: "eth-mainnet", os: "ethereum" },
  11155111: { name: "Sepolia", alchemy: "eth-sepolia", os: "sepolia" },
  137: { name: "Polygon", alchemy: "polygon-mainnet", os: "matic" },
  80002: { name: "Polygon Amoy", alchemy: "polygon-amoy", os: "amoy" },
  10: { name: "Optimism", alchemy: "opt-mainnet", os: "optimism" },
  42161: { name: "Arbitrum One", alchemy: "arb-mainnet", os: "arbitrum" },
  8453: { name: "Base", alchemy: "base-mainnet", os: "base" },
  84532: { name: "Base Sepolia", alchemy: "base-sepolia", os: "base_sepolia" },
  17000: { name: "Holesky", alchemy: "eth-holesky", os: null },
  5222: {
    name: "LAB Chain",
    alchemy: null,
    os: null,
    rpc: "https://rpc.labchain.la",
    explorer: "https://explorer.labchain.la",
    symbol: "LAB",
  },
};

const LAB_ABI = [
  "function getBadgesOf(address student) view returns (uint256[])",
  "function badges(uint256 tokenId) view returns (string badgeName, string activity, address issuedTo, uint256 issuedAt)",
  "function tokenURI(uint256 tokenId) view returns (string)",
];

let currentAccount = null;
let currentChainId = null;
let allNFTs = [];

// @time token
function setCookie(name, value, days) {
  const expires = new Date(Date.now() + days * 864e5).toUTCString();
  document.cookie =
    name +
    "=" +
    encodeURIComponent(value) +
    "; expires=" +
    expires +
    "; path=/";
}

// @ເລຶ່ມສ້າງ token
function getCookie(APialchemyKey) {
  return document.cookie.split("; ").reduce((r, v) => {
    const parts = v.split("=");
    return parts[0] === APialchemyKey ? decodeURIComponent(parts[1]) : r;
  }, "");
}

// @select token
// function opentoMadotoken() {
//   let token = getCookie("alchemyKey");
//   if (!token) {
//     token = localStorage.getItem("alchemyKey");
//   }
//   if (!token) {
//     alert("Please connect Alchemy Key first");
//     return;
//   }
//   console.log("Alchemy Key:", token);
// }

// @ສ້າງ token
function saveToCookie() {
  setCookie("alchemyKey", token, 7);
  localStorage.setItem("alchemyKey", token);
}

// @delete token
function cleartoken(alchemyKey) {
  setCookie(alchemyKey, "", -1);
  localStorage.removeItem(alchemyKey);
}

function toChainNum(chainId) {
  if (chainId === null || chainId === undefined) return 0;
  if (typeof chainId === "number") return chainId;
  return parseInt(chainId, 16);
}

// @ເປີດ modal Alchemy
function openAlchemyModal() {
  const saved = getCookie("alchemyKey");
  document.getElementById("alchemyInput").value = saved || "";
  const st = document.getElementById("alchemyStatus");
  st.textContent = saved ? "Save" : "";
  st.style.color = "#22c55e";
  document.getElementById("alchemyModal").classList.add("open");
}

// @ປິດ modal Alchemy
function closeAlchemyModal() {
  document.getElementById("alchemyModal").classList.remove("open");
}

// @ປິດ modal Alchemy ໂດຍການກົດນອກ modal
function closeBgAlchemy(e) {
  if (e.target.id === "alchemyModal") closeAlchemyModal();
}

// @ບັນທຶກ API Key Alchemy
function saveAlchemyKey() {
  const key = document.getElementById("alchemyInput").value.trim();
  const st = document.getElementById("alchemyStatus");
  if (!key) {
    st.textContent = "Please enter API Key";
    st.style.color = "#ef4444";
    return;
  }
  setCookie("alchemyKey", key, 7); // Save to cookie for 7 days
  st.textContent = "Saved!";
  st.style.color = "#22c55e";
  setAlchemyBtn(true);
  setTimeout(closeAlchemyModal, 700);
}

// @delete API Key Alchemy
function clearAlchemyKey() {
  cleartoken("alchemyKey");
  document.getElementById("alchemyInput").value = "";
  const st = document.getElementById("alchemyStatus");
  st.textContent = "Cleared";
  st.style.color = "#888";
  setAlchemyBtn(false);
}

// @open connected Alchemy button
function setAlchemyBtn(connected) {
  const btn = document.getElementById("alchemyBtn");
  btn.textContent = connected ? "Alchemy Connected" : "Connect Alchemy";
  connected
    ? btn.classList.add("connected")
    : btn.classList.remove("connected");
}

// @ເບິ່ງ API Key Alchemy in input
function getAlchemyKey() {
  return getCookie("alchemyKey");
}

// @ເບິ່ງຂໍ້ມູນ Network
function getNetworkInfo(chainId) {
  const num = toChainNum(chainId);
  return NETWORK_MAP[num] || { name: "Chain " + num, alchemy: null, os: null };
}

// @ເບິ່ງຂໍ້ມູນ Network
function getAlchemyBase(chainId) {
  const key = getAlchemyKey();
  const info = getNetworkInfo(chainId);
  if (!key || !info.alchemy) return null;
  return "https://" + info.alchemy + ".g.alchemy.com/nft/v3/" + key;
}

// @update Network
function updateNetworkUI(chainId) {
  const info = getNetworkInfo(chainId);
  document.getElementById("networkName").textContent = info.name;
  document.getElementById("statNetwork").textContent = info.name;
}

// @ເບິ່ງຂໍ້ມູນ Wallet
function shortenAddr(addr) {
  if (!addr) return "Unknown";
  return addr.slice(0, 6) + "..." + addr.slice(-4);
}

// @ແປງ URL ipfs ເປັນ HTTP
function ipfsToHttp(url) {
  if (!url) return null;
  if (url.startsWith("ipfs://")) return "https://ipfs.io/ipfs/" + url.slice(7);
  return url;
}

// @ເບິ່ງຂໍ້ມູນ URL ຂອງຮູບ
function getImageUrl(imageObj) {
  if (!imageObj) return null;
  const url =
    imageObj.cachedUrl ||
    imageObj.pngUrl ||
    imageObj.originalUrl ||
    imageObj.thumbnailUrl;
  return ipfsToHttp(url);
}

// @ເບິ່ງຂໍ້ມູນ URL ຂອງ NFT ໃນ OpenSea ຫຼື Alchemy

function showLoading(show) {
  document.getElementById("loading").style.display = show ? "block" : "none";
}

// @ແສດງຂໍ້ຄວາມສະຖານະໃນກ້ອງຮູບ
function showStatus(msg, isErr = false) {
  const el = document.getElementById("status");
  el.textContent = msg;
  el.style.display = msg ? "block" : "none";
  el.className = isErr ? "error" : "";
  el.style.whiteSpace = "pre-line";
}

// @ລ້າງ Grid ແລະ ປິດການເບິ່ງ NFT
function clearGrid() {
  const grid = document.getElementById("nftGrid");
  grid.innerHTML = "";
  grid.style.display = "none";
  document.getElementById("nftControls").style.display = "none";
}

// @connect Wallet ແລະ ຕິດຕໍ່ Event ຂອງ MetaMask
async function connectWallet() {
  if (!window.ethereum) {
    document.getElementById("noMetamask").style.display = "block";
    document.getElementById("heroSection").style.display = "none";
    return;
  }
  try {
    document.getElementById("connectBtn").textContent = "Connecting...";

    const accounts = await window.ethereum.request({
      method: "eth_requestAccounts",
    });
    currentAccount = accounts[0];
    const chainIdHex = await window.ethereum.request({ method: "eth_chainId" });
    currentChainId = toChainNum(chainIdHex);

    onWalletConnected();

    window.ethereum.on("accountsChanged", async (accs) => {
      if (!accs.length) {
        location.reload();
        return;
      }
      currentAccount = accs[0];
      document.getElementById("walletAddress").textContent = currentAccount;
      await fetchNFTs(currentAccount, null, currentChainId);
    });

    window.ethereum.on("chainChanged", async (chainId) => {
      currentChainId = toChainNum(chainId);
      updateNetworkUI(currentChainId);
      await fetchNFTs(currentAccount, null, currentChainId);
    });
  } catch (err) {
    showStatus("Connection failed: " + (err.message || ""), true);
    document.getElementById("connectBtn").textContent = "Connect MetaMask";
  }
}

// @ເບິ່ງຂໍ້ມູນ Wallet ແລະ ສະແດງ UI ຫຼັງຈາກ connect ແລ້ວ
function onWalletConnected() {
  document.getElementById("heroSection").style.display = "none";
  document.getElementById("walletInfo").style.display = "block";
  document.getElementById("contractForm").style.display = "block";
  document.getElementById("walletAddress").textContent = currentAccount;
  updateNetworkUI(currentChainId);
  fetchNFTs(currentAccount, null, currentChainId);
}

// @ເບິ່ງຂໍ້ມູນ NFT ຂອງ Address ໃນ Network
async function fetchNFTs(address, contractAddress, chainId) {
  if (chainId === undefined || chainId === null) chainId = currentChainId;
  showLoading(true);
  showStatus("");
  clearGrid();

  const chainNum = toChainNum(chainId);
  const netInfo = getNetworkInfo(chainNum);
  const base = getAlchemyBase(chainNum);

  if (chainNum === LAB_CHAIN_ID) {
    await fetchFromLabChain(address, contractAddress);
    return;
  }

  if (base) {
    try {
      let url =
        base +
        "/getNFTsForOwner?owner=" +
        address +
        "&withMetadata=true&pageSize=100";
      if (contractAddress) url += "&contractAddresses[]=" + contractAddress;

      const res = await fetch(url);
      if (!res.ok) throw new Error("Alchemy HTTP " + res.status);
      const data = await res.json();

      if (!data.ownedNfts || !data.ownedNfts.length) {
        showLoading(false);
        showStatus("ບໍ່ພົບ NFT ໃນ " + netInfo.name);
        document.getElementById("nftCount").textContent = 0;
        return;
      }

      allNFTs = data.ownedNfts.map((nft) => ({
        tokenId: nft.tokenId,
        name: nft.name || "Token #" + nft.tokenId,
        description: nft.description || "",
        image: getImageUrl(nft.image),
        collection:
          nft.contract && nft.contract.name
            ? nft.contract.name
            : shortenAddr(nft.contract && nft.contract.address),
        contractAddress: nft.contract && nft.contract.address,
        attributes:
          (nft.raw && nft.raw.metadata && nft.raw.metadata.attributes) || [],
      }));

      showLoading(false);
      renderGrid(allNFTs);
      document.getElementById("nftCount").textContent = allNFTs.length;
      return;
    } catch (err) {
      console.warn("Alchemy failed, fallback OpenSea:", err.message);
    }
  }

  if (!getAlchemyKey()) {
    showLoading(false);
    showStatus("Please connect Alchemy Key first", true);
    openAlchemyModal();
    return;
  }

  await fetchFromOpenSea(address, contractAddress, netInfo.os);
}

// @ເບິ່ງຂໍ້ມູນ NFT ຂອງ Address ໃນ Contract
async function fetchFromContract() {
  const contractAddr = document.getElementById("contractInput").value.trim();
  if (!contractAddr || !contractAddr.startsWith("0x")) {
    showStatus("Contract Address ບໍ່ຖືກຕ້ອງ", true);
    return;
  }
  await fetchNFTs(currentAccount, contractAddr, currentChainId);
}

async function fetchFromLabChain(address, contractAddress) {
  const contractAddr =
    contractAddress ||
    document.getElementById("contractInput").value.trim() ||
    LAB_CONTRACT_DEFAULT;

  if (!contractAddr || !contractAddr.startsWith("0x")) {
    showLoading(false);
    showStatus("Please input Contract Address ของ LAB Chain", true);
    return;
  }

  try {
    const labProvider = new ethers.JsonRpcProvider("https://rpc.labchain.la");
    const contract = new ethers.Contract(contractAddr, LAB_ABI, labProvider);
    const tokenIds = await contract.getBadgesOf(address);

    if (!tokenIds.length) {
      showLoading(false);
      showStatus("ບໍ່ພົບ NFT ໃນ LAB Chain");
      document.getElementById("nftCount").textContent = 0;
      return;
    }

    allNFTs = [];
    for (const tid of tokenIds) {
      try {
        const b = await contract.badges(tid);
        let image = null;
        let description = b.activity || "";

        try {
          const uri = await contract.tokenURI(tid);
          const url = ipfsToHttp(uri);
          const meta = await fetch(url).then((r) => r.json());
          image = ipfsToHttp(meta.image || null);
          description = meta.description || description;
        } catch (_) {}

        allNFTs.push({
          tokenId: Number(tid),
          name: b.badgeName || "Badge #" + tid,
          description,
          image,
          collection: "LAB Chain",
          contractAddress: contractAddr,
          attributes: [],
        });
      } catch (_) {
        allNFTs.push({
          tokenId: Number(tid),
          name: "Badge #" + tid,
          description: "",
          image: null,
          collection: "LAB Chain",
          contractAddress: contractAddr,
          attributes: [],
        });
      }
    }

    showLoading(false);
    renderGrid(allNFTs);
    document.getElementById("nftCount").textContent = allNFTs.length;
  } catch (err) {
    showLoading(false);
    showStatus(
      "ດືງຂໍ້ມູນຈາກ LAB Chain ບໍ່ສຳເລັດ\n" + (err.message || ""),
      true
    );
  }
}
// @ເບິ່ງຂໍ້ມູນ NFT ຂອງ Address ໃນ Contract ໃນການຫາຂໍ້ມູນຈາກ input
async function fetchFromOpenSea(address, contractAddress, osChain) {
  if (!osChain) {
    showLoading(false);
    showStatus(
      'Network ບໍ່ຮອງຮັບ\nກະລຸນາເລືອກ Network  Contract Address  "ດືງ NFT" ຂອງ OpenSea',
      true
    );
    return;
  }
  try {
    let url =
      "https://testnets-api.opensea.io/v2/chain/" +
      osChain +
      "/account/" +
      address +
      "/nfts?limit=50";
    if (contractAddress)
      url =
        "https://testnets-api.opensea.io/v2/chain/" +
        osChain +
        "/contract/" +
        contractAddress +
        "/nfts?limit=50";

    const res = await fetch(url, { headers: { Accept: "application/json" } });
    const data = await res.json();
    const items = data.nfts || data.results || [];

    if (!items.length) {
      showLoading(false);
      showStatus("ບໍ່ພົບ NFT");
      document.getElementById("nftCount").textContent = 0;
      return;
    }

    allNFTs = items.map((nft) => ({
      tokenId: nft.identifier || nft.token_id,
      name: nft.name || "Token #" + nft.identifier,
      description: nft.description || "",
      image: nft.image_url || nft.display_image_url || null,
      collection: nft.collection || shortenAddr(nft.contract),
      contractAddress: nft.contract,
      attributes: nft.traits || [],
    }));

    showLoading(false);
    renderGrid(allNFTs);
    document.getElementById("nftCount").textContent = allNFTs.length;
  } catch (err) {
    showLoading(false);
    showStatus("ດືງ NFT ບໍ່ສຳເລັດ: " + (err.message || ""), true);
  }
}

// @select NFT ແລະ ສະແດງໃນ Modal
function renderGrid(nfts) {
  const grid = document.getElementById("nftGrid");
  const controls = document.getElementById("nftControls");
  grid.style.display = "grid";
  controls.style.display = "flex";
  grid.innerHTML = "";

  nfts.forEach(function (nft, i) {
    const card = document.createElement("div");
    card.className = "nft-card";
    card.style.animationDelay = i * 0.05 + "s";
    card.onclick = function () {
      openModal(nft);
    };

    const imgHtml = nft.image
      ? '<img src="' +
        nft.image +
        '" alt="' +
        nft.name +
        "\" onerror=\"this.parentElement.innerHTML='<div class=\\'nft-no-image\\'><div class=\\'icon\\'>🖼️</div>No Image</div>'\">"
      : '<div class="nft-no-image"><div class="icon">🖼️</div>No Image</div>';

    card.innerHTML =
      '<div class="nft-image-wrap">' +
      imgHtml +
      '<div class="token-id-overlay">#' +
      nft.tokenId +
      "</div>" +
      "</div>" +
      '<div class="nft-info">' +
      '<div class="nft-name">' +
      nft.name +
      "</div>" +
      '<div class="nft-collection">' +
      nft.collection +
      "</div>" +
      (nft.description
        ? '<div class="nft-desc">' + nft.description + "</div>"
        : "") +
      "</div>";

    grid.appendChild(card);
  });
}

// @open NFT ໃນ Modal
function openModal(nft) {
  document.getElementById("modalName").textContent = nft.name;
  document.getElementById("modalCollection").textContent = nft.collection;
  document.getElementById("modalDesc").textContent =
    nft.description || "ບໍ່ມີຄຳອະທິບາຍ";

  const imgEl = document.getElementById("modalImg");
  if (nft.image) {
    imgEl.src = nft.image;
    imgEl.style.display = "block";
  } else imgEl.style.display = "none";

  const attrsEl = document.getElementById("modalAttrs");
  attrsEl.innerHTML = "";
  (nft.attributes || []).forEach(function (attr) {
    const tag = document.createElement("div");
    tag.className = "attr-tag";
    tag.innerHTML =
      "<span>" + (attr.trait_type || attr.type || "") + "</span>" + attr.value;
    attrsEl.appendChild(tag);
  });

  // @show Contract Address
  const ethEl = document.getElementById("modalEtherscan");
  if (ethEl) {
    const ethAddr = nft.contractAddress || LAB_CONTRACT_DEFAULT;
    ethEl.textContent = `${ethAddr.slice(0, 10)}...${ethAddr.slice(-6)}`;
    ethEl.title = ethAddr;
    ethEl.href = `https://sepolia.etherscan.io/address/${ethAddr}`;
    ethEl.target = "_blank";
    ethEl.rel = "noopener noreferrer";
  }

  // @show Verification
  const verifiedEl = document.getElementById("modalVerified");
  if (verifiedEl) {
    const netInfo = getNetworkInfo(currentChainId);
    verifiedEl.textContent = `✔ contract Network: ${netInfo.name}`;
    verifiedEl.style.color = "#22c55e";
  }

  document.getElementById("modal").classList.add("open");
}

function closeModal(e) {
  if (e.target.id === "modal")
    document.getElementById("modal").classList.remove("open");
}

window.addEventListener("load", function () {
  if (getAlchemyKey()) setAlchemyBtn(true);
});
