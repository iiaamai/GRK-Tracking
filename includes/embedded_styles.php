<?php
declare(strict_types=1);

/**
 * Embedded stylesheets (output in <style> tags; no /assets/css HTTP requests).
 * Edit the nowdoc strings below to change appearance.
 */

function embedded_styles_global(): string
{
    return <<<'GRKCSS_GLOBAL'
/* Express Urban Logistics — theme tokens */
:root {
  --neutral: #ffffff;
  --shade: #000000;
  --primary: #4ca9de;
  --primary-soft: #8ed5f3;
  --primary-deep: #287fb0;
  --bg: var(--neutral);
  --bg-elevated: var(--shade);
  --bg-card: #ffffff;
  --bg-muted: #f4fbff;
  --bg-soft: #eef9fe;
  --border: rgba(76, 169, 222, 0.18);
  --text: var(--shade);
  --muted: #4f5b66;
  --accent: var(--primary);
  --accent-dim: rgba(76, 169, 222, 0.16);
  --accent-ring: rgba(76, 169, 222, 0.42);
  --success: #0d9f6e;
  --warning: #d97706;
  --danger: #dc2626;
  --radius: 12px;
  --sidebar-w: 260px;
  --font: "DM Sans", system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
  --shadow-sm: 0 8px 18px rgba(0, 0, 0, 0.05);
  --shadow-md: 0 18px 40px rgba(0, 0, 0, 0.08);
}

*,
*::before,
*::after {
  box-sizing: border-box;
}

html {
  font-size: 16px;
  -webkit-text-size-adjust: 100%;
}

body {
  margin: 0;
  min-height: 100vh;
  font-family: var(--font);
  background: radial-gradient(900px 480px at 85% -5%, rgba(142, 213, 243, 0.24), transparent),
    radial-gradient(600px 360px at 0% 100%, rgba(76, 169, 222, 0.1), transparent), var(--bg);
  color: var(--text);
  line-height: 1.5;
}

a {
  color: var(--primary);
  text-decoration: none;
  transition: color 0.18s ease;
}
a:hover {
  text-decoration: underline;
  color: #2f8fc7;
}

img {
  max-width: 100%;
  height: auto;
}

.app-shell {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: var(--sidebar-w);
  flex-shrink: 0;
  background: linear-gradient(180deg, #000000 0%, #08253a 100%);
  border-right: 1px solid rgba(255, 255, 255, 0.1);
  padding: 1.25rem 0;
  position: sticky;
  top: 0;
  align-self: flex-start;
  min-height: 100vh;
  color: rgba(255, 255, 255, 0.92);
}

.sidebar__brand {
  padding: 0 1.25rem 1.25rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 1rem;
  text-align: center;
}

.sidebar__logoWrap {
  width: 110px;
  height: 110px;
  margin: 0 auto 0.8rem;
  padding: 0.5rem;
  border-radius: 28px;
  background:
    linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(142, 213, 243, 0.92));
  box-shadow:
    0 12px 28px rgba(0, 0, 0, 0.24),
    inset 0 0 0 1px rgba(255, 255, 255, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
}

.sidebar__brand img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  display: block;
  border-radius: 22px;
  background: #ffffff;
  padding: 0.25rem;
}

.sidebar__brand small {
  color: rgba(255, 255, 255, 0.72);
  font-size: 0.76rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  font-weight: 700;
}

.sidebar__nav {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  padding: 0 0.75rem;
}

.sidebar__nav a {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.72rem 0.95rem;
  border-radius: 14px;
  color: rgba(255, 255, 255, 0.88);
  text-decoration: none;
  font-size: 0.95rem;
  border: 1px solid transparent;
  transition: background 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
}

.sidebar__nav a:hover {
  background: rgba(255, 255, 255, 0.08);
  text-decoration: none;
  transform: translateX(2px);
}

.sidebar__nav a.is-active {
  background: var(--accent-dim);
  border-color: rgba(142, 213, 243, 0.42);
  color: #fff;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
}

.sidebar__foot {
  margin-top: auto;
  padding: 1rem 1.25rem 0;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar .btn--ghost {
  border-color: rgba(255, 255, 255, 0.2);
  color: rgba(255, 255, 255, 0.92);
}
.sidebar .btn--ghost:hover {
  background: rgba(255, 255, 255, 0.1);
}

.main {
  flex: 1;
  padding: 1.5rem clamp(1rem, 3vw, 2.5rem);
  max-width: 1280px;
  width: 100%;
}

.page-head {
  margin-bottom: 1.5rem;
  padding: 1.25rem 1.35rem;
  border: 1px solid var(--border);
  border-radius: calc(var(--radius) + 4px);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(238, 249, 254, 0.95));
  box-shadow: var(--shadow-sm);
}

.page-head h1 {
  margin: 0 0 0.35rem;
  font-size: clamp(1.35rem, 2.5vw, 1.75rem);
  font-weight: 800;
  color: var(--shade);
}

.page-head p {
  margin: 0;
  color: var(--muted);
  font-size: 0.95rem;
}

.flash {
  padding: 0.95rem 1.05rem;
  border-radius: 14px;
  margin-bottom: 1rem;
  font-size: 0.95rem;
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
}

.flash--ok {
  background: rgba(13, 159, 110, 0.1);
  border-color: rgba(13, 159, 110, 0.25);
  color: #0a6b4a;
}

.flash--err {
  background: rgba(220, 38, 38, 0.08);
  border-color: rgba(220, 38, 38, 0.22);
  color: #b91c1c;
}

.card {
  background: var(--bg-card);
  border: 1px solid var(--border);
  border-radius: 18px;
  padding: 1.35rem;
  margin-bottom: 1.1rem;
  box-shadow: var(--shadow-sm);
}

.card h2 {
  margin: 0 0 0.75rem;
  font-size: 1.12rem;
  font-weight: 800;
  color: var(--shade);
}

.grid {
  display: grid;
  gap: 1rem;
}

@media (min-width: 640px) {
  .grid--stats {
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  }
  .grid--2 {
    grid-template-columns: repeat(2, 1fr);
  }
}

.stat {
  padding: 1.1rem;
  border-radius: 18px;
  background: linear-gradient(145deg, rgba(142, 213, 243, 0.28), var(--bg-muted));
  border: 1px solid var(--border);
  box-shadow: var(--shadow-sm);
}

.stat__val {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--shade);
}

