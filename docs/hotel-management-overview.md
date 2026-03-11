# quick over view
# Hotel Management Platform

## Overview
This project is a multi-property hotel booking platform designed to be sold on CodeCanyon.

## Application Modes
The system supports two operating modes:

### Single Property Mode
A single business managing multiple branches.

### Multi Property Mode (Marketplace)
Multiple partners listing properties on the platform.

## Core Modules
Authentication
Geography
Property System
Room & Inventory
Booking Engine
Financial Engine
Search System
Customer Features
Platform Utilities

## Coding Architecture
Controllers → Services → Actions

Controllers remain thin.
Services orchestrate logic.
Actions perform atomic operations.

FormRequests handle validation.

## Booking Principles
- Inventory managed per room type per date
- Inventory locking during payment
- Booking snapshot stored
- Cancellation policies applied at booking time

## Financial Principles
- Commission calculated on original price
- Tax calculated on discounted price
- Partner wallet credited after checkout
- Refunds processed via gateway if possible


# High-level overview

These documents together define a full multi‑property hotel booking platform with three main interfaces:

- Customer app/web (guests)

- Partner Panel (property owners)

- Admin Panel (platform operators)

Plus a set of cross‑cutting flows: platform modes, booking/revenue/refund logic, wallets, promos, tax/commission, inventory locking, privacy, and analytics.

The system can run either as a single‑property owner site or as a multi‑property marketplace (like OYO/Airbnb).

Customer side (Guest App & Web)

Customers use a mobile‑first app or web to:

- Register/login using email, phone (OTP), Google/Apple, manage profile, guest profiles, and security (password reset, logout from all devices, account deletion).

- Search properties by city/area or “Near Me” using Google Places, with dates and guest count. Results support filters (price, star rating, amenities, property type, offers) and list/map views.

- View rich property detail pages: photos, room types, amenities, rules, policies, nearby places, verified badges, and reviews from completed stays only.

- Book stays via a single‑room‑type per booking flow: select dates, room type, quantity, guest details; apply one promo code; pay via UPI/cards/net banking/wallet. Booking is created only after successful payment.

- Manage bookings in “My Bookings”: view details, cancel if allowed by the cancellation policy, see refund amount/timeline, and quickly rebook completed stays. Bookings move through statuses: Pending Payment → Confirmed → Checked‑in → Completed or Cancelled (with optional No‑Show flag).

- Use a non‑withdrawable wallet: credited by refunds and rewards (e.g., Refer & Earn), debited during new bookings. Wallet + gateway mixed payments have strict refund allocation back to original sources.

- Refer friends (Refer & Earn): unique code/link, reward to referrer (and optionally referee) after first successful booking; rewards reversed if the booking is later refunded.

- Access informational Support & Help Center (FAQs, how‑to, policies), manage notification preferences, favorites/wishlist, and privacy/data controls including account deletion with anonymization of transactional data.

Key customer rules and validations:

- Must be logged in to book.

- Dates cannot be modified after confirmation.

- Cancellation only allowed before check‑in; no post check‑in refunds; no partial stays.

- Reviews allowed only for Completed bookings (no reviews for cancelled or no‑show).

- Inventory lock during payment to prevent double booking.

Partner side (Partner Panel)

Partners (property owners/managers) use a web panel to:

- Create a partner account, authenticate, and go through KYC/verification. They select property owner type(s) (hotel/villa/homestay etc.), upload required documents, and wait for admin approval if verification is enabled.

- Manage properties: add/edit properties with admin‑configured fields and documents (country + property‑type based). Properties start in Pending Verification; admin approves/rejects/asks for correction. Major updates can trigger re‑verification.

- Configure room types, pricing, and availability:

 ▫ For hotel/hostel/apartments: multiple room types (Deluxe, Suite…) with occupancy, facilities, base/seasonal pricing, date‑wise availability.

 ▫ For villa/homestays: bookable as a single unit, with property‑level pricing and availability.

 ▫ Add property rules, instructions, nearby facilities, FAQs and details for guests.

- Manage bookings: see all bookings for their properties, filter by property, date, status; view full booking details; mark Checked‑in/Checked‑out; initiate cancellations where permitted by admin‑controlled rules.

- Handle finances via a property wallet:

 ▫ Each property has its own wallet tied to bank details.

 ▫ Wallet is credited when bookings become revenue‑valid (on check‑in) or when a pre‑check‑in cancellation yields retained revenue.

 ▫ Partners see pending vs available balances, transaction history, and can request payouts; admin controls approval.

