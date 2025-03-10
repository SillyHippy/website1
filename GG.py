import os

def replace_logo_url_in_html(repo_path, old_logo_url, new_logo_url):
    """
    Searches all HTML files in a repository and replaces occurrences of the
    old logo URL with the new logo URL.

    Args:
        repo_path: The path to the root directory of your GitHub repository.
        old_logo_url: The URL pattern of the old logo to be replaced.
        new_logo_url: The URL to replace the old logo URL with.
    """

    files_modified = 0
    files_searched = 0

    print(f"Searching for and replacing logo URLs in HTML files within: {repo_path}")
    print(f"  Replacing URL: '{old_logo_url}' with '{new_logo_url}'\n")

    for root, dirs, files in os.walk(repo_path):
        for file in files:
            if file.lower().endswith((".html", ".htm")):
                filepath = os.path.join(root, file)
                files_searched += 1

                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        html_content = f.read()

                    original_content = html_content
                    updated_content = html_content.replace(old_logo_url, new_logo_url)

                    if updated_content != original_content:
                        with open(filepath, 'w', encoding='utf-8') as f:
                            f.write(updated_content)
                        files_modified += 1
                        print(f"  Modified: {filepath}")

                except Exception as e:
                    print(f"  Error processing file: {filepath} - {e}")

    print(f"\nSummary:")
    print(f"  HTML files searched: {files_searched}")
    print(f"  HTML files modified: {files_modified}")
    print(f"  Replaced URL: '{old_logo_url}' with '{new_logo_url}'")


if __name__ == "__main__":
    repo_path = "."  # Assumes script is in repo root
    old_logo_url = "wp-content/uploads/2025/02/cropped-cropped-cropped-cropped-ezgif-563de8ffd5926-e1738902542904.jpg"
    new_logo_url = "/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/logo_NaQ7yBFS.webp"  # MAKE SURE THIS IS CORRECT!

    print("WARNING: This script will REPLACE the old logo URL in your HTML files.")
    print(f"  Old URL: {old_logo_url}")
    print(f"  New URL: {new_logo_url}")
    confirmation = input("Are you sure you want to proceed? (yes/no): ").lower()

    if confirmation == "yes":
        if not os.path.isdir(repo_path):
            print("Error: Invalid repository path.")
        else:
            replace_logo_url_in_html(repo_path, old_logo_url, new_logo_url)
            print("\nLogo URL replacement process complete.")
    else:
        print("Logo URL replacement cancelled by user.")