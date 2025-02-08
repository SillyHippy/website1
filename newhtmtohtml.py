import os

def update_links_in_file(file_path):
    """Update links in an individual HTML file."""
    with open(file_path, 'r', encoding='utf-8') as file:
        content = file.read()

    # Replace .htm with .html in the links
    updated_content = content.replace('.htm', '.html')

    # Write the updated content back to the file
    with open(file_path, 'w', encoding='utf-8') as file:
        file.write(updated_content)
    print(f"Updated links in: {file_path}")

def update_links_in_directory(directory):
    """Walk through the directory and update all HTML files."""
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.html'):
                file_path = os.path.join(root, file)
                update_links_in_file(file_path)

# Specify the directory where your website files are located
directory_path = './your_website_folder'  # Update this path accordingly

# Start updating links in the specified directory
update_links_in_directory(directory_path)
