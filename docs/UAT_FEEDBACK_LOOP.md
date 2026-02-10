# UAT feedback loop – naar 100% geslaagde audit

**Doel:** Herhaalbare methode om de [Finale audit Roadmap 1.1.4](FINAL_AUDIT_ROADMAP_1.1.4.md) tot **100% geslaagd** te brengen met Playwright en handmatige UAT.

---

## Stappen (herhaal tot 100%)

| Stap | Actie |
|------|--------|
| **1. Voorbereiding** | Testomgeving voldoet aan [FINAL_AUDIT_ROADMAP_1.1.4.md – sectie 2](FINAL_AUDIT_ROADMAP_1.1.4.md#2-testomgeving-eisen). Run: `BASE_URL=... WP_USER=... WP_PASSWORD=... npm run test:p2`. |
| **2. Uitvoeren** | Playwright-suite draaien; output bewaren. Handmatige UAT voor P0/P1 (zie [UAT_P0_Failsafe_1.1.3.md](UAT_P0_Failsafe_1.1.3.md), [UAT_AND_AUDIT_1.0.4_REPORT.md](UAT_AND_AUDIT_1.0.4_REPORT.md)). |
| **3. Rapport** | In [FINAL_AUDIT_ROADMAP_1.1.4.md](FINAL_AUDIT_ROADMAP_1.1.4.md) status en opmerkingen per audit-item invullen. Bij skip/fail: oorzaak noteren (omgeving vs. code). |
| **4. Acties** | **Omgeving:** versie/onboarding/bands/data aanpassen of tweede omgeving. **Code:** bugfix; daarna opnieuw testen. |
| **5. Herhalen** | Terug naar stap 1 (of 2) tot alle items **Geslaagd** zijn. |
| **6. Vastleggen** | Bij 100%: in audit-doc datum, omgeving en “Release 1.1.4 audit geslaagd” invullen; eventueel in CHANGELOG vermelden. |

---

## Snelle referentie

- **Audit-checklist en status:** [FINAL_AUDIT_ROADMAP_1.1.4.md](FINAL_AUDIT_ROADMAP_1.1.4.md)
- **P2 Playwright-tests:** `tests/e2e/p2-uat.spec.js`; run: `npm run test:p2`
- **P2 handmatige UAT:** [UAT_P2_Roadmap_1.1.4.md](UAT_P2_Roadmap_1.1.4.md)
- **P0 handmatige UAT:** [UAT_P0_Failsafe_1.1.3.md](UAT_P0_Failsafe_1.1.3.md)
