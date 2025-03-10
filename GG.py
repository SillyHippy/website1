import os

def remove_favicon_references(repo_path):
    """
    Searches all HTML files in a repository and removes lines containing
    favicon references at the bottom of the <head> section that match
    a specific URL pattern.

    Args:
        repo_path: The path to the root directory of your GitHub repository.
    """

    target_url_pattern = "wp-content/uploads/2025/02/cropped-cropped-cropped-cropped-ezgif-563de8ffd5926"

    for root, dirs, files in os.walk(repo_path):
        for file in files:
            if file.lower().endswith((".html", ".htm")):
                filepath = os.path.join(root, file)
                print(f"Processing file: {filepath}")

                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        html_content = f.readlines()

                    new_html_lines = []
                    in_head = False
                    head_section_ended = False # Flag to ensure we only process within <head>

                    for line in html_content:
                        line_lower = line.strip().lower()

                        if "<head" in line_lower and not head_section_ended:
                            in_head = True
                            new_html_lines.append(line)
                            continue
                        if "</head" in line_lower:
                            in_head = False
                            head_section_ended = True # Mark head section as ended
                            new_html_lines.append(line)
                            continue

                        if in_head and not head_section_ended:
                            if target_url_pattern in line_lower and ("link" in line_lower or "meta" in line_lower):
                                # Skip lines containing the target favicon references within <head>
                                print(f"  Removed line: {line.strip()}")
                                continue  # Do not append this line to new_html_lines

                        new_html_lines.append(line)

                    # Write the modified content back to the file
                    with open(filepath, 'w', encoding='utf-8') as f:
                        f.writelines(new_html_lines)
                    print(f"  Successfully cleaned: {filepath}")

                except Exception as e:
                    print(f"  Error processing file: {filepath} - {e}")

if __name__ == "__main__":
    repo_path = input("Enter the path to your GitHub repository: ")
    if not os.path.isdir(repo_path):
        print("Error: Invalid repository path.")
    else:
        print(f"Searching for favicon references in HTML files within: {repo_path}")
        remove_favicon_references(repo_path)
        print("Favicon reference removal process complete.")