import os

def rename_htm_to_html(repo_folder):
    """
    Renames all files from 'index.htm' to 'index.html' in all folders and subfolders
    within the specified repository folder, skipping Python files (.py).

    Args:
        repo_folder (str): The path to the root folder of your GitHub repository.
    """

    if not os.path.isdir(repo_folder):
        print(f"Error: '{repo_folder}' is not a valid directory.")
        return

    count = 0
    skipped_python_files = 0 # Counter for skipped Python files
    for root, _, files in os.walk(repo_folder):
        for filename in files:
            if filename.lower().endswith(".py"): # Check if it's a Python file
                skipped_python_files += 1
                print(f"Skipping Python file: '{os.path.join(root, filename)}'")
                continue # Skip to the next file in the loop

            if filename == "index.htm":
                old_filepath = os.path.join(root, filename)
                new_filename = "index.html"
                new_filepath = os.path.join(root, new_filename)

                try:
                    os.rename(old_filepath, new_filepath)
                    print(f"Renamed: '{old_filepath}' to '{new_filepath}'")
                    count += 1
                except OSError as e:
                    print(f"Error renaming '{old_filepath}': {e}")

    if count > 0:
        print(f"\nSuccessfully renamed {count} files from 'index.htm' to 'index.html'.")
    else:
        print("\nNo files named 'index.htm' found to rename.")

    if skipped_python_files > 0:
        print(f"Skipped {skipped_python_files} Python files.")


if __name__ == "__main__":
    repo_path = input("Enter the path to your GitHub repository folder: ")
    repo_path = repo_path.strip() # Remove leading/trailing whitespace

    if not repo_path:
        print("Repository path cannot be empty.")
    else:
        rename_htm_to_html(repo_path)