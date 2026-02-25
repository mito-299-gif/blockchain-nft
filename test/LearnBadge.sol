// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;



import "@openzeppelin/contracts/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts/access/Ownable.sol";

contract LearnBadge is ERC721, ERC721URIStorage, Ownable {


    uint256 private _tokenIdCounter;

    // @ຊື່ ອອກໃບຢັ້ງຢືນ
    // @ຄຳອະທິບາຍ ກິດຈະການ ທີ່ໄດ້ຮັບ
    // @wallet ຂອງນັກສຶກສາ
    // @// timestamp ທີ່ອອກ

    struct Badge {
        string  badgeName;    
        string  activity;     
        address issuedTo;     
        uint256 issuedAt;    
    }

    mapping(uint256 => Badge) public badges;

    // @ເກັບ tokenId ທີ່ອອກໃບຢັ້ງຢືນໃຫ້ນັກສຶກສາ
    mapping(address => uint256[]) public studentBadges;



    event BadgeMinted(
        uint256 indexed tokenId,
        address indexed student,
        string  badgeName,
        string  activity
    );

    // @Name: LearnBadge
    constructor() ERC721("LearnBadge", "LBG") Ownable(msg.sender) {}


    function mintBadge(
        address student,
        string memory badgeName,
        string memory activity,
        string memory tokenURI_
    ) 
    public onlyOwner returns (uint256) {
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

    // @Mint ໃບຢັ້ງຢືນ ໃຫ້ນັກສຶກສາ ທີ່ຢູ່ໃນ list ທີ່ສົ່ງເຂົ້າມາ
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

    // @View: ໃບຢັ້ງຢືນທີ່ນັກສຶກສາທີ່ອອກໃບຢັ້ງຢືນແລ້ວ
    function getBadgesOf(address student)
        public view returns (uint256[] memory)
    {
        return studentBadges[student];
    }

    // @View: ຈຳນວນ ໃບຢັ້ງຢືນ ທັງໝົດທີ່ອອກໄປ (TokenId)
    function totalMinted() public view returns (uint256) {
        return _tokenIdCounter;
    }

    // @ກຳນົດໃຫ້ໂອນບໍ່ໄດ້ ເມື່ອ Mint ໃບຢັ້ງຢືນແລ້ວ (ລົບໄດ້ບໍ່ບັ້ງຄັບ)
    // @ການ mint (from == address(0)) Only (ແຖວ 103)
    function _update(
        address to,
        uint256 tokenId,
        address auth
    ) 
    internal override returns (address) {
        address from = _ownerOf(tokenId);
        require(from == address(0), "LearnBadge: Soulbound - cannot transfer");
        return super._update(to, tokenId, auth);
    }

    // @ໃຊ້ເພື່ອດຶງລິ້ງ Metadata ຂອງ NFT ແຕ່ລະ token
    function tokenURI(uint256 tokenId)
        public view override(ERC721, ERC721URIStorage)
        returns (string memory)
    {
        return super.tokenURI(tokenId);
    }

    // @ໃຊ້ກວດວ່າ Contract ນີ້ຮອງຮັບມາດຕະຖານຫຍັງແດ່
    function supportsInterface(bytes4 interfaceId)
        public view override(ERC721, ERC721URIStorage)
        returns (bool)
    {
        return super.supportsInterface(interfaceId);
    }
}
