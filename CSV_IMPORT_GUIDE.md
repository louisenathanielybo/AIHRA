# CSV Import Guide for User Accounts

## Sample CSV File
A sample CSV file (`sample_users_import.csv`) has been created in the project root folder with 10 example users.

## CSV Format Requirements

### Header Row (must match exactly):
```
Employee Number,Email,First Name,Last Name,Middle Name,Role,Gender,Date of Birth (YYYY-MM-DD),Status
```

### Column Details:

1. **Employee Number** (Required)
   - Unique identifier for each employee
   - Example: `2024001`

2. **Email** (Required)
   - Valid email address
   - Must be unique
   - Example: `john.smith@company.com`

3. **First Name** (Required)
   - Example: `John`

4. **Last Name** (Required)
   - Example: `Smith`

5. **Middle Name** (Optional)
   - Can be left empty
   - Example: `Michael`

6. **Role** (Optional, defaults to "Employee")
   - Valid values: `Employee`, `Admin`, `HR`
   - Case-sensitive

7. **Gender** (Optional, defaults to "Male")
   - Valid values: `Male`, `Female`
   - Case-sensitive

8. **Date of Birth (YYYY-MM-DD)** (Optional, defaults to age 25)
   - Format: YYYY-MM-DD
   - Example: `1990-05-15`
   - Age must be between 18-65

9. **Status** (Optional, defaults to "Active")
   - Valid values: `Active`, `Deactivated`
   - Case-sensitive

## Default Values

- **Password**: Auto-generated as LastName + BirthYear (e.g., `Smith1990` for John Smith born in 1990)
  - Spaces are removed from last names (e.g., "Dela Cruz" becomes `DelaCruz1990`)
- **Profile Picture**: `default.png`
- **About**: Empty

## Important Notes

1. The CSV file must use UTF-8 encoding
2. Employee numbers and emails must be unique (duplicates will be skipped)
3. Invalid rows will be skipped with error messages
4. Age is automatically calculated from the Date of Birth
5. Maximum file size: 2MB
6. Accepted file extensions: .csv, .txt

## Testing the Import

1. Navigate to Account Management section
2. Click "Import CSV" button
3. Select `sample_users_import.csv` from the project root
4. Click "Import Accounts"
5. Review the success/error messages

## Icons Updated

- **Import CSV**: Now uses `fa-file-import` icon
- **Export CSV**: Now uses `fa-file-export` icon

These icons are more semantically appropriate for CSV file operations.
