const PINATA_GATEWAY =
  "https://sapphire-hilarious-slug-96.mypinata.cloud/ipfs/";

const ABI = [
  "function getBadgesOf(address student) view returns (uint256[])",
  "function badges(uint256 tokenId) view returns (string badgeName, string activity, address issuedTo, uint256 issuedAt)",
  "function totalMinted() view returns (uint256)",
  "function owner() view returns (address)",
  "function mintBadge(address student, string badgeName, string activity, string tokenURI_) returns (uint256)",
  "event BadgeMinted(uint256 indexed tokenId, address indexed student, string badgeName, string activity)",
];

let account = null,
  provider = null,
  signer = null,
  contract = null;
let myBadges = [];
let lastUploadedUri = "";

function openPinataModal() {
  const saved = sessionStorage.getItem("jwt");
  document.getElementById("jwtInput").value = saved || "";
  document.getElementById("pinataStatus").textContent = saved
    ? "✅ Pinata Connected!"
    : "";
  document.getElementById("pinataStatus").style.color = "#22c55e";
  document.getElementById("pinataModal").classList.add("open");
}

function closePinataModal() {
  document.getElementById("pinataModal").classList.remove("open");
}

function closePinataModalOutside(e) {
  if (e.target.id === "pinataModal") closePinataModal();
}

function saveJWT() {
  const jwt = document.getElementById("jwtInput").value.trim();
  const statusEl = document.getElementById("pinataStatus");
  if (!jwt) {
    statusEl.textContent = "⚠ ຍັງບໍ່ມີ API JWT";
    statusEl.style.color = "#ef4444";
    return;
  }
  sessionStorage.setItem("jwt", jwt);
  statusEl.textContent = "✅ Pinata Connected!";
  statusEl.style.color = "#22c55e";
  document.getElementById("pinataBtn").textContent = "✅ Pinata Connected";
  document.getElementById("pinataBtn").classList.add("connected");
  toast("🔑 success", "success");
  setTimeout(closePinataModal, 800);
}

function clearJWT() {
  sessionStorage.removeItem("jwt");
  document.getElementById("jwtInput").value = "";
  document.getElementById("pinataStatus").textContent = "Clear";
  document.getElementById("pinataStatus").style.color = "#888";
  document.getElementById("pinataBtn").textContent = "🔑 Connect Pinata";
  document.getElementById("pinataBtn").classList.remove("connected");
}