.stat__lbl {
  font-size: 0.8rem;
  color: var(--muted);
  margin-top: 0.25rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  padding: 0.62rem 1.05rem;
  border-radius: 14px;
  font-size: 0.95rem;
  font-weight: 700;
  border: 1px solid transparent;
  cursor: pointer;
  font-family: inherit;
  text-decoration: none;
  color: inherit;
  transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease, border-color 0.18s ease;
}

.btn--primary {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-deep) 100%);
  color: #ffffff;
  box-shadow: 0 10px 22px rgba(76, 169, 222, 0.3);
}
.btn--primary:hover {
  filter: brightness(1.04);
  text-decoration: none;
  color: #ffffff;
  transform: translateY(-1px);
}

.btn--ghost {
  background: rgba(255, 255, 255, 0.8);
  border-color: rgba(76, 169, 222, 0.26);
  color: var(--text);
}
.btn--ghost:hover {
  background: var(--bg-soft);
  text-decoration: none;
  transform: translateY(-1px);
}

.btn--danger {
  background: rgba(220, 38, 38, 0.1);
  border-color: rgba(220, 38, 38, 0.35);
  color: #b91c1c;
}

.form-row {
  margin-bottom: 1rem;
}

.form-row label {
  display: block;
  font-size: 0.85rem;
  color: var(--muted);
  margin-bottom: 0.35rem;
}

.form-row input,
.form-row select,
.form-row textarea {
  width: 100%;
  padding: 0.78rem 0.88rem;
  border-radius: 14px;
  border: 1px solid var(--border);
  background: #fcfeff;
  color: var(--text);
  font-family: inherit;
  font-size: 1rem;
  box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02);
  transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
}

