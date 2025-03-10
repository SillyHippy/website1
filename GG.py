import os

def search_repo_for_string(repo_path, search_string):
    """
    Searches all files in a repository for a specific string in filenames and file content.

    Args:
        repo_path: The path to the root directory of your GitHub repository.
        search_string: The string to search for.
    """

    print(f"Searching for '{search_string}' in filenames and file content within: {repo_path}")

    for root, dirs, files in os.walk(repo_path):
        for file in files:
            filepath = os.path.join(root, file)

            # Check if filename contains the search string
            if search_string in file:
                print(f"\nFilename matches: {filepath}")

            try:
                with open(filepath, 'r', encoding='utf-8', errors='ignore') as f: # Added errors='ignore' for robustness
                    file_content = f.readlines()

                for line_number, line in enumerate(file_content, start=1):
                    if search_string in line:
                        print(f"  File content match in: {filepath}, Line {line_number}: {line.strip()}")

            except Exception as e:
                print(f"  Error processing file: {filepath} - {e}")

if __name__ == "__main__":
    repo_path = input("Enter the path to your GitHub repository: ")
    search_string = "563de8ffd5926"  # The string you want to search for

    if not os.path.isdir(repo_path):
        print("Error: Invalid repository path.")
    else:
        search_repo_for_string(repo_path, search_string)
        print("\nSearch process complete.")