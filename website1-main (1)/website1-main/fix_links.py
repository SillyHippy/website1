import os
import re

# Define the base path of your local repo
BASE_DIR = "./"  # Change this if needed

# Define the external domain to replace
OLD_DOMAIN = "https://justlegalsolutions.org"

# Define the local replacement path (modify according to your repo structure)
LOCAL_JS_PATH = "/wp-includes/js/"
LOCAL_CSS_PATH = "/wp-content/css/"

# Regex patterns for detecting script and link tags
SCRIPT_PATTERN = re.compile(r'(<script[^>]+src=["\'])(' + re.escape(OLD_DOMAIN) + r'[^"\']+)(["\'][^>]*>)', re.IGNORECASE)
LINK_PATTERN = re.compile(r'(<link[^>]+href=["\'])(' + re.escape(OLD_DOMAIN) + r'[^"\']+)(["\'][^>]*>)', re.IGNORECASE)

def replace_links_in_file(file_path):
    with open(file_path, "r", encoding="utf-8") as file:
        content = file.read()
    
    # Replace JavaScript links
    def replace_script(match):
        old_url = match.group(2)
        new_url = LOCAL_JS_PATH + os.path.basename(old_url)  # Extract filename and replace path
        print(f"Replacing JS: {old_url} -> {new_url}")
        return match.group(1) + new_url + match.group(3)
    
    # Replace CSS links
    def replace_link(match):
        old_url = match.group(2)
        new_url = LOCAL_CSS_PATH + os.path.basename(old_url)  # Extract filename and replace path
        print(f"Replacing CSS: {old_url} -> {new_url}")
        return match.group(1) + new_url + match.group(3)
    
    new_content = SCRIPT_PATTERN.sub(replace_script, content)
    new_content = LINK_PATTERN.sub(replace_link, new_content)

    # Save the modified file
    with open(file_path, "w", encoding="utf-8") as file:
        file.write(new_content)

def process_html_files(directory):
    for root, _, files in os.walk(directory):
        for file in files:
            if file.endswith(".html"):
                file_path = os.path.join(root, file)
                print(f"Processing: {file_path}")
                replace_links_in_file(file_path)

# Run the script on your repository
process_html_files(BASE_DIR)