.form-row textarea {
  min-height: 100px;
  resize: vertical;
}

.form-row input:focus,
.form-row select:focus,
.form-row textarea:focus {
  outline: 2px solid var(--accent-ring);
  outline-offset: 0;
  border-color: rgba(76, 169, 222, 0.45);
  background: #ffffff;
  box-shadow: 0 0 0 4px rgba(142, 213, 243, 0.18);
}

.form-error {
  color: var(--danger);
  font-size: 0.85rem;
  margin-top: 0.35rem;
}

.table-wrap {
  overflow-x: auto;
  border-radius: 18px;
  border: 1px solid var(--border);
  background: var(--neutral);
  box-shadow: var(--shadow-sm);
}

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

th,
td {
  padding: 0.65rem 0.85rem;
  text-align: left;
  border-bottom: 1px solid var(--border);
}

th {
  background: linear-gradient(180deg, #f9fdff, #eef9fe);
  color: var(--muted);
  font-weight: 700;
}

tbody tr:hover {
  background: rgba(142, 213, 243, 0.09);
}

tr:last-child td {
  border-bottom: none;
}

.badge {
  display: inline-block;
  padding: 0.3rem 0.65rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: capitalize;
}

.badge--pending {
  background: rgba(142, 213, 243, 0.28);
  color: #2f8fc7;
}
.badge--ready_for_assignment {
  background: rgba(76, 169, 222, 0.18);
  color: #1f7db4;
}
.badge--assigned {
  background: rgba(0, 0, 0, 0.08);
  color: var(--shade);
}
.badge--in_transit {
  background: rgba(76, 169, 222, 0.18);
  color: #1f7db4;
}
.badge--completed {
  background: rgba(13, 159, 110, 0.15);
  color: #0a6b4a;
}
.badge--cancelled {
  background: rgba(220, 38, 38, 0.12);
  color: #b91c1c;
}

@media (max-width: 900px) {
  .app-shell {
    flex-direction: column;
  }
  .sidebar {
    position: relative;
    width: 100%;
    min-height: unset;
    border-right: none;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  .sidebar__nav {
    flex-direction: row;
    flex-wrap: wrap;
  }
}

GRKCSS_GLOBAL;
}

function embedded_styles_landing(): string
{
    return <<<'GRKCSS_LANDING'
/* Landing pages — root + customer + driver + admin */
.landing {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  background: var(--neutral);
}

.landing__hero {
  padding: clamp(2rem, 6vw, 4rem) clamp(1rem, 4vw, 2rem);
  max-width: 1100px;
  margin: 0 auto;
  text-align: center;
}

.landing__eyebrow {
  display: inline-block;
  font-size: 0.8rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--primary);
  font-weight: 600;
  margin-bottom: 0.75rem;
}

.landing__hero h1 {
  font-size: clamp(1.75rem, 4vw, 2.5rem);
  margin: 0 0 1rem;
  line-height: 1.15;
  color: var(--shade);
  font-weight: 900;
}

.landing__hero p.lead {
  color: var(--muted);
  max-width: 640px;
  margin: 0 auto 1.5rem;
  font-size: 1.05rem;
}

.landing__actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  justify-content: center;
  margin-bottom: 2rem;
}

.landing__grid {
  display: grid;
  gap: 1rem;
  max-width: 1000px;
  margin: 0 auto;
  padding: 0 1rem 3rem;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
}

.landing-card {
  background: var(--neutral);
  border: 1px solid var(--border);
  border-radius: 20px;
  padding: 1.35rem;
  text-align: left;
  box-shadow: var(--shadow-sm);
}

.landing-card h3 {
  margin: 0 0 0.5rem;
  font-size: 1rem;
  color: var(--shade);
}

.landing-card p {
  margin: 0;
  color: var(--muted);
  font-size: 0.9rem;
}

.landing__topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem clamp(1rem, 3vw, 2rem);
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  background: linear-gradient(180deg, #000000 0%, #08253a 100%);
}

.landing__logo {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  color: #ffffff;
}

