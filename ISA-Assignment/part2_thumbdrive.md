# Part 2 – FAT Analysis and File Recovery (thumbdrive.img)

> Student: Alice Malambo. Re-run every command on your own workstation (case folder `~/AliceMalambo_2406344266`) and take a screenshot of each command + output.

## 0. Setup and integrity check

```bash
mkdir ~/AliceMalambo_2406344266 && cd ~/AliceMalambo_2406344266
md5sum thumbdrive.img
sha256sum thumbdrive.img
```

| | Computed | acquisition_details.txt |
|---|---|---|
| MD5 | `63e51c59ad75cb417be3580882c4f959` | `63e51c59ad75cb417be3580882c4f959` ✅ |
| SHA256 | `6dd2408d1f454daad40bec3aabdeb302105bcdcf93b645927e0cf22d865fff4d` | same ✅ |

## 1. fsstat

```bash
fsstat thumbdrive.img
```

Relevant output:

```
File System Type: FAT32
...
File System Layout (in sectors)
Total Range: 0 - 1001471
* Reserved: 0 - 31
** Boot Sector: 0
** FS Info Sector: 1
** Backup Boot Sector: 6
* FAT 0: 32 - 1015
* FAT 1: 1016 - 1999
* Data Area: 2000 - 1001471
** Cluster Area: 2000 - 1001471
*** Root Directory: 2000 - 2007
...
Sector Size: 512
Cluster Size: 4096
Total Cluster Range: 2 - 124935
```

| Question | Answer |
|---|---|
| i. File system type | **FAT32** |
| ii. Sector size | **512 bytes** |
| iii. Cluster size | **4096 bytes** (8 sectors per cluster) |
| iv. FAT 0 | **sectors 32 – 1015** (984 sectors) |
| v. Root directory | **sectors 2000 – 2007** (one cluster, cluster #2) |
| vi. Data area | **sectors 2000 – 1001471** |
| vii. Last sector of the file system | **1001471** |

Note: fsstat also reports `Total Range in Image: 0 - 679935`. The file system
declares 1,001,472 sectors but the image only contains 679,936 (348,127,232
bytes / 512). The image is shorter than the file system it holds. Everything
in use lives at the start of the volume, so no evidence is lost.

## 2. Extract the first sector of the root directory

```bash
dcfldd if=thumbdrive.img of=root_sector.bin bs=512 skip=2000 count=1
xxd root_sector.bin | head -8
```

```
00000000: e570 0061 0073 0073 002e 000f 00ba 7400  .p.a.s.s......t.   <- LFN entry (deleted)
00000010: 7800 7400 0000 ffff ffff 0000 ffff ffff  x.t.............
00000020: e541 5353 2020 2020 5458 5420 0071 5b99  .ASS    TXT .q[.   <- 8.3 entry (deleted)
00000030: 7b54 7b54 0000 108c 7854 0300 5500 0000  {T{T....xT..U...
00000040: 412e 0054 0072 0061 0073 000f 00e4 6800  A..T.r.a.s....h.   <- LFN for .Trash-1000
00000050: 2d00 3100 3000 3000 3000 0000 0000 ffff  -.1.0.0.0.......
00000060: 5452 4153 482d 7e31 2020 2010 0087 6999  TRASH-~1   ...i.   <- directory .Trash-1000
00000070: 7b54 7b54 0000 6999 7b54 0400 0000 0000  {T{T..i.{T......
```

i. Command: `dcfldd if=thumbdrive.img of=root_sector.bin bs=512 skip=2000 count=1`

ii. **Yes, there is a deleted file.** The 8.3 directory entry starts at **byte
offset 0x20 (32 decimal)** of the root directory sector. Its first byte is
`0xE5` (unallocated), followed by `ASS     TXT`, so it was `PASS.TXT`. Ignoring
LFN entries as instructed, it is the first 32-byte entry that is not an LFN
entry (attribute byte 0x0F). Absolute byte offset in the image:
2000 × 512 + 32 = **1,024,032**.

iii. Size: bytes `55 00 00 00` at entry offset 0x1C, read little-endian, give
0x00000055 = **85 bytes**.

iv. Clusters: ⌈85 / 4096⌉ = **1 cluster**. First cluster: high word at 0x14 is
`0x0000` and low word at 0x1A is `0x0003`, so **cluster 3**. Cluster 3 is
sector 2000 + (3 − 2) × 8 = **sector 2008**.

(Note: Table 1 in the assignment has the offsets slightly wrong. In the real
FAT32 layout, attributes are at 0x0B, the high cluster word at 0x14, the low
cluster word at 0x1A and the 4-byte size at 0x1C. The values above use the
real layout and were confirmed with `istat thumbdrive.img 4`.)

## 3. Modify the directory entry

Replace the deletion marker `0xE5` with the original first character `P`
(`0x50`). You can also restore the LFN entry's sequence byte (`0xE5` → `0x41`,
meaning "sequence 1, last entry") so the long name `pass.txt` comes back too.