- Manage reviews and ratings: see guest reviews (only from verified completed stays), respond publicly, and flag reviews for moderation (abusive, false, etc.). Admin decides on visibility.

- Use a partner‑focused Support & Help Center (knowledge base only), receive email/in‑panel notifications about bookings, payouts, verification updates, and manage settings like business info, notification preferences, security, payout details, and policy acknowledgments.

- Access analytics and reporting: dashboards for bookings, revenue, occupancy, ARR, cancellation ratios, rating trends, and downloadable reports (payout invoices, transaction history, property performance).

Partner account deletion must remove PII/business details, revoke access, ensure no active properties/booking/payouts/disputes, keep anonymized financial/booking records, prevent identifier reuse, and follow a soft‑delete period with full audit logging.

Admin side (Admin Panel)

The Admin Panel is the central control system used by Super Admin and Admin Employees.

Core responsibilities:

- Authentication & staff: admin login with optional 2FA, password reset, secure sessions. Role & Permission Management to define granular access; Staff Management to create/edit/suspend internal users (e.g., Onboarding Officer, Finance Officer, Support, Content Manager). All actions are audit‑logged.

- System configuration: choose platform mode (Single Property vs Multi Property), configure global settings, languages, currencies, taxes, commissions, payout and refund timelines, verification rules, maintenance mode, and branding.

Key modules:

1. Mode & property structure

 ▫ Property Management Configuration: define property types (Hotel, Villa, etc.).

 ▫ Country Management: define countries with their own base currency, commission defaults, taxes, allowed property types, and required property documents.

 ▫ City Management: define cities with country/state, polygon boundaries for precise mapping and “Nearby” search; deactivating a city hides its properties from new searches.

2. Financial configuration

 ▫ Commission Management: country default → property‑type overrides → partner‑country overrides (highest priority). Commission always on room amount.

 ▫ Tax Management: taxes defined per country + property type; system collects and keeps tax separate from partner wallet.

 ▫ Currency Management: per‑country base currency; prices stored only in base currency; optional display currency via daily‑refreshed FX rates; checkout always in base currency.

 ▫ Promo Code & Offer Management: flexible promo codes with scope (country, city, property type, customer segment), auto‑apply/manual, first‑booking promos; only one promo per booking; platform fully absorbs promo cost; partner earnings unaffected.

3. Onboarding & content

 ▫ Partner Management: configure onboarding fields and documents; review partner/property‑owner submissions; approve/reject; suspend/reactivate partners (which cascades to properties). Set partner bank details and commission overrides.

 ▫ Property Registration Fields: configurable per country + property type; support text, dropdown, checkbox, file upload; changes affect new registrations (existing may require revalidation).

 ▫ Facilities & Amenities, Property Rules: admin‑standardized lists used for property setup and as search filters (e.g., Pets Allowed, Smoking Allowed, Bachelor Allowed); partners can only select from these.

 ▫ Property Verification: document‑driven verification per property with approve/soft‑reject/reject; properties stay inactive until approved.

 ▫ Nearby Places & Attractions: auto‑fetched via Google Places post‑approval and stored; admin can refresh/edit.

 ▫ Banner Management, Blogs, FAQs, Events & Facilities: admin‑managed marketing content and knowledge base for customers and partners.

4. Bookings, payments & wallets

 ▫ Booking Management: oversee bookings, handle refunds, cancellations, and disputes according to configured policies.

 ▫ Payments & Finance: manage payment gateways, settlement rules, partner payout requests, and financial accuracy.

 ▫ User Management: manage customers, partners, and admin staff; activate/deactivate accounts, reset passwords, view related data.

5. Policies, support, communication

 ▫ Cancellation & Refund Policy: default policies per country + property type, with optional partner‑specific proposals that require approval. Policy is locked at booking time; no post‑stay refunds allowed.

 ▫ Terms & Conditions, Privacy Policy, Cancellation policy: system‑wide, language‑wise.

 ▫ Data Privacy & Compliance: handle export/deletion requests; anonymize or delete per policy, log all actions.

 ▫ Support & Dispute Handling: manage support tickets, escalations, and SLAs via settings.

 ▫ Notifications & Communication: send push/email notifications to customers and partners (promotions, updates, alerts).