async function uploadAll() {
  const JWT = sessionStorage.getItem("jwt");
  if (!JWT) {
    toast("Connect Pinata API", "error");
    openPinataModal();
    return;
  }

  const file = document.getElementById("file").files[0];
  if (!file) {
    toast("you must select a file", "error");
    return;
  }

  const progressEl = document.getElementById("uploadProgress");
  const resultEl = document.getElementById("uploadResult");
  const copyBtn = document.getElementById("copyUriBtn");

  progressEl.style.display = "block";
  resultEl.style.display = "none";
  copyBtn.style.display = "none";
  lastUploadedUri = "";

  try {
    progressEl.textContent = "(1/2) uploading image...";
    const formData = new FormData();
    formData.append("file", file);

    const imageRes = await fetch(
      "https://api.pinata.cloud/pinning/pinFileToIPFS",
      {
        method: "POST",
        headers: { Authorization: `Bearer ${JWT}` },
        body: formData,
      }
    );
    const imageData = await imageRes.json();
    const imageCID = imageData.IpfsHash;

    if (!imageCID)
      throw new Error("Upload image failed: " + JSON.stringify(imageData));

    progressEl.textContent = "(2/2) creating Metadata JSON...";

    const badgeName = document.getElementById("mintName").value.trim();
    const activity = document.getElementById("mintActivity").value.trim();

    const metadata = {
      name: badgeName || "",
      description: activity || "",
      image: `${PINATA_GATEWAY}${imageCID}`,
    };

    const jsonRes = await fetch(
      "https://api.pinata.cloud/pinning/pinJSONToIPFS",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${JWT}`,
        },
        body: JSON.stringify({
          pinataMetadata: { name: "nf.json" },
          pinataContent: metadata,
        }),
      }
    );
    const jsonData = await jsonRes.json();
    const jsonCID = jsonData.IpfsHash;
    if (!jsonCID) throw new Error("Upload JSON failed");

    const jsonURL = `${PINATA_GATEWAY}${jsonCID}`;
    lastUploadedUri = jsonURL;

    progressEl.style.display = "none";
    resultEl.style.display = "block";
    resultEl.textContent =
      `Upload success!\n` +
      `Image CID : ${imageCID}\n` +
      `JSON CID  : ${jsonCID}\n\n` +
      `JSON URL  :\n${jsonURL}`;

    document.getElementById("mintURI").value = jsonURL;

    copyBtn.style.display = "inline-block";
    toast(" Upload success URL filled automatically", "success");
  } catch (err) {
    progressEl.style.display = "none";
    resultEl.style.display = "block";
    resultEl.textContent = " " + (err.message || "Upload failed");
    resultEl.style.color = "var(--danger, #ef4444)";
    toast(" Upload failed", "error");
  }
}

function copyUri() {
  if (!lastUploadedUri) return;
  navigator.clipboard.writeText(lastUploadedUri);
  toast(" URL copied", "success");
}

async function connectWallet() {
  if (!window.ethereum) {
    toast("install MetaMask", "error");
    return;
  }
  try {
    provider = new ethers.BrowserProvider(window.ethereum);
    await provider.send("eth_requestAccounts", []);
    signer = await provider.getSigner();
    account = await signer.getAddress();
    contract = new ethers.Contract(CONTRACT_ADDRESS, ABI, provider);

    document.getElementById("walletBtn").textContent = shortenAddr(account);
    document.getElementById("walletBtn").classList.add("connected");
    document.getElementById("heroSection").style.display = "none";
    document.getElementById("appSection").style.display = "block";

    if (sessionStorage.getItem("jwt")) {
      document.getElementById("pinataBtn").textContent = "✅ Pinata Connected";
      document.getElementById("pinataBtn").classList.add("connected");
    }

    await loadBadges();
    await checkOwner();

    window.ethereum.on("accountsChanged", async (accs) => {
      account = accs[0];
      signer = await provider.getSigner();
      contract = new ethers.Contract(CONTRACT_ADDRESS, ABI, provider);
      loadBadges();
      checkOwner();
    });
  } catch (e) {
    toast(e.message || "Connection failed", "error");
  }
}

async function loadBadges() {
  showStatus("loading");
  try {
    const tokenIds = await contract.getBadgesOf(account);
    document.getElementById("myBadgeCount").textContent = tokenIds.length;
    try {
      const total = await contract.totalMinted();
      document.getElementById("totalMinted").textContent = total.toString();
    } catch (e) {}

    if (tokenIds.length === 0) {
      showStatus("empty");
      return;
    }

    myBadges = [];
    for (const tid of tokenIds) {
      try {
        const b = await contract.badges(tid);
        myBadges.push({
          tokenId: Number(tid),
          badgeName: b.badgeName,
          activity: b.activity,
          issuedAt: Number(b.issuedAt),
        });
      } catch (e) {
        myBadges.push({
          tokenId: Number(tid),
          badgeName: `Badge #${tid}`,
          activity: "",
          issuedAt: 0,
        });
      }
    }
    showStatus("");
    renderBadges(myBadges);
  } catch (e) {
    showStatus(
      "error",
      "ບໍ່ສາມາດດືງ NFT ໄດ້\n" + (e.reason || e.message || "")
    );
  }
}

async function checkOwner() {
  try {
    const ownerAddr = await contract.owner();
    if (ownerAddr.toLowerCase() === account.toLowerCase())
      document.getElementById("mintTab").style.display = "block";
  } catch (e) {}
}

function renderBadges(badges) {
  const grid = document.getElementById("badgeGrid");
  grid.innerHTML = "";
  badges.forEach((b, i) => {
    const card = document.createElement("div");
    card.className = "badge-card";
    card.style.animationDelay = `${i * 0.07}s`;
    card.onclick = () => openModal(b);
    const emoji = getBadgeEmoji(b.badgeName);
    const dateStr = b.issuedAt
      ? new Date(b.issuedAt * 1000).toLocaleDateString("th-TH")
      : "—";
    card.innerHTML = `
            <div class="badge-img-wrap">
              <div class="placeholder">${emoji}</div>
              <div class="token-chip">#${b.tokenId}</div>
            </div>
            <div class="badge-body">
              <div class="badge-name">${b.badgeName}</div>
              <div class="badge-activity">${b.activity}</div>
              <div class="badge-date">${dateStr}</div>
            </div>`;
    grid.appendChild(card);
  });
}

