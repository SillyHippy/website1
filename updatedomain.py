import os
import re

def update_domain_in_file(file_path):
    """Update domain name in an individual file's content."""
    try:
        # Open the file in read mode and get its content
        with open(file_path, 'r', encoding='utf-8') as file:
            content = file.read()

        # Debug: Show the first 200 characters of the file content
        print(f"Checking content in: {file_path}")
        print(f"Content preview: {content[:200]}")  # Preview the first 200 characters of content

        # Check if the domain "justlegalsolutions.tech" is in the file
        if "justlegalsolutions.tech" in content:
            print(f"Found domain in: {file_path}")
        
        # Replace just "justlegalsolutions.tech" with "justlegalsolutions.org"
        updated_content = re.sub(r'justlegalsolutions\.tech', 'justlegalsolutions.org', content)

        # Check if the content has changed
        if content != updated_content:
            with open(file_path, 'w', encoding='utf-8') as file:
                file.write(updated_content)
            print(f"Updated domain in: {file_path}")
        else:
            print(f"No domain updates needed in: {file_path}")

    except Exception as e:
        print(f"Error processing {file_path}: {e}")

def process_files_in_directory(directory):
    """Process all files in the directory and subdirectories, without file type restrictions."""
    for root, dirs, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            # Print out the files being processed
            print(f"Processing file: {file_path}")
            # Process only relevant files
            if file.endswith(('.html', '.htm', '.css', '.js', '.php', '.txt')):
                update_domain_in_file(file_path)  # Process each file without restriction

# Specify the directory where your website files are located
directory_path = './'  # This will use the current directory where the script is located

# Start processing files in the specified directory
process_files_in_directory(directory_path)
