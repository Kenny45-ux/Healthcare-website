#!/usr/bin/env bash
# Runs every CLI step of the assignment one at a time.
# After each step it pauses so you can take a screenshot, then press Enter.
#
# Usage (Linux / WSL), with thumbdrive.img, the laptop.E0* files and
# acquisition_details.txt in the current folder:
#   sudo apt install sleuthkit ewf-tools dcfldd p7zip-full libimage-exiftool-perl xxd
#   bash run_steps.sh
set -u
CASE=AliceMalambo_2406344266

step() {
  echo
  echo "=================================================================="
  echo "[$CASE] $(whoami)@$(hostname)  $(date)"
  echo "STEP: $1"
  echo "=================================================================="
}
pause() { read -rp $'\n>>> Take your screenshot, then press Enter to continue... '; clear; }

mkdir -p "$CASE" && cd "$CASE" || exit 1
for f in ../thumbdrive.img ../laptop.E0* ../acquisition_details.txt; do
  [ -e "$f" ] && ln -sf "$f" .
done

# ---------------- Part 1 Q1: integrity of laptop image ----------------
step "1.1 Laptop image hash verification"
cat acquisition_details.txt
ewfverify -d sha1 laptop.E01
pause

# ---------------- Part 2 ----------------
step "2.0 Thumbdrive hashes"
md5sum thumbdrive.img; sha256sum thumbdrive.img
pause

step "2.1 fsstat"
fsstat thumbdrive.img
pause

step "2.2 Extract first sector of root directory (sector 2000)"
dcfldd if=thumbdrive.img of=root_sector.bin bs=512 skip=2000 count=1
xxd root_sector.bin | head -8
pause

step "2.3 Undelete directory entry (0xE5 -> 'P' at offset 0x20, LFN 0xE5 -> 0x41 at 0x00)"
cp root_sector.bin root_sector_mod.bin
printf '\x50' | dd of=root_sector_mod.bin bs=1 seek=32 conv=notrunc status=none
printf '\x41' | dd of=root_sector_mod.bin bs=1 seek=0  conv=notrunc status=none
xxd root_sector_mod.bin | head -4
pause

step "2.4 Extract FAT 0 (sectors 32-1015), check FAT[3] (offset 12)"
dcfldd if=thumbdrive.img of=fat0.bin bs=512 skip=32 count=984
xxd fat0.bin | head -3
cp fat0.bin fat0_mod.bin
printf '\xff\xff\xff\x0f' | dd of=fat0_mod.bin bs=1 seek=12 conv=notrunc status=none
cmp fat0.bin fat0_mod.bin && echo "FAT[3] already 0x0FFFFFFF (EOF) - chain of 1 cluster intact"
pause

step "2.5 Rebuild new image with modified root directory"
dcfldd if=thumbdrive.img of=part1.bin bs=512 count=2000
dcfldd if=thumbdrive.img of=part3.bin bs=512 skip=2001
cat part1.bin root_sector_mod.bin part3.bin | dcfldd of=thumbdrive_recovered.img bs=512
ls -l thumbdrive.img thumbdrive_recovered.img
cmp -l thumbdrive.img thumbdrive_recovered.img
pause

step "2.6 Mount recovered image, read and hash pass.txt"
sudo mkdir -p /mnt/${CASE}_usb
sudo mount -o loop,ro thumbdrive_recovered.img /mnt/${CASE}_usb
ls -la /mnt/${CASE}_usb
cat /mnt/${CASE}_usb/pass.txt
sha256sum /mnt/${CASE}_usb/pass.txt
pause
sudo umount /mnt/${CASE}_usb

# ---------------- Part 3 ----------------
step "3.0 Extract vacation.zip from the laptop image"
OFF=104448
mmls laptop.E01
INODE=$(ifind -o $OFF -n "Users/Charles Kingston/Pictures/vacation.zip" laptop.E01)
echo "vacation.zip inode: $INODE"
icat -o $OFF laptop.E01 "$INODE" > vacation.zip
ls -l vacation.zip
pause

step "3.1 Decrypt archive with password from pass.txt"
7z l -slt vacation.zip | grep -E 'Path|Size|Encrypted|Method'
7z x -y -p'QGw8rK^GM^y$%7977Sf66#85$*bcE' -ovacation vacation.zip
sha256sum vacation/*
pause

step "3.2/3.3 EXIF date and GPS of vacation.jpg"
exiftool -Make -Model -DateTimeOriginal -GPSLatitude -GPSLongitude -GPSAltitude -c '%.6f' vacation/vacation.jpg
echo "Map: https://www.google.com/maps?q=35.330057,24.998574"
pause

step "3.4 knossos.png metadata"
exiftool vacation/knossos.png
echo "Now open OpenStego GUI -> Extract Data -> knossos.png (try empty password first)."
pause

echo "All CLI steps done. Remaining: Autopsy screenshots (Part 1), Google Maps screenshot, OpenStego screenshot."
