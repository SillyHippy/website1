import os

def replace_htm_references_with_html_simple(repo_folder):
    """
    Simplest version: Replaces all instances of '.htm' with '.html' in code files
    (excluding Python files).  Uses plain string replacement, no regex.

    Args:
        repo_folder (str): The path to your GitHub repository.
    """

    if not os.path.isdir(repo_folder):
        print(f"Error: '{repo_folder}' is not a valid directory.")
        return

    code_extensions = ['.html', '.htm', '.css', '.js', '.php', '.xml', '.json', '.txt', '.svg', '.md', '.config', '.ini', '.yaml', '.yml'] # Add more if needed, remove .py

    total_files_modified = 0
    total_replacements = 0
    skipped_python_files = 0

    for root, _, files in os.walk(repo_folder):
        for filename in files:
            if filename.lower().endswith(".py"): # Skip Python files
                skipped_python_files += 1
                print(f"Skipping Python file: '{os.path.join(root, filename)}'")
                continue

            if any(filename.lower().endswith(ext) for ext in code_extensions):
                filepath = os.path.join(root, filename)
                modified = False
                replacements_in_file = 0

                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        file_content = f.read()

                    old_content = file_content  # Keep original content for comparison
                    new_content = file_content.replace(".htm", ".html") # Simple string replace
                    replacements_in_file = new_content.count(".html") - old_content.count(".html") # Rough count

                    if replacements_in_file > 0:
                        with open(filepath, 'w', encoding='utf-8') as f:
                            f.write(new_content)
                        modified = True
                        total_files_modified += 1
                        total_replacements += replacements_in_file
                        print(f"Modified: '{filepath}' - {replacements_in_file} references updated.")

                except UnicodeDecodeError:
                    print(f"Warning: Could not decode file '{filepath}' with UTF-8, skipping content modification.")
                except Exception as e:
                    print(f"Error processing file '{filepath}': {e}")

    if total_files_modified > 0:
        print(f"\nSuccessfully modified {total_files_modified} files and updated approximately {total_replacements} '.htm' references to '.html'.") # Note "approximately"
    else:
        print("\nNo files modified. No '.htm' references found in code files (excluding Python files).")

    if skipped_python_files > 0:
        print(f"Skipped {skipped_python_files} Python files.")


if __name__ == "__main__":
    repo_path = input("Enter the path to your GitHub repository folder: ")
    repo_path = repo_path.strip()

    if not repo_path:
        print("Repository path cannot be empty.")
    else:
        replace_htm_references_with_html_simple(repo_path)