```bash
cp root_sector.bin root_sector_mod.bin
printf '\x50' | dd of=root_sector_mod.bin bs=1 seek=32 conv=notrunc   # 8.3 entry
printf '\x41' | dd of=root_sector_mod.bin bs=1 seek=0  conv=notrunc   # LFN entry (optional)
xxd root_sector_mod.bin | head -4
```

```
00000000: 4170 0061 0073 0073 002e 000f 00ba 7400  Ap.a.s.s......t.
00000020: 5041 5353 2020 2020 5458 5420 0071 5b99  PASS    TXT .q[.
```

(A hex editor such as `hexedit`, `ghex` or HxD works just as well.)

## 4. Extract FAT 0 and fix the cluster chain

```bash
dcfldd if=thumbdrive.img of=fat0.bin bs=512 skip=32 count=984
xxd fat0.bin | head -3
```

```
00000000: f8ff ff0f ffff ff0f f8ff ff0f ffff ff0f  ................
00000010: ffff ff0f ffff ff0f ffff ff0f ffff ff0f  ................
00000020: 0000 0000 0000 0000 0000 0000 0000 0000  ................
```

i. **The chain is 1 cluster long.** The file is 85 bytes, so the only entry
needed is FAT[3] = end-of-chain (`0x0FFFFFFF`). FAT[3] is at byte offset
3 × 4 = **12 (0x0C)** in the FAT.

ii. Change: write `FF FF FF 0F` at offset 12 of the FAT:

```bash
cp fat0.bin fat0_mod.bin
printf '\xff\xff\xff\x0f' | dd of=fat0_mod.bin bs=1 seek=12 conv=notrunc
cmp fat0.bin fat0_mod.bin
```

**Observation:** FAT[3] already contained `FF FF FF 0F`, so the FAT needed
**no change**. The file had not been destroyed; it had been *moved to the
Linux trash* (`.Trash-1000/files/pass.txt`, deletion date 2022-03-27 22:11:19
according to `.Trash-1000/info/pass.txt.trashinfo`). A move on FAT creates a
new directory entry in `.Trash-1000/files` that points at the **same
cluster 3**, and marks the old root entry as deleted. Cluster 3 is therefore
still allocated. `istat thumbdrive.img 4` and `istat thumbdrive.img 518` both
report sector 2008.

## 5. Build the new image

```bash
dcfldd if=thumbdrive.img of=part1.bin bs=512 count=2000          # sectors 0-1999 (boot, FATs)
dcfldd if=thumbdrive.img of=part3.bin bs=512 skip=2001           # sectors 2001-end
cat part1.bin root_sector_mod.bin part3.bin | dcfldd of=thumbdrive_recovered.img bs=512
cmp -l thumbdrive.img thumbdrive_recovered.img                    # shows only the 2 changed bytes
```

```
1024001 345 101     (0xE5 -> 0x41, LFN entry)
1024033 345 120     (0xE5 -> 0x50, 'P')
```

(If the FAT had needed a change, you would also insert `fat0_mod.bin` at sector
32, and a copy at sector 1016 for FAT 1.)

Alternative, writing in place:
`cp thumbdrive.img thumbdrive_recovered.img && dcfldd if=root_sector_mod.bin of=thumbdrive_recovered.img bs=512 seek=2000 conv=notrunc`.
Warning: some dcfldd builds ignore `conv=notrunc` and truncate the output, so
check the file size afterwards. This is why the concatenation method above is
used.

## 6. Mount and verify

```bash
sudo mkdir -p /mnt/AliceMalambo_2406344266_usb
sudo mount -o loop,ro thumbdrive_recovered.img /mnt/AliceMalambo_2406344266_usb
ls -la /mnt/AliceMalambo_2406344266_usb
cat /mnt/AliceMalambo_2406344266_usb/pass.txt
sha256sum /mnt/AliceMalambo_2406344266_usb/pass.txt
```