.landing__logoFrame {
  width: 82px;
  height: 82px;
  padding: 0.42rem;
  border-radius: 24px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(142, 213, 243, 0.92));
  box-shadow:
    0 12px 28px rgba(0, 0, 0, 0.24),
    inset 0 0 0 1px rgba(255, 255, 255, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
}

.landing__logo img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  background: #ffffff;
  border-radius: 18px;
  padding: 0.25rem;
}

.landing__logoText {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
}

.landing__logoText strong {
  font-size: 1rem;
  letter-spacing: 0.08em;
  font-weight: 800;
}

.landing__logoText small {
  color: rgba(255, 255, 255, 0.72);
  font-size: 0.8rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  font-weight: 700;
}

.landing__back {
  font-size: 0.9rem;
  color: rgba(255, 255, 255, 0.82);
  padding: 0.6rem 0.95rem;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
}

.landing__back:hover {
  color: #ffffff;
  text-decoration: none;
  background: rgba(255, 255, 255, 0.12);
}

.landing__tagline {
  color: rgba(255, 255, 255, 0.65);
  font-size: 0.9rem;
}

@media (max-width: 720px) {
  .landing__topbar {
    flex-direction: column;
    gap: 0.75rem;
    text-align: center;
  }

  .landing__logo {
    flex-direction: column;
  }

  .landing__logoText {
    align-items: center;
  }
}

GRKCSS_LANDING;
}

function embedded_styles_login(): string
{
    return <<<'GRKCSS_LOGIN'
/* Shared login panels */
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  background: radial-gradient(700px 400px at 50% -10%, rgba(247, 114, 22, 0.08), transparent), var(--neutral);
}

.login-card {
  width: 100%;
  max-width: 420px;
  background: var(--neutral);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 2rem;
  box-shadow: var(--shadow-md);
}

.login-card--wide {
  max-width: 560px;
}

.login-card--stack {
  max-width: 520px;
}

.login-card--stack.login-card--wide {
  max-width: 560px;
}

.login-card h1 {
  margin: 0 0 0.35rem;
  font-size: 1.35rem;
  color: var(--shade);
}

.login-card .sub {
  color: var(--muted);
  font-size: 0.9rem;
  margin: 0 0 1.5rem;
}

.login-card .actions {
  margin-top: 1.25rem;
}

.login-card .actions .btn {
  width: 100%;
}

.login-meta {
  margin-top: 1.25rem;
  font-size: 0.85rem;
  color: var(--muted);
  text-align: center;
}

.login-meta a {
  color: var(--primary);
}

.login-meta a:hover {
  color: #e0650f;
}

.demo-hint {
  margin-top: 1rem;
  padding: 0.75rem;
  border-radius: 10px;
  background: rgba(247, 114, 22, 0.08);
  border: 1px dashed rgba(247, 114, 22, 0.35);
  font-size: 0.8rem;
  color: var(--muted);
}

.demo-hint code {
  color: var(--shade);
  font-size: 0.85em;
}

.login-divider {
  height: 1px;
  background: var(--border);
  margin: 1.75rem 0;
}

.login-register-title {
  margin: 0 0 0.35rem;
  font-size: 1.1rem;
  font-weight: 650;
  color: var(--shade);
}

.login-register-lead {
  margin: 0 0 1.25rem;
  color: var(--muted);
  font-size: 0.9rem;
}

.login-card--stack .actions {
  margin-top: 0.75rem;
}

GRKCSS_LOGIN;
}