6. Monitoring, analytics & audit

 ▫ Review Monitoring & Management: moderate customer reviews, handle partner removal requests, auto‑remove reviews for refunded bookings; full audit of changes.

 ▫ Reports Management: extensive tabular, exportable reports (bookings, cancellations, customers, wallets, referrals, partners, property verification, payouts, revenue, commission, tax, refunds, transactions, feedback, logins, notifications, etc.).

 ▫ Analytics Module: dashboards for bookings, revenue/commission/refunds, partner performance, customer behavior, support efficiency, property/city analytics, optional real‑time KPIs. Role‑based visibility and drill‑down to reports.

 ▫ Audit & Activity Logs: track logins, role changes, verification actions, exports, and configuration changes.

Platform modes & configuration logic

There are two main operating modes:

1. Single Property Mode (single owner)

 ▫ No marketplace: partner onboarding, commission, partner wallet, and payouts are disabled.

 ▫ One non‑editable property type.

 ▫ Admin manages everything: room types, inventory, pricing, facilities, rules, marketing modules (Events & Facilities, Inquiry).

 ▫ Promo codes have no property‑type conditions; they simply apply to the single property.

 ▫ Customer features (search, booking, wallet, promos, cancellation, reviews) still apply, but all inventory is effectively under one owner.

2. Multi Property Mode (marketplace)

 ▫ Multiple partners, property types, property wallets, commissions, payouts, and partner‑level overrides.

 ▫ Partner registration and property add flows use dynamic configuration by country + property type (fields, rules, default cancellation policies, facilities).

 ▫ Commission hierarchy and cancellation policy overrides apply at partner and country levels.

Mode selection at installation is permanent: single → can be upgraded to multi (with existing property treated as one partner); multi → cannot revert to single.

Booking, revenue, promo, refund & wallet logic (core flows)

The documents define a detailed financial engine:

- Booking lifecycle

 ▫ Booking is created only after successful payment.

 ▫ Inventory is locked when user hits “Pay Now” (room type + date range + quantity; time‑bound lock).

 ▫ On check‑in date, booking becomes revenue‑valid; no cancellations or refunds allowed after this point; no partial stay logic; no‑show treated as completed for revenue purposes.

- Commission & tax

 ▫ Commission always calculated on room amount (original, not discounted).

 ▫ Tax defined per country + property type; calculated on discounted room amount (after promo).

 ▫ Tax is stored in system ledger, not shared with partner, not part of property wallet.

- Promo codes

 ▫ Discount applied on room amount before tax.

 ▫ Only one promo per booking; system picks the best/priority promo when multiple apply.

 ▫ Promo discounts are 100% platform cost; partner net revenue is room amount minus commission regardless of promo.

- Refunds

 ▫ Cancellation allowed only pre‑check‑in; refund % based on booked policy locked at booking time.

 ▫ Standard model:

 ⁃ Retained Room = R × (1 − X)

 ⁃ Retained Tax = Retained Room × T

 ⁃ Commission = Retained Room × C

 ⁃ Property Wallet Credit = Retained Room − Commission

 ⁃ Refund Amount = Original Total − (Retained Room + Retained Tax)

 ▫ When promo is applied: same logic, but R is original room amount; promo does not reduce retained base.

 ▫ Refund is processed within admin‑configured days; once processed, revenue and wallet credits are final.

- Wallet + Gateway refunds

 ▫ If user paid with wallet + gateway:

 ⁃ Refund goes to wallet first up to original wallet contribution.

 ⁃ Remaining goes back via gateway.

 ⁃ Neither source can be refunded more than it originally paid.

- Property wallet

 ▫ For each property (in multi‑property mode), wallet is credited:

 ⁃ On check‑in (normal bookings), or

 ⁃ Immediately when a pre‑check‑in cancellation yields retained revenue.

 ▫ Partners can request withdrawal; admin approves and transfers to bank.

- Customer wallet

 ▫ Non‑withdrawable balance, used to pay for bookings.

 ▫ Credited by refunds and referral rewards; debited at payment time.

 ▫ Cannot go negative.

Privacy, data deletion, and compliance

Both customers and partners have data privacy support:

- Account deletion flows remove PII and sensitive documents, revoke sessions and access.

- Transactional/financial records (bookings, payments, disputes, reviews) are retained but anonymized for audit and legal compliance.

- Soft‑deletion period (e.g., 30 days) before permanent anonymization.

- Identifiers (email, phone, business registration) are blocked from reuse to prevent fraud or referral abuse.

- All export/deletion actions are logged.

In essence, the documents describe a robust, highly configurable hotel booking system that can run as a single‑hotel engine or as a full marketplace, with clear separation of concerns for customers, partners, and admins, and carefully defined financial, operational, and compliance flows.


