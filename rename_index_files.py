import os

def rename_htm_to_html(repo_folder):
    """
    Renames all files from 'index.html' to 'index.html' in all folders and subfolders
    within the specified repository folder.

    Args:
        repo_folder (str): The path to the root folder of your GitHub repository.
    """

    if not os.path.isdir(repo_folder):
        print(f"Error: '{repo_folder}' is not a valid directory.")
        return

    count = 0
    for root, _, files in os.walk(repo_folder):
        for filename in files:
            if filename == "index.html":
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
        print(f"\nSuccessfully renamed {count} files from 'index.html' to 'index.html'.")
    else:
        print("\nNo files named 'index.html' found to rename.")

if __name__ == "__main__":
    repo_path = input("Enter the path to your GitHub repository folder: ")
    repo_path = repo_path.strip() # Remove leading/trailing whitespace

    if not repo_path:
        print("Repository path cannot be empty.")
    else:
        rename_htm_to_html(repo_path)