// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

// ============================================================
//  LearnBadge — NFT Certificate for Students
//  ERC-721 พื้นฐาน สำหรับออก Badge ให้นักเรียน
//  Deploy บน LabChain (EVM Compatible)
// ============================================================

import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/Ownable.sol";

contract LearnBadge is ERC721, ERC721URIStorage, Ownable {

    // ── State Variables ──────────────────────────────────────
    uint256 private _tokenIdCounter;

    // เก็บข้อมูล Badge แต่ละใบ
    struct Badge {
        string  badgeName;    // ชื่อ Badge เช่น "Blockchain Beginner"
        string  activity;     // กิจกรรมที่ทำ เช่น "ผ่านการทดสอบ Blockchain 101"
        address issuedTo;     // wallet ของนักเรียน
        uint256 issuedAt;     // timestamp ที่ออก
    }

    mapping(uint256 => Badge) public badges;

    // เก็บ badge ทั้งหมดของนักเรียนคนนึง
    mapping(address => uint256[]) public studentBadges;

    // ── Events ───────────────────────────────────────────────
    event BadgeMinted(
        uint256 indexed tokenId,
        address indexed student,
        string  badgeName,
        string  activity
    );

    // ── Constructor ──────────────────────────────────────────
    constructor() ERC721("LearnBadge", "LBG") Ownable(msg.sender) {}

    // ── Mint Badge (เฉพาะ Owner/Admin เท่านั้น) ───────────────
    // @param student   - wallet address ของนักเรียน
    // @param badgeName - ชื่อ badge เช่น "Web3 Starter"
    // @param activity  - คำอธิบายกิจกรรม
    // @param tokenURI_ - IPFS link ของรูป badge (metadata JSON)
    function mintBadge(
        address student,
        string memory badgeName,
        string memory activity,
        string memory tokenURI_
    ) public onlyOwner returns (uint256) {
        uint256 tokenId = _tokenIdCounter;
        _tokenIdCounter++;

        _safeMint(student, tokenId);
        _setTokenURI(tokenId, tokenURI_);

        badges[tokenId] = Badge({
            badgeName: badgeName,
            activity:  activity,
            issuedTo:  student,
            issuedAt:  block.timestamp
        });

        studentBadges[student].push(tokenId);

        emit BadgeMinted(tokenId, student, badgeName, activity);

        return tokenId;
    }

    // ── Batch Mint (ออก Badge หลายคนพร้อมกัน) ────────────────
    function batchMintBadge(
        address[] memory students,
        string memory badgeName,
        string memory activity,
        string memory tokenURI_
    ) public onlyOwner {
        for (uint256 i = 0; i < students.length; i++) {
            mintBadge(students[i], badgeName, activity, tokenURI_);
        }
    }

    // ── View: ดู Badge ทั้งหมดของนักเรียน ─────────────────────
    function getBadgesOf(address student)
        public view returns (uint256[] memory)
    {
        return studentBadges[student];
    }

    // ── View: จำนวน Badge ทั้งหมดที่ออกไป ─────────────────────
    function totalMinted() public view returns (uint256) {
        return _tokenIdCounter;
    }

    // ── SoulBound: ห้ามโอน Badge (ติดกับ wallet นักเรียน) ────
    // ถ้าอยากให้โอนได้ → ลบ function นี้ออก
    function _update(
        address to,
        uint256 tokenId,
        address auth
    ) internal override returns (address) {
        address from = _ownerOf(tokenId);
        // อนุญาตเฉพาะการ mint (from == address(0)) เท่านั้น
        require(from == address(0), "LearnBadge: Soulbound - cannot transfer");
        return super._update(to, tokenId, auth);
    }

    // ── Required Overrides ────────────────────────────────────
    function tokenURI(uint256 tokenId)
        public view override(ERC721, ERC721URIStorage)
        returns (string memory)
    {
        return super.tokenURI(tokenId);
    }

    function supportsInterface(bytes4 interfaceId)
        public view override(ERC721, ERC721URIStorage)
        returns (bool)
    {
        return super.supportsInterface(interfaceId);
    }
}
