# GDMO Feature Implementation Plan

## Current Repository Assessment

The current repository does not contain the target application source code, framework files, database migrations, route definitions, controllers, models, menu configuration, or existing salary/report modules. Because of that, the GDMO feature cannot be implemented directly in this repository without first adding or locating the actual application codebase.

If this feature belongs to another local project opened in VS Code, that project should be provided instead of this profile repository.

## Can Any Module Be Implemented Without Changing Existing Code?

No application module can be implemented here without changing or adding code because there is no existing application structure to extend.

In a real application codebase, some parts may be added as independent modules with minimal impact on existing code:

- New GDMO database tables.
- New GDMO routes/controllers/views.
- New GDMO menu section.
- New salary calculation service.
- New Excel export utilities for GDMO reports.

However, some integration points usually require existing-code changes:

- Main navigation/menu registration.
- Authentication/authorization permissions.
- Existing report menu changes for TDS and EPF employee-type filtering.
- Salary lock integration if salary data already exists elsewhere.

## Proposed Main Menu

Add a new main menu item:

- **GDMO**
  - Rate Master
  - TDS Master
  - GDMO Master
  - Attendance
  - Create Salary
  - View Salary
  - Salary Slip
  - TDS Report

## Proposed Modules

### 1. Rate Master

Purpose: Maintain hourly rates used for salary calculation.

Fields:

- Shift Rate, hourly amount.
- Emergency Rate, hourly amount.
- Effective from date, recommended.
- Active/inactive status, recommended.

Notes:

- Although the requirement lists Shift Rate and Emergency Rate together, keeping them as typed rate records allows cleaner dropdown use.
- If historical salary recalculation matters, salaries should store copied rate values at creation time instead of only referencing rate IDs.

### 2. TDS Master

Purpose: Maintain TDS percentage used in GDMO salary calculation.

Fields:

- TDS percentage.
- Effective from date, recommended.
- Active/inactive status, recommended.

Salary formula:

```text
TDS Amount = Total Amount * TDS%
Net Amount = Total Amount - TDS Amount
```

### 3. GDMO Master

Purpose: Maintain GDMO doctors/persons and their assigned rates.

Fields:

- GDMO name.
- Shift rate dropdown.
- Emergency rate dropdown.
- Active/inactive status.

Dropdown sources:

- Shift Rate: Rate Master records where type is shift.
- Emergency Rate: Rate Master records where type is emergency.

### 4. Attendance

Purpose: Capture monthly shift-wise attendance per GDMO.

Screen inputs:

- Select month.
- Enter shift-wise data.

Grid columns:

| Field | Meaning |
| --- | --- |
| GDMO Name | Selected/loaded from GDMO Master |
| Shift A (In Days) | Number of Shift A days |
| Shift B (In Days) | Number of Shift B days |
| Shift C (In Days) | Number of Shift C days |
| Emergency (In Days) | Number of emergency days |
| Extra (In Hour) | Extra hours |

Validation:

- Month is required.
- GDMO is required.
- Shift and extra values must be numeric and non-negative.
- One attendance record per GDMO per month should be enforced.
- If salary is locked for the month, attendance modifications should be blocked.

### 5. Create Salary

Purpose: Generate salary sheet for selected month using attendance, GDMO rates, and TDS percentage.

Input:

- Select month.

Salary calculation columns:

| No. | Column | Formula / Source |
| --- | --- | --- |
| 1 | Name | GDMO Master |
| 2 | Month | Selected month |
| 3 | Shift Rate | GDMO assigned shift rate |
| 4 | Shift A Hour | Shift A days * 6 |
| 5 | Shift B Hour | Shift B days * 6 |
| 6 | Shift C Hour | Shift C days * 12 |
| 7 | Shift Total | Shift A Hour + Shift B Hour + Shift C Hour |
| 8 | Shift Amount | Shift Total * Shift Rate |
| 9 | Emergency Rate | GDMO assigned emergency rate |
| 10 | Emergency Hour | Emergency days * 12 |
| 11 | Emergency Total | Emergency Rate * Emergency Hour |
| 12 | Extra Hour | Attendance extra hours |
| 13 | Extra Amount | Shift Rate * Extra Hour |
| 14 | Total Amount | Shift Amount + Emergency Total + Extra Amount |
| 15 | TDS | Total Amount * TDS% |
| 16 | Net Amount | Total Amount - TDS |

