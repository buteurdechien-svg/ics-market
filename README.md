# ICS Market — Cyber Range

> ⚠️ **Application volontairement vulnérable**, à usage de formation au pentest web et API.
> Données 100 % fictives. À installer **uniquement dans une VM isolée**, jamais exposée sur Internet.

ICS Market est une **marketplace fictive** (vente en ligne) conçue pour un
parcours de pentest réaliste : catalogue multi-catégories, panier, codes
promo, commande, avis, comptes, espace vendeur et administration.

Le participant arrive sans connaître les failles et documente ses
découvertes dans un **rapport d'audit**. Pas de score ni de flags affichés :
le livrable est le rapport, corrigé avec la grille réservée au formateur
(fournie séparément, hors de ce dépôt public).

---

## Installation sur une VM Ubuntu 22.04

### 1. Préparer la VM
- Ubuntu Server 22.04 LTS dans VMware Fusion / VirtualBox.
- **Réseau : NAT ou Host-only** (jamais « Bridged »).
- ~4 Go de RAM, 20 Go de disque. Cocher « Install OpenSSH server ».

### 2. Installer
```bash
sudo apt update && sudo apt install -y git
git clone https://github.com/buteurdechien-svg/ics-market.git
cd ics-market
sudo ./scripts/install.sh
```

### 3. Accès
| Service | Adresse (dans la VM) |
|---|---|
| Site ICS Market | http://localhost/ |
| Mailpit (OTP) | http://localhost:8025/ |
| API REST | http://localhost/api/v1/ |

Depuis Kali (réseau host-only), remplacez `localhost` par l'IP de la VM (`hostname -I`).

### 4. Réinitialiser
```bash
sudo ./scripts/reset-lab.sh
```

---

## Technologies
HTML5, CSS3 natif, JavaScript vanilla · PHP 8.3 (sans framework), PDO ·
Apache 2 · MySQL 8 / InnoDB · Mailpit.

## Structure
```
public/        Racine web (index.php = routeur, assets, uploads)
app/
  controllers/ public, auth, shop, account, pro
  views/       gabarits
  lib/         Database, helpers, shop, Mailer
  api.php      API REST /api/v1/
config/        configuration
database/      schema.sql + seed.php
scripts/       install.sh, reset-lab.sh, services SSRF & Mailpit
storage/       uploads privés, factures, journaux (hors public/)
docs/          lettre de mission, modèle de rapport
```

## Avertissement
Vulnérabilités **intentionnelles**. Ne jamais déployer en production ni
exposer sur un réseau public. Usage strictement en laboratoire isolé.
