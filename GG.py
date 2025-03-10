import os
import shutil

def delete_php_files(repo_path):
    """
    Deletes all PHP files within a given repository path.

    Args:
        repo_path: The path to the root directory of your GitHub repository.
    """

    php_files_deleted = 0
    php_files_found = 0

    print(f"Searching for and deleting PHP files within: {repo_path}")

    for root, dirs, files in os.walk(repo_path):
        for file in files:
            if file.lower().endswith(".php"):
                filepath = os.path.join(root, file)
                php_files_found += 1
                try:
                    os.remove(filepath)
                    php_files_deleted += 1
                    print(f"  Deleted: {filepath}")
                except Exception as e:
                    print(f"  Error deleting: {filepath} - {e}")

    print(f"\nSummary:")
    print(f"  PHP files found: {php_files_found}")
    print(f"  PHP files deleted: {php_files_deleted}")

if __name__ == "__main__":
    repo_path = "."  # Assumes the script is run from the repository root

    print("WARNING: This script will DELETE all PHP files in the repository.")
    confirmation = input("Are you sure you want to proceed? (yes/no): ").lower()

    if confirmation == "yes":
        if not os.path.isdir(repo_path):
            print("Error: Invalid repository path.")
        else:
            delete_php_files(repo_path)
            print("\nPHP file deletion process complete.")
    else:
        print("PHP file deletion cancelled by user.")