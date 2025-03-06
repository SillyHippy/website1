import os
import re

def update_html_links(directory):
    for root, _, files in os.walk(directory):  # Walk through all subdirectories
        for file in files:
            if file.endswith(".html"):  # Only modify .html files
                file_path = os.path.join(root, file)
                
                with open(file_path, "r", encoding="utf-8") as f:
                    content = f.read()

                # Replace anchor links pointing to #Courier
                updated_content = re.sub(
                    r'<a\s+href="#Courier"',
                    '<a href="https://justlegalsolutions.org/#Courier"',
                    content
                )

                # Write back only if changes were made
                if content != updated_content:
                    with open(file_path, "w", encoding="utf-8") as f:
                        f.write(updated_content)
                    print(f"Updated: {file_path}")

# Specify the correct path to your GitHub project
html_directory = r"C:\Users\ianna\OneDrive\Desktop\GitHub\website1"

# Run the function
update_html_links(html_directory)
