# Deploying to Namecheap cPanel (push-to-server)

This repo deploys straight to your primary domain's `public_html` using cPanel's built-in
**Git™ Version Control**. You `git push` to a repository on the server, then deploy — and
`.cpanel.yml` copies the site into `public_html`.

```
local repo  ──(git push)──►  cPanel repo on server  ──(.cpanel.yml)──►  public_html  ──►  live site
```

---

## One-time setup

### 1. Enable SSH access + add your key (Namecheap)
cPanel push uses SSH, which needs key authentication.

1. In **cPanel → Security → SSH Access → Manage SSH Keys**.
2. **Generate a new key** (or **Import** an existing public key). If you generate one in cPanel,
   download the **private** key to your machine.
3. Click **Manage → Authorize** on the public key so the server trusts it.
4. (Namecheap shared hosting) If SSH is off, enable it from your Namecheap dashboard
   *Hosting List → Manage → enable SSH Access*. Note the **SSH port** — Namecheap is often
   **21098**, not 22.

Test it:
```bash
ssh -p 21098 YOURCPANELUSER@YOURDOMAIN.com
```

### 2. Create the repository on the server
In **cPanel → Files → Git™ Version Control → Create**:
- **Clone a Repository:** OFF (we're creating an empty one to push into)
- **Repository Path:** `repositories/msr-website`
- **Repository Name:** `msr-website`
- Click **Create**.

After it's created, open **Manage** on that repo and copy the **Clone URL** it shows. It looks like:
```
ssh://YOURCPANELUSER@YOURDOMAIN.com//home/YOURCPANELUSER/repositories/msr-website
```

### 3. Point this local repo at the server and push
From inside this folder:
```bash
# Use the Clone URL from step 2 (note the Namecheap SSH port via the GIT_SSH_COMMAND line)
git remote add cpanel ssh://YOURCPANELUSER@YOURDOMAIN.com//home/YOURCPANELUSER/repositories/msr-website

# If your SSH port is NOT 22 (Namecheap is usually 21098), tell git which port + key to use:
git config core.sshCommand "ssh -p 21098 -i ~/.ssh/your_private_key"

git push cpanel main
```

### 4. Deploy
In **cPanel → Git™ Version Control → Manage** (the msr-website repo) → **Pull or Deploy** tab →
click **Deploy HEAD Commit**.

That runs `.cpanel.yml` and your site goes live at your domain. Done. ✅

---

## Day-to-day: pushing updates
After the one-time setup, every update is:
```bash
git add -A
git commit -m "Update headline / swap image / etc."
git push cpanel main
```
…then click **Deploy HEAD Commit** in cPanel (or set up auto-deploy below).

---

## Optional: auto-deploy on every push (no clicking)
cPanel doesn't auto-run `.cpanel.yml` on push by default. To make a push deploy itself, add a
`post-receive` hook to the **server** repo (do this once, over SSH):

```bash
ssh -p 21098 YOURCPANELUSER@YOURDOMAIN.com
cat > ~/repositories/msr-website/.git/hooks/post-receive <<'EOF'
#!/bin/bash
/usr/local/cpanel/bin/uapi --user=$(whoami) VersionControlDeployment create \
  repository_root=$HOME/repositories/msr-website
EOF
chmod +x ~/repositories/msr-website/.git/hooks/post-receive
```
Now `git push cpanel main` deploys automatically.

---

## Before launch — set your real domain
The site's SEO tags currently point to `https://www.midwestspecialtyrobotics.com/`.
If you launch on a different domain, find-and-replace that string in `index.html`,
`sitemap.xml`, and `robots.txt`, then commit and push.

## After SSL is active
Once Namecheap AutoSSL has issued a certificate (cPanel → Security → SSL/TLS Status),
uncomment the "Force HTTPS" block at the top of `.htaccess`, then commit and push.

---

## Notes & troubleshooting
- **Deploying to a subdomain instead?** Change `$HOME/public_html/` in `.cpanel.yml` to e.g.
  `$HOME/public_html/msr/` (or the addon-domain docroot).
- **`rsync: command not found`** (rare on cPanel): replace the rsync line in `.cpanel.yml` with:
  `- /bin/cp -R index.html assets .htaccess favicon.ico favicon-16x16.png favicon-32x32.png apple-touch-icon.png icon-192.png icon-512.png robots.txt sitemap.xml site.webmanifest $DEPLOYPATH`
- **`$HOME` not expanding:** hardcode the path — `export DEPLOYPATH=/home/YOURCPANELUSER/public_html/`.
- **Permission denied (publickey):** the key from step 1 isn't authorized, or git is using the
  wrong port/key. Re-check `git config core.sshCommand` and that the key is **Authorized** in cPanel.
- This repo's root **is** the website. `README.md`, `DEPLOYMENT.md`, `.cpanel.yml`, and `.git`
  are excluded from what lands in `public_html`.