async function mintBadge() {
  const to = document.getElementById("mintTo").value.trim();
  const name = document.getElementById("mintName").value.trim();
  const activity = document.getElementById("mintActivity").value.trim();
  const uri =
    document.getElementById("mintURI").value.trim() || "ipfs://placeholder";

  if (!to || !name || !activity) {
    toast(" ກອບຂໍ້ມູນບໍ່ຄົບ", "error");
    return;
  }
  if (!to.startsWith("0x")) {
    toast(" Wallet address ບໍ່ຖືກຕ້ອງ", "error");
    return;
  }

  const btn = document.getElementById("mintBtn");
  btn.disabled = true;
  btn.textContent = " ກຳລັງ Mint...";
  setMintStatus("loading");

  try {
    const c = new ethers.Contract(CONTRACT_ADDRESS, ABI, signer);
    const tx = await c.mintBadge(to, name, activity, uri);
    setMintStatus("loading", `  confirm... Tx: ${tx.hash}`);
    await tx.wait();
    setMintStatus("success", ` Mint success!\nTx: ${tx.hash}`);
    toast(" Mint ໃບຍັນຍືນ success!", "success");
    document.getElementById("mintTo").value = "";
    document.getElementById("mintName").value = "";
    document.getElementById("mintActivity").value = "";
    document.getElementById("mintURI").value = "";
    document.getElementById("uploadResult").style.display = "none";
    document.getElementById("copyUriBtn").style.display = "none";
    setTimeout(() => {
      switchTab("my");
      loadBadges();
    }, 2000);
  } catch (e) {
    setMintStatus(
      "error",
      " " + (e.reason || e.message || "Transaction failed")
    );
    toast(" Mint unsuccessful", "error");
  } finally {
    btn.disabled = false;
    btn.textContent = "Mint";
  }
}

function openModal(b) {
  document.getElementById("modalName").textContent = b.badgeName;
  document.getElementById("modalActivity").textContent = b.activity;
  document.getElementById("modalImgWrap").textContent = getBadgeEmoji(
    b.badgeName
  );
  document.getElementById("modal").classList.add("open");
  const date = b.issuedAt
    ? new Date(b.issuedAt * 1000).toLocaleString("th-TH")
    : "—";
  document.getElementById("modalMeta").innerHTML =
    `Token ID: <span>#${b.tokenId}</span><br>` +
    `ອອກວັນທີ່: <span>${date}</span><br>` +
    `Network: <span>Sepolia Testnet</span>`;
}
function closeModal(e) {
  if (e.target.id === "modal")
    document.getElementById("modal").classList.remove("open");
}

function switchTab(tab) {
  document.getElementById("tabMy").style.display =
    tab === "my" ? "block" : "none";
  document.getElementById("tabMint").style.display =
    tab === "mint" ? "block" : "none";
  document.querySelectorAll(".tab").forEach((t, i) => {
    t.classList.toggle(
      "active",
      (i === 0 && tab === "my") || (i === 1 && tab === "mint")
    );
  });
}

function fillPreset(name, activity) {
  document.getElementById("mintName").value = name;
  document.getElementById("mintActivity").value = activity;
}

function getBadgeEmoji(name = "") {
  const n = name.toLowerCase();
  if (n.includes("winner") || n.includes("hackathon")) return "🏆";
  if (n.includes("smart") || n.includes("contract")) return "⚡";
  if (n.includes("web3") || n.includes("explorer")) return "🌐";
  if (n.includes("blockchain")) return "🔗";
  return "🎓";
}

function shortenAddr(addr) {
  return addr.slice(0, 6) + "..." + addr.slice(-4);
}

function showStatus(type, msg = "") {
  const el = document.getElementById("status");
  if (type === "loading") {
    el.style.display = "block";
    el.className = "";
    el.innerHTML = '<div class="spinner"></div><p>loading...</p>';
  } else if (type === "empty") {
    el.style.display = "block";
    el.className = "";
    el.innerHTML =
      '<div class="empty"><div class="icon">🎓</div><p>ຍັງບໍ່ມີ ໃບຍັນຍືນ</p></div>';
  } else if (type === "error") {
    el.style.display = "block";
    el.className = "error";
    el.textContent = msg;
  } else {
    el.style.display = "none";
    el.textContent = "";
  }
}

function setMintStatus(type, msg = "") {
  const el = document.getElementById("mintStatus");
  el.style.display = type ? "block" : "none";
  if (type === "loading") {
    el.innerHTML =
      '<div class="spinner" style="width:28px;height:28px;border-width:2px"></div>' +
      (msg || "กำลัง Mint...");
    el.style.color = "var(--muted)";
  } else if (type === "success") {
    el.textContent = msg;
    el.style.color = "var(--accent2)";
  } else if (type === "error") {
    el.textContent = msg;
    el.style.color = "var(--danger)";
  }
}

function toast(msg, type = "") {
  const el = document.getElementById("toast");
  el.textContent = msg;
  el.className = "toast " + type + " show";
  setTimeout(() => el.classList.remove("show"), 3500);
}
