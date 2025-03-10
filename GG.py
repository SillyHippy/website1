import os

def replace_with_correct_relative_paths(repo_path):
    """
    Searches for specific (incorrect) relative <link> tags in HTML files within a repository and
    replaces them with the correct relative paths pointing to the favicon subdirectory.

    Args:
        repo_path: The path to the root directory of your repository.
    """

    tags_to_remove = [
        '<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />',
        '<link rel="icon" type="image/svg+xml" href="/favicon.svg" />',
        '<link rel="shortcut icon" href="/favicon.ico" />',
        '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />',
        '<link rel="manifest" href="/site.webmanifest" />',
        '<meta name="apple-mobile-web-app-title" content="JLS" />' # Keep meta tag in remove list if you want to replace it
    ]

    replacement_tags = [
        '<link rel="icon" type="image/png" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon-96x96.png" sizes="96x96" />',
        '<link rel="icon" type="image/svg+xml" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.svg" />',
        '<link rel="shortcut icon" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/favicon.ico" />',
        '<link rel="apple-touch-icon" sizes="180x180" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/apple-touch-icon.png" />',
        '<link rel="manifest" href="/website1/oPKyDQlOdjnFtEbtOfVCRpiYRmCLmZut/site.webmanifest" />'
    ]


    for root, _, files in os.walk(repo_path):
        for file in files:
            if file.endswith(('.html', '.htm')):  # Check for HTML files
                filepath = os.path.join(root, file)
                with open(filepath, 'r', encoding='utf-8') as f: # Open with utf-8 encoding
                    content = f.read()

                original_content = content

                for tag in tags_to_remove:
                    content = content.replace(tag, "") # Remove old tags

                if original_content != content: # Only insert new tags if removals happened
                    for new_tag in replacement_tags:
                        content = content.replace("</head>", f"{new_tag}\n</head>") # Insert new tags before </head>

                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.write(content)
                    print(f"Processed and modified: {filepath}")
                else:
                    print(f"Processed (no changes): {filepath}")

if __name__ == "__main__":
    repo_directory = input("Enter the path to your repository: ")
    if os.path.isdir(repo_directory):
        print(f"Starting to process HTML files in: {repo_directory}")
        replace_with_correct_relative_paths(repo_directory)
        print("Finished processing HTML files.")
    else:
        print("Invalid repository path. Please enter a valid directory path.")