function embedded_styles_admin(string $section): string
{
    $map = [
        'overview' => <<<'GRKCSS_ADM_OVERVIEW'
/* admin/sections/overview.php */

GRKCSS_ADM_OVERVIEW,
        'earnings_reports' => <<<'GRKCSS_ADM_EARNINGS_REPORTS'
/* admin/sections/earnings_reports.php */

.tabs {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.tab {
  display: inline-flex;
  align-items: center;
  padding: 0.45rem 0.65rem;
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 999px;
  color: inherit;
  text-decoration: none;
  font-size: 0.9rem;
}

.tab.is-active {
  background: rgba(255,255,255,0.08);
  border-color: rgba(255,255,255,0.22);
}


GRKCSS_ADM_EARNINGS_REPORTS,
        'bookings' => <<<'GRKCSS_ADM_BOOKINGS'
/* admin/sections/bookings.php */
.table-wrap select {
  font-size: 0.85rem;
}

GRKCSS_ADM_BOOKINGS,
        'drivers' => <<<'GRKCSS_ADM_DRIVERS'
/* admin/sections/drivers.php */
.table-wrap label {
  font-size: 0.75rem;
  color: var(--muted);
}

GRKCSS_ADM_DRIVERS,
        'customers' => <<<'GRKCSS_ADM_CUSTOMERS'
/* admin/sections/customers.php */
.table-wrap label {
  font-size: 0.75rem;
  color: var(--muted);
}

GRKCSS_ADM_CUSTOMERS,
        'fleet' => <<<'GRKCSS_ADM_FLEET'
/* admin/sections/fleet.php */
.table-wrap label {
  font-size: 0.75rem;
  color: var(--muted);
}

GRKCSS_ADM_FLEET,
        'settings' => <<<'GRKCSS_ADM_SETTINGS'
/* admin/sections/settings.php */

GRKCSS_ADM_SETTINGS,
    ];
    return $map[$section] ?? '';
}

function embedded_styles_customer(string $section): string
{
    $map = [        'overview' => <<<'GRKCSS_CUS_OVERVIEW'
/* customer/sections/overview.php */
.page-head p {
  max-width: 52ch;
}

GRKCSS_CUS_OVERVIEW,
        'booking' => <<<'GRKCSS_CUS_BOOKING'
/* customer/sections/booking.php */
.booking-payout-summary {
  margin: 1rem 0 0.75rem;
  padding: 0.85rem 1rem;
  border-radius: 10px;
  border: 1px solid var(--border, rgba(255, 255, 255, 0.12));
  background: var(--surface-2, rgba(0, 0, 0, 0.2));
}

.booking-payout-summary__label {
  font-size: 0.75rem;
  font-weight: 600;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 0.35rem;
}

.booking-payout-summary__value {
  font-size: 1.05rem;
  color: var(--muted);
  line-height: 1.35;
}

.booking-payout-summary__value.is-amount {
  font-size: 1.35rem;
  font-weight: 700;
  color: var(--text, inherit);
}

.card form .btn {
  margin-top: 0.25rem;
}

GRKCSS_CUS_BOOKING,
        'bookings' => <<<'GRKCSS_CUS_BOOKINGS'
/* customer/sections/bookings.php */
.table-wrap th,
.table-wrap td {
  white-space: nowrap;
}

GRKCSS_CUS_BOOKINGS,
        'profile' => <<<'GRKCSS_CUS_PROFILE'
/* customer/sections/profile.php */
.card code {
  font-size: 0.85em;
}

GRKCSS_CUS_PROFILE,
        'track' => <<<'GRKCSS_CUS_TRACK'
/* customer/sections/track.php */
.track-search-form .grid {
  margin-bottom: 0;
}

.track-shipment-panel {
  margin-top: 0;
}

.track-shipment-panel__head {
  margin-bottom: 1.25rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--border);
}

.track-shipment-panel__head h2 {
  margin: 0 0 0.5rem;
}

.track-shipment-panel__ref {
  margin: 0;
  font-size: 0.95rem;
  color: var(--muted);
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.5rem;
}

.track-shipment-panel__ref strong {
  color: var(--shade);
  font-size: 1.05rem;
}

.track-shipment-panel__grid {
  display: grid;
  gap: 1.25rem;
}

@media (min-width: 720px) {
  .track-shipment-panel__grid {
    grid-template-columns: repeat(2, 1fr);
  }
  .track-shipment-panel__block--wide {
    grid-column: 1 / -1;
  }
}

.track-shipment-panel__block {
  background: var(--bg-muted);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 1rem 1.1rem;
}

.track-shipment-panel__block h3 {
  margin: 0 0 0.75rem;
  font-size: 0.85rem;
  font-weight: 650;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--muted);
}

.track-dl {
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
}

.track-dl > div {
  display: grid;
  grid-template-columns: minmax(120px, 38%) 1fr;
  gap: 0.5rem 1rem;
  align-items: start;
  font-size: 0.92rem;
}

.track-dl dt {
  margin: 0;
  color: var(--muted);
  font-weight: 500;
}

.track-dl dd {
  margin: 0;
  color: var(--shade);
  word-break: break-word;
}

.track-dl > div.track-dl__full {
  display: block;
}

.track-dl > div.track-dl__full dt {
  margin-bottom: 0.25rem;
}

.track-dl > div.track-dl__full dd {
  white-space: pre-wrap;
}

.track-shipment-panel__pending {
  margin: 0;
  font-size: 0.92rem;
  color: var(--muted);
}

GRKCSS_CUS_TRACK,
    ];
    return $map[$section] ?? '';
}

