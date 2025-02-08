import os

def rename_htm_to_html(file_path):
    """Rename files with .htm to .html."""
    if file_path.endswith('.htm'):
        new_file_path = file_path[:-4] + '.html'  # Replace .htm with .html
        os.rename(file_path, new_file_path)
        print(f"Renamed: {file_path} -> {new_file_path}")

def update_links_in_file(file_path):
    """Update links in an individual HTML file."""
    with open(file_path, 'r', encoding='utf-8') as file:
        content = file.read()

    # Replace .htm with .html in the links (only replacing the href or src attributes)
    updated_content = content.replace('.htm', '.html')

    # Only write back to the file if changes were made
    if content != updated_content:
        with open(file_path, 'w', encoding='utf-8') as file:
            file.write(updated_content)
        print(f"Updated links in: {file_path}")
    else:
        print(f"No changes needed in: {file_path}")

def process_files_in_directory(directory):
    """Process all HTML files in the directory."""
    for root, dirs, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)

            # Step 1: Rename any .htm files to .html
            if file.endswith('.htm'):
                rename_htm_to_html(file_path)
            
            # Step 2: Update links in .html files
            if file.endswith('.html'):
                update_links_in_file(file_path)

# Specify the directory where your website files are located
directory_path = './your_website_folder'  # Update this path to match your folder structure

# Start processing files in the specified directory
process_files_in_directory(directory_path)
