import os
import re

def replace_index_htm_courier_references(repo_folder):
    """
    Searches all code files (excluding Python files) within the repository folder
    and its subfolders for references to 'index.htm#Courier' and replaces them with 'index.html#Courier'.
    USING REGEX TO HANDLE WHITESPACE IN HREF ATTRIBUTE.

    Args:
        repo_folder (str): The path to the root folder of your GitHub repository.
    """

    if not os.path.isdir(repo_folder):
        print(f"Error: '{repo_folder}' is not a valid directory.")
        return

    code_extensions = ['.html', '.htm', '.css', '.js', '.php', '.xml', '.json', '.txt', '.svg', '.md', '.config', '.ini', '.yaml', '.yml'] # Add more if needed, remove .py

    total_files_modified = 0
    total_replacements = 0
    skipped_python_files = 0

    old_reference_regex = re.compile(r"index\.htm\s*#Courier")  # Regex to match index.htm + optional whitespace + #Courier
    new_reference = "index.html#Courier"

    for root, _, files in os.walk(repo_folder):
        for filename in files:
            filepath = os.path.join(root, filename) # Full file path for debugging prints

            if filename.lower().endswith(".py"): # Skip Python files
                skipped_python_files += 1
                print(f"DEBUG: Skipping Python file: '{filepath}'") # DEBUG PRINT
                continue # Skip to the next file

            if any(filename.lower().endswith(ext) for ext in code_extensions):
                print(f"DEBUG: Processing code file: '{filepath}'") # DEBUG PRINT
                modified = False
                replacements_in_file = 0

                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        file_content = f.read()

                    print(f"DEBUG: Content BEFORE replacement (first 100 chars): '{file_content[:100]}...'") # DEBUG PRINT - Before

                    old_content = file_content  # Keep original content for comparison
                    new_content = re.sub(old_reference_regex, new_reference, file_content) # Use re.sub with regex
                    replacements_in_file = len(re.findall(old_reference_regex, old_content)) # Count using regex findall

                    print(f"DEBUG: Content AFTER replacement (first 100 chars): '{new_content[:100]}...'") # DEBUG PRINT - After

                    if replacements_in_file > 0:
                        with open(filepath, 'w', encoding='utf-8') as f:
                            f.write(new_content)
                        modified = True
                        total_files_modified += 1
                        total_replacements += replacements_in_file
                        print(f"Modified: '{filepath}' - {replacements_in_file} references updated in: '{filepath}'")
                    else:
                        print(f"DEBUG: No replacements made in '{filepath}'") # DEBUG PRINT - No replace

                except UnicodeDecodeError:
                    print(f"Warning: Could not decode file '{filepath}' with UTF-8, skipping content modification: '{filepath}'")
                except Exception as e:
                    print(f"Error processing file '{filepath}': {e}")

    if total_files_modified > 0:
        print(f"\nSuccessfully modified {total_files_modified} files and updated approximately {total_replacements} references from '{old_reference}' to '{new_reference}'.") # Note "approximately"
    else:
        print(f"\nNo files modified. No references to '{old_reference}' found in code files (excluding Python files).")

    if skipped_python_files > 0:
        print(f"Skipped {skipped_python_files} Python files.")


if __name__ == "__main__":
    repo_path = input("Enter the path to your GitHub repository folder: ")
    repo_path = repo_path.strip()

    if not repo_path:
        print("Repository path cannot be empty.")
    else:
        replace_index_htm_courier_references(repo_path)