(Verified here with `fls -p thumbdrive_recovered.img` and
`mdir -i thumbdrive_recovered.img ::/`, which list `pass.txt` (85 bytes) as
allocated again. On Windows you can open the image in FTK Imager or use
OSFMount.)

Contents of the recovered file:

```
Facebook: 82d%x2Eg%829Wui

email: 6gT@5729

archive: QGw8rK^GM^y$%7977Sf66#85$*bcE
```

ii. **SHA256(pass.txt) = `eb3142c20578ff7f00fcae00ffec9bce5fd507e653556a960a2135650019c018`**

iii. **Significance:** the file holds the suspect's credentials for Facebook,
for an e-mail account, and above all for an **archive**
(`QGw8rK^GM^y$%7977Sf66#85$*bcE`). This is the password needed to decrypt the
encrypted archive on the laptop in Part 3. The suspect "deleted" the file and
hid the USB drive in the hotel room, which shows an attempt to conceal it.
However, he only moved it to the trash, so the data was never overwritten.

## 7. Anti-forensics: secure erasure

i. **Method:**
- **Magnetic HDD:** overwrite the whole device, not individual files. Use a
  single pass of zeros or random data over every addressable sector, for
  example `dd if=/dev/urandom of=/dev/sdX bs=1M status=progress`,
  `shred -n 1 -z /dev/sdX`, or `nwipe`. Also wipe the HPA/DCO areas
  (`hdparm`).
- **SSD / USB flash:** use the device's built-in sanitize command:
  ATA Secure Erase / Sanitize Block Erase (`hdparm --security-erase`),
  NVMe Format/Sanitize (`nvme sanitize`, `nvme format -s1`), or a
  **cryptographic erase** (destroy the key of a drive that was fully
  encrypted from day one with BitLocker/LUKS/VeraCrypt).
- For media that held classified data: physical destruction (shred,
  disintegrate, incinerate) or, for HDDs only, degaussing.
- Verify afterwards. Re-read the device and confirm it holds only the wipe
  pattern (`cmp /dev/sdX /dev/zero`, or a hex dump of random samples).

ii. **Why it works:**
- The recovery in this assignment worked because deleting a file on FAT only
  changes **metadata**. The first byte of the directory entry becomes `0xE5`
  and the FAT entries are zeroed. The **data clusters are never touched**.
  Overwriting every sector removes the data itself, so there is nothing left
  for `fls`, `icat`, or file carving tools (photorec, foremost) to find.
- On modern high-density HDDs one overwrite pass is enough. NIST SP 800-88
  Rev. 1 says a single overwrite pass "is sufficient" for the *Clear* level on
  ATA hard drives. Wright, Kleiman & Sundhar (2008) showed that recovering
  overwritten bits with magnetic force microscopy is essentially no better than
  guessing (~50 %). This disproves the old Gutmann 35-pass myth for modern
  drives.
- Flash memory is different. Wear levelling, over-provisioning and
  bad-block remapping mean that an OS-level overwrite does not reach every
  physical cell. Wei et al. (USENIX FAST 2011) found that single-file
  overwrite on SSDs left 4–75 % of the data recoverable. Whole-device software
  overwrites were usually, but not always, effective. Firmware sanitize
  commands erase all NAND blocks, including hidden ones. Crypto-erase
  makes any remaining ciphertext useless because the key no longer exists
  (NIST SP 800-88 *Purge*; IEEE 2883-2022).
- Physical destruction is the only method that covers firmware bugs. That
  is why NIST requires *Destroy* for the highest-sensitivity media.

References:
1. Kissel R. et al., *NIST SP 800-88 Rev. 1 – Guidelines for Media Sanitization*, NIST, 2014.
2. Wright C., Kleiman D., Sundhar S., "Overwriting Hard Drive Data: The Great Wiping Controversy", ICISS 2008, LNCS 5352.
3. Wei M., Grupp L., Spada F., Swanson S., "Reliably Erasing Data From Flash-Based Solid State Drives", USENIX FAST 2011.
4. IEEE Std 2883-2022, *Standard for Sanitizing Storage*.
5. Carrier B., *File System Forensic Analysis*, Addison-Wesley, 2005 (Ch. 9–10, FAT).
6. Gutmann P., "Secure Deletion of Data from Magnetic and Solid-State Memory", USENIX Security 1996 (and its epilogue noting the 35 passes are unnecessary for modern drives).
