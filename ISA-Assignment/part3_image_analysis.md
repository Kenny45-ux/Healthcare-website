# Part 3 – Image Analysis (laptop.E01)

## 1. Decrypt the archive

Export `C:\Users\Charles Kingston\Pictures\vacation.zip` from Autopsy
(right-click → *Extract File(s)*) into `AliceMalambo_2406344266/export`, then:

```bash
7z l -slt vacation.zip            # shows: Method = AES-256 Deflate, Encrypted = +
7z x -p'QGw8rK^GM^y$%7977Sf66#85$*bcE' vacation.zip -ovacation
sha256sum vacation/*
```

(On Windows: 7-Zip → *Extract* → enter the password. The built-in Windows
ZIP tool cannot open AES-encrypted archives.)

The password is the **"archive"** entry from the `pass.txt` file recovered in
step 2.6: `QGw8rK^GM^y$%7977Sf66#85$*bcE`. The archive decrypts successfully:

| File | Size | Modified (ZIP) | SHA-256 |
|---|---|---|---|
| vacation.jpg | 13,089,579 B | 2022-02-19 16:02:13 | fe00132122fd6823b6626c51b07756d099d01698e6cae5c8d6819af1fdb4c367 |
| knossos.png | 4,082,607 B | 2022-03-29 07:38:36 | 2debd21296f8d53cc9c9b6e14980db55f45b08b69f6acbe180486c3d22eed451 |

## 2. Date of vacation.jpg

```bash
exiftool vacation.jpg
```

(Or Autopsy → *Data Artifacts → EXIF Metadata* when you add the extracted
files as a *Logical Files* data source. On Windows you can also use
*Properties → Details*.)

```
Make                 : DJI
Camera Model Name    : FC3411
Date/Time Original   : 2022:02:19 17:16:53
Create Date          : 2022:02:19 17:16:53
GPS Latitude         : 35.330057 N
GPS Longitude        : 24.998574 E
GPS Altitude         : 498.9 m Above Sea Level
Image Size           : 5472x3648
```

**The photo was taken on 19 February 2022 at 17:16:53** (camera local time).
This is more than a month **before** the suspect's laptop activity
(28–29 March 2022), so he had been in Crete, or had received the photo,
well before his "tourist" trip was supposedly taking place.

The camera is a **DJI FC3411**, the camera module of the **DJI Air 2S
drone**. The picture is an **aerial drone photograph**, not a holiday
snapshot.

## 3. Location of vacation.jpg

**GPS: 35.330057 N, 24.998574 E, altitude ≈ 499 m.**

Google Maps link to open and screenshot:
**https://www.google.com/maps?q=35.330057,24.998574**
(type `35.330057, 24.998574` in the Google Maps search box, switch to
*Satellite* view, and take the screenshot with your browser window visible).

Interpretation:
- The point is in the **hills about 13 km west of Heraklion, Crete**, in
  the mountainous area west of the city.
- The photo shows, from about 500 m altitude:
  - a **newly paved road**, with fresh cut-and-fill and a drainage culvert;
  - two dirt tracks;
  - a small isolated building;
  - barren limestone hills.
- It contains nothing a tourist would photograph, such as an
  archaeological site, a beach or a town.
- It is a deliberate **aerial survey of a remote road and infrastructure**,
  the kind of reconnaissance image an intelligence officer would collect.
- Crete hosts important NATO and Greek military installations, so
  infrastructure imagery from the island is of interest to foreign services.

*(When you take the screenshot, compare the Google satellite view with the
photo: match the curved paved road and the dirt tracks to confirm the
location. Mention in your report what Google labels the area or nearest
village.)*

## 4. Other pictures

| Image | Findings |
|---|---|
| **knossos.png** (1364×1600, 4.1 MB) | Looks like an ordinary tourist photo of the **North Entrance / Charging Bull fresco at the Palace of Knossos**. Several things make it suspicious: <br>• It is a **PNG**, the only lossless output format of OpenStego, and unusual for a camera photo. <br>• At 4.1 MB it is very large for its resolution. <br>• It has **no EXIF/camera metadata**, so it was re-saved by software. <br>• Its timestamp is 29 Mar 2022, the same day he searched for, downloaded and installed **OpenStego 0.8.4**. <br>• It was hidden inside the **AES-encrypted** archive together with the drone photo. <br>This is very likely the **steganographic carrier** holding a hidden message. |
| Other pictures on the laptop | Only Windows Store tile logos and three small Firefox new-tab thumbnails. Nothing of interest. |

**How to check knossos.png on your workstation** (OpenStego is free):

1. Install OpenStego 0.8.4 (needs Java) from https://www.openstego.com.
2. *Extract Data* → Input Stego File = `knossos.png`, Output Folder =
   `AliceMalambo_2406344266/stego`.
3. Leave the password **empty** first, then try the passwords from `pass.txt`.
4. Take a screenshot of the result and report the extracted file and its
   contents.

I tried the three passwords from `pass.txt` (archive, Facebook and email)
from the command line, and none of them opened a payload. I could not test
an empty password. **Run this step on your own PC.**

For a quick visual check, view the least-significant-bit plane of
`knossos.png` in StegSolve or https://aperisolve.com (for a local copy, use
`zsteg`). Random noise in the LSBs of the top rows is the usual signature of
OpenStego's RandomLSB algorithm.

**Conclusion of Part 3:**
- The "vacation" archive on the laptop is protected with a strong password,
  and that password was hidden on a USB stick in his hotel room. The archive
  contains:
  - a **covert drone reconnaissance photo** of remote infrastructure west of
    Heraklion, with precise GPS;
  - a tourist-looking **Knossos image that was very probably used as a
    steganographic carrier** right after he installed OpenStego.
- Together with the other evidence, this is strongly inconsistent with his
  claim of being a tourist and supports the suspicion of espionage:
  - the hotel searches;
  - his interest in military news (the F-35 article);
  - the use of steganography and encryption;
  - the deliberate deletion of the password file.

### Image previews

![vacation.jpg (DJI FC3411, 19 Feb 2022, 35.330057 N 24.998574 E)](img/vacation_preview.jpg)

![knossos.png (probable OpenStego carrier)](img/knossos_preview.jpg)

## Screenshots

![Figure 17: Part 3 Q1 - Decrypt vacation.zip with password from pass.txt](screenshots/17_p3q1_decrypt.png)

![Figure 18: Part 3 Q2/Q3 - Date and GPS location of vacation.jpg](screenshots/18_p3q2_exif.png)

![Figure 19: Part 3 Q4 - Other pictures: knossos.png metadata](screenshots/19_p3q4_knossos.png)
