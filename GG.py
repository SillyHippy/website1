import os
import re

html_file_path = "index.html"  # Path to your index.html file
js_directory_to_scan = "."  # Directory to scan for Javascript files (e.g., ".", "js", "wp-content/themes/yourtheme/js")
output_file_path = "unreferenced_js_files.txt"
delete_files = True  # Set to True to ENABLE file deletion (after review!)
delete_backup_files = False # Set to True to also delete backup files (.backup extension)

def find_javascript_files_in_html(html_filepath):
    """Parses HTML file and extracts Javascript file paths from <script src="..."> tags."""
    js_files = set()
    try:
        with open(html_filepath, 'r', encoding='utf-8') as f:
            html_content = f.read()
            script_tags = re.findall(r'<script.*?src=["\'](.*?\.js.*?)(?:[\?#].*)?["\'].*?</script>', html_content, re.IGNORECASE)
            for src in script_tags:
                js_files.add(os.path.normpath(src.split('?')[0].strip())) # Normalize paths
    except FileNotFoundError:
        print(f"Error: HTML file not found at '{html_filepath}'.")
    return js_files

def find_all_js_files_in_directory(directory):
    """Recursively finds all .js files in a directory."""
    all_js_files = set()
    for root, _, files in os.walk(directory):
        for file in files:
            if file.lower().endswith(".js"):
                filepath = os.path.join(root, file)
                all_js_files.add(os.path.normpath(filepath)) # Normalize paths
    return all_js_files

def identify_unreferenced_js_files(html_js_files, all_js_files):
    """Identifies Javascript files in directory that are not referenced in HTML."""
    unreferenced_files = all_js_files - html_js_files
    return unreferenced_files

def delete_files_if_enabled(files_to_delete, delete_flag, file_type_desc="Javascript"):
    """Deletes files if the delete_flag is set to True, otherwise just lists them."""
    if not delete_flag:
        print(f"\n{file_type_desc} files that WOULD BE deleted (deletion is DISABLED):")
    else:
        print(f"\nDeleting {file_type_desc} files:")

    if not files_to_delete:
        print("  No unreferenced files found.")
        return

    for filepath in files_to_delete:
        print(f"  {filepath}")
        if delete_flag:
            try:
                os.remove(filepath)
                print(f"    Deleted.")
            except Exception as e:
                print(f"    Error deleting: {e}")

    if not delete_flag:
        print("\nTo ENABLE deletion, set 'delete_files = True' in the script.")


if __name__ == "__main__":
    print("WARNING: This script can DELETE files. BACKUP YOUR WEBSITE FIRST!\n")

    html_referenced_js = find_javascript_files_in_html(html_file_path)
    all_site_js_files = find_all_js_files_in_directory(js_directory_to_scan)
    unreferenced_js = identify_unreferenced_js_files(html_referenced_js, all_site_js_files)

    try:
        with open(output_file_path, 'w', encoding='utf-8') as outfile:
            outfile.write("Unreferenced Javascript Files (from index.html):\n")
            outfile.write("--------------------------------------------\n\n")
            if not unreferenced_js:
                outfile.write("No unreferenced Javascript files found.\n")
            else:
                for filepath in sorted(list(unreferenced_js)):
                    outfile.write(f"{filepath}\n")
        print(f"List of unreferenced Javascript files written to: '{output_file_path}'")
    except Exception as e:
        print(f"Error writing to output file: {e}")


    delete_files_if_enabled(unreferenced_js, delete_files, "Javascript")

    if delete_backup_files:
        backup_files_to_delete = set(f for f in all_site_js_files if f.lower().endswith(".js.backup"))
        delete_files_if_enabled(backup_files_to_delete, delete_backup_files, "Backup Javascript")


    print("\nScript execution finished.")