import os
import re

def replace_domain_references(repo_folder, old_domain, new_domain):
    """
    Searches all code files within the repository folder and its subfolders
    for references to the old domain and replaces them with the new domain.

    Args:
        repo_folder (str): The path to the root folder of your GitHub repository.
        old_domain (str): The domain name to be replaced (e.g., "justlegalsolutions.org").
        new_domain (str): The domain name to replace with (e.g., "justlegalsolutions.org").
    """

    if not os.path.isdir(repo_folder):
        print(f"Error: '{repo_folder}' is not a valid directory.")
        return

    code_extensions = ['.html', '.html', '.css', '.js', '.py', '.php', '.xml', '.json', '.txt', '.svg', '.md', '.config', '.ini', '.yaml', '.yml'] # Add more if needed
    domain_regex = re.compile(re.escape(old_domain)) # Regex to find the old domain, escape special chars in domain

    total_files_modified = 0
    total_replacements = 0

    for root, _, files in os.walk(repo_folder):
        for filename in files:
            if any(filename.lower().endswith(ext) for ext in code_extensions):
                filepath = os.path.join(root, filename)
                modified = False
                replacements_in_file = 0

                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        file_content = f.read()

                    new_content, num_replacements = re.subn(domain_regex, new_domain, file_content)
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
        print(f"\nSuccessfully modified {total_files_modified} files and updated a total of {total_replacements} references from '{old_domain}' to '{new_domain}'.")
    else:
        print(f"\nNo files modified. No references to '{old_domain}' found in code files.")

if __name__ == "__main__":
    repo_path = input("Enter the path to your GitHub repository folder: ")
    repo_path = repo_path.strip()

    old_domain_to_replace = "justlegalsolutions.org"
    new_domain_replacement = "justlegalsolutions.org"

    if not repo_path:
        print("Repository path cannot be empty.")
    else:
        replace_domain_references(repo_path, old_domain_to_replace, new_domain_replacement)