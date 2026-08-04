---
applyTo: "**"
---
# Copilot Instructions
# This file contains instructions for GitHub Copilot to follow when generating code.

# webERP Coding Guidelines
# General prompt rules applying to PHP, CSS, and JS related files: 

- Do not commit changes. Leave commits to the user.
- Keep code simple, readable, and easy for business users and PHP newcomers to follow.
# - Do not cut long lines, unless specificaly defined in the user prompt.
- Do not delete any comment in the code, unless it contains wrong information or it is belonging to the code you are deleting.
- Do not add comments similar to //Added lines, //Removed lines, //Modified lines, etc.
- Do not change, modify, upgrade any other part of the code not related to the prompt.
- Trailing whitespaces: Remove all trailing whitespaces
- Indentation: Convert indentation spaces into tab with size 1 tab = 4 spaces.
- Line Length: For any line longer than 120 characters divide it into several lines to improve readability.
- Fix any potential Divide By Zero error.

## PHP

- Use `<?php` only. Omit the closing `?>` tag.
- Keywords lower case; constants `UPPER_CASE` with underscores; prefer literals over constants.
- Indent with tabs.
- Use long, descriptive PascalCase variables (`$LongVariableName`). `$i`, `$j`, `$k` allowed as counters.
- Wrap user-facing strings in `__('...')` with no trailing spaces inside.
- Reference superglobals directly: `$_POST['FieldName']` (single quotes).
- Use single-quoted PHP strings; concatenate variables. Use double quotes only for SQL strings.
- Use 1TBS braces; always use `{}` even for single-line blocks. Else on same line as closing brace.
- Use C-style comments (`/* ... */`). Keep comments current.
- Use long, descriptive PascalCase variables.
- PascalCase: All variables must be written in PascalCase style and fix the wrong ones. Except variables starting with $id*, $webERP*, $SQL*, $HTML* or counters like $i, $j.
- Binary Operators: ensure are surrounded by one space on each side
- Unary Operators: ensure no spaces after the operators
- Ternary Operators: Must have a space before and after the ? and the :
- Function Calls: No space between the function name and the opening parenthesis. No space after the opening parenthesis or before the closing parenthesis.

## HTML

- Lower-case tags and attributes.
- Use double quotes around HTML attribute values.
- Lay out tables and echo statements with line breaks/indentation for readability.

## SQL

- Write ANSI-compliant SQL. No backticks; no double-quoted string literals.
- Capitalise SQL keywords. Put each major clause and column on its own line.
- Quote all variables inside SQL with single quotes, even numbers.
# - Use `DB_query()`/`DB_escape_string()` abstractions; no direct DB driver calls in scripts.
- Minimise database round trips.