# SQL Query Quality

The project includes practical SQL for CRUD, joins, grouping, aggregation, subqueries, filtering, and reporting.

See `database/sample_queries.sql` for 21 runnable examples.

## Concepts Covered

- `SELECT`, `INSERT`, `UPDATE`, `DELETE`
- `INNER JOIN` and `LEFT JOIN`
- `GROUP BY` and `HAVING`
- Aggregation with `COUNT`, `SUM`, and `AVG`
- Subqueries for above-average unemployment and employer activity
- Date filtering for applications and registrations
- Area filtering by district/upazila/ward
- Skill matching by job seeker skill and required job skill
- Views for repeated analytics

## Important Report Queries

- District-wise unemployment
- Application status summary
- Job posts by district
- Top available skills
- Top demanded skills
- Skill demand vs supply gap
- Employer activity count
- Training need score

## Safety Notes

Several newer pages use prepared statements. Older pages still contain some direct SQL strings; values are commonly escaped or cast to integers. Future improvement should convert all write actions and filter queries to prepared statements.