function embedded_styles_driver(string $section): string
{
    $map = [        'overview' => <<<'GRKCSS_DRV_OVERVIEW'
/* driver/sections/overview.php */

GRKCSS_DRV_OVERVIEW,
        'jobs' => <<<'GRKCSS_DRV_JOBS'
/* driver/sections/jobs.php + driver cards */

.driver-cards {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.1rem;
  align-items: stretch;
}

.driver-job-card {
  background: #ffffff;
  border: 1px solid rgba(2, 7, 24, 0.08);
  border-radius: 12px;
  padding: 16px 18px 14px 18px;
  overflow: hidden; /* ensures rounded corners clip the CTA bar */
}

.driver-job-card__head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 14px;
}

.driver-job-card__left {
  min-width: 0;
}

.driver-job-card__route {
  font-size: 1rem;
  font-weight: 900;
  color: #020718;
  margin-bottom: 3px;
}

.driver-job-card__biz {
  font-size: 0.8rem;
  font-weight: 700;
  color: #6b7280;
}

.driver-job-card__payout {
  color: var(--primary);
  font-weight: 900;
  font-size: 1rem;
  white-space: nowrap;
}

.driver-job-card__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  column-gap: 34px;
  row-gap: 16px;
}

.driver-job-card__label {
  font-size: 0.65rem;
  color: #6b7280;
  text-transform: uppercase;
  font-weight: 900;
  letter-spacing: 0.03em;
  margin-bottom: 4px;
}

.driver-job-card__value {
  font-size: 0.85rem;
  color: #020718;
  font-weight: 600;
  line-height: 1.25;
  word-break: break-word;
}

.driver-job-card__docs {
  margin-top: 12px;
  font-size: 0.82rem;
  font-weight: 700;
}

.driver-job-card__docs a {
  color: var(--primary);
  text-decoration: none;
}

.driver-job-card__docs a:hover {
  text-decoration: underline;
}

.driver-job-card__docsSep {
  margin: 0 0.35rem;
  color: var(--muted);
  font-weight: 600;
}

.driver-job-card__eirForm {
  margin-top: 10px;
}

.driver-job-card__eirLbl {
  font-size: 0.72rem;
  color: var(--muted);
  font-weight: 700;
  margin-bottom: 0.35rem;
}

.driver-job-card__eirRow {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: center;
}

.driver-job-card__eirRow input[type="file"] {
  flex: 1 1 140px;
  min-width: 0;
  font-size: 0.78rem;
}

.driver-job-card__hint {
  margin: 0;
  padding: 0.65rem 0.5rem;
  text-align: center;
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--muted);
  background: var(--bg-muted);
  border: 1px dashed var(--border);
}

.driver-job-card__cta {
  margin-top: 22px;
}

