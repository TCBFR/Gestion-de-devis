/* ── Reset et base ── */
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    font-size: 13px;
    color: #1a1a2e;
    line-height: 1.6;
}

/* ── En-tête du devis ── */
.header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    padding-bottom: 20px;
    border-bottom: 3px solid #f97316;
}
.header-logo { max-height: 60px; }
.header-title { font-size: 28px; font-weight: 800; color: #1a1a2e; letter-spacing: -0.5px; }
.header-meta { text-align: right; color: #555; font-size: 12px; }
.header-meta p { margin: 2px 0; }

/* ── Bloc vendeur / client côte à côte ── */
.parties { display: flex; gap: 40px; margin-bottom: 32px; }
.partie { flex: 1; }
.partie-label {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #f97316;
    font-weight: 700;
    margin-bottom: 8px;
}
.partie h3 { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
.partie p { font-size: 12px; color: #444; margin: 2px 0; }
.partie a { color: #1a1a2e; text-decoration: underline; }

/* ── Tableau des produits ── */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 24px;
}
thead th {
    background: #1a1a2e;
    color: #fff;
    padding: 10px 14px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
}
tbody tr:nth-child(even) { background: #f8f9fa; }
tbody td {
    padding: 9px 14px;
    border-bottom: 1px solid #eee;
    font-size: 13px;
}
tbody td:last-child { text-align: right; }

/* ── Lignes fixes (livraison, MO) ── */
.row-auto { color: #666; font-style: italic; }

/* ── Total ── */
.total-row td {
    padding: 12px 14px;
    font-size: 15px;
    font-weight: 700;
    border-top: 2px solid #1a1a2e;
    background: #fff8f5;
}
.total-row td:last-child { color: #f97316; }

/* ── Mentions légales ── */
.mentions {
    margin-top: 40px;
    padding-top: 16px;
    border-top: 1px solid #ddd;
    font-size: 11px;
    color: #777;
}
