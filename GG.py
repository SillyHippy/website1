import os

def insert_favicon_links(repo_path):
    """
    Searches for <head> tags in HTML files and inserts favicon <link> tags right after.

    Args:
        repo_path: The path to the root directory of your repository.
    """

    favicon_tags_to_insert = """
<link rel="icon" type="image/png" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.svg" />
<link rel="shortcut icon" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/apple-touch-icon.png" />
<link rel="manifest" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/site.webmanifest" />
"""

    for root, _, files in os.walk(repo_path):
        for file in files:
            if file.endswith(('.html', '.htm')):
                filepath = os.path.join(root, file)
                with open(filepath, 'r', encoding='utf-8') as f:
                    content = f.read()

                head_tag_lower = content.lower() # For case-insensitive search
                head_start_index = head_tag_lower.find('<head>')

                if head_start_index != -1:
                    # Find the end of the <head> tag to insert after it
                    head_end_index = head_tag_lower.find('>', head_start_index) + 1

                    if head_end_index > head_start_index:
                        original_content = content
                        content = content[:head_end_index] + favicon_tags_to_insert + content[head_end_index:]

                        if original_content != content:
                            with open(filepath, 'w', encoding='utf-8') as f:
                                f.write(content)
                            print(f"Processed and modified (favicon links inserted): {filepath}")
                        else:
                            print(f"Processed (no changes needed): {filepath}")
                    else:
                        print(f"Warning: Could not find end of <head> tag in: {filepath}")
                else:
                    print(f"Warning: <head> tag not found in: {filepath}")

if __name__ == "__main__":
    repo_directory = input("Enter the path to your repository: ")
    if os.path.isdir(repo_directory):
        print(f"Starting to process HTML files in: {repo_directory} (inserting favicon links)")
        insert_favicon_links(repo_directory)
        print("Finished processing HTML files (favicon links insertion).")
    else:
        print("Invalid repository path. Please enter a valid directory path.")