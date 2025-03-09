import os
import re

def replace_htm_references_with_html_skip_python(repo_folder):
    """
    Searches all code files (excluding Python files) within the repository folder
    and its subfolders for references to '.htm' and replaces them with '.html'.

    Args:
        repo_folder (str): The path to the root folder of your GitHub repository.
    """

    if not os.path.isdir(repo_folder):
        print(f"Error: '{repo_folder}' is not a valid directory.")
        return

    code_extensions = ['.html', '.htm', '.css', '.js', '.php', '.xml', '.json', '.txt', '.svg', '.md', '.config', '.ini', '.yaml', '.yml'] # Add more if needed, remove .py
    htm_regex = re.compile(r'\.htm(?=["\'\s>]|$)') # Regex to find .htm followed by quote, space, >, or end of line

    total_files_modified = 0
    total_replacements = 0
    skipped_python_files = 0

    for root, _, files in os.walk(repo_folder):
        for filename in files:
            if filename.lower().endswith(".py"): # Skip Python files
                skipped_python_files += 1
                print(f"Skipping Python file: '{os.path.join(root, filename)}'")
                continue # Skip to the next file

            if any(filename.lower().endswith(ext) for ext in code_extensions):
                filepath = os.path.join(root, filename)
                modified = False
                replacements_in_file = 0

                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        file_content = f.read()

                    new_content, num_replacements = re.subn(htm_regex, '.html', file_content)
                    replacements_in_file = num_replacements

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
        print(f"\nSuccessfully modified {total_files_modified} files and updated a total of {total_replacements} '.htm' references to '.html'.")
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
        replace_htm_references_with_html_skip_python(repo_path)