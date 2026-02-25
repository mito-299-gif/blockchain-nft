const LAB_CHAIN_ID = 5222;
const LAB_CONTRACT_DEFAULT = "0xE76CAF6C344230f319D70f97F712a424E6b61B72";

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

function toChainNum(chainId) {
  if (chainId === null || chainId === undefined) return 0;
  if (typeof chainId === "number") return chainId;
  return parseInt(chainId, 16);
}

function openAlchemyModal() {
  const saved = sessionStorage.getItem("alchemyKey");
  document.getElementById("alchemyInput").value = saved || "";
  const st = document.getElementById("alchemyStatus");
  st.textContent = saved ? "Save" : "";
  st.style.color = "#22c55e";
  document.getElementById("alchemyModal").classList.add("open");
}

function closeAlchemyModal() {
  document.getElementById("alchemyModal").classList.remove("open");
}

function closeBgAlchemy(e) {
  if (e.target.id === "alchemyModal") closeAlchemyModal();
}

function saveAlchemyKey() {
  const key = document.getElementById("alchemyInput").value.trim();
  const st = document.getElementById("alchemyStatus");
  if (!key) {
    st.textContent = "Please enter API Key";
    st.style.color = "#ef4444";
    return;
  }
  sessionStorage.setItem("alchemyKey", key);
  st.textContent = "Saved!";
  st.style.color = "#22c55e";
  setAlchemyBtn(true);
  setTimeout(closeAlchemyModal, 700);
}

function clearAlchemyKey() {
  sessionStorage.removeItem("alchemyKey");
  document.getElementById("alchemyInput").value = "";
  const st = document.getElementById("alchemyStatus");
  st.textContent = "Cleared";
  st.style.color = "#888";
  setAlchemyBtn(false);
}

function setAlchemyBtn(connected) {
  const btn = document.getElementById("alchemyBtn");
  btn.textContent = connected ? "Alchemy Connected" : "Connect Alchemy";
  connected
    ? btn.classList.add("connected")
    : btn.classList.remove("connected");
}

function getAlchemyKey() {
  return sessionStorage.getItem("alchemyKey");
}

function getNetworkInfo(chainId) {
  const num = toChainNum(chainId);
  return NETWORK_MAP[num] || { name: "Chain " + num, alchemy: null, os: null };
}

function getAlchemyBase(chainId) {
  const key = getAlchemyKey();
  const info = getNetworkInfo(chainId);
  if (!key || !info.alchemy) return null;
  return "https://" + info.alchemy + ".g.alchemy.com/nft/v3/" + key;
}

function updateNetworkUI(chainId) {
  const info = getNetworkInfo(chainId);
  document.getElementById("networkName").textContent = info.name;
  document.getElementById("statNetwork").textContent = info.name;
}

function shortenAddr(addr) {
  if (!addr) return "Unknown";
  return addr.slice(0, 6) + "..." + addr.slice(-4);
}

function ipfsToHttp(url) {
  if (!url) return null;
  if (url.startsWith("ipfs://")) return "https://ipfs.io/ipfs/" + url.slice(7);
  return url;
}

function getImageUrl(imageObj) {
  if (!imageObj) return null;
  const url =
    imageObj.cachedUrl ||
    imageObj.pngUrl ||
    imageObj.originalUrl ||
    imageObj.thumbnailUrl;
  return ipfsToHttp(url);
}

function showLoading(show) {
  document.getElementById("loading").style.display = show ? "block" : "none";
}

function showStatus(msg, isErr = false) {
  const el = document.getElementById("status");
  el.textContent = msg;
  el.style.display = msg ? "block" : "none";
  el.className = isErr ? "error" : "";
  el.style.whiteSpace = "pre-line";
}

function clearGrid() {
  const grid = document.getElementById("nftGrid");
  grid.innerHTML = "";
  grid.style.display = "none";
  document.getElementById("nftControls").style.display = "none";
}

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

function onWalletConnected() {
  document.getElementById("heroSection").style.display = "none";
  document.getElementById("walletInfo").style.display = "block";
  document.getElementById("contractForm").style.display = "block";
  document.getElementById("walletAddress").textContent = currentAccount;
  updateNetworkUI(currentChainId);
  fetchNFTs(currentAccount, null, currentChainId);
}

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

  document.getElementById("modal").classList.add("open");
}

function closeModal(e) {
  if (e.target.id === "modal")
    document.getElementById("modal").classList.remove("open");
}

window.addEventListener("load", function () {
  if (getAlchemyKey()) setAlchemyBtn(true);
});
