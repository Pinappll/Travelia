
# 🌍 Travelia

**Travelia** est une plateforme web permettant d'explorer des voyages et des activités organisés par pays. Elle offre une expérience utilisateur dynamique et adaptée selon les rôles des utilisateurs. 🧳✈️

---

## 🚀 Déploiement

### ⚙️ Prérequis
- **PHP** (version >= 8.1)
- **Composer**
- **Symfony CLI**
- **Node.js** (avec npm ou yarn)
- **Base de données MySQL**

### 🛠️ Étapes de déploiement

#### 1️⃣ Cloner le dépôt
```bash
git clone https://github.com/Pinappll/Travelia.git
cd Travelia
```

#### 2️⃣ Installer les dépendances
- Backend :
  ```bash
  composer install
  ```
- Frontend :
  ```bash
  npm install
  npm run build
  ```

#### 3️⃣ Configurer les variables d'environnement
- Copier le fichier `.env` :
  ```bash
  cp .env .env.local
  ```
- Modifier les paramètres dans `.env.local` :
  ```makefile
  DATABASE_URL="mysql://user:password@127.0.0.1:3306/travelia"
  MAILER_DSN="smtp://user:password@smtp.mailtrap.io:2525"
  ```

#### 4️⃣ Créer la base de données et appliquer les migrations
```bash
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

#### 5️⃣ Charger les fixtures 🎉
```bash
symfony console doctrine:fixtures:load
```

#### 6️⃣ Lancer le serveur 🚀
```bash
symfony serve
```
Le site sera accessible sur [http://localhost:8000](http://localhost:8000). 🌐

---

## 🔑 Utilisateurs par défaut (fixtures)

- **Administrateur :**
  - Email : `admin@mail.fr`
  - Mot de passe : `admin`

- **Utilisateur régulier :**
  - Email : `user@mail.fr`
  - Mot de passe : `user`

- **Utilisateur banni :**
  - Email : `banned@mail.fr`
  - Mot de passe : `banned`

---

## 🛠️ Technologies utilisées

- **Backend** : Symfony
- **Frontend** : Twig
- **Base de données** : MySQL
- **Pagination** : KnpPaginator
- **Génération de données fictives** : Faker
- **Gestion des emails** : Symfony Mailer, Mailtrap
- **API pour drapeaux** : [Restcountries](https://restcountries.com)

---

## 🌟 Fonctionnalités principales

### 🌍 Vue d'ensemble
Le site permet :
- 🔎 Explorer des voyages et activités par pays.
- 💬 Laisser des commentaires.
- 🛠️ Administrer le contenu et les utilisateurs selon leur rôle.

### 👤 Rôles des utilisateurs
1. **Administrateur (ADMIN)** :
  - Accès total : gestion des voyages, activités, utilisateurs, et modération.
2. **Utilisateur régulier (USER)** :
  - Exploration des voyages, ajout de commentaires.
3. **Utilisateur banni (BANNED)** :
  - Accès restreint. 🚫

---
