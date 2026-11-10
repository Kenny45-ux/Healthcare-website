---
title: "Information Systems Audit Assignment – Forensic Analysis of Charles Kingston's Laptop and USB Drive"
---

| | |
|---|---|
| **Name** | Alice Malambo |
| **SIN** | 2406344266 |
| **Course** | Information Systems Audit |
| **Programme** | IT in Business Management |
| **Email** | malamboalice78@gmail.com |
| **Institution** | Information and Communications University – School of Engineering |
| **Due date** | 10 November 2026 |

**Workstation:** Linux forensic workstation, case folder and Autopsy case `AliceMalambo_2406344266`.
Tools: Autopsy, The Sleuth Kit (fsstat, fls, istat, icat, ifind, mmls), libewf (ewfinfo, ewfverify), dcfldd, xxd, sqlite3, RegRipper / python-registry, 7-Zip, ExifTool, OpenStego.

## Executive summary

- **Image integrity:** the laptop image (MD5 `e6966b68…f08a`, SHA1
  `22a24339…81b3`) and the USB image (MD5 `63e51c59…f959`) both match the
  acquisition hashes, so neither was altered.
- **The laptop:** a Windows 10 Pro virtual machine. Its single user,
  **Charles Kingston**, is a **local administrator**.
- **29 March 2022 activity:** he searched Google Maps for hotels near
  Knossos. He focused on **Galaxy Hotel Iraklio**, and planned a route from
  there to Knossos. He read about an F-35 military deal. Then he searched
  for, downloaded and installed the steganography tool **OpenStego 0.8.4**.
- **No VPN, Tor, encrypted messengers, remote-access tools or
  VeraCrypt/BitLocker volumes** were found.
- **USB drive:** a "deleted" `pass.txt` was recovered by editing the FAT32
  directory entry. It holds his Facebook, e-mail and **archive** passwords.
- **The archive:** the archive password decrypts the AES-256
  **vacation.zip**. It contains:
  - a **DJI drone photo** taken on **19 Feb 2022** at
    **35.330057 N, 24.998574 E**, in the hills west of Heraklion, showing a
    remote new road;
  - **knossos.png**, a probable OpenStego carrier.
- **Conclusion:** the evidence contradicts the tourist story and is
  consistent with espionage tradecraft.

> **Screenshots:** terminal screenshots of every CLI step are included at the end of each part (Figures 1–19). Autopsy GUI, Google Maps and OpenStego screenshots must be added from a desktop session.