.driver-job-card__ctaBtn {
  width: 100%;
  border: 0;
  height: 44px;
  background: linear-gradient(90deg, #020718 0%, #071a3c 50%, #020718 100%);
  color: #ffffff;
  font-weight: 900;
  font-size: 0.9rem;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  cursor: pointer;
  font-family: inherit;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0;
}

.driver-job-card__ctaBtn:hover {
  filter: brightness(1.05);
}

@media (max-width: 680px) {
  .driver-cards {
    grid-template-columns: 1fr;
  }
  .driver-job-card__grid {
    column-gap: 24px;
  }
}

@media (min-width: 681px) and (max-width: 980px) {
  .driver-cards {
    grid-template-columns: 1fr;
  }
}

GRKCSS_DRV_JOBS,
        'deliveries' => <<<'GRKCSS_DRV_DELIVERIES'
/* driver/sections/deliveries.php */

.driver-cards {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.1rem;
  align-items: stretch;
}

.driver-job-card {
  background: #ffffff;
  border: 1px solid rgba(2, 7, 24, 0.08);
  border-radius: 12px;
  padding: 16px 18px 14px 18px;
  overflow: hidden; /* ensures rounded corners clip the CTA bar */
}

.driver-job-card__head {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 14px;
}

.driver-job-card__left {
  min-width: 0;
}

.driver-job-card__route {
  font-size: 1rem;
  font-weight: 900;
  color: #020718;
  margin-bottom: 3px;
}

.driver-job-card__biz {
  font-size: 0.8rem;
  font-weight: 700;
  color: #6b7280;
}

.driver-job-card__payout {
  color: var(--primary);
  font-weight: 900;
  font-size: 1rem;
  white-space: nowrap;
}

.driver-job-card__grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  column-gap: 34px;
  row-gap: 16px;
}

.driver-job-card__label {
  font-size: 0.65rem;
  color: #6b7280;
  text-transform: uppercase;
  font-weight: 900;
  letter-spacing: 0.03em;
  margin-bottom: 4px;
}

.driver-job-card__value {
  font-size: 0.85rem;
  color: #020718;
  font-weight: 600;
  line-height: 1.25;
  word-break: break-word;
}

.driver-job-card__docs {
  margin-top: 12px;
  font-size: 0.82rem;
  font-weight: 700;
}

.driver-job-card__docs a {
  color: var(--primary);
  text-decoration: none;
}

.driver-job-card__docs a:hover {
  text-decoration: underline;
}

.driver-job-card__docsSep {
  margin: 0 0.35rem;
  color: var(--muted);
  font-weight: 600;
}

.driver-job-card__eirForm {
  margin-top: 10px;
}

.driver-job-card__eirLbl {
  font-size: 0.72rem;
  color: var(--muted);
  font-weight: 700;
  margin-bottom: 0.35rem;
}

.driver-job-card__eirRow {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: center;
}

.driver-job-card__eirRow input[type="file"] {
  flex: 1 1 140px;
  min-width: 0;
  font-size: 0.78rem;
}

.driver-job-card__hint {
  margin: 0;
  padding: 0.65rem 0.5rem;
  text-align: center;
  font-size: 0.82rem;
  font-weight: 700;
  color: var(--muted);
  background: var(--bg-muted);
  border: 1px dashed var(--border);
}

.driver-job-card__cta {
  margin-top: 22px;
}

.driver-job-card__ctaBtn {
  width: 100%;
  border: 0;
  height: 44px;
  background: linear-gradient(90deg, #020718 0%, #071a3c 50%, #020718 100%);
  color: #ffffff;
  font-weight: 900;
  font-size: 0.9rem;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  cursor: pointer;
  font-family: inherit;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0;
}

.driver-job-card__ctaBtn:hover {
  filter: brightness(1.05);
}

@media (max-width: 680px) {
  .driver-cards {
    grid-template-columns: 1fr;
  }

  .driver-job-card__grid {
    column-gap: 24px;
  }
}

@media (min-width: 681px) and (max-width: 980px) {
  .driver-cards {
    grid-template-columns: 1fr;
  }
}

GRKCSS_DRV_DELIVERIES,
        'earnings' => <<<'GRKCSS_DRV_EARNINGS'
/* driver/sections/earnings.php */

GRKCSS_DRV_EARNINGS,
        'profile' => <<<'GRKCSS_DRV_PROFILE'
/* driver/sections/profile.php */

GRKCSS_DRV_PROFILE,
    ];
    return $map[$section] ?? '';
}
