# Design Document: Championship Results Feature

This document outlines the plan for implementing the Championship Results feature in the Digital Silat Portal.

## 1. Objectives
- Automatically ingest winner data from external scoring systems via an API.
- Store results for "Tanding" and "Seni" categories.
- Provide a public-facing page for each event to display these results.

## 2. Database Design
### New Table: `event_results`
| Column | Type | Description |
|--------|------|-------------|
| `id` | INT (PK, AI) | Unique identifier |
| `event_id` | INT (FK) | Reference to `events.id` |
| `category_type` | ENUM('tanding', 'seni') | Category classification |
| `class_name` | VARCHAR(100) | Match class (e.g., 'Kelas A Putra') |
| `rank` | INT | Winner rank (1, 2, or 3) |
| `winner_name` | VARCHAR(255) | Name of the athlete |
| `contingent` | VARCHAR(255) | Contingent/School/Team name |
| `created_at` | TIMESTAMP | Record creation time |

## 3. API Implementation
### Endpoint: `POST /api/push_results`
- **Security**: Requires `X-API-KEY` header.
- **Payload Format**:
  ```json
  {
    "event_id": 123,
    "results": [
      {
        "category_type": "tanding",
        "class_name": "Kelas A Putra",
        "rank": 1,
        "winner_name": "Ahmad",
        "contingent": "Sakti Team"
      }
    ]
  }
  ```
- **Logic**:
  1. Validate API Key.
  2. Verify if `event_id` exists.
  3. Clear existing results for that event (optional, based on "push" vs "append" preference).
  4. Batch insert new results.

## 4. Frontend Implementation
### Controller: `Event.php`
- New method `detail($id)`:
  - Fetches event data.
  - Fetches associated results from `event_results`.
  - Passes data to the new view.

### View: `application/views/event_detail.php`
- Layout: Bootstrap 5.
- Sections:
  - Event Header (Title, Date, Location).
  - Tabs/Sections for "Kategori Tanding" and "Kategori Seni".
  - Sortable tables for results.

### View: `application/views/landing_page.php`
- Update event cards/banners to link to `base_url('event/detail/' . $event['id'])`.

## 5. Admin Dashboard
- Add a section in the Admin Panel to view/delete results for a specific event.

---
**Status**: Ready for approval.
