# Toasted Marshmallow Mode (WordPress Plugin)

**Version:** 1.8  
**Author:** Jason Cox  
**Plugin URI:** [https://jasoncox.cloud/maintenance-coming-soon-plugin/](https://jasoncox.cloud/maintenance-coming-soon-plugin/)  
**License:** GPLv2 or later  

Add a playful **Coming Soon / Maintenance splash screen** to your WordPress site with customizable logo, tagline, and subtext. Choose between **Coming Soon (200 OK)**, **Maintenance (503)**, or disable the mode entirely.

---

## Features
- Toggle between:
  - **Coming Soon Mode (200 OK)** → Site returns normal HTTP status.  
  - **Maintenance Mode (503)** → Sends 503 header + `Retry-After` (SEO-friendly).  
  - **Disabled** → Plugin does nothing.  
- Customizable:
  - Logo image (from Media Library or URL).  
  - Main tagline (H1).  
  - Subtext paragraph (supports simple HTML like `<br>`).  
- Clean, centered splash layout with responsive design.  
- Defaults auto-populate on activation.  
- Admin-only bypass: site administrators still see the site normally.  

---

## Installation
1. Download the release ZIP from GitHub Releases.  
2. In WordPress: **Plugins → Add New → Upload Plugin** and select the ZIP.  
3. Activate the plugin.  
4. Go to **Settings → Toasted Marshmallow** to configure mode and content.  

---

## Usage
- Select **Mode** from the dropdown:  
  - *Coming Soon (200 OK)* → Display splash to visitors, but search engines see 200 OK.  
  - *Maintenance (503)* → Display splash with 503 status for temporary downtime.  
  - *Disabled* → Turn off the splash screen.  
- Upload or paste a **Logo URL**.  
- Set your **Main Tagline (H1)** (e.g., “Site Under Construction”).  
- Add a **Subtext paragraph** with optional HTML (e.g., `We're building something gooey and golden.<br>Please check back soon!`).  
- Save changes.  

---

## Example Output
![Toasted Marshmallow Mode Screenshot](https://jasoncox.cloud/wp-content/uploads/2025/08/cropped-55C968AA-F132-483E-AFC9-B79720A27193.png)  

*Maintenance Mode — 503*  

Site Under Construction
We’re building something gooey and golden.
Please check back soon!

---

## Changelog
### 1.8
- Added color distinction for Coming Soon vs Maintenance backgrounds.  
- Improved default logo and subtext.  

### 1.0
- Initial release with Coming Soon and Maintenance modes.  

---

## License
GPLv2 or later — see [LICENSE](./LICENSE) for details.
