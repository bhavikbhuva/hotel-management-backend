🎯 Goal

Allow admin to upload JSON translation files (admin.json, web.json, app.json) for a selected language, validate them, and safely store them in Laravel’s resources/lang directory.

🧱 Tech Stack
	•	Laravel (latest)
	•	Filament (admin panel)
	•	Livewire (default Filament)
	•	No external translation packages

📁 File Structure (IMPORTANT)

Translations must be stored like:

resources/lang/{language_code}/admin.json
resources/lang/{language_code}/web.json
resources/lang/{language_code}/app.json

Example:
resources/lang/en/admin.json
resources/lang/guj/admin.json

🧠 Core Concept
	•	System is key-value based
	•	Example JSON:
{
“Save”: “Save”,
“Cancel”: “Cancel”
}
	•	Keys are predefined (English base)
	•	Admin only edits VALUES for other languages


🧩 Features to Build

1. Language Selection
	•	Dropdown/select for language (e.g. en, guj, ar)
	•	Language codes should be dynamic (can be hardcoded for now)


2. File Upload (Filament)

For each type:
	•	admin.json
	•	web.json
	•	app.json

Use Filament FileUpload component.

Constraints:
	•	Accept only JSON files
	•	Store temporarily in storage/app/temp/translations


3. Validation (VERY IMPORTANT)

After upload, before saving:

Must validate:
	1.	Valid JSON
	2.	Must be a flat object (no nested arrays/objects)
	3.	Keys must be strings
	4.	Values must be strings
	5.	Keys must not be empty

Reject examples:
{
“Save”: 123
}

{
“buttons”: {
“save”: “Save”
}
}

[
“Save”
]



4. Save Logic (CRITICAL)

DO NOT write directly.

Use atomic write:
	•	Read file from temp storage
	•	Validate
	•	Write to temp file:
resources/lang/{lang}/{file}.json.tmp
	•	Then rename → actual file

Example:
file_put_contents($tempPath, $content);
rename($tempPath, $finalPath);

⸻

5. Overwrite Behavior
	•	Uploaded file should completely overwrite existing file
	•	No merging logic


6. Directory Handling
	•	If language folder does not exist → create it
	•	Example:
resources/lang/guj/


7. UI/UX
	•	Clean Filament page
	•	Section for:
	•	Language selection
	•	Upload fields (admin/web/app)
	•	Submit button: “Save Translations”

8. Notifications

Use Filament Notification:
	•	Success → “Translations updated successfully”
	•	Error → show validation message

⚠️ Important Constraints
	•	Do NOT use database for translations
	•	Do NOT allow partial saves (all validation must pass before write)
	•	Do NOT allow nested JSON
	•	Must prevent corrupted writes (atomic replace required)

🧪 Edge Cases

Handle:
	•	Invalid JSON upload
	•	Empty file
	•	Duplicate uploads
	•	Missing keys (allowed for now)

🚀 Output Expectation

Provide:
	1.	Filament Page class
	2.	Blade view (if needed)
	3.	Form schema (FileUpload fields)
	4.	Save logic with validation
	5.	Clean, readable code

🧠 Future Scope (just keep in mind, don’t implement now)
	•	Key syncing with English base file
	•	UI-based editing instead of file upload
	•	DB-based translations for dynamic content

