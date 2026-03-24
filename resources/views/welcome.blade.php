<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DPMR – Documentation API</title>
    <meta name="description" content="Documentation complète de l'API REST DPMR – Articles, Projets, Biographie, Documents, Médias, Newsletter et plus.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0d1117;
            --surface: #161b22;
            --surface2: #1c2333;
            --border: #30363d;
            --text: #e6edf3;
            --muted: #8b949e;
            --accent: #58a6ff;
            --accent2: #3fb950;
            --accent3: #f78166;
            --accent4: #d2a8ff;
            --accent5: #ffa657;
            --tag-get: #1a4a2e;
            --tag-get-text: #3fb950;
            --tag-post: #1a3a5c;
            --tag-post-text: #58a6ff;
            --tag-put: #4a3a1a;
            --tag-put-text: #ffa657;
            --tag-delete: #4a1a1a;
            --tag-delete-text: #f78166;
            --tag-auth: #3a1a4a;
            --tag-auth-text: #d2a8ff;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: 260px;
            height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 24px 20px 16px;
            border-bottom: 1px solid var(--border);
        }

        .sidebar-brand h1 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .sidebar-brand .version {
            font-size: 0.72rem;
            color: var(--muted);
            margin-top: 2px;
        }

        .base-url-box {
            margin: 12px 20px;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 8px 12px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: var(--accent);
            word-break: break-all;
        }

        .nav-section {
            padding: 16px 20px 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--muted);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 20px;
            color: var(--muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.15s;
            border-left: 2px solid transparent;
        }

        .nav-link:hover {
            color: var(--text);
            background: var(--surface2);
            border-left-color: var(--accent);
        }

        .nav-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* ── MAIN ── */
        .main {
            margin-left: 260px;
            padding: 40px 48px;
            max-width: 1000px;
        }

        /* ── HERO ── */
        .hero {
            margin-bottom: 56px;
            padding-bottom: 40px;
            border-bottom: 1px solid var(--border);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(88,166,255,0.1);
            border: 1px solid rgba(88,166,255,0.3);
            color: var(--accent);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 16px;
        }

        .hero h1 {
            font-size: 2.4rem;
            font-weight: 700;
            letter-spacing: -0.04em;
            line-height: 1.15;
            margin-bottom: 14px;
        }

        .hero h1 span {
            background: linear-gradient(135deg, var(--accent), var(--accent4));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1rem;
            color: var(--muted);
            max-width: 600px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin-top: 28px;
        }

        .info-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
        }

        .info-card .label { font-size: 0.72rem; color: var(--muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.06em; }
        .info-card .value { font-size: 0.9rem; font-weight: 600; color: var(--text); }

        /* ── SECTION ── */
        .section {
            margin-bottom: 52px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
        }

        .section-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .section-header h2 {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .section-header p {
            font-size: 0.82rem;
            color: var(--muted);
        }

        /* ── ENDPOINT ── */
        .endpoint {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
            transition: border-color 0.15s;
        }

        .endpoint:hover { border-color: #444c56; }

        .endpoint-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            cursor: pointer;
        }

        .method-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
            min-width: 52px;
            text-align: center;
            flex-shrink: 0;
        }

        .GET  { background: var(--tag-get);    color: var(--tag-get-text); }
        .POST { background: var(--tag-post);   color: var(--tag-post-text); }
        .PUT  { background: var(--tag-put);    color: var(--tag-put-text); }
        .DELETE { background: var(--tag-delete); color: var(--tag-delete-text); }

        .endpoint-path {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            color: var(--text);
            flex: 1;
        }

        .endpoint-desc {
            font-size: 0.8rem;
            color: var(--muted);
            margin-left: auto;
            text-align: right;
        }

        .auth-badge {
            background: var(--tag-auth);
            color: var(--tag-auth-text);
            font-size: 0.65rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .endpoint-body {
            border-top: 1px solid var(--border);
            padding: 16px 18px;
            display: none;
        }

        .endpoint.open .endpoint-body { display: block; }
        .endpoint.open .endpoint-header { background: var(--surface2); }

        .param-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            margin-top: 8px;
        }

        .param-table th {
            text-align: left;
            padding: 6px 10px;
            color: var(--muted);
            font-weight: 500;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        .param-table td {
            padding: 8px 10px;
            border-bottom: 1px solid rgba(48,54,61,0.5);
            vertical-align: top;
        }

        .param-table tr:last-child td { border-bottom: none; }

        .param-name {
            font-family: 'JetBrains Mono', monospace;
            color: var(--accent5);
        }

        .param-type {
            font-family: 'JetBrains Mono', monospace;
            color: var(--accent4);
            font-size: 0.78rem;
        }

        .required-badge {
            background: rgba(247,129,102,0.15);
            color: var(--accent3);
            font-size: 0.65rem;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 3px;
        }

        .optional-badge {
            background: rgba(139,148,158,0.15);
            color: var(--muted);
            font-size: 0.65rem;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 3px;
        }

        .code-block {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 14px 16px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            color: var(--muted);
            overflow-x: auto;
            margin-top: 10px;
        }

        .code-label {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            margin-top: 14px;
            margin-bottom: 4px;
        }

        /* ── AUTH SECTION ── */
        .auth-box {
            background: linear-gradient(135deg, rgba(31,47,79,0.6), rgba(22,27,34,1));
            border: 1px solid rgba(88,166,255,0.25);
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 28px;
        }

        .auth-box h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--accent);
        }

        .auth-box p {
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 14px;
        }

        /* ── RESPONSES ── */
        .response-row {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.82rem;
            padding: 5px 0;
        }

        .status-code {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            font-size: 0.78rem;
            min-width: 38px;
        }

        .s2xx { color: var(--accent2); }
        .s4xx { color: var(--accent3); }
        .s5xx { color: #e3b341; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; padding: 24px 20px; }
            .hero h1 { font-size: 1.6rem; }
            .endpoint-desc { display: none; }
        }
    </style>
