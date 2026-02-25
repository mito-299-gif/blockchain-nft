<!DOCTYPE html>
<html>

<head>
    <title>Auto Image + JSON Upload</title>
</head>
<style>
    #copyBtn {
        display: none;
    }
</style>

<body>

    <form style="margin-bottom: 20px;" id="jwtForm">
        <input type="text" id="jwtInput" placeholder="ใส่ JWT ของเธอ" style="width: 300px;">
        <button type="submit">บันทึก JWT</button>
        <button type="reset">ล้าง</button>
    </form>


    <h2>Upload Image → Auto JSON → Upload to Pinata</h2>

    <input type="file" id="fileInput">
    <button onclick="uploadAll()">Upload Full</button>

    <p id="showtimeresult"></p>
    <pre id="result"></pre>

    <button id="copyBtn">Copy</button>

    <script>
        const jwtInput = document.getElementById("jwtInput");

        if (sessionStorage.getItem("jwt")) {
            jwtInput.value = sessionStorage.getItem("jwt");
        }
        document.querySelector("#jwtForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const jwt = jwtInput.value.trim();
            if (jwt) {
                sessionStorage.setItem("jwt", jwt);
                alert("JWT ถูกบันทึกแล้ว");
            } else {
                alert("กรุณาใส่ JWT");
            }
        });
        document.querySelector("#jwtForm").addEventListener("reset", function (e) {
            e.preventDefault();
            sessionStorage.removeItem("jwt");
            jwtInput.value = "";
        });



        const GATEWAY = "https://sapphire-hilarious-slug-96.mypinata.cloud/ipfs/";

        async function uploadAll() {
            const JWT = sessionStorage.getItem("jwt");
            const file = document.getElementById("fileInput").files[0];
            const showtimeresult = document.getElementById("showtimeresult");
            showtimeresult.textContent = "กำลังอัปโหลด...";
            if (!JWT) {
                return alert("กรุณาใส่ JWT ก่อน");
            }

            if (!file) {
                return alert("กรุณาเลือกไฟล์ก่อน");
            }

            // STEP 1: Upload รูป
            const formData = new FormData();
            formData.append("file", file);

            const imageRes = await fetch(
                "https://api.pinata.cloud/pinning/pinFileToIPFS",
                {
                    method: "POST",
                    headers: {
                        Authorization: `Bearer ${JWT}`
                    },
                    body: formData
                }
            );

            const imageData = await imageRes.json();
            const imageCID = imageData.IpfsHash;

            if (!imageCID) {
                console.log(imageData);
                return alert("Upload รูปไม่สำเร็จ");
            }

            // STEP 2: สร้าง JSON อัตโนมัติ (แบบที่เธอต้องการ)
            const metadata = {
                image: `${GATEWAY}${imageCID}`
            };

            // STEP 3: Upload JSON ขึ้น Pinata (จะได้ nf.json ใน dashboard)
            const jsonRes = await fetch(
                "https://api.pinata.cloud/pinning/pinJSONToIPFS",
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Authorization: `Bearer ${JWT}`
                    },
                    body: JSON.stringify({
                        pinataMetadata: {
                            name: "nf.json"
                        },
                        pinataContent: metadata
                    })
                }
            );

            const jsonData = await jsonRes.json();
            const jsonCID = jsonData.IpfsHash;


            document.getElementById("result").textContent =
                `Image CID: ${imageCID}
JSON CID: ${jsonCID}


JSON URL:
${GATEWAY}${jsonCID}`;




            const ipfsURL = `${GATEWAY}${jsonCID}`;

            document.getElementById("showtimeresult").style.display = "none";
            document.getElementById("copyBtn").style.display = "inline-block";
            document.getElementById("copyBtn").addEventListener("click", function () {
                navigator.clipboard.writeText(ipfsURL);
            });
        }

    </script>

</body>

</html>