Important clarification:

- The original note says `13 Extra Amount (3 *12)`. Based on the numbered formula list, item 3 is Shift Rate and item 12 is Extra Hour, so this plan interprets Extra Amount as `Shift Rate * Extra Hour`.

Generation behavior:

- If salary for the selected month does not exist, create it.
- If salary already exists and is unlocked, allow regenerate/update after confirmation.
- If salary is locked, block regeneration.
- Store calculated values as snapshots so future rate/TDS changes do not silently alter old salary sheets.

### 6. View Salary

Purpose: Display generated salary sheet by month.

Features:

- Select month.
- Show full salary sheet.
- Export salary sheet to Excel.
- If locked, display locked status.

### 7. Salary Slip

Purpose: Generate individual GDMO salary slip.

Filters:

- Month.
- GDMO name.

Output:

- Name.
- Month.
- Shift hours and amount.
- Emergency hours and amount.
- Extra hours and amount.
- Gross/total amount.
- TDS.
- Net amount.

Exports:

- Excel, required.
- PDF, optional if the existing application supports it.

### 8. TDS Report

Purpose: Display TDS deduction report for GDMO salaries.

Filters:

- Month range.

Columns:

| Column | Meaning |
| --- | --- |
| Sl.No. | Serial number |
| GDMO Name | GDMO name |
| Salary Amount | Gross/total amount |
| TDS | TDS amount |
| Total | Net amount or gross total, to be clarified |

Exports:

- Excel.

Clarification needed:

- The report column named `Total` could mean gross total, net amount, or salary amount plus/minus TDS. This should be confirmed.

### 9. Existing TDS & EPF Report Enhancement

Requirement:

- Display TDS & EPF Report for all employee.
- Same as TDS Report.
- Select Month and Employee Type:
  - Permanent.
  - Contractual.
  - Apprentice.

Implementation approach:

- Add employee type filter to existing report if the application already has an employee model and salary module.
- If existing modules cannot be modified safely, add a separate report screen using existing salary data sources.

Clarification needed:

- Should GDMO be included as an employee type in this report, or should GDMO remain separate?

### 10. Lock Salary

Purpose: Prevent modifications after salary is finalized.

Screen:

- Select month.
- Lock salary sheet.

Rules:

- Once locked, no attendance modification for that month.
- Once locked, no salary regeneration/modification for that month.
- Reports and salary slips remain viewable/exportable.
- Unlock should require admin permission if supported.

## Proposed Database Tables

### gdmo_rates

Stores shift and emergency rates.

Suggested columns:

- id
- rate_type: `shift` or `emergency`
- rate_amount
- effective_from
- is_active
- created_at
- updated_at

### gdmo_tds_rates

Stores TDS percentage.

Suggested columns:

- id
- tds_percentage
- effective_from
- is_active
- created_at
- updated_at

### gdmo_masters

Stores GDMO profiles.

Suggested columns:

- id
- name
- shift_rate_id
- emergency_rate_id
- is_active
- created_at
- updated_at

### gdmo_attendance

Stores monthly attendance data.

Suggested columns:

- id
- gdmo_id
- salary_month
- shift_a_days
- shift_b_days
- shift_c_days
- emergency_days
- extra_hours
- created_at
- updated_at

Recommended constraint:

- Unique key on `gdmo_id + salary_month`.

### gdmo_salaries

Stores generated salary sheet snapshot.

Suggested columns:

- id
- gdmo_id
- salary_month
- gdmo_name_snapshot
- shift_rate_snapshot
- emergency_rate_snapshot
- tds_percentage_snapshot
- shift_a_days
- shift_b_days
- shift_c_days
- emergency_days
- extra_hours
- shift_a_hours
- shift_b_hours
- shift_c_hours
- shift_total_hours
- shift_amount
- emergency_hours
- emergency_total
- extra_amount
- total_amount
- tds_amount
- net_amount
- is_locked
- generated_at
- created_at
- updated_at

Recommended constraint:

- Unique key on `gdmo_id + salary_month`.

### gdmo_salary_locks

Stores month-level locks.

Suggested columns:

- id
- salary_month
- is_locked
- locked_by
- locked_at
- unlocked_by
- unlocked_at
- created_at
- updated_at

## Suggested Backend Structure

Exact paths depend on the actual framework. For a typical MVC/PHP application:

```text
controllers/GdmoRateMasterController.php
controllers/GdmoTdsMasterController.php
controllers/GdmoMasterController.php
controllers/GdmoAttendanceController.php
controllers/GdmoSalaryController.php
controllers/GdmoReportController.php
models/GdmoRate.php
models/GdmoTdsRate.php
models/GdmoMaster.php
models/GdmoAttendance.php
models/GdmoSalary.php
models/GdmoSalaryLock.php
services/GdmoSalaryCalculator.php
exports/GdmoSalaryExport.php
exports/GdmoTdsReportExport.php
views/gdmo/rate-master
views/gdmo/tds-master
views/gdmo/gdmo-master
views/gdmo/attendance
views/gdmo/create-salary
views/gdmo/view-salary
views/gdmo/salary-slip
views/gdmo/tds-report
```

## Salary Calculator Pseudocode

```text
for each attendance row in selected month:
    gdmo = attendance.gdmo
    shift_rate = gdmo.shift_rate.amount
    emergency_rate = gdmo.emergency_rate.amount
    tds_percent = active_tds_percent

    shift_a_hours = shift_a_days * 6
    shift_b_hours = shift_b_days * 6
    shift_c_hours = shift_c_days * 12
    shift_total_hours = shift_a_hours + shift_b_hours + shift_c_hours
    shift_amount = shift_total_hours * shift_rate

    emergency_hours = emergency_days * 12
    emergency_total = emergency_hours * emergency_rate

    extra_amount = extra_hours * shift_rate

    total_amount = shift_amount + emergency_total + extra_amount
    tds_amount = total_amount * tds_percent / 100
    net_amount = total_amount - tds_amount

    save salary snapshot
```

## Excel Export Requirements

Every data entry and report screen should provide Excel export where applicable:

- Rate Master export.
- TDS Master export.
- GDMO Master export.
- Attendance export.
- View Salary export.
- Salary Slip export.
- TDS Report export.
- Existing TDS & EPF report export if enhanced.

Import from Excel was not requested and is not included unless confirmed.

## Permission Recommendations

Suggested permissions:

- `gdmo.rate.view`
- `gdmo.rate.create`
- `gdmo.rate.update`
- `gdmo.tds.view`
- `gdmo.tds.create`
- `gdmo.tds.update`
- `gdmo.master.view`
- `gdmo.master.create`
- `gdmo.master.update`
- `gdmo.attendance.view`
- `gdmo.attendance.create`
- `gdmo.attendance.update`
- `gdmo.salary.create`
- `gdmo.salary.view`
- `gdmo.salary.lock`
- `gdmo.report.view`
- `gdmo.export`

## Implementation Phases

### Phase 1: Foundation

- Add database migrations/tables.
- Add models/entities.
- Add menu item and routes.
- Add permissions if the application has role-based access control.

### Phase 2: Masters

- Implement Rate Master.
- Implement TDS Master.
- Implement GDMO Master.
- Add Excel exports for master screens.

### Phase 3: Attendance

- Implement month selection.
- Implement GDMO attendance grid.
- Add validation and duplicate prevention.
- Add Excel export.

### Phase 4: Salary Generation

- Implement salary calculator service.
- Implement Create Salary screen.
- Store salary snapshots.
- Prevent modification when month is locked.

### Phase 5: Salary Reports

- Implement View Salary.
- Implement Salary Slip.
- Implement GDMO TDS Report with month range.
- Add Excel exports.

### Phase 6: Existing Report and Locking

- Enhance existing TDS & EPF report with month and employee-type filters.
- Implement Lock Salary screen.
- Enforce lock in attendance and salary generation flows.

## Open Questions

1. Which actual application repository/framework should this be implemented in?
2. What database is used: MySQL, PostgreSQL, SQL Server, or something else?
3. Is the project CodeIgniter, Laravel, plain PHP, Node.js, or another framework?
4. Should GDMO users be linked to existing employee records, or should GDMO Master remain completely separate?
5. Should Rate Master allow multiple historical rates by effective date, or only one active shift rate and one active emergency rate?
6. Should TDS Master be global for all GDMOs, or can it vary per GDMO?
7. For Extra Amount, should the formula be `Shift Rate * Extra Hour`?
8. In TDS Report, should the `Total` column show net amount after TDS or gross salary amount?
9. Should salary lock be reversible by admin, or permanent?
10. Should Excel export mean `.xlsx`, `.csv`, or both?