</head>
<body>

<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <h1>DPMR API</h1>
        <div class="version">REST · JSON · Sanctum Auth</div>
    </div>
    <div class="base-url-box">/api</div>

    <div class="nav-section">Général</div>
    <a href="#intro" class="nav-link"><span class="nav-dot" style="background:#58a6ff"></span> Introduction</a>
    <a href="#auth" class="nav-link"><span class="nav-dot" style="background:#d2a8ff"></span> Authentification</a>

    <div class="nav-section">Ressources</div>
    <a href="#articles" class="nav-link"><span class="nav-dot" style="background:#3fb950"></span> Articles</a>
    <a href="#projects" class="nav-link"><span class="nav-dot" style="background:#ffa657"></span> Projets</a>
    <a href="#biography" class="nav-link"><span class="nav-dot" style="background:#f78166"></span> Biographie</a>
    <a href="#documents" class="nav-link"><span class="nav-dot" style="background:#58a6ff"></span> Documents</a>
    <a href="#media" class="nav-link"><span class="nav-dot" style="background:#d2a8ff"></span> Médias</a>
    <a href="#newsletter" class="nav-link"><span class="nav-dot" style="background:#e3b341"></span> Newsletter</a>
    <a href="#contacts" class="nav-link"><span class="nav-dot" style="background:#3fb950"></span> Contacts</a>
</aside>

<!-- ══════════════ MAIN ══════════════ -->
<main class="main">

    <!-- HERO -->
    <div class="hero" id="intro">
        <div class="hero-badge">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor"><circle cx="5" cy="5" r="5"/></svg>
            API v1 · En ligne
        </div>
        <h1>Documentation<br><span>API DPMR</span></h1>
        <p>Interface REST complète pour gérer les contenus, projets, documents, médias et la newsletter. Les réponses sont retournées en JSON.</p>

        <div class="info-grid">
            <div class="info-card">
                <div class="label">Base URL</div>
                <div class="value" style="font-family:'JetBrains Mono',monospace;font-size:0.8rem">/api</div>
            </div>
            <div class="info-card">
                <div class="label">Format</div>
                <div class="value">JSON</div>
            </div>
            <div class="info-card">
                <div class="label">Authentification</div>
                <div class="value">Bearer Token</div>
            </div>
            <div class="info-card">
                <div class="label">Version Laravel</div>
                <div class="value">{{ app()->version() }}</div>
            </div>
        </div>
    </div>

    <!-- AUTH SECTION -->
    <div class="section" id="auth">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(210,168,255,0.1);color:#d2a8ff">🔐</div>
            <div>
                <h2>Authentification</h2>
                <p>Inscription, connexion, déconnexion via Laravel Sanctum</p>
            </div>
        </div>

        <div class="auth-box">
            <h3>🛡️ Laravel Sanctum – Token Bearer</h3>
            <p>Les routes protégées nécessitent un token d'accès. Incluez-le dans l'en-tête HTTP de chaque requête :</p>
            <div class="code-block">Authorization: Bearer &lt;votre_token&gt;</div>
            <p style="margin-top:12px;margin-bottom:0">Obtenez votre token via <code style="color:var(--accent);font-family:'JetBrains Mono',monospace">POST /api/login</code></p>
        </div>

        <!-- Register -->
        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/register</span>
                <span class="endpoint-desc">Créer un compte</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Corps de la requête</div>
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">name</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Nom complet</td></tr>
                        <tr><td class="param-name">email</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Adresse e-mail unique</td></tr>
                        <tr><td class="param-name">password</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Minimum 8 caractères</td></tr>
                        <tr><td class="param-name">password_confirmation</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Confirmation du mot de passe</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">201</span><span>Compte créé – retourne le token d'accès</span></div>
                <div class="response-row"><span class="status-code s4xx">422</span><span>Données invalides</span></div>
            </div>
        </div>

        <!-- Login -->
        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/login</span>
                <span class="endpoint-desc">Obtenir un token</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Corps de la requête</div>
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">email</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Adresse e-mail</td></tr>
                        <tr><td class="param-name">password</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Mot de passe</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Exemple de réponse</div>
                <div class="code-block">{ "token": "1|abc123...", "user": { "id": 1, "name": "Alice", "email": "alice@example.com" } }</div>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Token généré avec succès</span></div>
                <div class="response-row"><span class="status-code s4xx">401</span><span>Identifiants incorrects</span></div>
            </div>
        </div>

        <!-- Logout -->
        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/logout</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Révoquer le token</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Révoque le token Bearer courant. Requiert l'en-tête <code style="color:var(--accent);font-family:'JetBrains Mono',monospace">Authorization: Bearer &lt;token&gt;</code>.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Déconnexion réussie</span></div>
                <div class="response-row"><span class="status-code s4xx">401</span><span>Token manquant ou invalide</span></div>
            </div>
        </div>

        <!-- Current user -->
        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/user</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Utilisateur connecté</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Retourne les informations de l'utilisateur associé au token.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Objet utilisateur</span></div>
                <div class="response-row"><span class="status-code s4xx">401</span><span>Non authentifié</span></div>
            </div>
        </div>
    </div>

    <!-- ARTICLES -->
    <div class="section" id="articles">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(63,185,80,0.1);color:#3fb950">📝</div>
            <div>
                <h2>Articles</h2>
                <p>Publication, gestion et modération des articles</p>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/articles/published</span>
                <span class="endpoint-desc">Articles publiés</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Retourne la liste de tous les articles dont le statut est <em>publié</em>. Route publique, aucun token requis.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Tableau d'articles publiés</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/articles/{id}</span>
                <span class="endpoint-desc">Détail d'un article</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Paramètres URL</div>
                <table class="param-table">
                    <thead><tr><th>Paramètre</th><th>Type</th><th>Description</th></tr></thead>
                    <tbody><tr><td class="param-name">id</td><td class="param-type">integer</td><td>Identifiant de l'article</td></tr></tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Objet article</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Article introuvable</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/articles</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Tous les articles (admin)</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Retourne tous les articles (brouillons inclus). Nécessite un token administrateur.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Tableau de tous les articles</span></div>
                <div class="response-row"><span class="status-code s4xx">401</span><span>Non authentifié</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/articles</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Créer un article</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Corps de la requête (multipart/form-data)</div>
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">title</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Titre de l'article</td></tr>
                        <tr><td class="param-name">content</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Corps de l'article</td></tr>
                        <tr><td class="param-name">image</td><td class="param-type">file</td><td><span class="optional-badge">optionnel</span></td><td>Image de couverture (jpg/png/webp)</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">201</span><span>Article créé</span></div>
                <div class="response-row"><span class="status-code s4xx">422</span><span>Données invalides</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge PUT">PUT</span>
                <span class="endpoint-path">/api/articles/{id}/publish</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Publier un article</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Passe le statut de l'article à <em>publié</em>.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Article publié</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Article introuvable</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge PUT">PUT</span>
                <span class="endpoint-path">/api/articles/{id}/unpublish</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Dépublier un article</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Repasse l'article en brouillon.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Article dépublié</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge PUT">PUT</span>
                <span class="endpoint-path">/api/articles/{id}</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Modifier un article</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Mise à jour partielle ou complète d'un article existant.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Article mis à jour</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Article introuvable</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge DELETE">DELETE</span>
                <span class="endpoint-path">/api/articles/{id}</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Supprimer un article</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Suppression définitive. Réservé aux administrateurs.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">204</span><span>Supprimé (sans contenu)</span></div>
                <div class="response-row"><span class="status-code s4xx">403</span><span>Action interdite</span></div>
            </div>
        </div>
    </div>

    <!-- PROJECTS -->
    <div class="section" id="projects">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(255,166,87,0.1);color:#ffa657">🚀</div>
            <div>
                <h2>Projets</h2>
                <p>Portfolio et gestion des réalisations</p>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/projects</span>
                <span class="endpoint-desc">Liste des projets</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Retourne tous les projets. Route publique.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Tableau de projets</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/projects/{id}</span>
                <span class="endpoint-desc">Détail d'un projet</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Objet projet</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Projet introuvable</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/projects</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Créer un projet</span>
            </div>
            <div class="endpoint-body">
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">title</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Titre du projet</td></tr>
                        <tr><td class="param-name">description</td><td class="param-type">string</td><td><span class="optional-badge">optionnel</span></td><td>Description</td></tr>
                        <tr><td class="param-name">image</td><td class="param-type">file</td><td><span class="optional-badge">optionnel</span></td><td>Visuel du projet</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">201</span><span>Projet créé</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge PUT">PUT</span>
                <span class="endpoint-path">/api/projects/{id}</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Modifier un projet</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Projet mis à jour</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Projet introuvable</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge DELETE">DELETE</span>
                <span class="endpoint-path">/api/projects/{id}</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Supprimer un projet</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">204</span><span>Supprimé</span></div>
                <div class="response-row"><span class="status-code s4xx">403</span><span>Droits insuffisants</span></div>
            </div>
        </div>
    </div>

    <!-- BIOGRAPHY -->
    <div class="section" id="biography">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(247,129,102,0.1);color:#f78166">👤</div>
            <div>
                <h2>Biographie</h2>
                <p>Singleton – une seule biographie par application</p>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/biography</span>
                <span class="endpoint-desc">Lire la biographie</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Retourne l'unique biographie ou <code style="color:var(--accent);font-family:'JetBrains Mono',monospace">null</code> si aucune n'existe encore.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Objet biographie (ou null)</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/biography</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Créer ou remplacer</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Crée la biographie si elle n'existe pas, sinon la remplace (comportement upsert).</p>
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">content</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Texte de la biographie</td></tr>
                        <tr><td class="param-name">photo</td><td class="param-type">file</td><td><span class="optional-badge">optionnel</span></td><td>Photo de profil</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Biographie enregistrée</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge DELETE">DELETE</span>
                <span class="endpoint-path">/api/biography</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Supprimer la biographie</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">204</span><span>Supprimée</span></div>
                <div class="response-row"><span class="status-code s4xx">403</span><span>Réservé aux administrateurs</span></div>
            </div>
        </div>
    </div>

    <!-- DOCUMENTS -->
    <div class="section" id="documents">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(88,166,255,0.1);color:#58a6ff">📄</div>
            <div>
                <h2>Documents</h2>
                <p>Gestion et téléchargement de fichiers</p>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/documents</span>
                <span class="endpoint-desc">Liste des documents</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Tableau de documents</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/documents/{id}</span>
                <span class="endpoint-desc">Détail d'un document</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Objet document</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Introuvable</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/documents/{id}/download</span>
                <span class="endpoint-desc">Télécharger un fichier</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Retourne le fichier en pièce jointe (Content-Disposition: attachment).</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Fichier binaire</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Fichier introuvable</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/documents</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Uploader un document</span>
            </div>
            <div class="endpoint-body">
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">title</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Nom du document</td></tr>
                        <tr><td class="param-name">file</td><td class="param-type">file</td><td><span class="required-badge">requis</span></td><td>Fichier à stocker</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">201</span><span>Document enregistré</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge DELETE">DELETE</span>
                <span class="endpoint-path">/api/documents/{id}</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Supprimer un document</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">204</span><span>Supprimé</span></div>
            </div>
        </div>
    </div>

    <!-- MEDIA -->
    <div class="section" id="media">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(210,168,255,0.1);color:#d2a8ff">🖼️</div>
            <div>
                <h2>Médias</h2>
                <p>Bibliothèque d'images et fichiers multimédias</p>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/media</span>
                <span class="endpoint-desc">Galerie médias</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Tableau de médias</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/media/{id}</span>
                <span class="endpoint-desc">Détail d'un média</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Objet média</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Introuvable</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/media</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Uploader un média</span>
            </div>
            <div class="endpoint-body">
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">file</td><td class="param-type">file</td><td><span class="required-badge">requis</span></td><td>Fichier image/vidéo</td></tr>
                        <tr><td class="param-name">title</td><td class="param-type">string</td><td><span class="optional-badge">optionnel</span></td><td>Légende</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">201</span><span>Média uploadé</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge DELETE">DELETE</span>
                <span class="endpoint-path">/api/media/{id}</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Supprimer un média</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">204</span><span>Supprimé</span></div>
            </div>
        </div>
    </div>

    <!-- NEWSLETTER -->
    <div class="section" id="newsletter">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(227,179,65,0.1);color:#e3b341">📧</div>
            <div>
                <h2>Newsletter</h2>
                <p>Abonnements, envoi de newsletters et gestion des abonnés</p>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/newsletter/subscribe</span>
                <span class="endpoint-desc">S'abonner</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Inscription publique à la newsletter.</p>
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">email</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Adresse e-mail de l'abonné</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">201</span><span>Inscription confirmée</span></div>
                <div class="response-row"><span class="status-code s4xx">422</span><span>E-mail invalide ou déjà inscrit</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/abonnes</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Liste des abonnés</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Tableau d'abonnés</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge DELETE">DELETE</span>
                <span class="endpoint-path">/api/abonnes/{id}</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Désinscrire un abonné</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">204</span><span>Désinscrit</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/newsletters</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Newsletters créées</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Retourne la liste des newsletters (brouillons et envoyées).</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Tableau de newsletters</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/newsletters/{id}/send</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Envoyer une newsletter</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Déclenche l'envoi de la newsletter à tous les abonnés actifs.</p>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Envoi lancé</span></div>
                <div class="response-row"><span class="status-code s4xx">404</span><span>Newsletter introuvable</span></div>
            </div>
        </div>
    </div>

    <!-- CONTACTS -->
    <div class="section" id="contacts">
        <div class="section-header">
            <div class="section-icon" style="background:rgba(63,185,80,0.1);color:#3fb950">💬</div>
            <div>
                <h2>Contacts</h2>
                <p>Formulaire de contact et gestion des messages reçus</p>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge POST">POST</span>
                <span class="endpoint-path">/api/contacts</span>
                <span class="endpoint-desc">Envoyer un message</span>
            </div>
            <div class="endpoint-body">
                <p style="font-size:0.85rem;color:var(--muted)">Route publique. Enregistre un message de contact.</p>
                <table class="param-table">
                    <thead><tr><th>Champ</th><th>Type</th><th>Requis</th><th>Description</th></tr></thead>
                    <tbody>
                        <tr><td class="param-name">name</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Nom de l'expéditeur</td></tr>
                        <tr><td class="param-name">email</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>E-mail de l'expéditeur</td></tr>
                        <tr><td class="param-name">message</td><td class="param-type">string</td><td><span class="required-badge">requis</span></td><td>Contenu du message</td></tr>
                    </tbody>
                </table>
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">201</span><span>Message enregistré</span></div>
                <div class="response-row"><span class="status-code s4xx">422</span><span>Données invalides</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge GET">GET</span>
                <span class="endpoint-path">/api/contacts</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Liste des messages</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">200</span><span>Tableau de messages</span></div>
            </div>
        </div>

        <div class="endpoint" onclick="toggle(this)">
            <div class="endpoint-header">
                <span class="method-badge DELETE">DELETE</span>
                <span class="endpoint-path">/api/contacts/{id}</span>
                <span class="auth-badge">🔒 Auth</span>
                <span class="endpoint-desc">Supprimer un message</span>
            </div>
            <div class="endpoint-body">
                <div class="code-label">Réponses</div>
                <div class="response-row"><span class="status-code s2xx">204</span><span>Supprimé</span></div>
            </div>
        </div>
    </div>

</main>

<script>
function toggle(el) {
    el.classList.toggle('open');
}
// Smooth scroll for sidebar links
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        e.preventDefault();
        document.querySelector(a.getAttribute('href'))?.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>
</body>
</html>
